<?php

namespace App\Models;

use CodeIgniter\Model;

class InstitusiPendidikanModel extends Model
{
    protected $table            = 'institusi_pendidikan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nama_institusi',
        'jenis_institusi',
        'alamat',
        'no_telp',
        'nama_kontak',
        'jabatan_pj',
        'hp_pj',
        'email_pj',
        'file_mou',
        'file_permohonan',
        'tgl_mulai_mou',
        'tgl_selesai_mou',
        'file_lainnya',
        'status_verifikasi',
        'status_pembayaran',
        'catatan_revisi',
        'revisi_dikirim_at',
        'alasan_penolakan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
