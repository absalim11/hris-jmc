<?php

namespace App\Models;

use CodeIgniter\Model;

class ForceLogoutModel extends Model
{
    protected $table = 'force_logout_queue';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['user_id', 'created_at'];
    protected $useTimestamps = false;
}
