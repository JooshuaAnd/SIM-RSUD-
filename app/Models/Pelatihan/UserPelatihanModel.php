<?php

namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class UserPelatihanModel extends Model
{
    protected $table            = 'users_pelatihan';
    protected $primaryKey       = 'nik'; // Sesuai database
    protected $useAutoIncrement = false; // NIK is a manual VARCHAR primary key
    protected $allowedFields    = ['nik', 'nama_lengkap', 'email', 'no_wa', 'jenis_peserta', 'role', 'admin_akses_peserta', 'id_unit_kerja', 'id_profesi', 'capaian_jpl', 'password', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Fungsi untuk mempermudah saat menampilkan data di tabel admin
    public function getUserWithRelations()
    {
        return $this->select('users_pelatihan.*, profesi_pelatihan.nama_profesi, profesi_pelatihan.target_jpl as profesi_target_jpl, unit_kerja_pelatihan.nama_unit')
                    ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
                    ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
                    ->findAll();
    }

    public function recalculateJpl($userId, $selectedYear = null)
    {
        if ($selectedYear === null) {
            $selectedYear = date('Y');
        }
        $db = \Config\Database::connect();

        // 1. Internal completed trainings
        $myCompletedPelat = $db->table('peserta_pelatihan')
            ->select('master_pelatihan.jpl, master_pelatihan.jadwal_selesai')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.user_id', $userId)
            ->where('peserta_pelatihan.status_peserta', 'Lulus')
            ->where('master_pelatihan.cert_published', 1)
            ->get()->getResultArray();

        // 2. Approved external / mandiri / surat tugas certificates (non-RSUD)
        $myApprovedCerts = $db->table('sertifikat_pelatihan')
            ->where('user_id', $userId)
            ->where('verifikasi', 'approved')
            ->where('jenis_dokumen !=', 'rsud')
            ->get()->getResultArray();

        // 3. Approved RSUD certificates that do NOT have a corresponding peserta_pelatihan Lulus record
        // This covers cases where the master_pelatihan or peserta record was removed but the certificate remains
        $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
            ->select('sp.skp, sp.tgl_selesai')
            ->where('sp.user_id', $userId)
            ->where('sp.verifikasi', 'approved')
            ->where('sp.jenis_dokumen', 'rsud')
            ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
            ->get()->getResultArray();

        $completedJpl = 0;
        foreach ($myCompletedPelat as $cp) {
            $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
            if ($yearOfTraining == $selectedYear) {
                $completedJpl += (int)($cp['jpl'] ?? 0);
            }
        }
        foreach ($myApprovedCerts as $ac) {
            $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
            if ($yearOfTraining == $selectedYear) {
                $completedJpl += (int)($ac['skp'] ?? 0);
            }
        }

        // Include RSUD fallback certificates (count their SKP if within selected year)
        foreach ($rsudFallbackCerts as $rc) {
            $yearOfTraining = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
            if ($yearOfTraining == $selectedYear) {
                $completedJpl += (int)($rc['skp'] ?? 0);
            }
        }

        // Update user capaian_jpl
        $this->update($userId, ['capaian_jpl' => $completedJpl]);
        return $completedJpl;
    }
}
