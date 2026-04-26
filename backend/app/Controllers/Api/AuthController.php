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

        $rememberMe = $this->request->getVar('remember_me') === true;
        $jwtLib = new JwtLibrary();
        $token = $jwtLib->generateToken($user, $rememberMe);

        return $this->respond([
            'status' => true,
            'message' => 'Login berhasil',
            'data' => [
                'access_token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'role' => $user['role_nama'],
                    'role_slug' => $user['role_slug']
                ]
            ]
        ]);
    }

    public function me()
    {
        // User data retrieved from superglobal
        $user = $_REQUEST['user'] ?? null;
        return $this->respond([
            'status' => true,
            'data' => $user
        ]);
    }
}
