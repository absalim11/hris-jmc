<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle Pre-flight request (OPTIONS)
        if ($request->getMethod() === 'options') {
            $response = Services::response();
            $this->setCorsHeaders($response);
            return $response->setStatusCode(200)->setBody('');
        }

        // Set headers for regular requests
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ensure every outgoing response has CORS headers
        $this->setCorsHeaders($response);
    }

    private function setCorsHeaders(ResponseInterface $response)
    {
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        return $response;
    }
}
