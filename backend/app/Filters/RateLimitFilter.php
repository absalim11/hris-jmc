<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RateLimitFilter implements FilterInterface
{
    protected $storagePath;
    protected $window = 60; // seconds
    protected $defaultLimit = 120; // requests per window
    protected $routeLimits = [
        'api/auth/login' => 10,
        'api/auth/refresh' => 30,
    ];

    public function __construct()
    {
        $this->storagePath = WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . 'rate_limit';
        if (!is_dir($this->storagePath)) @mkdir($this->storagePath, 0755, true);
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip rate limiting for preflight requests
        if (strtoupper($request->getMethod() ?: '') === 'OPTIONS') {
            return $request;
        }

        $path = trim($request->getUri()->getPath(), '/');
        $limit = $this->defaultLimit;
        foreach ($this->routeLimits as $route => $rLimit) {
            $r = trim($route, '/');
            if ($path === $r || strpos($path, $r) === 0) { $limit = $rLimit; break; }
        }

        // determine key: user id (from token) or IP
        $authHeader = $request->getHeaderLine('Authorization');
        $key = null;
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
            $token = $m[1];
            try {
                $jwtLib = new \App\Libraries\JwtLibrary();
                $decoded = $jwtLib->decodeToken($token);
                if ($decoded && isset($decoded->sub)) $key = 'user:' . $decoded->sub;
            } catch (\Throwable $e) {}
        }
        if (!$key) {
            $ip = $request->getIPAddress();
            $key = 'ip:' . $ip;
        }

        $windowStart = floor(time() / $this->window) * $this->window;
        $file = $this->storagePath . DIRECTORY_SEPARATOR . md5($key) . '.json';

        $data = ['window' => $windowStart, 'count' => 0];

        $fp = @fopen($file, 'c+');
        if (!$fp) return; // cannot persist, allow request

        if (flock($fp, LOCK_EX)) {
            clearstatcache(true, $file);
            $size = filesize($file);
            if ($size > 0) {
                rewind($fp);
                $contents = stream_get_contents($fp);
                $existing = json_decode($contents, true);
                if (is_array($existing) && isset($existing['window']) && isset($existing['count'])) $data = $existing;
            }

            if (!isset($data['window']) || $data['window'] != $windowStart) {
                $data['window'] = $windowStart;
                $data['count'] = 1;
            } else {
                $data['count'] = $data['count'] + 1;
            }

            if ($data['count'] > $limit) {
                flock($fp, LOCK_UN);
                fclose($fp);
                $retryAfter = ($data['window'] + $this->window) - time();
                $response = service('response');
                $body = ['status' => false, 'message' => 'Too many requests', 'retry_after' => $retryAfter];
                return $response->setStatusCode(429)->setHeader('Retry-After', (string)$retryAfter)->setJSON($body);
            }

            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($data));
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
        } else {
            fclose($fp);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // add rate-limit headers where possible
        $path = trim($request->getUri()->getPath(), '/');
        $authHeader = $request->getHeaderLine('Authorization');
        $key = null;
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
            $token = $m[1];
            try {
                $jwtLib = new \App\Libraries\JwtLibrary();
                $decoded = $jwtLib->decodeToken($token);
                if ($decoded && isset($decoded->sub)) $key = 'user:' . $decoded->sub;
            } catch (\Throwable $e) {}
        }
        if (!$key) {
            $ip = $request->getIPAddress();
            $key = 'ip:' . $ip;
        }

        $file = $this->storagePath . DIRECTORY_SEPARATOR . md5($key) . '.json';
        if (file_exists($file)) {
            $contents = @file_get_contents($file);
            $data = json_decode($contents, true);
            $limit = $this->defaultLimit;
            foreach ($this->routeLimits as $route => $rLimit) {
                $r = trim($route, '/');
                if ($path === $r || strpos($path, $r) === 0) { $limit = $rLimit; break; }
            }
            $remaining = max(0, $limit - ($data['count'] ?? 0));
            $reset = (($data['window'] ?? 0) + $this->window) - time();
            $response->setHeader('X-RateLimit-Limit', (string)$limit)
                     ->setHeader('X-RateLimit-Remaining', (string)$remaining)
                     ->setHeader('X-RateLimit-Reset', (string)max(0, $reset));
        }
    }
}
