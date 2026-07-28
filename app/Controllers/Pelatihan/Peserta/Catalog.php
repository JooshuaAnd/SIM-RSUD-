<?php
namespace App\Controllers\Pelatihan\Peserta;
use App\Controllers\BaseController;

class Catalog extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $search = $this->request->getGet('search');
        $program = $this->request->getGet('program');
        $kategori = $this->request->getGet('kategori');
        $cakupan = $this->request->getGet('cakupan');
        $mekanisme = $this->request->getGet('mekanisme');
        $sasaran = $this->request->getGet('sasaran');

        $builder = $db->table('master_pelatihan')->whereIn('status', ['Publish', 'Aktif']);

        if (!empty($search)) $builder->like('nama', $search);
        if (!empty($program)) $builder->where('program', $program);
        if (!empty($kategori)) $builder->where('kategori', $kategori);
        if (!empty($cakupan)) $builder->where('cakupan', $cakupan);
        if (!empty($mekanisme)) $builder->where('mekanisme', $mekanisme);
        
        if (!empty($sasaran)) {
            if (is_array($sasaran)) {
                $builder->groupStart();
                foreach ($sasaran as $s) {
                    $builder->orLike('target_profesi', $s);
                    $builder->orLike('target_khusus_profesi', $s);
                }
                $builder->groupEnd();
            } else {
                $builder->groupStart();
                $builder->like('target_profesi', $sasaran);
                $builder->orLike('target_khusus_profesi', $sasaran);
                $builder->groupEnd();
            }
        }

        $pelatihan = $builder->orderBy('id', 'DESC')->get()->getResultArray();
            
        // Count participants for each training
        foreach ($pelatihan as &$p) {
            $p['peserta'] = $db->table('peserta_pelatihan')
                ->where('pelatihan_id', $p['id'])
                ->countAllResults();
            
            $penyelenggaraList = $db->table('penyelenggara_pelatihan')
                ->select('master_penyelenggara.nama')
                ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id', 'left')
                ->where('penyelenggara_pelatihan.pelatihan_id', $p['id'])
                ->get()->getResultArray();
            $p['penyelenggara'] = implode(', ', array_column($penyelenggaraList, 'nama'));
        }

        // Fetch filter options dynamically
        $filter_options = [
            'program' => $db->query("SELECT DISTINCT program FROM master_pelatihan WHERE program IS NOT NULL")->getResultArray(),
            'kategori' => $db->query("SELECT DISTINCT kategori FROM master_pelatihan WHERE kategori IS NOT NULL")->getResultArray(),
            'cakupan' => $db->query("SELECT DISTINCT cakupan FROM master_pelatihan WHERE cakupan IS NOT NULL")->getResultArray(),
            'mekanisme' => $db->query("SELECT DISTINCT mekanisme FROM master_pelatihan WHERE mekanisme IS NOT NULL")->getResultArray(),
            'profesi' => $db->table('profesi_pelatihan')->select('id_profesi as id, nama_profesi')->orderBy('nama_profesi', 'ASC')->get()->getResultArray()
        ];

        $data = [
            'title' => 'Katalog Pelatihan',
            'pelatihan' => $pelatihan,
            'filters' => $filter_options,
            'req' => $this->request->getGet()
        ];
        return view('pelatihan/peserta/katalog/index', $data);
    }

    public function detail($id)
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $item = $db->table('master_pelatihan')->where('id', $id)->get()->getRowArray();
        if (!$item) {
            return redirect()->to('/pelatihan/peserta/pembelajaran');
        }

        // Count registered participants
        $item['peserta'] = $db->table('peserta_pelatihan')
            ->where('pelatihan_id', $id)
            ->countAllResults();

        // Get Narasumber and Penyelenggara
        $narasumberList = $db->table('narasumber_pelatihan')
            ->select('pejabat_ttd_pelatihan.nama_pejabat, pejabat_ttd_pelatihan.gelar_depan, pejabat_ttd_pelatihan.gelar_belakang')
            ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id', 'left')
            ->where('narasumber_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();
        $item['narasumber'] = implode(', ', array_map(function($n) {
            $nama = $n['nama_pejabat'] ?? '';
            $depan = $n['gelar_depan'] ?? '';
            $belakang = $n['gelar_belakang'] ?? '';
            return trim($depan . ' ' . $nama . ' ' . $belakang);
        }, $narasumberList));
        
        $penyelenggaraList = $db->table('penyelenggara_pelatihan')
            ->select('master_penyelenggara.nama')
            ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id', 'left')
            ->where('penyelenggara_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();
        $item['penyelenggara'] = implode(', ', array_column($penyelenggaraList, 'nama'));

        // Get registration status
        $reg = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        $regStatus = 'belum_daftar';
        if ($reg) {
            if ($reg['status_peserta'] == 'Gagal') {
                $regStatus = 'ditolak';
            } elseif ($reg['status_pembayaran'] == 'Pending' || $reg['status_akses'] == 'Pending') {
                $regStatus = 'pending';
            } else {
                $regStatus = 'disetujui';
            }
        }
        
        $now = date('Y-m-d H:i:s');
        $regBuka = $item['reg_buka_tgl'] . ' ' . ($item['reg_buka_jam'] ?: '00:00:00');
        $regTutup = $item['reg_tutup_tgl'] . ' ' . ($item['reg_tutup_jam'] ?: '23:59:59');
        $jadwalMulai = $item['jadwal_mulai'] . ' ' . ($item['jam_mulai'] ?: '00:00:00');
        $jadwalSelesai = $item['jadwal_selesai'] . ' ' . ($item['jam_selesai'] ?: '23:59:59');
        
        $isRegOpen = ($now >= $regBuka && $now <= $regTutup);
        $isLearningOpen = ($now >= $jadwalMulai && $now <= $jadwalSelesai);
        $isLearningFinished = ($now > $jadwalSelesai);

        $konten = [];
        
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $id)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) {
            $konten[] = ['tipe' => 'pre_test', 'judul' => 'Pre Test', 'durasi' => 'Menyesuaikan'];
        }

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
        foreach ($sesi as $s) {
            if (strtolower($s['tipe_sesi'] ?? '') == 'offline') {
                $konten[] = [
                    'tipe' => 'presensi', 
                    'judul' => 'Sesi Tatap Muka: ' . $s['nama_sesi'], 
                    'deskripsi' => 'Tanggal: ' . date('d M Y', strtotime($s['tanggal'])) . ' ' . $s['waktu'] . ' | Lokasi: ' . $s['tempat']
                ];
            } else {
                $konten[] = [
                    'tipe' => 'sesi', 
                    'judul' => 'Sesi Online/Hybrid: ' . $s['nama_sesi'], 
                    'deskripsi' => 'Tanggal: ' . date('d M Y', strtotime($s['tanggal'])) . ' ' . $s['waktu']
                ];
            }
            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->orderBy('urutan', 'ASC')->get()->getResultArray();
            foreach ($materi as $m) {
                $konten[] = ['tipe' => 'materi', 'judul' => 'Materi: ' . $m['judul'], 'deskripsi' => $m['deskripsi']];
            }
        }
        
        $postTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $id)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
        if ($postTest) {
            $konten[] = ['tipe' => 'post_test', 'judul' => 'Post Test', 'durasi' => 'Menyesuaikan'];
        }
        $konten[] = ['tipe' => 'evaluasi', 'judul' => 'Evaluasi Pelatihan', 'deskripsi' => 'Ulasan Fasilitator, Modul, dan Penyelenggara'];
        $konten[] = ['tipe' => 'sertifikat', 'judul' => 'Sertifikat Kelulusan'];

        $hasProgress = false;
        if ($reg && $regStatus == 'disetujui') {
            $progressVal = $reg['progress'] ?? 0;
            $hasProgress = ($progressVal > 0);
        }

        $data = [
            'title' => 'Detail Pelatihan',
            'p' => $item,
            'reg' => $reg,
            'reg_status' => $regStatus,
            'is_reg_open' => $isRegOpen,
            'is_learning_open' => $isLearningOpen,
            'is_learning_finished' => $isLearningFinished,
            'reg_buka' => $regBuka,
            'reg_tutup' => $regTutup,
            'jadwal_mulai' => $jadwalMulai,
            'jadwal_selesai' => $jadwalSelesai,
            'konten' => $konten,
            'has_progress' => $hasProgress,
        ];
        return view('pelatihan/peserta/katalog/detail', $data);
    }
}
