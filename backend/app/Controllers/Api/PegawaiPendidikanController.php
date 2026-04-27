<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PegawaiPendidikanModel;
use CodeIgniter\API\ResponseTrait;

class PegawaiPendidikanController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new PegawaiPendidikanModel();
    }

    public function index($pegawai_id = null)
    {
        if (!$pegawai_id) return $this->fail('Pegawai id required');
        $data = $this->model->where('pegawai_id', $pegawai_id)->orderBy('urutan', 'ASC')->findAll();
        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function create($pegawai_id = null)
    {
        if (!$pegawai_id) return $this->fail('Pegawai id required');
        if (!$this->validate(['jenjang' => 'required'])) {
            return $this->fail($this->validator->getErrors());
        }
        $data = [
            'pegawai_id' => $pegawai_id,
            'jenjang' => $this->request->getPost('jenjang'),
            'urutan' => (int)$this->request->getPost('urutan') ?: 0
        ];

        $this->model->insert($data);
        return $this->respondCreated(['status' => true, 'message' => 'Pendidikan added']);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Pendidikan not found');
        $data = $this->request->getRawInput();
        $this->model->update($id, $data);
        return $this->respond(['status' => true, 'message' => 'Pendidikan updated']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) return $this->failNotFound('Pendidikan not found');
        $this->model->delete($id);
        return $this->respondDeleted(['status' => true, 'message' => 'Pendidikan deleted']);
    }
}
