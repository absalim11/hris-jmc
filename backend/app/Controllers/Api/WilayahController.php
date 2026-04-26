<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProvinsiModel;
use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use CodeIgniter\API\ResponseTrait;

class WilayahController extends BaseController
{
    use ResponseTrait;

    public function getKecamatan()
    {
        $q = $this->request->getGet('q');
        if (strlen($q) < 3) return $this->respond(['status' => true, 'data' => []]);

        $model = new KecamatanModel();
        $data = $model->like('nama', $q)->limit(20)->findAll();
        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function getKabupaten($kecamatan_id)
    {
        $kecModel = new KecamatanModel();
        $kec = $kecModel->find($kecamatan_id);
        if (!$kec) return $this->failNotFound();

        $kabModel = new KabupatenModel();
        $data = $kabModel->find($kec['kabupaten_id']);
        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function getProvinsi($kabupaten_id)
    {
        $kabModel = new KabupatenModel();
        $kab = $kabModel->find($kabupaten_id);
        if (!$kab) return $this->failNotFound();

        $provModel = new ProvinsiModel();
        $data = $provModel->find($kab['provinsi_id']);
        return $this->respond(['status' => true, 'data' => $data]);
    }
}
