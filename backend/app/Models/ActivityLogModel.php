<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'username', 'role', 'modul', 'aksi', 'deskripsi', 'ip_address', 'user_agent', 'created_at'];
    protected $useTimestamps = false;
}
