<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JwtLibrary;
use Config\Services;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');
        if (!$authHeader) {
            return Services::response()
                ->setJSON(['status' => false, 'message' => 'Token required'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $jwtLib = new JwtLibrary();
        $decoded = $jwtLib->decodeToken($token);

        if (!$decoded) {
            return Services::response()
                ->setJSON(['status' => false, 'message' => 'Invalid or expired token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        // Inject user data into $_REQUEST for global access via getVar('user')
        $_REQUEST['user'] = $decoded->user;

        // Check force logout queue
        $flModel = new \App\Models\ForceLogoutModel();
        if ($flModel->find($decoded->user->id)) {
            return Services::response()
                ->setJSON(['status' => false, 'message' => 'Your account has been deactivated. Please login again.'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
