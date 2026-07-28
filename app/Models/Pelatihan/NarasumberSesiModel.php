<?php

namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class NarasumberSesiModel extends Model
{
    protected $table            = 'narasumber_sesi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = ['pejabat_ttd_id', 'sesi_id', 'created_at'];
    protected $useTimestamps = false;
}
