<?php
// Run from browser: http://localhost:8081/run_seed_kategori_skp.php
// Place this file at: public/run_seed_kategori_skp.php

require __DIR__ . '/../app/Config/Paths.php';

$paths = new Config\Paths();
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', $paths->systemDirectory . DIRECTORY_SEPARATOR);
define('APPPATH', $paths->appDirectory . DIRECTORY_SEPARATOR);
define('WRITEPATH', $paths->writableDirectory . DIRECTORY_SEPARATOR);
define('ROOTPATH', realpath(FCPATH . '..') . DIRECTORY_SEPARATOR);

$app = require_once APPPATH . 'Config/App.php';

// Minimal bootstrap
$bootstrap = SYSTEMPATH . 'bootstrap.php';
$app = require_once $bootstrap;

// Use CodeIgniter's DB
$db = \Config\Database::connect();

// Check existing
$existing = $db->table('master_kategori_skp_pelatihan')->countAllResults();
echo "Existing rows: $existing<br>";

if ($existing === 0) {
    $data = [
        // Pembelajaran
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Peserta Seminar'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Moderator Pada Seminar / Webinar'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Peserta Konferensi/Simposium'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Pembicara/Narasumber dalam kegiatan Konferensi/Simposium'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Moderator Konferensi/Simposium'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Pembicara/Narasumber dalam Kegiatan Seminar'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Peserta Pelatihan/Workshop'],
        ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Pembicara/Narasumber dalam kegiatan Pelatihan/Workshop'],
        // Pelayanan
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Program penanggulangan TBC'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Pemeriksaan/Diagnosis'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Pemeriksaan Laboratorium / penunjang lainnya'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Melakukan tindakan Intervensi keprofesian tertentu'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Pelayanan Administratif Keprofesian'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Penanggulangan Kejadian Luar Biasa (KLB)/Wabah/Bencana'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Penelitian'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Publikasi'],
        ['ranah' => 'Pelayanan', 'nama_kategori' => 'Kegiatan Manajerial pelayanan kesehatan'],
        // Pengabdian
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Program pengabdian penanggulangan TBC'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Kegiatan pelayanan medis, pengobatan massal untuk masyarakat'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Penyuluhan kesehatan/edukasi medis keprofesian'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Penugasan (Khusus) pemerintah'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Keterlibatan dalam tim khusus (relawan bencana, tim haji dll)'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Terlibat dalam organisasi keilmuan atau organisasi masyarakat'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Penyuluhan melalui media Sosial'],
        ['ranah' => 'Pengabdian', 'nama_kategori' => 'Narasumber rubrik kesehatan/wawancara/edukasi di TV/Media massa'],
    ];
    $db->table('master_kategori_skp_pelatihan')->insertBatch($data);
    echo "Berhasil insert " . count($data) . " kategori SKP default.<br>";
} else {
    echo "Data sudah ada, skip insert.<br>";
}

$all = $db->table('master_kategori_skp_pelatihan')->get()->getResultArray();
echo "<pre>"; print_r($all); echo "</pre>";
