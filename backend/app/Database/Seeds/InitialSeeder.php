<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // 1. Roles
        $roles = [
            ['slug' => 'superadmin', 'nama' => 'Superadmin', 'deskripsi' => 'Administrator tertinggi'],
            ['slug' => 'manager_hrd', 'nama' => 'Manager HRD', 'deskripsi' => 'Pengelola SDM'],
            ['slug' => 'admin_hrd', 'nama' => 'Admin HRD', 'deskripsi' => 'Staf administrasi HRD'],
        ];
        $this->db->table('roles')->insertBatch($roles);

        // 2. Permissions
        $permissions = [
            ['modul' => 'role', 'aksi' => 'create'],
            ['modul' => 'role', 'aksi' => 'read'],
            ['modul' => 'role', 'aksi' => 'update'],
            ['modul' => 'role', 'aksi' => 'delete'],
            ['modul' => 'user', 'aksi' => 'create'],
            ['modul' => 'user', 'aksi' => 'read'],
            ['modul' => 'user', 'aksi' => 'update'],
            ['modul' => 'user', 'aksi' => 'delete'],
            ['modul' => 'pegawai', 'aksi' => 'create'],
            ['modul' => 'pegawai', 'aksi' => 'read'],
            ['modul' => 'pegawai', 'aksi' => 'update'],
            ['modul' => 'pegawai', 'aksi' => 'delete'],
            ['modul' => 'tunjangan', 'aksi' => 'create'],
            ['modul' => 'tunjangan', 'aksi' => 'read'],
            ['modul' => 'tunjangan', 'aksi' => 'update'],
            ['modul' => 'tunjangan', 'aksi' => 'delete'],
            ['modul' => 'setting_tunjangan', 'aksi' => 'create'],
            ['modul' => 'setting_tunjangan', 'aksi' => 'read'],
            ['modul' => 'setting_tunjangan', 'aksi' => 'update'],
            ['modul' => 'setting_tunjangan', 'aksi' => 'delete'],
            ['modul' => 'log', 'aksi' => 'read'],
        ];
        $this->db->table('permissions')->insertBatch($permissions);

        // 3. Role Permissions Mapping (Simple loop for MVP)
        $roleIds = [];
        foreach ($this->db->table('roles')->get()->getResult() as $r) {
            $roleIds[$r->slug] = $r->id;
        }

        $permIds = [];
        foreach ($this->db->table('permissions')->get()->getResult() as $p) {
            $permIds[$p->modul][$p->aksi] = $p->id;
        }

        // Superadmin: All permissions
        $rolePerms = [];
        foreach ($this->db->table('permissions')->get()->getResult() as $p) {
            $rolePerms[] = ['role_id' => $roleIds['superadmin'], 'permission_id' => $p->id];
        }

        // Manager HRD: Read all, Update own user (simplified here)
        // Specific matrix enforcement will be in RbacFilter
        $rolePerms[] = ['role_id' => $roleIds['manager_hrd'], 'permission_id' => $permIds['pegawai']['read']];
        $rolePerms[] = ['role_id' => $roleIds['manager_hrd'], 'permission_id' => $permIds['tunjangan']['read']];

        // Admin HRD: CRUD Pegawai, CRUD Tunjangan, CRUD Setting
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['pegawai']['create']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['pegawai']['read']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['pegawai']['update']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['pegawai']['delete']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['tunjangan']['create']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['tunjangan']['read']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['tunjangan']['update']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['tunjangan']['delete']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['setting_tunjangan']['create']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['setting_tunjangan']['read']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['setting_tunjangan']['update']];
        $rolePerms[] = ['role_id' => $roleIds['admin_hrd'], 'permission_id' => $permIds['setting_tunjangan']['delete']];

        $this->db->table('role_permissions')->insertBatch($rolePerms);

        // 4. Initial Superadmin Pegawai & User
        $pegawaiId = $this->db->table('pegawai')->insert([
            'nip' => '12345678',
            'nama' => 'Master Superadmin',
            'email' => 'admin@jmc.co.id',
            'no_hp' => '+628123456789',
            'tempat_lahir' => 'Yogyakarta',
            'tanggal_lahir' => '1990-01-01',
            'tanggal_masuk' => '2020-01-01',
            'jabatan' => 'Manager',
            'departemen' => 'Executive',
            'status' => 1,
        ]);
        $pegawaiId = $this->db->insertID();

        $password = 'Admin@123'; // Static for MVP, usually auto-gen
        $this->db->table('users')->insert([
            'pegawai_id' => $pegawaiId,
            'role_id' => $roleIds['superadmin'],
            'username' => 'superadmin',
            'email' => 'admin@jmc.co.id',
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'status' => 1,
        ]);

        echo "Initial Superadmin Created:\n";
        echo "Username: superadmin\n";
        echo "Password: $password\n";
    }
}
