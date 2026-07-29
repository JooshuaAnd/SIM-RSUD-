<?php

namespace App\Controllers\Pendidikan\Mahasiswa;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    private function getMahasiswaData()
    {
        $userId = session()->get('user_id');
        if (!$userId) return null;
        
        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->where('user_id', $userId)->first();
        
        if ($mahasiswa) {
            $institusiName = '-';
            if ($mahasiswa['institusi_id']) {
                $institusiModel = new \App\Models\InstitusiPendidikanModel();
                $institusi = $institusiModel->find($mahasiswa['institusi_id']);
                if ($institusi) {
                    $institusiName = $institusi['nama_institusi'];
                }
            }
            
            $db = \Config\Database::connect();
            $builder = $db->table('penempatan_peserta_pendidikan');
            $builder->select('pengajuan_praktik_pendidikan.tanggal_mulai, pengajuan_praktik_pendidikan.tanggal_selesai');
            $builder->join('pengajuan_praktik_pendidikan', 'pengajuan_praktik_pendidikan.id = penempatan_peserta_pendidikan.pengajuan_id');
            $builder->where('penempatan_peserta_pendidikan.mahasiswa_id', $mahasiswa['id']);
            $pengajuan = $builder->get()->getRowArray();
            
            $periode = '-';
            if ($pengajuan) {
                $periode = date('d M Y', strtotime($pengajuan['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($pengajuan['tanggal_selesai']));
            }
            
            $mahasiswa['institusi_name'] = $institusiName;
            $mahasiswa['periode'] = $periode;
            
            // For backward compatibility with the view that expects these specific keys:
            $mahasiswa['nama'] = $mahasiswa['nama_lengkap'];
            $mahasiswa['prodi'] = $mahasiswa['program_studi'];
            $mahasiswa['institusi'] = $institusiName;
            
            return $mahasiswa;
        }
        return null;
    }

    public function index()
    {
        $mhs = $this->getMahasiswaData();
        $payment_status = $mhs ? ($mhs['payment_status'] ?? 'Belum Invoice') : 'Belum Invoice';
        
        $totalStase = 0;
        $activeStase = 0;

        if ($mhs) {
            $db = \Config\Database::connect();
            $builder = $db->table('stase_ruangan_ci_pendidikan');
            $builder->select('stase_pendidikan.id, stase_pendidikan.tanggal_mulai, stase_pendidikan.tanggal_akhir');
            $builder->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id');
            $builder->where("JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '\"" . $mhs['id'] . "\"') OR JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '" . $mhs['id'] . "')");
            $builder->groupBy('stase_pendidikan.id');
            $builder->orderBy('stase_pendidikan.tanggal_mulai', 'ASC');
            $staseList = $builder->get()->getResultArray();

            $totalStase = count($staseList);
            $now = time();

            foreach ($staseList as $index => $s) {
                $start = strtotime($s['tanggal_mulai']);
                if ($now >= $start) {
                    $activeStase = $index + 1;
                }
            }
        }

        $data = [
            'title' => 'Dashboard Mahasiswa',
            'active_menu' => 'dashboard',
            'mahasiswa' => $mhs,
            'totalStase' => $totalStase,
            'activeStase' => $activeStase
        ];
        return view('Pendidikan/mahasiswa/dashboard', $data);
    }

    public function stase()
    {
        $mhs = $this->getMahasiswaData();
        $staseList = [];

        if ($mhs) {
            $db = \Config\Database::connect();
            $builder = $db->table('stase_ruangan_ci_pendidikan');
            $builder->select('stase_pendidikan.id, stase_pendidikan.nama_stase, stase_pendidikan.tanggal_mulai, stase_pendidikan.tanggal_akhir');
            $builder->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id');
            $builder->where("JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '\"" . $mhs['id'] . "\"') OR JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '" . $mhs['id'] . "')");
            $builder->groupBy('stase_pendidikan.id');
            $staseList = $builder->get()->getResultArray();
        }

        $data = [
            'title' => 'Rotasi Stase',
            'active_menu' => 'stase',
            'mahasiswa' => $mhs,
            'staseList' => $staseList
        ];
        return view('Pendidikan/mahasiswa/stase', $data);
    }

    public function stase_detail($id)
    {
        $mhs = $this->getMahasiswaData();
        $db = \Config\Database::connect();
        
        $stase = $db->table('stase_pendidikan')
            ->where('id', $id)
            ->get()->getRowArray();
        
        if (!$stase) {
            return redirect()->to('/pendidikan/mahasiswa/stase')->with('error', 'Stase tidak ditemukan');
        }

        // Ambil data ruangan dan CI untuk stase ini
        $rooms = $db->table('stase_ruangan_ci_pendidikan')
            ->select('stase_ruangan_ci_pendidikan.ruangan_id, ci_pendidikan.nama_lengkap as ci_name, unit_kerja_pelatihan.nama_unit as nama_ruangan')
            ->join('ci_pendidikan', 'ci_pendidikan.id = stase_ruangan_ci_pendidikan.ci_id', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = stase_ruangan_ci_pendidikan.ruangan_id', 'left')
            ->where('stase_ruangan_ci_pendidikan.stase_id', $id)
            ->where("(JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '\"" . $mhs['id'] . "\"') OR JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '" . $mhs['id'] . "'))")
            ->get()->getResultArray();

        // Ambil penempatan_id (dari pengajuan) untuk stase ini karena dibutuhkan oleh tabel logbook_pendidikan
        $penempatan = $db->table('penempatan_peserta_pendidikan')
            ->select('id as penempatan_id, pengajuan_id')
            ->where('mahasiswa_id', $mhs['id'])
            ->where('pengajuan_id IS NOT NULL')
            ->get()->getRowArray();

        $pengajuan = null;
        if ($penempatan && $penempatan['pengajuan_id']) {
            $pengajuan = $db->table('pengajuan_praktik_pendidikan')
                ->select('file_logbook')
                ->where('id', $penempatan['pengajuan_id'])
                ->get()->getRowArray();
        }
            
        if ($penempatan && $pengajuan) {
            $penempatan['file_logbook'] = $pengajuan['file_logbook'];
        }

        foreach ($rooms as &$room) {
            $lb = null;
            if ($penempatan) {
                $lb = $db->table('logbook_pendidikan')
                    ->where('penempatan_id', $penempatan['penempatan_id'])
                    ->where('stase_id', $stase['id'])
                    ->where('ruangan_id', $room['ruangan_id'])
                    ->orderBy('created_at', 'DESC')
                    ->get()->getRowArray();
            }
            $room['logbook'] = $lb;
        }

        // Get Tasks
        $ruanganIds = array_column($rooms, 'ruangan_id');
        if (empty($ruanganIds)) $ruanganIds = [0];

        $tasks = $db->table('tugas_pendidikan')
            ->select('tugas_pendidikan.*, ci_pendidikan.nama_lengkap as ci_name_giver, unit_kerja_pelatihan.nama_unit as ruangan_name')
            ->join('ci_pendidikan', 'ci_pendidikan.id = tugas_pendidikan.ci_id', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = tugas_pendidikan.ruangan_id', 'left')
            ->where('tugas_pendidikan.stase_id', $stase['id'])
            ->whereIn('tugas_pendidikan.ruangan_id', $ruanganIds)
            ->orderBy('tugas_pendidikan.deadline', 'ASC')
            ->get()->getResultArray();

        // Get Submissions
        foreach ($tasks as &$t) {
            $sub = $db->table('pengumpulan_tugas_pendidikan')
                ->where('tugas_id', $t['id'])
                ->where('mahasiswa_id', $mhs['id'])
                ->get()->getRowArray();
            $t['submission'] = $sub;
        }

        $data = [
            'title' => 'Detail Stase ' . $stase['nama_stase'],
            'active_menu' => 'stase',
            'id' => $stase['id'], // stase id untuk upload_logbook/upload_tugas redirect back
            'stase' => $stase,
            'rooms' => $rooms,
            'penempatan' => $penempatan,
            'tasks' => $tasks,
            'mahasiswa' => $mhs
        ];
        return view('Pendidikan/mahasiswa/stase_detail', $data);
    }



    public function upload_logbook()
    {
        $mhs = $this->getMahasiswaData();
        if (!$mhs) {
            return redirect()->to('/pendidikan/auth/login');
        }

        $penempatan_id = $this->request->getPost('penempatan_id');
        $tanggal_kegiatan = date('Y-m-d');
        $judul_kegiatan = 'Logbook Stase';
        $deskripsi_kegiatan = 'Laporan Kegiatan Stase';
        $file = $this->request->getFile('file_lampiran');
        $stase_id = $this->request->getPost('stase_id');
        $ruangan_id = $this->request->getPost('ruangan_id');

        $fileName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $db = \Config\Database::connect();
            $stase = $db->table('stase_pendidikan')->where('id', $stase_id)->get()->getRowArray();
            if (!$stase) {
                return redirect()->back()->with('error', 'Stase tidak ditemukan.');
            }
            
            $now = time();
            $start = strtotime($stase['tanggal_mulai']);
            $end = strtotime($stase['tanggal_akhir'] . ' 23:59:59');

            if ($now < $start || $now > $end) {
                return redirect()->back()->with('error', 'Upload logbook hanya dapat dilakukan selama periode stase berlangsung.');
            }

            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/pendidikan/logbookmhs', $fileName);
        }

        if ($fileName) {
            $db = \Config\Database::connect();
            $existing = $db->table('logbook_pendidikan')
                ->where('penempatan_id', $penempatan_id)
                ->where('stase_id', $stase_id)
                ->where('ruangan_id', $ruangan_id)
                ->get()->getRowArray();
            if ($existing) {
                $db->table('logbook_pendidikan')->where('id', $existing['id'])->update([
                    'tanggal_kegiatan' => $tanggal_kegiatan,
                    'file_lampiran' => $fileName,
                    'status_validasi' => 'Pending',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $db->table('logbook_pendidikan')->insert([
                    'penempatan_id' => $penempatan_id,
                    'stase_id' => $stase_id,
                    'ruangan_id' => $ruangan_id,
                    'tanggal_kegiatan' => $tanggal_kegiatan,
                    'judul_kegiatan' => $judul_kegiatan,
                    'deskripsi_kegiatan' => $deskripsi_kegiatan,
                    'file_lampiran' => $fileName,
                    'status_validasi' => 'Pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->to('/pendidikan/mahasiswa/stase/detail/' . $stase_id)->with('success', 'Logbook berhasil diunggah.');
    }

    public function upload_tugas()
    {
        $mhs = $this->getMahasiswaData();
        if (!$mhs) {
            return redirect()->to('/pendidikan/auth/login');
        }

        $tugas_id = $this->request->getPost('tugas_id');
        $stase_id = $this->request->getPost('stase_id');
        $file = $this->request->getFile('file_tugas');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/pendidikan/tugasmhs', $fileName);

            $db = \Config\Database::connect();
            
            // Check if already submitted
            $existing = $db->table('pengumpulan_tugas_pendidikan')
                ->where('tugas_id', $tugas_id)
                ->where('mahasiswa_id', $mhs['id'])
                ->get()->getRowArray();

            if ($existing) {
                // Update file
                $db->table('pengumpulan_tugas_pendidikan')->where('id', $existing['id'])->update([
                    'file_tugas' => $fileName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert new
                $db->table('pengumpulan_tugas_pendidikan')->insert([
                    'tugas_id' => $tugas_id,
                    'mahasiswa_id' => $mhs['id'],
                    'file_tugas' => $fileName,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            return redirect()->to('/pendidikan/mahasiswa/stase/detail/' . $stase_id)->with('success', 'Tugas berhasil dikumpulkan.');
        }

        return redirect()->to('/pendidikan/mahasiswa/stase/detail/' . $stase_id)->with('error', 'Gagal mengunggah file tugas.');
    }

    public function sertifikat()
    {
        $mhs = $this->getMahasiswaData();
        $payment_status = $mhs ? ($mhs['payment_status'] ?? 'Belum Invoice') : 'Belum Invoice';

        if ($payment_status !== 'Lunas') {
            return redirect()->to('/pendidikan/mahasiswa/dashboard')->with('error', 'Akses sertifikat terkunci. Harap selesaikan administrasi pembayaran stase terlebih dahulu.');
        }

        if (empty($mhs['nilai_akhir'])) {
            return redirect()->to('/pendidikan/mahasiswa/dashboard')->with('error', 'Sertifikat belum tersedia karena Institusi Anda belum mengunggah nilai akhir.');
        }

        $data = [
            'title' => 'Sertifikat Diklat',
            'active_menu' => 'sertifikat',
            'mahasiswa' => $mhs
        ];
        return view('Pendidikan/mahasiswa/sertifikat', $data);
    }

    public function download_sertifikat()
    {
        $mhs = $this->getMahasiswaData();
        $payment_status = $mhs ? ($mhs['payment_status'] ?? 'Belum Invoice') : 'Belum Invoice';

        if ($payment_status !== 'Lunas' || empty($mhs['nilai_akhir'])) {
            return redirect()->to('/pendidikan/mahasiswa/dashboard')->with('error', 'Sertifikat tidak tersedia.');
        }

        $data = [
            'mahasiswa' => $mhs
        ];

        // Load Dompdf
        $dompdf = new \Dompdf\Dompdf();
        
        // Load view HTML
        $html = view('Pendidikan/Institusi/mahasiswa/sertifikat_pdf', $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Sertifikat_Lulus_' . str_replace(' ', '_', $mhs['nama_lengkap']) . '_' . $mhs['nim'];
        $dompdf->stream($filename . ".pdf", ["Attachment" => false]);
    }

    public function profil()
    {
        $userId = session()->get('user_id');
        
        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->where('user_id', $userId)->first();
        
        $institusiName = 'Institusi';
        if ($mahasiswa && $mahasiswa['institusi_id']) {
            $institusiModel = new \App\Models\InstitusiPendidikanModel();
            $institusi = $institusiModel->find($mahasiswa['institusi_id']);
            if ($institusi) {
                $institusiName = $institusi['nama_institusi'];
            }
        }

        $data = [
            'title' => 'Profil & Dokumen Mahasiswa',
            'active_menu' => 'profil',
            'mahasiswa' => $mahasiswa,
            'institusiName' => $institusiName
        ];
        return view('Pendidikan/mahasiswa/profil', $data);
    }

    public function update_profil()
    {
        $mhs = $this->getMahasiswaData();
        if (!$mhs) {
            return redirect()->to('/pendidikan/auth/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('mahasiswa_pendidikan');
        $updateData = [];

        $files = [
            'pas_foto' => 'file_foto',
            'ijazah' => 'file_ijazah',
            'surat_aktif' => 'file_sk',
            'bukti_pembayaran' => 'file_bukti_bayar'
        ];

        foreach ($files as $inputName => $dbField) {
            $file = $this->request->getFile($inputName);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/dokumen_mahasiswa', $newName);
                $updateData[$dbField] = $newName;
            }
        }

        if (!empty($updateData)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            $builder->where('id', $mhs['id'])->update($updateData);
        }

        session()->set('profil_lengkap', true);
        return redirect()->to('pendidikan/mahasiswa/profil')->with('success', 'Data profil dan dokumen berhasil diperbarui!');
    }

    public function update_password()
    {
        $userId = session()->get('user_id');
        $passwordLama = $this->request->getPost('password_lama');
        $passwordBaru = $this->request->getPost('password_baru');
        $konfirmasiPassword = $this->request->getPost('konfirmasi_password');

        if ($passwordBaru !== $konfirmasiPassword) {
            return redirect()->to('pendidikan/mahasiswa/profil')->with('error', 'Konfirmasi password baru tidak cocok!');
        }

        $userModel = new \App\Models\UserPendidikanModel();
        $user = $userModel->find($userId);

        if (!$user || !password_verify((string)$passwordLama, $user['password'])) {
            return redirect()->to('pendidikan/mahasiswa/profil')->with('error', 'Password lama yang Anda masukkan salah!');
        }

        $userModel->update($userId, [
            'password' => password_hash((string)$passwordBaru, PASSWORD_DEFAULT)
        ]);

        return redirect()->to('pendidikan/mahasiswa/profil')->with('success', 'Password berhasil diubah!');
    }

    public function penilaian()
    {
        $mhs = $this->getMahasiswaData();
        $payment_status = $mhs ? ($mhs['payment_status'] ?? 'Belum Invoice') : 'Belum Invoice';

        if ($payment_status !== 'Lunas') {
            return redirect()->to('/pendidikan/mahasiswa/dashboard')->with('error', 'Akses lembar penilaian Anda saat ini terkunci. Harap selesaikan administrasi pembayaran stase.');
        }

        $staseList = [];
        if ($mhs) {
            $db = \Config\Database::connect();
            $builder = $db->table('stase_ruangan_ci_pendidikan');
            $builder->select('stase_ruangan_ci_pendidikan.id, stase_ruangan_ci_pendidikan.stase_id, stase_ruangan_ci_pendidikan.ruangan_id, stase_pendidikan.nama_stase, stase_pendidikan.tanggal_akhir, ci_pendidikan.nama_lengkap as ci_name, unit_kerja_pelatihan.nama_unit as nama_ruangan');
            $builder->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id');
            $builder->join('ci_pendidikan', 'ci_pendidikan.id = stase_ruangan_ci_pendidikan.ci_id', 'left');
            $builder->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = stase_ruangan_ci_pendidikan.ruangan_id', 'left');
            $builder->where("JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '\"" . $mhs['id'] . "\"') OR JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '" . $mhs['id'] . "')");
            $staseList = $builder->get()->getResultArray();

            foreach ($staseList as &$stase) {
                $tasks = $db->table('tugas_pendidikan')
                    ->select('tugas_pendidikan.nama_tugas, pengumpulan_tugas_pendidikan.nilai, pengumpulan_tugas_pendidikan.status')
                    ->join('pengumpulan_tugas_pendidikan', 'pengumpulan_tugas_pendidikan.tugas_id = tugas_pendidikan.id AND pengumpulan_tugas_pendidikan.mahasiswa_id = ' . (int)$mhs['id'], 'left')
                    ->where('tugas_pendidikan.stase_id', $stase['stase_id'])
                    ->where('tugas_pendidikan.ruangan_id', $stase['ruangan_id'])
                    ->get()->getResultArray();
                $stase['tasks'] = $tasks;
                
                // Assuming you'll have file_penilaian_ci in penempatan table later, we can just set it to null for now
                $stase['file_penilaian_ci'] = null; 
            }
        }

        $data = [
            'title' => 'Penilaian Per Stase',
            'active_menu' => 'penilaian',
            'mahasiswa' => $mhs,
            'staseList' => $staseList
        ];
        return view('Pendidikan/mahasiswa/penilaian', $data);
    }

}
