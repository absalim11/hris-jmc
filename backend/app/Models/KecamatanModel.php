<?php

namespace App\Models;

use CodeIgniter\Model;

class KecamatanModel extends Model
{
    protected $table         = 'kecamatan';
    protected $allowedFields = ['kabupaten_id', 'nama'];
}
