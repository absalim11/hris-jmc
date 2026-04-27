<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\JwtLibrary;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $rules = [
            'identifier' => 'required',
            'password'   => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $identifier = $this->request->getVar('identifier');
        $password   = $this->request->getVar('password');

        $userModel = new UserModel();
        $user = $userModel->getWithRole($identifier);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Kredensial tidak valid');
        }

        if ($user['status'] == 0) {
            return $this->failForbidden('Akun Anda tidak aktif');
        }

        $rememberMe = filter_var($this->request->getVar('remember_me'), FILTER_VALIDATE_BOOLEAN);
        $jwtLib = new JwtLibrary();
        $token = $jwtLib->generateToken($user, $rememberMe);
        $refreshToken = $jwtLib->generateRefreshToken($user);

        // fetch permissions for this user's role
        $db = \Config\Database::connect();
        $perms = $db->table('role_permissions')
            ->select('permissions.modul, permissions.aksi')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $user['role_id'])
            ->get()
            ->getResultArray();
        $permList = [];
        foreach ($perms as $p) {
            $permList[] = ($p['modul'] ?? '') . ':' . ($p['aksi'] ?? '');
        }

        return $this->respond([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'user' => [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'role' => $user['role_nama'],
                    'role_slug' => $user['role_slug'],
                    'permissions' => $permList
                ]
            ]
        ]);
    }

    public function me()
    {
        // User data retrieved from superglobal (populated by auth filter)
        $user = $_REQUEST['user'] ?? null;
        if (!$user) return $this->respond(['status' => false, 'message' => 'User not authenticated'], 401);

        $u = is_array($user) ? $user : (is_object($user) ? (array)$user : []);
        $db = \Config\Database::connect();
        $roleId = $u['role_id'] ?? null;
        if (!$roleId && isset($u['role_slug'])) {
            $r = $db->table('roles')->select('id')->where('slug', $u['role_slug'])->get()->getRowArray();
            $roleId = $r['id'] ?? null;
        }

        $permList = [];
        if ($roleId) {
            $perms = $db->table('role_permissions')
                ->select('permissions.modul, permissions.aksi')
                ->join('permissions', 'permissions.id = role_permissions.permission_id')
                ->where('role_permissions.role_id', $roleId)
                ->get()
                ->getResultArray();
            foreach ($perms as $p) {
                $permList[] = ($p['modul'] ?? '') . ':' . ($p['aksi'] ?? '');
            }
        }
        $u['permissions'] = $permList;

        return $this->respond([
            'status' => true,
            'data' => $u
        ]);
    }

    public function refresh()
    {
        $rules = ['refresh_token' => 'required'];
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $refresh = $this->request->getVar('refresh_token');
        $jwtLib = new JwtLibrary();
        $decoded = $jwtLib->decodeToken($refresh);
        if (!$decoded || (($decoded->typ ?? '') !== 'refresh')) {
            return $this->failUnauthorized('Invalid refresh token');
        }

        $userModel = new UserModel();
        $user = $userModel->select('users.*, roles.nama as role_nama, roles.slug as role_slug, pegawai.nama as nama')
            ->join('roles', 'roles.id = users.role_id')
            ->join('pegawai', 'pegawai.id = users.pegawai_id')
            ->where('users.id', $decoded->sub)
            ->first();

        if (!$user) return $this->failNotFound('User not found');
        if ($user['status'] == 0) return $this->failForbidden('Akun Anda tidak aktif');

        $accessToken = $jwtLib->generateToken($user, false);
        $newRefresh = $jwtLib->generateRefreshToken($user);

        // fetch permissions for this user's role
        $db = \Config\Database::connect();
        $perms = $db->table('role_permissions')
            ->select('permissions.modul, permissions.aksi')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $user['role_id'])
            ->get()
            ->getResultArray();
        $permList = [];
        foreach ($perms as $p) {
            $permList[] = ($p['modul'] ?? '') . ':' . ($p['aksi'] ?? '');
        }

        return $this->respond([
            'status' => true,
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $newRefresh,
                'user' => [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'role' => $user['role_nama'],
                    'role_slug' => $user['role_slug'],
                    'permissions' => $permList
                ]
            ]
        ]);
    }
}
