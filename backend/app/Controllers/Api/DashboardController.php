<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PegawaiModel;
use CodeIgniter\API\ResponseTrait;

class DashboardController extends BaseController
{
    use ResponseTrait;

    public function manager()
    {
        $model = new PegawaiModel();
        
        // 1. Widgets
        $total = $model->where('status', 1)->countAllResults();
        $staf  = $model->where('status', 1)->where('jabatan', 'Staf')->countAllResults();
        $manager = $model->where('status', 1)->where('jabatan', 'Manager')->countAllResults();
        $magang = $model->where('status', 1)->where('jabatan', 'Magang')->countAllResults();

        // 2. Charts Data (Gender)
        $db = \Config\Database::connect();
        $gender = $db->table('pegawai')
            ->select('COALESCE(jenis_kelamin, "Tidak Diketahui") as jenis_kelamin, count(*) as total')
            ->groupBy('jenis_kelamin')
            ->get()
            ->getResult();

        // 3. Tabular 5 newest contract (Staf)
        $latest = $model->where('jabatan', 'Staf')->orderBy('tanggal_masuk', 'DESC')->limit(5)->findAll();

        return $this->respond([
            'status' => true,
            'data' => [
                'widgets' => [
                    'total' => $total,
                    'staf' => $staf,
                    'manager' => $manager,
                    'magang' => $magang
                ],
                'gender_chart' => $gender,
                'latest_staf' => $latest
            ]
        ]);
    }
}
