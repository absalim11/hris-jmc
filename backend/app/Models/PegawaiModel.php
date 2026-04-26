<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nip', 'nama', 'email', 'no_hp', 'foto', 'tempat_lahir', 'tanggal_lahir',
        'status_kawin', 'jumlah_anak', 'tanggal_masuk', 'jabatan', 'departemen',
        'kecamatan_id', 'kabupaten_id', 'provinsi_id', 'alamat_lengkap', 'status',
        'created_by', 'updated_by'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    public function searchAndFilter($params)
    {
        $builder = $this->select('pegawai.*, kecamatan.nama as kecamatan, kabupaten.nama as kabupaten, provinsi.nama as provinsi')
            ->join('kecamatan', 'kecamatan.id = pegawai.kecamatan_id', 'left')
            ->join('kabupaten', 'kabupaten.id = pegawai.kabupaten_id', 'left')
            ->join('provinsi', 'provinsi.id = pegawai.provinsi_id', 'left');

        if (!empty($params['search'])) {
            $builder->groupStart()
                ->like('pegawai.nama', $params['search'])
                ->orLike('nip', $params['search'])
                ->orLike('jabatan', $params['search'])
                ->groupEnd();
        }

        if (!empty($params['jabatan'])) {
            $builder->whereIn('jabatan', explode(',', $params['jabatan']));
        }

        if (!empty($params['masa_kerja_value'])) {
            $operator = $params['masa_kerja_operator'] ?? '=';
            $years = (int)$params['masa_kerja_value'];
            // masa_kerja = floor((today - tanggal_masuk) / 365.25)
            // Example: masa_kerja > 5 means tanggal_masuk < today - 5 years
            $dateLimit = date('Y-m-d', strtotime("-$years years"));
            
            if ($operator === '>') {
                $builder->where('tanggal_masuk <', $dateLimit);
            } elseif ($operator === '<') {
                $builder->where('tanggal_masuk >', $dateLimit);
            } else {
                // Approximate for '='
                $dateLimitPrev = date('Y-m-d', strtotime("-".($years+1)." years"));
                $builder->where('tanggal_masuk <=', $dateLimit)
                        ->where('tanggal_masuk >', $dateLimitPrev);
            }
        }

        return $builder;
    }
}
