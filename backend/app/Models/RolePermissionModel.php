<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role_id', 'permission_id'];

    public function checkPermission($roleId, $modul, $aksi)
    {
        return $this->select('role_permissions.id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_id', $roleId)
            ->where('modul', $modul)
            ->where('aksi', $aksi)
            ->first() !== null;
    }
}
