<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PegawaiModel;
use CodeIgniter\API\ResponseTrait;

class PegawaiController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new PegawaiModel();
    }

    private function calculateMasaKerja($tanggalMasuk)
    {
        $start = new \DateTime($tanggalMasuk);
        $end   = new \DateTime();
        $diff  = $start->diff($end);
        return "{$diff->y} tahun {$diff->m} bulan";
    }

    public function index()
    {
        $params = $this->request->getGet();
        $builder = $this->model->searchAndFilter($params);
        
        $limit = $params['limit'] ?? 10;
        $page = $params['page'] ?? 1;
        $offset = ($page - 1) * $limit;

        $total = $builder->countAllResults(false);
        $data = $builder->limit($limit, $offset)->findAll();

        foreach ($data as &$row) {
            $row['masa_kerja'] = $this->calculateMasaKerja($row['tanggal_masuk']);
            $row['foto_url'] = $row['foto'] ? base_url('uploads/foto/' . $row['foto']) : null;
        }

        return $this->respond([
            'status' => true,
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => (int)$page,
                'limit' => (int)$limit
            ]
        ]);
    }

    public function create()
    {
        // ... (validation code)
        $user = $_REQUEST['user'] ?? null;
        $data = $this->request->getPost();
        $data['created_by'] = $user->id ?? null;

        $id = $this->model->insert($data);
        return $this->respondCreated(['status' => true, 'message' => 'Pegawai created', 'id' => $id]);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Pegawai not found');
        }

        $user = $_REQUEST['user'] ?? null;
        $data = $this->request->getRawInput();
        $data['updated_by'] = $user->id ?? null;

        $this->model->update($id, $data);
        return $this->respond(['status' => true, 'message' => 'Pegawai updated']);
    }

    public function delete($id = null)
    {
        $pegawai = $this->model->find($id);
        if (!$pegawai) {
            return $this->failNotFound('Pegawai not found');
        }

        // Check if has user with superadmin role
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('pegawai_id', $id)->first();
        if ($user) {
            $roleModel = new \App\Models\RoleModel();
            $role = $roleModel->find($user['role_id']);
            if ($role && $role['slug'] === 'superadmin') {
                return $this->fail('Cannot delete pegawai with superadmin role');
            }
        }

        $this->model->delete($id);
        return $this->respondDeleted(['status' => true, 'message' => 'Pegawai deleted']);
    }

    public function getAutocomplete()
    {
        $q = $this->request->getGet('q');
        if (strlen($q) < 2) return $this->respond(['status' => true, 'data' => []]);

        $data = $this->model->select('id, nama, nip')
            ->like('nama', $q)
            ->orLike('nip', $q)
            ->limit(10)
            ->findAll();

        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function exportPdf()
    {
        $data = $this->model->findAll();
        foreach ($data as &$row) {
            $row['masa_kerja'] = $this->calculateMasaKerja($row['tanggal_masuk']);
        }

        $html = view('exports/pegawai_pdf', ['pegawai' => $data]);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($html);
        
        return $this->response->setHeader('Content-Type', 'application/pdf')
                              ->setBody($mpdf->Output('daftar_pegawai.pdf', 'S'));
    }

    public function uploadFoto($id = null)
    {
        $pegawai = $this->model->find($id);
        if (!$pegawai) {
            return $this->failNotFound('Pegawai not found');
        }

        $file = $this->request->getFile('foto');
        if (!$file->isValid()) {
            return $this->fail($file->getErrorString());
        }

        if (!$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/foto', $newName);
            
            $this->model->update($id, ['foto' => $newName]);
            return $this->respond(['status' => true, 'message' => 'Foto uploaded', 'url' => base_url('uploads/foto/' . $newName)]);
        }

        return $this->fail('Failed to upload foto');
    }
}
