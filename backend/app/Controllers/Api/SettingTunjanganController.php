<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SettingTunjanganModel;
use CodeIgniter\API\ResponseTrait;

class SettingTunjanganController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new SettingTunjanganModel();
    }

    public function index()
    {
        return $this->respond(['status' => true, 'data' => $this->model->orderBy('berlaku_dari', 'DESC')->findAll()]);
    }

    public function create()
    {
        $user = $_REQUEST['user'] ?? null;
        $data = $this->request->getPost();
        $data['created_by'] = $user->id ?? null;
        $this->model->insert($data);
        return $this->respondCreated(['status' => true, 'message' => 'Setting saved']);
    }

    public function getAktif()
    {
        return $this->respond(['status' => true, 'data' => $this->model->getAktif()]);
    }
}
