<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run()
    {
        // Clear existing wilayah data to avoid duplicates
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('kecamatan')->truncate();
        $this->db->table('kabupaten')->truncate();
        $this->db->table('provinsi')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $provinsi = [
            ['nama' => 'DKI Jakarta'],
            ['nama' => 'Jawa Barat'],
            ['nama' => 'Jawa Tengah'],
            ['nama' => 'Jawa Timur'],
            ['nama' => 'DI Yogyakarta'],
            ['nama' => 'Banten'],
            ['nama' => 'Bali'],
            ['nama' => 'Sumatera Utara'],
        ];
        $this->db->table('provinsi')->insertBatch($provinsi);

        $provMap = [];
        foreach ($this->db->table('provinsi')->get()->getResult() as $p) {
            $provMap[$p->nama] = $p->id;
        }

        $kabupaten = [
            ['provinsi_id' => $provMap['DKI Jakarta'],    'nama' => 'Jakarta Selatan'],
            ['provinsi_id' => $provMap['DKI Jakarta'],    'nama' => 'Jakarta Pusat'],
            ['provinsi_id' => $provMap['DKI Jakarta'],    'nama' => 'Jakarta Utara'],
            ['provinsi_id' => $provMap['DKI Jakarta'],    'nama' => 'Jakarta Barat'],
            ['provinsi_id' => $provMap['DKI Jakarta'],    'nama' => 'Jakarta Timur'],
            ['provinsi_id' => $provMap['Jawa Barat'],     'nama' => 'Kota Bandung'],
            ['provinsi_id' => $provMap['Jawa Barat'],     'nama' => 'Kota Bogor'],
            ['provinsi_id' => $provMap['Jawa Barat'],     'nama' => 'Kota Bekasi'],
            ['provinsi_id' => $provMap['Jawa Barat'],     'nama' => 'Kota Depok'],
            ['provinsi_id' => $provMap['Jawa Tengah'],    'nama' => 'Kota Semarang'],
            ['provinsi_id' => $provMap['Jawa Tengah'],    'nama' => 'Kota Solo'],
            ['provinsi_id' => $provMap['Jawa Timur'],     'nama' => 'Kota Surabaya'],
            ['provinsi_id' => $provMap['Jawa Timur'],     'nama' => 'Kota Malang'],
            ['provinsi_id' => $provMap['DI Yogyakarta'],  'nama' => 'Kota Yogyakarta'],
            ['provinsi_id' => $provMap['DI Yogyakarta'],  'nama' => 'Kabupaten Sleman'],
            ['provinsi_id' => $provMap['DI Yogyakarta'],  'nama' => 'Kabupaten Bantul'],
            ['provinsi_id' => $provMap['Banten'],         'nama' => 'Kota Tangerang'],
            ['provinsi_id' => $provMap['Banten'],         'nama' => 'Kota Tangerang Selatan'],
            ['provinsi_id' => $provMap['Bali'],           'nama' => 'Kota Denpasar'],
            ['provinsi_id' => $provMap['Sumatera Utara'], 'nama' => 'Kota Medan'],
        ];
        $this->db->table('kabupaten')->insertBatch($kabupaten);

        $kabMap = [];
        foreach ($this->db->table('kabupaten')->get()->getResult() as $k) {
            $kabMap[$k->nama] = $k->id;
        }

        $kecamatan = [
            // Jakarta Selatan
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Kebayoran Baru'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Kebayoran Lama'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Pesanggrahan'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Cilandak'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Pasar Minggu'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Jagakarsa'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Mampang Prapatan'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Pancoran'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Tebet'],
            ['kabupaten_id' => $kabMap['Jakarta Selatan'], 'nama' => 'Setiabudi'],
            // Jakarta Pusat
            ['kabupaten_id' => $kabMap['Jakarta Pusat'],   'nama' => 'Menteng'],
            ['kabupaten_id' => $kabMap['Jakarta Pusat'],   'nama' => 'Gambir'],
            ['kabupaten_id' => $kabMap['Jakarta Pusat'],   'nama' => 'Tanah Abang'],
            ['kabupaten_id' => $kabMap['Jakarta Pusat'],   'nama' => 'Senen'],
            // Jakarta Barat
            ['kabupaten_id' => $kabMap['Jakarta Barat'],   'nama' => 'Kebon Jeruk'],
            ['kabupaten_id' => $kabMap['Jakarta Barat'],   'nama' => 'Taman Sari'],
            // Jakarta Timur
            ['kabupaten_id' => $kabMap['Jakarta Timur'],   'nama' => 'Matraman'],
            ['kabupaten_id' => $kabMap['Jakarta Timur'],   'nama' => 'Jatinegara'],
            ['kabupaten_id' => $kabMap['Jakarta Timur'],   'nama' => 'Kramat Jati'],
            // Jakarta Utara
            ['kabupaten_id' => $kabMap['Jakarta Utara'],   'nama' => 'Penjaringan'],
            ['kabupaten_id' => $kabMap['Jakarta Utara'],   'nama' => 'Tanjung Priok'],
            // Bandung
            ['kabupaten_id' => $kabMap['Kota Bandung'],    'nama' => 'Coblong'],
            ['kabupaten_id' => $kabMap['Kota Bandung'],    'nama' => 'Cicendo'],
            ['kabupaten_id' => $kabMap['Kota Bandung'],    'nama' => 'Sukajadi'],
            ['kabupaten_id' => $kabMap['Kota Bandung'],    'nama' => 'Bandung Wetan'],
            // Bekasi
            ['kabupaten_id' => $kabMap['Kota Bekasi'],     'nama' => 'Bekasi Utara'],
            ['kabupaten_id' => $kabMap['Kota Bekasi'],     'nama' => 'Bekasi Selatan'],
            ['kabupaten_id' => $kabMap['Kota Bekasi'],     'nama' => 'Bekasi Timur'],
            ['kabupaten_id' => $kabMap['Kota Bekasi'],     'nama' => 'Bekasi Barat'],
            // Depok
            ['kabupaten_id' => $kabMap['Kota Depok'],      'nama' => 'Beji'],
            ['kabupaten_id' => $kabMap['Kota Depok'],      'nama' => 'Pancoran Mas'],
            ['kabupaten_id' => $kabMap['Kota Depok'],      'nama' => 'Sukmajaya'],
            // Tangerang
            ['kabupaten_id' => $kabMap['Kota Tangerang'],              'nama' => 'Cipondoh'],
            ['kabupaten_id' => $kabMap['Kota Tangerang'],              'nama' => 'Benda'],
            ['kabupaten_id' => $kabMap['Kota Tangerang Selatan'],      'nama' => 'Ciputat'],
            ['kabupaten_id' => $kabMap['Kota Tangerang Selatan'],      'nama' => 'Pamulang'],
            ['kabupaten_id' => $kabMap['Kota Tangerang Selatan'],      'nama' => 'Pondok Aren'],
            // Semarang
            ['kabupaten_id' => $kabMap['Kota Semarang'],   'nama' => 'Semarang Selatan'],
            ['kabupaten_id' => $kabMap['Kota Semarang'],   'nama' => 'Semarang Tengah'],
            ['kabupaten_id' => $kabMap['Kota Semarang'],   'nama' => 'Semarang Utara'],
            // Solo
            ['kabupaten_id' => $kabMap['Kota Solo'],       'nama' => 'Banjarsari'],
            ['kabupaten_id' => $kabMap['Kota Solo'],       'nama' => 'Laweyan'],
            // Surabaya
            ['kabupaten_id' => $kabMap['Kota Surabaya'],   'nama' => 'Gubeng'],
            ['kabupaten_id' => $kabMap['Kota Surabaya'],   'nama' => 'Rungkut'],
            ['kabupaten_id' => $kabMap['Kota Surabaya'],   'nama' => 'Sukolilo'],
            // Yogyakarta
            ['kabupaten_id' => $kabMap['Kota Yogyakarta'], 'nama' => 'Gondokusuman'],
            ['kabupaten_id' => $kabMap['Kota Yogyakarta'], 'nama' => 'Umbulharjo'],
            ['kabupaten_id' => $kabMap['Kota Yogyakarta'], 'nama' => 'Mergangsan'],
            ['kabupaten_id' => $kabMap['Kabupaten Sleman'],'nama' => 'Depok'],
            ['kabupaten_id' => $kabMap['Kabupaten Sleman'],'nama' => 'Mlati'],
            ['kabupaten_id' => $kabMap['Kabupaten Bantul'],'nama' => 'Bantul'],
            ['kabupaten_id' => $kabMap['Kabupaten Bantul'],'nama' => 'Sewon'],
            // Malang
            ['kabupaten_id' => $kabMap['Kota Malang'],     'nama' => 'Klojen'],
            ['kabupaten_id' => $kabMap['Kota Malang'],     'nama' => 'Lowokwaru'],
            // Medan
            ['kabupaten_id' => $kabMap['Kota Medan'],      'nama' => 'Medan Baru'],
            ['kabupaten_id' => $kabMap['Kota Medan'],      'nama' => 'Medan Kota'],
            // Denpasar
            ['kabupaten_id' => $kabMap['Kota Denpasar'],   'nama' => 'Denpasar Selatan'],
            ['kabupaten_id' => $kabMap['Kota Denpasar'],   'nama' => 'Denpasar Utara'],
        ];
        $this->db->table('kecamatan')->insertBatch($kecamatan);

        echo "Wilayah seed: " . count($provinsi) . " provinsi, " . count($kabupaten) . " kabupaten, " . count($kecamatan) . " kecamatan.\n";
    }
}
