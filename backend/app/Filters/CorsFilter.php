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
        $method = strtoupper($request->getMethod() ?: '');
        $origin = $request->getHeaderLine('Origin') ?: $request->getServer('HTTP_ORIGIN');

        // Preflight
        if ($method === 'OPTIONS') {
            $response = Services::response();
            $this->setCorsHeaders($response, $origin);
            // Return immediately for preflight so other filters do not run
            return $response->setStatusCode(200)->setBody('');
        }

        // For regular requests, ensure headers are present on PHP global level as fallback
        if ($origin) {
            header('Access-Control-Allow-Origin: ' . $origin);
        } else {
            header('Access-Control-Allow-Origin: *');
        }
        header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Expose-Headers: X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset');
        header('Access-Control-Max-Age: 86400');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ensure every outgoing response has CORS headers
        $origin = $request->getHeaderLine('Origin') ?: $request->getServer('HTTP_ORIGIN');
        $this->setCorsHeaders($response, $origin);
    }

    private function setCorsHeaders(ResponseInterface $response, $origin = null)
    {
        if ($origin) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        } else {
            $response->setHeader('Access-Control-Allow-Origin', '*');
        }
        $response->setHeader('Access-Control-Allow-Headers', 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
        $response->setHeader('Access-Control-Expose-Headers', 'X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset');
        $response->setHeader('Access-Control-Max-Age', '86400');
        return $response;
    }
}
