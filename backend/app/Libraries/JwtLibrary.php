<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtLibrary
{
    private $key;

    public function __construct()
    {
        $this->key = getenv('JWT_SECRET') ?: 'default_secret';
    }

    public function generateToken($user, $rememberMe = false)
    {
        $iat = time();
        $exp = $rememberMe ? ($iat + (30 * 24 * 3600)) : ($iat + 3600); // 30 days vs 1 hour

        $payload = [
            'iss' => 'jmc-employee-app',
            'iat' => $iat,
            'exp' => $exp,
            'sub' => $user['id'],
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'nama' => $user['nama'],
                'role_id' => $user['role_id'],
                'role_nama' => $user['role_nama'],
                'role_slug' => $user['role_slug'],
            ]
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function decodeToken($token)
    {
        try {
            return JWT::decode($token, new Key($this->key, 'HS256'));
        } catch (Exception $e) {
            return null;
        }
    }
}
