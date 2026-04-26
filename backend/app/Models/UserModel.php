<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pegawai_id', 'role_id', 'username', 'email', 'password', 'status', 'created_by', 'updated_by'];
    protected $useTimestamps = true;

    public function getWithRole($identifier)
    {
        return $this->select('users.*, roles.nama as role_nama, roles.slug as role_slug, pegawai.nama as nama')
            ->join('roles', 'roles.id = users.role_id')
            ->join('pegawai', 'pegawai.id = users.pegawai_id')
            ->where('username', $identifier)
            ->orWhere('users.email', $identifier)
            ->orWhere('users.no_hp', $identifier)
            ->first();
    }
}
