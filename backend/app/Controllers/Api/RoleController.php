<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

class RoleController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new RoleModel();
    }

    public function index()
    {
        $roles = $this->model->findAll();
        // Count users per role
        $userModel = new UserModel();
        foreach ($roles as &$role) {
            $role['user_count'] = $userModel->where('role_id', $role['id'])->countAllResults();
        }

        return $this->respond([
            'status' => true,
            'data' => $roles
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost();
        if (!$this->validate([
            'slug' => 'required|is_unique[roles.slug]',
            'nama' => 'required'
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        $this->model->insert($data);
        return $this->respondCreated(['status' => true, 'message' => 'Role created']);
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        if (!$this->model->find($id)) {
            return $this->failNotFound('Role not found');
        }

        $this->model->update($id, $data);
        return $this->respond(['status' => true, 'message' => 'Role updated']);
    }

    public function delete($id = null)
    {
        $role = $this->model->find($id);
        if (!$role) {
            return $this->failNotFound('Role not found');
        }

        // Check if default role
        if (in_array($role['slug'], ['superadmin', 'manager_hrd', 'admin_hrd'])) {
            return $this->fail('Cannot delete default roles');
        }

        // Check if used by users
        $userModel = new UserModel();
        if ($userModel->where('role_id', $id)->countAllResults() > 0) {
            return $this->fail('Role is still used by users');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['status' => true, 'message' => 'Role deleted']);
    }
}
