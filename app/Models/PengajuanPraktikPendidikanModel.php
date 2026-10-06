<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanPraktikPendidikanModel extends Model
{
    protected $table            = 'pengajuan_praktik_pendidikan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'institusi_id',
        'nama_program',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_peserta',
        'file_proposal',
        'file_surat_pengantar',
        'file_logbook',
        'file_panduan',
        'file_daftar_mhs',
        'file_kompetensi',
        'file_sk_pembimbing',
        'file_bukti_bayar',
        'file_dokumen_penilaian',
        'status',
        'catatan_admin'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
