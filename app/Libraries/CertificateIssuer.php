<?php

namespace App\Libraries;

use App\Models\Pelatihan\MasterPelatihanModel;
use App\Models\Pelatihan\PesertaPelatihanModel;
use App\Models\Pelatihan\SertifikatPelatihanModel;
use App\Models\Pelatihan\SertifTerbitPelatihanModel;
use App\Models\Pelatihan\UserPelatihanModel;

/**
 * Menerbitkan sertifikat pelatihan RSUD untuk satu peserta.
 *
 * Sertifikat langsung terbit begitu peserta lulus, selama admin sudah membuat
 * template sertifikat (status "diterbitkan") untuk pelatihan tersebut. Tidak
 * perlu menunggu admin menekan tombol publish.
 */
class CertificateIssuer
{
    /**
     * Template dianggap sudah dibuat admin bila ada record dengan status
     * "diterbitkan". Record "draft" dibuat otomatis oleh halaman admin dan
     * belum dikonfigurasi.
     */
    public function hasTemplate(int $pelatihanId): bool
    {
        return (new SertifTerbitPelatihanModel())
            ->where('pelatihan_id', $pelatihanId)
            ->where('status', 'diterbitkan')
            ->countAllResults() > 0;
    }

    /**
     * @return bool true bila sertifikat baru dibuat; false bila syarat belum
     *              terpenuhi atau sertifikat peserta sudah ada.
     */
    public function issue(int $pelatihanId, string $nik, bool $requireFinalTemplate = true): bool
    {
        // Penerbitan otomatis mensyaratkan template "diterbitkan"; tombol publish
        // manual admin (requireFinalTemplate = false) menerima template apa pun.
        $templates = new SertifTerbitPelatihanModel();
        $templates->where('pelatihan_id', $pelatihanId);
        if ($requireFinalTemplate) {
            $templates->where('status', 'diterbitkan');
        }
        $template = $templates->orderBy('id', 'DESC')->first();
        if (!$template) {
            return false;
        }

        $peserta = (new PesertaPelatihanModel())
            ->where('pelatihan_id', $pelatihanId)
            ->where('user_id', $nik)
            ->first();
        if (!$peserta || ($peserta['status_peserta'] ?? null) !== 'Lulus') {
            return false;
        }

        $userModel = new UserPelatihanModel();
        $user = $userModel->find($nik);
        $pelatihan = (new MasterPelatihanModel())->find($pelatihanId);
        if (!$user || !$pelatihan) {
            return false;
        }

        $certModel = new SertifikatPelatihanModel();
        $exists = $certModel->where('user_id', $user['nik'])
            ->where('pelatihan_id', $pelatihanId)
            ->where('jenis_dokumen', 'rsud')
            ->first();
        if ($exists) {
            return false;
        }

        $noTemplate = $template['no_sertifikat'] ?: 'SERT/' . date('Y');
        $now = date('Y-m-d H:i:s');

        $certModel->insert([
            'user_id'           => $user['nik'],
            'user_nama'         => $user['nama_lengkap'],
            'user_profesi'      => $user['id_profesi'] ? 'Tenaga Kesehatan' : 'Staff Umum',
            'judul'             => $pelatihan['nama'],
            'ranah'             => 'Pembelajaran',
            'kategori_kegiatan' => 'Peserta Pelatihan',
            'skp'               => $pelatihan['jpl'],
            'tgl_mulai'         => $pelatihan['jadwal_mulai'],
            'tgl_selesai'       => $pelatihan['jadwal_selesai'],
            'penerbit'          => 'RSUD Kota Yogyakarta',
            'jenis_dokumen'     => 'rsud',
            'verifikasi'        => 'approved',
            'tgl_upload'        => $now,
            'tgl_verifikasi'    => $now,
            'pelatihan_id'      => $pelatihanId,
            'no_sertifikat'     => str_replace('{id}', str_pad((string) $peserta['id'], 4, '0', STR_PAD_LEFT), $noTemplate),
        ]);

        \Config\Database::connect()->table('notifikasi_pelatihan')->insert([
            'user_id'    => $user['nik'],
            'title'      => 'Sertifikat Diterbitkan',
            'message'    => 'Sertifikat untuk pelatihan ' . $pelatihan['nama'] . ' telah diterbitkan. Silakan unduh di menu Sertifikat Saya.',
            'type'       => 'success',
            'is_read'    => 0,
            'created_at' => $now,
        ]);

        $userModel->recalculateJpl($user['nik']);

        return true;
    }

    /**
     * Terbitkan sertifikat untuk seluruh peserta yang sudah lulus, misalnya
     * setelah admin baru selesai membuat template.
     *
     * @return int jumlah sertifikat baru
     */
    public function issueForPassedParticipants(int $pelatihanId): int
    {
        $passed = (new PesertaPelatihanModel())
            ->where('pelatihan_id', $pelatihanId)
            ->where('status_peserta', 'Lulus')
            ->findAll();

        $count = 0;
        foreach ($passed as $p) {
            if ($this->issue($pelatihanId, (string) $p['user_id'])) {
                $count++;
            }
        }

        return $count;
    }
}
