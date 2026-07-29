<?php

namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class PejabatTtdPelatihanModel extends Model
{
    protected $table            = 'pejabat_ttd_pelatihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'status', 'an_pejabat', 'jabatan', 'nama_pejabat', 'nip_pejabat', 'ttd_image',
        'gelar_depan', 'gelar_belakang', 'pendidikan', 'riwayat', 'keahlian',
        'foto', 'kontak', 'email', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = false;
}
