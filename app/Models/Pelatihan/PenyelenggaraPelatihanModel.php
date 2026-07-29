<?php

namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class PenyelenggaraPelatihanModel extends Model
{
    protected $table            = 'penyelenggara_pelatihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = ['penyelenggara_id', 'pelatihan_id', 'sesi_id', 'created_at'];
    protected $useTimestamps = false;
}
