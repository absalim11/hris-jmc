<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\RolePermissionModel;
use CodeIgniter\API\ResponseTrait;

class RbacController extends BaseController
{
    use ResponseTrait;

    /**
     * Return role -> permissions matrix.
     * Each role includes ['id','slug','nama','permissions' => [ ['modul','aksi'], ... ] ]
     */
    public function matrix()
    {
        $roleModel = new RoleModel();
        $roles = $roleModel->findAll();

        $db = \Config\Database::connect();

        foreach ($roles as &$role) {
            $perms = $db->table('role_permissions')
                ->select('permissions.modul, permissions.aksi')
                ->join('permissions', 'permissions.id = role_permissions.permission_id')
                ->where('role_permissions.role_id', $role['id'])
                ->get()
                ->getResultArray();
            $role['permissions'] = $perms;
        }

        return $this->respond(['status' => true, 'data' => $roles]);
    }
}
