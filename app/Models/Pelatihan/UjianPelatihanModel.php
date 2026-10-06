<?php
namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class UjianPelatihanModel extends Model
{
    protected $table            = 'ujian_pelatihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pelatihan_id', 'sesi_id', 'tipe_evaluasi', 'kkm', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
}
