<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvinsiModel extends Model { protected $table = 'provinsi'; protected $allowedFields = ['nama']; }
class KabupatenModel extends Model { protected $table = 'kabupaten'; protected $allowedFields = ['provinsi_id', 'nama']; }
class KecamatanModel extends Model { protected $table = 'kecamatan'; protected $allowedFields = ['kabupaten_id', 'nama']; }
