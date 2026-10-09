<?php

namespace App\Models;

use CodeIgniter\Model;

class PublikasiRisetModel extends Model
{
    protected $table            = 'publikasi_riset';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_riset_id',
        'pengajuan_riset_id',
        'tujuan_laporan',
        'judul',
        'nama',
        'identitas',
        'prodi',
        'institusi',
        'jenis_jurnal',
        'nama_publikasi',
        'kategori_jurnal',
        'issn',
        'scope',
        'alamat_web',
        'abstrak',
        'dokumen_path',
        'status',
        'catatan_revisi',
        'nominal_bayar',
        'bukti_file',
        'no_surat_izin',
        'waktu_mulai',
        'waktu_selesai'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
