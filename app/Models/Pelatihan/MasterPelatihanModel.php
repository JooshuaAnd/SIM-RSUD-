<?php
namespace App\Models\Pelatihan;

use CodeIgniter\Model;

class MasterPelatihanModel extends Model
{
    protected $table            = 'master_pelatihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tema', 'nama', 'program', 'kategori', 'kategori_skp_id', 'skp', 'jpl', 'biaya', 'nama_bank', 'no_rekening', 'atas_nama', 'biaya_nominal', 'level', 'cakupan', 'mekanisme', 'target_khusus_profesi', 'target_khusus_unit', 'metode', 'kontak', 'reg_buka_tgl', 'reg_buka_jam', 'reg_tutup_tgl', 'reg_tutup_jam', 'jadwal_mulai', 'jam_mulai', 'jadwal_selesai', 'jam_selesai', 'kuota', 'target_profesi', 'pengumuman', 'tujuan', 'deskripsi', 'kompetensi', 'status', 'avg_rating', 'cert_published', 'gambar_pelatihan', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = false;
}