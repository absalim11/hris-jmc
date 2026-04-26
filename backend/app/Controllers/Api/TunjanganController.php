<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\TunjanganTransportModel;
use App\Models\SettingTunjanganModel;
use App\Models\PegawaiModel;
use CodeIgniter\API\ResponseTrait;

class TunjanganController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new TunjanganTransportModel();
    }

    public function index()
    {
        $user = $_REQUEST['user'] ?? null;
        $filters = [];
        
        // Manager/Admin HRD (own only in PRD 15.1)
        if ($user && $user->role_slug !== 'superadmin' && $user->role_slug !== 'admin_hrd') {
            // Get pegawai_id of this user
            $userModel = new \App\Models\UserModel();
            $currUser = $userModel->find($user->id);
            $filters['pegawai_id'] = $currUser['pegawai_id'];
        }

        $data = $this->model->getWithPegawai($filters)->findAll();
        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function calculatePreview()
    {
        $jarak = (float)$this->request->getVar('jarak_km');
        $hari  = (int)$this->request->getVar('hari_masuk');
        $pegawaiId = (int)$this->request->getVar('pegawai_id');

        $result = $this->calculate($pegawaiId, $jarak, $hari);
        return $this->respond(['status' => true, 'data' => $result]);
    }

    private function calculate($pegawaiId, $jarak, $hari)
    {
        $pegawaiModel = new PegawaiModel();
        $pegawai = $pegawaiModel->find($pegawaiId);

        if (!$pegawai) return ['error' => 'Pegawai tidak ditemukan'];
        if (!in_array($pegawai['jabatan'], ['Manager', 'Staf'])) return ['error' => 'Pegawai tidak berhak menerima tunjangan'];

        // Rules
        if ($hari < 19) return ['total' => 0, 'reason' => 'Hari masuk < 19'];
        if ($jarak <= 5) return ['total' => 0, 'reason' => 'Jarak <= 5km'];

        $roundedJarak = ($jarak - floor($jarak) < 0.5) ? floor($jarak) : ceil($jarak);
        if ($roundedJarak > 25) $roundedJarak = 25;

        $settingModel = new SettingTunjanganModel();
        $setting = $settingModel->getAktif();
        if (!$setting) return ['error' => 'Setting tunjangan tidak aktif'];

        $total = $setting['base_fare'] * $roundedJarak * $hari;

        return [
            'base_fare' => (float)$setting['base_fare'],
            'jarak_dibulatkan' => (int)$roundedJarak,
            'total' => (float)$total
        ];
    }

    public function create()
    {
        $user = $_REQUEST['user'] ?? null;
        $data = $this->request->getPost();
        $calc = $this->calculate($data['pegawai_id'], $data['jarak_km'], $data['hari_masuk']);

        if (isset($calc['error'])) return $this->fail($calc['error']);

        $data['jarak_km_dibulatkan'] = $calc['jarak_dibulatkan'] ?? 0;
        $data['base_fare'] = $calc['base_fare'] ?? 0;
        $data['total_tunjangan'] = $calc['total'] ?? 0;
        $data['created_by'] = $user->id ?? null;

        try {
            $this->model->insert($data);
        } catch (\Exception $e) {
            return $this->fail('Data untuk periode ini sudah ada');
        }

        return $this->respondCreated(['status' => true, 'message' => 'Tunjangan saved']);
    }
}
