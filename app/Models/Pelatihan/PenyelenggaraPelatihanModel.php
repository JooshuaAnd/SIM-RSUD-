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
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pelatihan', 'sesi_id', 'nama_penyelenggara', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
}
