<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialTables extends Migration
{
    public function up()
    {
        // 1. Roles
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('roles');

        // 2. Permissions
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'modul' => ['type' => 'VARCHAR', 'constraint' => 100],
            'aksi' => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('permissions');

        // 3. Role Permissions
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'role_id' => ['type' => 'INT', 'unsigned' => true],
            'permission_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissions');

        // 4. Wilayah Tables
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('provinsi');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'provinsi_id' => ['type' => 'INT', 'unsigned' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('provinsi_id', 'provinsi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kabupaten');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kabupaten_id' => ['type' => 'INT', 'unsigned' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kabupaten_id', 'kabupaten', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kecamatan');

        // 5. Pegawai (Circular ref with users, will add FK later or create table first)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nip' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 150],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'no_hp' => ['type' => 'VARCHAR', 'constraint' => 20],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 100],
            'tanggal_lahir' => ['type' => 'DATE'],
            'status_kawin' => ['type' => 'ENUM', 'constraint' => ['kawin', 'tidak kawin'], 'default' => 'tidak kawin'],
            'jumlah_anak' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'null' => true],
            'tanggal_masuk' => ['type' => 'DATE'],
            'jabatan' => ['type' => 'ENUM', 'constraint' => ['Manager', 'Staf', 'Magang']],
            'departemen' => ['type' => 'ENUM', 'constraint' => ['Marketing', 'HRD', 'Production', 'Executive', 'Commissioner']],
            'kecamatan_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'kabupaten_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'provinsi_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'alamat_lengkap' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('pegawai');

        // 6. Pegawai Pendidikan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'unsigned' => true],
            'jenjang' => ['type' => 'VARCHAR', 'constraint' => 50],
            'urutan' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pegawai_id', 'pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pegawai_pendidikan');

        // 7. Users
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'unsigned' => true, 'unique' => true],
            'role_id' => ['type' => 'INT', 'unsigned' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'no_hp' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pegawai_id', 'pegawai', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('users');

        // 8. Other Tables
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'jti' => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'expired_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('token_blacklist');

        $this->forge->addField([
            'user_id' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('force_logout_queue');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'base_fare' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'berlaku_dari' => ['type' => 'DATE'],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('setting_tunjangan_transport');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'unsigned' => true],
            'periode_bulan' => ['type' => 'TINYINT', 'constraint' => 2],
            'periode_tahun' => ['type' => 'SMALLINT', 'constraint' => 4],
            'jarak_km' => ['type' => 'DECIMAL', 'constraint' => '8,2'],
            'jarak_km_dibulatkan' => ['type' => 'TINYINT', 'unsigned' => true],
            'hari_masuk' => ['type' => 'TINYINT', 'unsigned' => true],
            'base_fare' => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'total_tunjangan' => ['type' => 'DECIMAL', 'constraint' => '14,2'],
            'catatan' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('pegawai_id', 'pegawai', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addUniqueKey(['pegawai_id', 'periode_bulan', 'periode_tahun']);
        $this->forge->createTable('tunjangan_transport');

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'role' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'modul' => ['type' => 'VARCHAR', 'constraint' => 100],
            'aksi' => ['type' => 'VARCHAR', 'constraint' => 50],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('activity_log');
    }

    public function down()
    {
        $this->forge->dropTable('activity_log', true);
        $this->forge->dropTable('tunjangan_transport', true);
        $this->forge->dropTable('setting_tunjangan_transport', true);
        $this->forge->dropTable('force_logout_queue', true);
        $this->forge->dropTable('token_blacklist', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('pegawai_pendidikan', true);
        $this->forge->dropTable('pegawai', true);
        $this->forge->dropTable('kecamatan', true);
        $this->forge->dropTable('kabupaten', true);
        $this->forge->dropTable('provinsi', true);
        $this->forge->dropTable('role_permissions', true);
        $this->forge->dropTable('permissions', true);
        $this->forge->dropTable('roles', true);
    }
}
