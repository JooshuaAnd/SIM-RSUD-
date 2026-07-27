<?php
namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class NarasumberPelatihanModel extends Model
{
    protected $table            = 'narasumber_pelatihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pelatihan', 'sesi_id', 'nama_narasumber', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
}
