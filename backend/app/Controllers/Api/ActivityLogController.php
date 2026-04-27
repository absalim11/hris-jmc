<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use CodeIgniter\API\ResponseTrait;

class ActivityLogController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new ActivityLogModel();
    }

    public function index()
    {
        $params = $this->request->getGet();
        $limit = isset($params['limit']) ? (int)$params['limit'] : 20;
        $page = isset($params['page']) ? (int)$params['page'] : 1;
        $offset = ($page - 1) * $limit;

        $builder = $this->model->select('*');

        if (!empty($params['modul'])) $builder->where('modul', $params['modul']);
        if (!empty($params['aksi'])) $builder->where('aksi', $params['aksi']);
        if (!empty($params['user_id'])) $builder->where('user_id', (int)$params['user_id']);
        if (!empty($params['from'])) $builder->where('created_at >=', $params['from']);
        if (!empty($params['to'])) $builder->where('created_at <=', $params['to']);

        if (!empty($params['q'])) {
            $q = $params['q'];
            $builder->groupStart()
                    ->like('username', $q)
                    ->orLike('deskripsi', $q)
                    ->orLike('modul', $q)
                    ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('created_at', 'DESC')->limit($limit, $offset)->findAll();

        return $this->respond([
            'status' => true,
            'data' => $data,
            'meta' => ['total' => $total, 'page' => (int)$page, 'limit' => (int)$limit]
        ]);
    }

    public function show($id = null)
    {
        if (!$id) return $this->fail('Log id required');
        $log = $this->model->find($id);
        if (!$log) return $this->failNotFound('Log not found');
        return $this->respond(['status' => true, 'data' => $log]);
    }
}
