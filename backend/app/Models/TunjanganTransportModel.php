<?php

namespace App\Models;

use CodeIgniter\Model;

class TunjanganTransportModel extends Model
{
    protected $table = 'tunjangan_transport';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pegawai_id', 'periode_bulan', 'periode_tahun', 'jarak_km', 
        'jarak_km_dibulatkan', 'hari_masuk', 'base_fare', 
        'total_tunjangan', 'catatan', 'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;

    public function getWithPegawai($filters = [])
    {
        $builder = $this->select('tunjangan_transport.*, pegawai.nama as pegawai_nama')
            ->join('pegawai', 'pegawai.id = tunjangan_transport.pegawai_id');
        
        if (!empty($filters['pegawai_id'])) {
            $builder->where('tunjangan_transport.pegawai_id', $filters['pegawai_id']);
        }

        return $builder;
    }
}
