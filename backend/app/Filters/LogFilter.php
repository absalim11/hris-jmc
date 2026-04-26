<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\LogModel;

class LogFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do nothing
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            return;
        }

        $user = $request->user ?? null;
        if (!$user && strpos($request->getUri()->getPath(), 'auth/login') === false) {
            return;
        }

        $path = $request->getUri()->getPath();
        $segments = explode('/', $path);
        // api/v1/modul -> segments[0]=api, [1]=modul
        $modul = $segments[1] ?? 'unknown';
        
        $method = $request->getMethod();
        $aksiMap = [
            'get' => 'read',
            'post' => 'create',
            'put' => 'update',
            'patch' => 'update',
            'delete' => 'delete'
        ];
        $aksi = $aksiMap[strtolower($method)] ?? 'unknown';

        if (strpos($path, 'auth/login') !== false) {
            $modul = 'auth';
            $aksi = 'login';
        }

        $logModel = new LogModel();
        $logModel->insert([
            'user_id' => $user->id ?? null,
            'username' => $user->username ?? 'guest',
            'role' => $user->role_slug ?? 'guest',
            'modul' => $modul,
            'aksi' => $aksi,
            'deskripsi' => "$method request to $path",
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
