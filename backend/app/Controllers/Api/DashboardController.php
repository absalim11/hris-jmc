<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PegawaiModel;
use App\Models\TunjanganTransportModel;
use CodeIgniter\API\ResponseTrait;

class DashboardController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $user = $_REQUEST['user'] ?? null;
        $role_slug = null;
        if (is_object($user)) {
            $role_slug = $user->role_slug ?? ($user->role ?? null);
        } elseif (is_array($user)) {
            $role_slug = $user['role_slug'] ?? ($user['role'] ?? null);
        }

        if (in_array($role_slug, ['superadmin', 'admin_hrd'])) {
            return $this->admin();
        } elseif (in_array($role_slug, ['manager_hrd', 'manager'])) {
            return $this->manager();
        }

        // default
        return $this->manager();
    }

    public function manager()
    {
        $model = new PegawaiModel();
        
        // 1. Widgets
        $total = $model->where('status', 1)->countAllResults();
        $staf  = $model->where('status', 1)->where('jabatan', 'Staf')->countAllResults();
        $manager = $model->where('status', 1)->where('jabatan', 'Manager')->countAllResults();
        $magang = $model->where('status', 1)->where('jabatan', 'Magang')->countAllResults();

        // 2. Charts Data (Gender)
        $db = \Config\Database::connect();
        $gender = $db->table('pegawai')
            ->select('COALESCE(jenis_kelamin, "Tidak Diketahui") as jenis_kelamin, count(*) as total')
            ->groupBy('jenis_kelamin')
            ->get()
            ->getResult();

        // 3. Tabular 5 newest contract (Staf)
        $latest = $model->where('jabatan', 'Staf')->orderBy('tanggal_masuk', 'DESC')->limit(5)->findAll();

        return $this->respond([
            'status' => true,
            'data' => [
                'widgets' => [
                    'total' => $total,
                    'staf' => $staf,
                    'manager' => $manager,
                    'magang' => $magang
                ],
                'gender_chart' => $gender,
                'latest_staf' => $latest
            ]
        ]);
    }

    public function admin()
    {
        $model = new PegawaiModel();
        $tModel = new TunjanganTransportModel();

        $total = $model->where('status',1)->countAllResults();
        $staf  = $model->where('status',1)->where('jabatan','Staf')->countAllResults();
        $manager = $model->where('status',1)->where('jabatan','Manager')->countAllResults();
        $magang = $model->where('status',1)->where('jabatan','Magang')->countAllResults();

        $db = \Config\Database::connect();
        $tuning = $db->table('tunjangan_transport')->select('COALESCE(SUM(total_tunjangan),0) as total')->get()->getRowArray();
        $totalTunjangan = isset($tuning['total']) ? (float)$tuning['total'] : 0.0;

        return $this->respond([
            'status' => true,
            'data' => [
                'widgets' => [
                    'total' => $total,
                    'staf' => $staf,
                    'manager' => $manager,
                    'magang' => $magang,
                    'tunjangan_total' => $totalTunjangan
                ]
            ]
        ]);
    }
}
