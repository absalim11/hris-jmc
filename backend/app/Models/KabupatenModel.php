<?php

namespace App\Models;

use CodeIgniter\Model;

class KabupatenModel extends Model
{
    protected $table         = 'kabupaten';
    protected $allowedFields = ['provinsi_id', 'nama'];
}
