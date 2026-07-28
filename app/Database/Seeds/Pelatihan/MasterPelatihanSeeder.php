<?php
namespace App\Database\Seeds\Pelatihan;
use CodeIgniter\Database\Seeder;

class MasterPelatihanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => 1,
                "nama" => "Pelatihan K3 Rumah Sakit",
                "program" => "Pelatihan",
                "kategori" => "Kesehatan",
                "kategori_skp_id" => 3,
                "skp" => 2.50,
                "jpl" => 10,
                "biaya" => "Gratis",
                "no_rekening" => null,
                "biaya_nominal" => null,
                "level" => "Pemula",
                "cakupan" => "Lokal",
                "mekanisme" => "Terbuka",
                "target_khusus_profesi" => null,
                "target_khusus_unit" => null,
                "metode" => "Online",
                "narasumber" => "Dr. Budi Santoso, Sp.A",
                "penyelenggara" => "RSUD Kota Yogyakarta",
                "kontak" => "081234567890",
                "reg_buka_tgl" => date("Y-m-d", strtotime("-5 days")),
                "reg_buka_jam" => "08:00:00",
                "reg_tutup_tgl" => date("Y-m-d", strtotime("+5 days")),
                "reg_tutup_jam" => "23:59:00",
                "jadwal_mulai" => date("Y-m-d", strtotime("+7 days")),
                "jam_mulai" => "08:00:00",
                "jadwal_selesai" => date("Y-m-d", strtotime("+12 days")),
                "jam_selesai" => "15:00:00",
                "kuota" => 100,
                "target_profesi" => "Dokter, Perawat, Administrasi Rumah Sakit",
                "pengumuman" => "Wajib menyiapkan modul dan koneksi stabil",
                "tujuan" => "Meningkatkan pemahaman K3 dasar di lingkungan rumah sakit",
                "deskripsi" => "Pelatihan internasional standar kesehatan kerja untuk seluruh tenaga kesehatan dan tenaga penunjang.",
                "kompetensi" => "K3 Dasar, Keselamatan Kerja",
                "status" => "Publish",
                "created_at" => date("Y-m-d H:i:s")
            ],
            [
                "id" => 2,
                "nama" => "Pelatihan Manajemen RS dan Kepemimpinan",
                "program" => "Workshop",
                "kategori" => "Manajemen",
                "kategori_skp_id" => 3,
                "skp" => 5.00,
                "jpl" => 20,
                "biaya" => "Berbayar",
                "no_rekening" => "123-456-789",
                "biaya_nominal" => 150000.00,
                "level" => "Lanjut",
                "cakupan" => "Nasional",
                "mekanisme" => "Tertutup",
                "target_khusus_profesi" => "Kepala Ruangan",
                "target_khusus_unit" => null,
                "metode" => "Offline / Clasical",
                "narasumber" => "Prof. dr. Azrul, M.Kes",
                "penyelenggara" => "Kemenkes RI",
                "kontak" => "08111222333",
                "reg_buka_tgl" => date("Y-m-d", strtotime("-2 days")),
                "reg_buka_jam" => "08:00:00",
                "reg_tutup_tgl" => date("Y-m-d", strtotime("+6 days")),
                "reg_tutup_jam" => "23:59:00",
                "jadwal_mulai" => date("Y-m-d", strtotime("+8 days")),
                "jam_mulai" => "08:00:00",
                "jadwal_selesai" => date("Y-m-d", strtotime("+13 days")),
                "jam_selesai" => "16:00:00",
                "kuota" => 50,
                "target_profesi" => "Kepala Ruangan, Administrasi Rumah Sakit",
                "pengumuman" => "Harap hadir 30 menit sebelum acara",
                "tujuan" => "Memperkuat kemampuan manajemen bangsal dan tim",
                "deskripsi" => "Workshop untuk meningkatkan kapabilitas pimpinan unit pelayanan kesehatan dalam menjalankan operasional rumah sakit.",
                "kompetensi" => "Manajemen, Kepemimpinan",
                "status" => "Publish",
                "created_at" => date("Y-m-d H:i:s")
            ],
            [
                "id" => 3,
                "nama" => "Pelatihan Keperawatan Anak",
                "program" => "Pelatihan",
                "kategori" => "Keperawatan",
                "kategori_skp_id" => 3,
                "skp" => 3.00,
                "jpl" => 15,
                "biaya" => "Berbayar",
                "no_rekening" => "123-456-789",
                "biaya_nominal" => 120000.00,
                "level" => "Pemula",
                "cakupan" => "Lokal",
                "mekanisme" => "Terbuka",
                "target_khusus_profesi" => null,
                "target_khusus_unit" => null,
                "metode" => "Online",
                "narasumber" => "dr. Anak Agung, Sp.A",
                "penyelenggara" => "RSUD Kota Yogyakarta",
                "kontak" => "081234567890",
                "reg_buka_tgl" => date("Y-m-d", strtotime("-5 days")),
                "reg_buka_jam" => "08:00:00",
                "reg_tutup_tgl" => date("Y-m-d", strtotime("+5 days")),
                "reg_tutup_jam" => "23:59:00",
                "jadwal_mulai" => date("Y-m-d", strtotime("+10 days")),
                "jam_mulai" => "08:00:00",
                "jadwal_selesai" => date("Y-m-d", strtotime("+14 days")),
                "jam_selesai" => "15:00:00",
                "kuota" => 80,
                "target_profesi" => "Perawat, Bidan",
                "pengumuman" => "Wajib menyiapkan modul dan catatan praktik",
                "tujuan" => "Meningkatkan pemahaman keperawatan anak",
                "deskripsi" => "Pelatihan asuhan keperawatan anak terkini untuk perawat dan bidan di rumah sakit.",
                "kompetensi" => "Keperawatan Anak",
                "status" => "Publish",
                "created_at" => date("Y-m-d H:i:s")
            ],
            [
                "id" => 4,
                "nama" => "Pelatihan Resusitasi Neonatus",
                "program" => "Workshop",
                "kategori" => "Kesehatan",
                "kategori_skp_id" => 3,
                "skp" => 4.00,
                "jpl" => 12,
                "biaya" => "Gratis",
                "no_rekening" => null,
                "biaya_nominal" => null,
                "level" => "Pemula",
                "cakupan" => "Lokal",
                "mekanisme" => "Tertutup",
                "target_khusus_profesi" => null,
                "target_khusus_unit" => null,
                "metode" => "Offline / Clasical",
                "narasumber" => "Dr. Budi Santoso, Sp.A",
                "penyelenggara" => "RSUD Kota Yogyakarta",
                "kontak" => "081234567890",
                "reg_buka_tgl" => date("Y-m-d", strtotime("-5 days")),
                "reg_buka_jam" => "08:00:00",
                "reg_tutup_tgl" => date("Y-m-d", strtotime("+5 days")),
                "reg_tutup_jam" => "23:59:00",
                "jadwal_mulai" => date("Y-m-d", strtotime("+10 days")),
                "jam_mulai" => "08:00:00",
                "jadwal_selesai" => date("Y-m-d", strtotime("+12 days")),
                "jam_selesai" => "15:00:00",
                "kuota" => 40,
                "target_profesi" => "Dokter, Perawat",
                "pengumuman" => "Terbatas untuk unit perinatologi",
                "tujuan" => "Meningkatkan keahlian resusitasi neonatus",
                "deskripsi" => "Workshop hands-on resusitasi neonatus untuk dokter dan perawat neonatologi.",
                "kompetensi" => "Resusitasi",
                "status" => "Publish",
                "created_at" => date("Y-m-d H:i:s")
            ],
            [
                "id" => 5,
                "nama" => "Pelatihan Farmasi Klinik dan Layanan",
                "program" => "Pelatihan",
                "kategori" => "Farmasi",
                "kategori_skp_id" => 3,
                "skp" => 4.50,
                "jpl" => 18,
                "biaya" => "Berbayar",
                "no_rekening" => "321-654-987",
                "biaya_nominal" => 180000.00,
                "level" => "Menengah",
                "cakupan" => "Nasional",
                "mekanisme" => "Terbuka",
                "target_khusus_profesi" => null,
                "target_khusus_unit" => null,
                "metode" => "Blended / Hybrid",
                "narasumber" => "apt. Sari Dewi, M.Farm",
                "penyelenggara" => "RSUD Kota Yogyakarta",
                "kontak" => "08122334455",
                "reg_buka_tgl" => date("Y-m-d", strtotime("-3 days")),
                "reg_buka_jam" => "08:00:00",
                "reg_tutup_tgl" => date("Y-m-d", strtotime("+7 days")),
                "reg_tutup_jam" => "23:59:00",
                "jadwal_mulai" => date("Y-m-d", strtotime("+9 days")),
                "jam_mulai" => "09:00:00",
                "jadwal_selesai" => date("Y-m-d", strtotime("+14 days")),
                "jam_selesai" => "17:00:00",
                "kuota" => 60,
                "target_profesi" => "Apoteker, Teknisi Laboratorium",
                "pengumuman" => "Khusus apoteker dan teknisi laboratorium",
                "tujuan" => "Memperbaiki kualitas layanan farmasi dan pemeriksaan laboratorium",
                "deskripsi" => "Pelatihan interprofesional untuk meningkatkan kualitas layanan farmasi klinik dan laboratorium rumah sakit.",
                "kompetensi" => "Farmasi Klinik, Layanan Terintegrasi",
                "status" => "Publish",
                "created_at" => date("Y-m-d H:i:s")
            ]
        ];
        $narasumberNames = [];
        $penyelenggaraNames = [];
        $narasumberData = [];
        $penyelenggaraData = [];

        foreach ($data as &$row) {
            if (isset($row['narasumber'])) {
                if (!empty($row['narasumber']) && $row['narasumber'] !== '-') {
                    $narasumberData[] = [
                        'pelatihan_id' => $row['id'],
                        'nama_narasumber' => $row['narasumber'],
                    ];
                }
                unset($row['narasumber']);
            }
            if (isset($row['penyelenggara'])) {
                if (!empty($row['penyelenggara']) && $row['penyelenggara'] !== '-') {
                    $penyelenggaraData[] = [
                        'pelatihan_id' => $row['id'],
                        'nama_penyelenggara' => $row['penyelenggara'],
                    ];
                }
                unset($row['penyelenggara']);
            }
        }

        $this->db->table("master_pelatihan")->insertBatch($data);

        foreach ($narasumberData as &$nd) {
            $name = $nd['nama_narasumber'];
            $existing = $this->db->table('pejabat_ttd_pelatihan')
                ->where('nama_pejabat', $name)->where('status', 'Narasumber')->get()->getRowArray();
            if (!$existing) {
                $this->db->table('pejabat_ttd_pelatihan')->insert([
                    'status' => 'Narasumber', 'nama_pejabat' => $name,
                    'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")
                ]);
                $nd['pejabat_ttd_id'] = $this->db->insertID();
            } else {
                $nd['pejabat_ttd_id'] = $existing['id'];
            }
            unset($nd['nama_narasumber']);
            $nd['created_at'] = date("Y-m-d H:i:s");
        }
        foreach ($penyelenggaraData as &$pd) {
            $name = $pd['nama_penyelenggara'];
            $existing = $this->db->table('master_penyelenggara')
                ->where('nama', $name)->get()->getRowArray();
            if (!$existing) {
                $this->db->table('master_penyelenggara')->insert([
                    'nama' => $name, 'status' => 'Aktif',
                    'created_at' => date("Y-m-d H:i:s"), 'updated_at' => date("Y-m-d H:i:s")
                ]);
                $pd['penyelenggara_id'] = $this->db->insertID();
            } else {
                $pd['penyelenggara_id'] = $existing['id'];
            }
            unset($pd['nama_penyelenggara']);
            $pd['created_at'] = date("Y-m-d H:i:s");
        }

        if (!empty($narasumberData)) {
            $this->db->table("narasumber_pelatihan")->insertBatch($narasumberData);
        }
        if (!empty($penyelenggaraData)) {
            $this->db->table("penyelenggara_pelatihan")->insertBatch($penyelenggaraData);
        }
    }
}
