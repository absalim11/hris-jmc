<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingTunjanganModel extends Model
{
    protected $table = 'setting_tunjangan_transport';
    protected $primaryKey = 'id';
    protected $allowedFields = ['base_fare', 'berlaku_dari', 'keterangan', 'created_by', 'updated_by'];
    protected $useTimestamps = true;

    public function getAktif()
    {
        return $this->where('berlaku_dari <=', date('Y-m-d'))
            ->orderBy('berlaku_dari', 'DESC')
            ->first();
    }
}
