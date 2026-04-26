<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\RolePermissionModel;
use App\Libraries\JwtLibrary;
use Config\Services;

class RbacFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$arguments || count($arguments) < 2) {
            return $request;
        }

        $modul = $arguments[0];
        $aksi  = $arguments[1];
        
        // 1. Coba ambil user dari request var (hasil JwtFilter)
        $user = $request->getVar('user');

        // 2. Jika Kosong, lakukan validasi JWT ulang di sini (Zero Trust)
        if (!$user) {
            $authHeader = $request->getServer('HTTP_AUTHORIZATION') ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
            if (!$authHeader) {
                return Services::response()
                    ->setJSON(['status' => false, 'message' => 'Unauthorized: Token missing'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $token = str_replace('Bearer ', '', $authHeader);
            $jwtLib = new JwtLibrary();
            $decoded = $jwtLib->decodeToken($token);

            if (!$decoded) {
                return Services::response()
                    ->setJSON(['status' => false, 'message' => 'Unauthorized: Invalid token'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            $user = $decoded->user;
            // Simpan ke $_REQUEST agar bisa diakses filter lain/controller
            $_REQUEST['user'] = $user;
        }

        // 3. Superadmin bypass
        if ($user->role_slug === 'superadmin') {
            return $request;
        }

        // 4. Cek Permission ke Database
        $rpModel = new RolePermissionModel();
        if (!$rpModel->checkPermission($user->role_id, $modul, $aksi)) {
            return Services::response()
                ->setJSON(['status' => false, 'message' => 'Forbidden: You do not have permission to ' . $aksi . ' ' . $modul])
                ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
