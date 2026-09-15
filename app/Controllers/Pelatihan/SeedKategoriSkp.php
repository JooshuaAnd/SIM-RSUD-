<?php
// This script runs inside CodeIgniter as a controller
// Access: http://localhost:8081/pelatihan/seed_kategori_skp
namespace App\Controllers\Pelatihan;
use App\Controllers\BaseController;

class SeedKategoriSkp extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $existing = $db->table('master_kategori_skp_pelatihan')->countAllResults();
        
        if ($existing <= 5) {
            // Truncate and re-insert with complete data
            $db->table('master_kategori_skp_pelatihan')->truncate();
            $data = [
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Peserta Seminar'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Moderator Pada Seminar / Webinar'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Peserta Konferensi/Simposium'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Pembicara/Narasumber dalam kegiatan Konferensi/Simposium'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Moderator Konferensi/Simposium'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Pembicara/Narasumber dalam Kegiatan Seminar'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Peserta Pelatihan/Workshop'],
                ['ranah' => 'Pembelajaran', 'nama_kategori' => 'Pembicara/Narasumber dalam kegiatan Pelatihan/Workshop'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Program penanggulangan TBC'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Pemeriksaan/Diagnosis'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Pemeriksaan Laboratorium / penunjang lainnya'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Melakukan tindakan Intervensi keprofesian tertentu'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Pelayanan Administratif Keprofesian'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Penanggulangan Kejadian Luar Biasa (KLB)/Wabah/Bencana'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Penelitian'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Publikasi'],
                ['ranah' => 'Pelayanan', 'nama_kategori' => 'Kegiatan Manajerial pelayanan kesehatan'],
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
            echo 'Berhasil insert ' . count($data) . ' kategori SKP default.';
        } else {
            echo 'Data sudah ada (' . $existing . ' baris), skip.';
        }
        
        $all = $db->table('master_kategori_skp_pelatihan')->orderBy('ranah')->get()->getResultArray();
        echo '<br><br><strong>Semua data:</strong><br>';
        foreach ($all as $row) {
            echo '[' . $row['id'] . '] ' . $row['ranah'] . ' — ' . $row['nama_kategori'] . '<br>';
        }
    }
}
