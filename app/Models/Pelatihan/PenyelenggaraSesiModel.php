<?php

namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class PenyelenggaraSesiModel extends Model
{
    protected $table            = 'penyelenggara_sesi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = ['penyelenggara_id', 'sesi_id', 'created_at'];
    protected $useTimestamps = false;
}
