<?php
namespace App\Controllers\Pelatihan\Peserta;
use App\Controllers\BaseController;

class MyLearning extends BaseController
{
    public function index()
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $registrations = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, master_pelatihan.id as id, master_pelatihan.nama as nama, master_pelatihan.metode, master_pelatihan.biaya, master_pelatihan.mekanisme, master_pelatihan.jpl, master_pelatihan.jadwal_mulai, master_pelatihan.jam_mulai, master_pelatihan.jadwal_selesai, master_pelatihan.jam_selesai')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.user_id', $userId)
            ->get()->getResultArray();

        $list = [];
        foreach ($registrations as $reg) {
            $item = $reg;
            
            // Map status
            if ($reg['status_peserta'] == 'Gagal') {
                $item['reg_status'] = 'ditolak';
            } elseif ($reg['status_pembayaran'] == 'Pending' || $reg['status_akses'] == 'Pending') {
                $item['reg_status'] = 'pending';
            } else {
                $item['reg_status'] = 'disetujui';
            }

            // Progress from DB
            $progressVal = (float)($reg['progress'] ?? 0);
            $isSelesai = in_array($reg['status_peserta'], ['Lulus', 'Gagal']);

            if ($isSelesai) {
                $progressVal = 100;
            }

            $item['progress'] = $progressVal;
            $item['is_selesai'] = $isSelesai;
            
            $penyelenggaraList = $db->table('penyelenggara_pelatihan')
                ->select('master_penyelenggara.nama')
                ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id', 'left')
                ->where('penyelenggara_pelatihan.pelatihan_id', $reg['id'])
                ->get()->getResultArray();
            $penyelenggaraNames = array_unique(array_filter(array_column($penyelenggaraList, 'nama')));
            $item['penyelenggara'] = implode(', ', $penyelenggaraNames);
            
            $list[] = $item;
        }

        $data = [
            'title' => 'Pembelajaran Saya',
            'minta_akses' => array_filter($list, fn($l) => $l['reg_status'] == 'pending'),
            'belum_dimulai' => array_filter($list, fn($l) => $l['reg_status'] == 'disetujui' && $l['progress'] == 0),
            'berjalan' => array_filter($list, fn($l) => $l['reg_status'] == 'disetujui' && $l['progress'] > 0 && !$l['is_selesai']),
            'selesai' => array_filter($list, fn($l) => $l['is_selesai']),
            'dibatalkan' => array_filter($list, fn($l) => $l['reg_status'] == 'ditolak'),
        ];
        
        return view('Pelatihan/peserta/pembelajaran_saya/index', $data);
    }

    public function batalkan_pelatihan($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->update([
                'status_peserta' => 'Gagal',
                'status_pembayaran' => 'Pending',
                'status_akses' => 'Pending'
            ]);

        return redirect()->back()->with('success', 'Pelatihan berhasil dibatalkan.');
    }
}
