<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seed demo users for Manager HRD and Admin HRD roles.
 * Safe to re-run: skips username if it already exists.
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Resolve role IDs
        $roles = [];
        foreach ($db->table('roles')->get()->getResult() as $r) {
            $roles[$r->slug] = $r->id;
        }

        $users = [
            [
                'pegawai' => [
                    'nip'           => '20260001',
                    'nama'          => 'Budi Santoso',
                    'email'         => 'budi.santoso@jmc.co.id',
                    'no_hp'         => '+6281234500001',
                    'tempat_lahir'  => 'Bandung',
                    'tanggal_lahir' => '1985-06-15',
                    'jenis_kelamin' => 'L',
                    'status_kawin'  => 'kawin',
                    'jumlah_anak'   => 2,
                    'tanggal_masuk' => '2018-03-01',
                    'jabatan'       => 'Manager',
                    'departemen'    => 'HRD',
                    'status'        => 1,
                ],
                'user' => [
                    'username' => 'manager_hrd',
                    'email'    => 'budi.santoso@jmc.co.id',
                    'password' => 'Manager@456',
                    'role_key' => 'manager_hrd',
                ],
            ],
            [
                'pegawai' => [
                    'nip'           => '20260002',
                    'nama'          => 'Sari Dewi Lestari',
                    'email'         => 'sari.dewi@jmc.co.id',
                    'no_hp'         => '+6281234500002',
                    'tempat_lahir'  => 'Jakarta',
                    'tanggal_lahir' => '1992-09-22',
                    'jenis_kelamin' => 'P',
                    'status_kawin'  => 'tidak kawin',
                    'jumlah_anak'   => 0,
                    'tanggal_masuk' => '2021-07-15',
                    'jabatan'       => 'Staf',
                    'departemen'    => 'HRD',
                    'status'        => 1,
                ],
                'user' => [
                    'username' => 'admin_hrd',
                    'email'    => 'sari.dewi@jmc.co.id',
                    'password' => 'Admin@456',
                    'role_key' => 'admin_hrd',
                ],
            ],
        ];

        foreach ($users as $entry) {
            $uData = $entry['user'];

            // Skip if username already exists
            $exists = $db->table('users')
                ->where('username', $uData['username'])
                ->countAllResults();
            if ($exists > 0) {
                echo "Skipped (already exists): {$uData['username']}\n";
                continue;
            }

            // Reuse existing pegawai by NIP, or insert new
            $existingPegawai = $db->table('pegawai')
                ->where('nip', $entry['pegawai']['nip'])
                ->get()->getRowArray();

            if ($existingPegawai) {
                $pegawaiId = $existingPegawai['id'];
            } else {
                $db->table('pegawai')->insert($entry['pegawai']);
                $pegawaiId = $db->insertID();
            }

            // Insert user
            $db->table('users')->insert([
                'pegawai_id' => $pegawaiId,
                'role_id'    => $roles[$uData['role_key']],
                'username'   => $uData['username'],
                'email'      => $uData['email'],
                'password'   => password_hash($uData['password'], PASSWORD_BCRYPT),
                'status'     => 1,
            ]);

            echo "Created: {$uData['username']} / {$uData['password']} (role: {$uData['role_key']})\n";
        }
    }
}
