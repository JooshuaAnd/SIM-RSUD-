<?php
namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class PesertaPelatihanModel extends Model
{
    /**
     * Kondisi (join peserta_pelatihan + master_pelatihan) untuk pelatihan yang
     * sertifikatnya sudah terbit: dipublish admin, atau sudah terbit otomatis
     * untuk peserta bersangkutan saat lulus.
     */
    public const CERT_ISSUED_SQL = "(master_pelatihan.cert_published = 1 OR EXISTS (SELECT 1 FROM sertifikat_pelatihan sx WHERE sx.user_id = peserta_pelatihan.user_id AND sx.pelatihan_id = peserta_pelatihan.pelatihan_id AND sx.jenis_dokumen = 'rsud' AND sx.verifikasi = 'approved'))";

    protected $table            = 'peserta_pelatihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'pelatihan_id', 'status_peserta', 'bukti_bayar', 'status_pembayaran', 'status_akses', 'waktu_daftar', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
}