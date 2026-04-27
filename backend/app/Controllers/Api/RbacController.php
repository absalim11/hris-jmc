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

    /**
     * Toggle a single permission for a role.
     * Body: { role_id, modul, aksi }
     * Protected by rbac:role,update filter on the route.
     */
    public function toggle()
    {
        $data   = $this->request->getJSON(true) ?? $this->request->getPost();
        $roleId = (int)($data['role_id'] ?? 0);
        $modul  = trim($data['modul']    ?? '');
        $aksi   = trim($data['aksi']     ?? '');

        if (!$roleId || !$modul || !$aksi) {
            return $this->fail('Parameter role_id, modul, aksi wajib diisi.');
        }

        $db = \Config\Database::connect();

        // Find permission id
        $perm = $db->table('permissions')
            ->where('modul', $modul)->where('aksi', $aksi)
            ->get()->getRowArray();
        if (!$perm) {
            return $this->failNotFound("Permission {$modul}:{$aksi} tidak ditemukan.");
        }

        $exists = $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $perm['id'])
            ->countAllResults() > 0;

        if ($exists) {
            $db->table('role_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $perm['id'])
                ->delete();
            $action = 'revoked';
        } else {
            $db->table('role_permissions')->insert([
                'role_id'       => $roleId,
                'permission_id' => $perm['id'],
            ]);
            $action = 'granted';
        }

        return $this->respond(['status' => true, 'action' => $action]);
    }
}
