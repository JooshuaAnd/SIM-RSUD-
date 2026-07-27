<?php
namespace App\Database\Seeds\Pelatihan;
use CodeIgniter\Database\Seeder;

class DataAwalPelatihanSeeder extends Seeder
{
    public function run()
    {
        $tables = [
            'pengaturan_logo_sistem_pelatihan',
            'master_kategori_skp_pelatihan',
            'kategori_evaluasi_pelatihan',
            'master_pelatihan',
            'narasumber_pelatihan',
            'penyelenggara_pelatihan',
            'materi_pelatihan',
            'sesi_interaktif_pelatihan',
            'ujian_pelatihan',
            'ujian_soal_pelatihan',
            'kuesioner_master_pelatihan',
            'peserta_presensi_pelatihan',
            'peserta_jawaban_ujian_pelatihan',
            'peserta_ujian_pelatihan',
            'peserta_pelatihan',
            'pejabat_ttd_pelatihan',
            'sertif_terbit_pelatihan',
            'sertifikat_pelatihan',
            'notifikasi_pelatihan',
            'profesi_pelatihan',
            'unit_kerja_pelatihan',
            'users_pelatihan',
            // Pendidikan
            'stase_pendidikan',
            'ci_pendidikan',
            'mahasiswa_pendidikan',
            'institusi_pendidikan',
            'users_pendidikan',
            'roles_pendidikan',
            // Riset
            'users_riset',
        ];

        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->table($table)->truncate();
            }
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        $this->call('App\Database\Seeds\Pelatihan\MasterKategoriSkpPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\KategoriEvaluasiPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\ProfesiPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\UnitKerjaPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\MasterPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\MateriPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\SesiInteraktifPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\UjianPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\KuesionerMasterPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\UsersPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\PesertaPelatihanSeeder');
        $this->call('App\Database\Seeds\Pelatihan\SertifikatPelatihanSeeder');
        $this->call('App\Database\Seeds\InstitusiPendidikanSeeder');
        $this->call('App\Database\Seeds\UserPendidikanSeeder');
        $this->call('App\Database\Seeds\RisetSeeder');
    }
}
