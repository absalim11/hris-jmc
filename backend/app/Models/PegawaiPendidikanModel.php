<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiPendidikanModel extends Model
{
    protected $table = 'pegawai_pendidikan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pegawai_id', 'jenjang', 'urutan'];
    protected $useTimestamps = false;
}
