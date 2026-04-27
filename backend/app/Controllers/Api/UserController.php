<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ForceLogoutModel;
use CodeIgniter\API\ResponseTrait;

class UserController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $users = $this->model->select('users.*, roles.nama as role_nama, pegawai.nama as pegawai_nama')
            ->join('roles', 'roles.id = users.role_id')
            ->join('pegawai', 'pegawai.id = users.pegawai_id')
            ->findAll();

        return $this->respond([
            'status' => true,
            'data' => $users
        ]);
    }

    public function create()
    {
        if (!$this->validate([
            'pegawai_id' => 'required|is_unique[users.pegawai_id]',
            'role_id'    => 'required',
            'username'   => ['label' => 'Username', 'rules' => 'required|min_length[6]|is_unique[users.username]|regex_match[/^[a-z0-9]+$/]'],
            'email'      => 'required|valid_email|is_unique[users.email]'
        ])) {
            return $this->fail($this->validator->getErrors());
        }

        $user = $_REQUEST['user'] ?? null;
        $data = $this->request->getPost();
        // Normalize username to lowercase
        if (isset($data['username'])) $data['username'] = strtolower($data['username']);

        // Generate a strong temporary password that satisfies PRD rules
        $upper = chr(rand(65,90));
        $lower = chr(rand(97,122));
        $digit = chr(rand(48,57));
        $specials = '!@#$%^&*()-_+=';
        $special = $specials[rand(0, strlen($specials)-1)];
        $random = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
        $plainPassword = $upper . $lower . $digit . $special . $random; // e.g. At least 8 chars with required categories

        $data['password'] = password_hash($plainPassword, PASSWORD_BCRYPT);
        $data['status'] = 1;
        $data['created_by'] = $user->id ?? null;

        $this->model->insert($data);

        return $this->respondCreated([
            'status' => true,
            'message' => 'User created',
            'data' => ['temp_password' => $plainPassword]
        ]);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('User not found');
        }

        $data = $this->request->getRawInput();
        unset($data['password']); // Don't update password here

        $this->model->update($id, $data);
        return $this->respond(['status' => true, 'message' => 'User updated']);
    }

    public function delete($id = null)
    {
        $user = $_REQUEST['user'] ?? null;
        if ($user && $user->id == $id) {
            return $this->fail('Cannot delete yourself');
        }

        if (!$this->model->find($id)) {
            return $this->failNotFound('User not found');
        }

        $this->model->delete($id);
        return $this->respondDeleted(['status' => true, 'message' => 'User deleted']);
    }

    public function toggleStatus($id = null)
    {
        $user = $this->model->find($id);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $newStatus = $user['status'] == 1 ? 0 : 1;
        $this->model->update($id, ['status' => $newStatus]);

        $flModel = new ForceLogoutModel();
        if ($newStatus == 0) {
            $flModel->insert(['user_id' => $id, 'created_at' => date('Y-m-d H:i:s')]);
        } else {
            $flModel->delete($id);
        }

        return $this->respond(['status' => true, 'message' => 'Status updated']);
    }
}
