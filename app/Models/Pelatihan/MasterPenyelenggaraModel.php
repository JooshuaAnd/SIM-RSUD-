<?php

namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class MasterPenyelenggaraModel extends Model
{
    protected $table            = 'master_penyelenggara';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = [
        'nama', 'alamat', 'fokus_bidang', 'kontak', 'email',
        'logo', 'status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = false;
}
