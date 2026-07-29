<?php

namespace App\Controllers\Pendidikan\Institusi;

use App\Controllers\BaseController;

class Pengajuan extends BaseController
{
    public function create()
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return redirect()->to('pendidikan/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $profil = $institusiModel->find($sessionData['institusi_id']);

        $profesiModel = new \App\Models\Pelatihan\ProfesiPelatihanModel();
        $list_profesi = $profesiModel->findAll();

        $data = [
            'title' => 'Form Pengajuan Mahasiswa Koas',
            'profil' => $profil,
            'list_profesi' => $list_profesi
        ];
        return view('Pendidikan/Institusi/pengajuan/create', $data);
    }

    public function store()
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return redirect()->to('pendidikan/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $institusi_id = $sessionData['institusi_id'];

        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $penempatanModel = new \App\Models\PenempatanPesertaPendidikanModel();

        // Handle File Uploads for Pengajuan
        $file_proposal = $this->request->getFile('doc_proposal');
        $file_pengantar = $this->request->getFile('doc_pengantar');
        $file_logbook = $this->request->getFile('doc_logbook');
        $file_panduan = $this->request->getFile('doc_panduan');
        $file_daftar_mhs = $this->request->getFile('doc_daftar_mhs');
        $file_kompetensi = $this->request->getFile('doc_kompetensi');
        $file_sk_pembimbing = $this->request->getFile('doc_sk_pembimbing');
        $file_bukti_bayar = $this->request->getFile('doc_bukti_bayar');
        $file_dokumen_penilaian = $this->request->getFile('doc_penilaian');

        $uploadPath = FCPATH . 'uploads/dokumen_pengajuan/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        $dataPengajuan = [
            'institusi_id' => $institusi_id,
            'nama_program' => $this->request->getPost('jenis_program'),
            'tanggal_mulai' => $this->request->getPost('tgl_mulai'),
            'tanggal_selesai' => $this->request->getPost('tgl_selesai'),
            'jumlah_peserta' => count($this->request->getPost('mhs_nama') ?? []),
            'status' => 'Menunggu'
        ];

        if ($file_proposal && $file_proposal->isValid() && !$file_proposal->hasMoved()) {
            $newName = $file_proposal->getRandomName();
            $file_proposal->move($uploadPath, $newName);
            $dataPengajuan['file_proposal'] = $newName;
        }
        if ($file_pengantar && $file_pengantar->isValid() && !$file_pengantar->hasMoved()) {
            $newName = $file_pengantar->getRandomName();
            $file_pengantar->move($uploadPath, $newName);
            $dataPengajuan['file_surat_pengantar'] = $newName;
        }
        if ($file_logbook && $file_logbook->isValid() && !$file_logbook->hasMoved()) {
            $newName = $file_logbook->getRandomName();
            $file_logbook->move($uploadPath, $newName);
            $dataPengajuan['file_logbook'] = $newName;
        }
        if ($file_panduan && $file_panduan->isValid() && !$file_panduan->hasMoved()) {
            $newName = $file_panduan->getRandomName();
            $file_panduan->move($uploadPath, $newName);
            $dataPengajuan['file_panduan'] = $newName;
        }
        if ($file_daftar_mhs && $file_daftar_mhs->isValid() && !$file_daftar_mhs->hasMoved()) {
            $newName = $file_daftar_mhs->getRandomName();
            $file_daftar_mhs->move($uploadPath, $newName);
            $dataPengajuan['file_daftar_mhs'] = $newName;
        }
        if ($file_kompetensi && $file_kompetensi->isValid() && !$file_kompetensi->hasMoved()) {
            $newName = $file_kompetensi->getRandomName();
            $file_kompetensi->move($uploadPath, $newName);
            $dataPengajuan['file_kompetensi'] = $newName;
        }
        if ($file_sk_pembimbing && $file_sk_pembimbing->isValid() && !$file_sk_pembimbing->hasMoved()) {
            $newName = $file_sk_pembimbing->getRandomName();
            $file_sk_pembimbing->move($uploadPath, $newName);
            $dataPengajuan['file_sk_pembimbing'] = $newName;
        }
        if ($file_bukti_bayar && $file_bukti_bayar->isValid() && !$file_bukti_bayar->hasMoved()) {
            $newName = $file_bukti_bayar->getRandomName();
            $file_bukti_bayar->move($uploadPath, $newName);
            $dataPengajuan['file_bukti_bayar'] = $newName;
        }
        if ($file_dokumen_penilaian && $file_dokumen_penilaian->isValid() && !$file_dokumen_penilaian->hasMoved()) {
            $newName = $file_dokumen_penilaian->getRandomName();
            $file_dokumen_penilaian->move($uploadPath, $newName);
            $dataPengajuan['file_dokumen_penilaian'] = $newName;
        }

        // Insert Pengajuan
        $pengajuanModel->insert($dataPengajuan);
        $pengajuan_id = $pengajuanModel->getInsertID();

        // Process Mahasiswa
        $mhsNames = $this->request->getPost('mhs_nama');
        $mhsNims = $this->request->getPost('mhs_nim');
        $mhsTglLahir = $this->request->getPost('mhs_tgl_lahir');
        $mhsJk = $this->request->getPost('mhs_jk');
        $mhsSemester = $this->request->getPost('mhs_semester');
        $mhsHp = $this->request->getPost('mhs_hp');
        $mhsEmail = $this->request->getPost('mhs_email');
        
        $mhsUploadPath = FCPATH . 'uploads/dokumen_mahasiswa/';
        if (!is_dir($mhsUploadPath)) mkdir($mhsUploadPath, 0777, true);

        if ($mhsNames && is_array($mhsNames)) {
            for ($i = 0; $i < count($mhsNames); $i++) {
                $idx = $i + 1; // input names are mhs_foto_1, mhs_foto_2, dll.

                $foto = $this->request->getFile('mhs_foto_' . $idx);
                $ijazah = $this->request->getFile('mhs_ijazah_' . $idx);
                $sk = $this->request->getFile('mhs_sk_' . $idx);

                $fotoName = null;
                $ijazahName = null;
                $skName = null;

                if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                    $fotoName = $foto->getRandomName();
                    $foto->move($mhsUploadPath, $fotoName);
                }
                if ($ijazah && $ijazah->isValid() && !$ijazah->hasMoved()) {
                    $ijazahName = $ijazah->getRandomName();
                    $ijazah->move($mhsUploadPath, $ijazahName);
                }
                if ($sk && $sk->isValid() && !$sk->hasMoved()) {
                    $skName = $sk->getRandomName();
                    $sk->move($mhsUploadPath, $skName);
                }

                $dataMahasiswa = [
                    'institusi_id' => $institusi_id,
                    'id_profesi' => $this->request->getPost('profesi_id'),
                    'status' => 'Menunggu',
                    'nim' => $mhsNims[$i],
                    'nama_lengkap' => $mhsNames[$i],
                    'jenis_kelamin' => $mhsJk[$i],
                    'jenjang' => 'Profesi', // mock
                    'program_studi' => $this->request->getPost('prodi_asal'),
                    'tanggal_lahir' => $mhsTglLahir[$i],
                    'semester' => $mhsSemester[$i],
                    'no_hp' => $mhsHp[$i],
                    'email' => $mhsEmail[$i],
                    'file_foto' => $fotoName,
                    'file_ijazah' => $ijazahName,
                    'file_sk' => $skName
                ];

                // Check if user account needs to be created
                $userModel = new \App\Models\UsersPendidikanModel();
                $existingUser = $userModel->where('email', $mhsEmail[$i])->first();
                if (!$existingUser) {
                    $userModel->insert([
                        'role_id' => 3, // Assuming 3 is mahasiswa
                        'email' => $mhsEmail[$i] ?: ($mhsNims[$i] . '@student.com'),
                        'password' => password_hash(date('dmY', strtotime($mhsTglLahir[$i])), PASSWORD_DEFAULT),
                        'is_active' => 1
                    ]);
                    $dataMahasiswa['user_id'] = $userModel->getInsertID();
                } else {
                    $dataMahasiswa['user_id'] = $existingUser['id'];
                }

                // Check if mahasiswa already exists by NIM and Institusi
                $existingMhs = $mahasiswaModel->where('nim', $mhsNims[$i])->where('institusi_id', $institusi_id)->first();
                if ($existingMhs) {
                    $mahasiswa_id = $existingMhs['id'];
                    $mahasiswaModel->update($mahasiswa_id, $dataMahasiswa);
                } else {
                    $mahasiswaModel->insert($dataMahasiswa);
                    $mahasiswa_id = $mahasiswaModel->getInsertID();
                }

                // Create penempatan
                $penempatanModel->insert([
                    'pengajuan_id' => $pengajuan_id,
                    'mahasiswa_id' => $mahasiswa_id,
                    'stase_id' => null, // will be assigned by admin
                    'status_aktif' => 1
                ]);
            }
        }

        return redirect()->to('pendidikan/institusi/pengajuan/status')->with('success', 'Pengajuan berhasil dikirim dan sedang menunggu verifikasi.');
    }

    public function status()
    {
        $institusi_id = session()->get('institusi_id');
        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        
        $list_pengajuan = $pengajuanModel->where('institusi_id', $institusi_id)->orderBy('created_at', 'DESC')->findAll();

        $pengajuan_aktif = [];
        $pengajuan_selesai = [];
        foreach ($list_pengajuan as $p) {
            if ($p['status'] === 'Disetujui' || $p['status'] === 'Ditolak') {
                $pengajuan_selesai[] = $p;
            } else {
                $pengajuan_aktif[] = $p;
            }
        }

        $data = [
            'title' => 'Status Pengajuan',
            'pengajuan_aktif' => $pengajuan_aktif,
            'pengajuan_selesai' => $pengajuan_selesai
        ];
        return view('Pendidikan/Institusi/pengajuan/status', $data);
    }

    public function detail($id)
    {
        $institusi_id = session()->get('institusi_id');
        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        $pengajuan = $pengajuanModel->where('id', $id)->where('institusi_id', $institusi_id)->first();

        if (!$pengajuan) {
            return redirect()->to('/pendidikan/institusi/pengajuan/status')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $institusi = $institusiModel->find($institusi_id);

        $db = \Config\Database::connect();
        $builder = $db->table('penempatan_peserta_pendidikan');
        $builder->select('mahasiswa_pendidikan.*');
        $builder->join('mahasiswa_pendidikan', 'mahasiswa_pendidikan.id = penempatan_peserta_pendidikan.mahasiswa_id');
        $builder->where('penempatan_peserta_pendidikan.pengajuan_id', $id);
        $mahasiswaRaw = $builder->get()->getResultArray();

        $mahasiswa = [];
        foreach ($mahasiswaRaw as $m) {
            $mahasiswa[] = [
                'nama' => $m['nama_lengkap'],
                'nim' => $m['nim'],
                'jk' => $m['jenis_kelamin'],
                'dob' => $m['tanggal_lahir'],
                'semester' => $m['semester'],
                'hp' => $m['no_hp'],
                'email' => $m['email'],
                'file_foto' => $m['file_foto'],
                'file_ijazah' => $m['file_ijazah'],
                'file_sk' => $m['file_sk'],
            ];
        }

        $data_pengajuan = [
            'id' => $pengajuan['id'],
            'no_pengajuan' => 'REQ-' . str_pad($pengajuan['id'], 4, '0', STR_PAD_LEFT),
            'institusi' => $institusi['nama_institusi'],
            'fakultas' => '-',
            'prodi' => $mahasiswaRaw[0]['program_studi'] ?? '-',
            'periode' => date('d M Y', strtotime($pengajuan['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($pengajuan['tanggal_selesai'])),
            'tgl_mulai' => $pengajuan['tanggal_mulai'],
            'tgl_selesai' => $pengajuan['tanggal_selesai'],
            'jumlah' => $pengajuan['jumlah_peserta'],
            'penanggung_jawab' => $institusi['nama_kontak'],
            'jabatan' => '-',
            'hp_pj' => $institusi['no_telp'],
            'email_pj' => '-',
            'status' => $pengajuan['status'],
            'catatan' => $pengajuan['catatan_admin'] ?? 'Belum ada catatan.',
            'file_proposal' => $pengajuan['file_proposal'],
            'file_surat_pengantar' => $pengajuan['file_surat_pengantar'],
            'file_logbook' => $pengajuan['file_logbook'],
            'file_panduan' => $pengajuan['file_panduan'],
            'file_daftar_mhs' => $pengajuan['file_daftar_mhs'],
            'file_kompetensi' => $pengajuan['file_kompetensi'],
            'file_sk_pembimbing' => $pengajuan['file_sk_pembimbing'],
            'file_bukti_bayar' => $pengajuan['file_bukti_bayar'],
            'mahasiswa' => $mahasiswa
        ];

        $data = [
            'title' => 'Detail Pengajuan',
            'pengajuan' => $data_pengajuan
        ];
        return view('Pendidikan/Institusi/pengajuan/detail', $data);
    }

    public function edit($id)
    {
        $institusi_id = session()->get('institusi_id');
        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        $pengajuan = $pengajuanModel->where('id', $id)->where('institusi_id', $institusi_id)->first();

        if (!$pengajuan) {
            return redirect()->to('/pendidikan/institusi/pengajuan/status')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $institusi = $institusiModel->find($institusi_id);

        $profesiModel = new \App\Models\Pelatihan\ProfesiPelatihanModel();
        $list_profesi = $profesiModel->findAll();

        $db = \Config\Database::connect();
        $builder = $db->table('penempatan_peserta_pendidikan');
        $builder->select('mahasiswa_pendidikan.*');
        $builder->join('mahasiswa_pendidikan', 'mahasiswa_pendidikan.id = penempatan_peserta_pendidikan.mahasiswa_id');
        $builder->where('penempatan_peserta_pendidikan.pengajuan_id', $id);
        $mahasiswaRaw = $builder->get()->getResultArray();

        $mahasiswa = [];
        foreach ($mahasiswaRaw as $m) {
            $mahasiswa[] = [
                'nama' => $m['nama_lengkap'],
                'nim' => $m['nim'],
                'jk' => $m['jenis_kelamin'],
                'dob' => $m['tanggal_lahir'],
                'semester' => $m['semester'],
                'hp' => $m['no_hp'],
                'email' => $m['email'],
                'file_foto' => $m['file_foto'],
                'file_ijazah' => $m['file_ijazah'],
                'file_sk' => $m['file_sk'],
            ];
        }

        $data_pengajuan = [
            'id' => $pengajuan['id'],
            'no_pengajuan' => 'REQ-' . str_pad($pengajuan['id'], 4, '0', STR_PAD_LEFT),
            'institusi' => $institusi['nama_institusi'],
            'fakultas' => '-',
            'prodi' => !empty($mahasiswaRaw) ? $mahasiswaRaw[0]['program_studi'] : '-',
            'id_profesi' => !empty($mahasiswaRaw) ? $mahasiswaRaw[0]['id_profesi'] : '',
            'jenis_program' => $pengajuan['nama_program'],
            'periode' => date('d M Y', strtotime($pengajuan['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($pengajuan['tanggal_selesai'])),
            'tgl_mulai' => $pengajuan['tanggal_mulai'],
            'tgl_selesai' => $pengajuan['tanggal_selesai'],
            'jumlah' => $pengajuan['jumlah_peserta'],
            'penanggung_jawab' => $institusi['nama_kontak'],
            'jabatan' => '-',
            'hp_pj' => $institusi['no_telp'],
            'email_pj' => '-',
            'status' => $pengajuan['status'],
            'catatan' => $pengajuan['catatan_admin'],
            'file_proposal' => $pengajuan['file_proposal'],
            'file_surat_pengantar' => $pengajuan['file_surat_pengantar'],
            'file_logbook' => $pengajuan['file_logbook'],
            'file_panduan' => $pengajuan['file_panduan'],
            'file_daftar_mhs' => $pengajuan['file_daftar_mhs'],
            'file_kompetensi' => $pengajuan['file_kompetensi'],
            'file_sk_pembimbing' => $pengajuan['file_sk_pembimbing'],
            'file_bukti_bayar' => $pengajuan['file_bukti_bayar'],
            'mahasiswa' => $mahasiswa
        ];

        $data = [
            'title' => 'Edit Pengajuan Mahasiswa',
            'pengajuan' => $data_pengajuan,
            'list_profesi' => $list_profesi
        ];
        return view('Pendidikan/Institusi/pengajuan/edit', $data);
    }

    public function update($id)
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return redirect()->to('pendidikan/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $institusi_id = $sessionData['institusi_id'];

        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        $pengajuan = $pengajuanModel->where('id', $id)->where('institusi_id', $institusi_id)->first();

        if (!$pengajuan) {
            return redirect()->to('/pendidikan/institusi/pengajuan/status')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $penempatanModel = new \App\Models\PenempatanPesertaPendidikanModel();

        // Handle File Uploads for Pengajuan
        $file_proposal = $this->request->getFile('doc_proposal');
        $file_pengantar = $this->request->getFile('doc_pengantar');
        $file_logbook = $this->request->getFile('doc_logbook');
        $file_panduan = $this->request->getFile('doc_panduan');
        $file_daftar_mhs = $this->request->getFile('doc_daftar_mhs');
        $file_kompetensi = $this->request->getFile('doc_kompetensi');
        $file_sk_pembimbing = $this->request->getFile('doc_sk_pembimbing');
        $file_bukti_bayar = $this->request->getFile('doc_bukti_bayar');
        $file_dokumen_penilaian = $this->request->getFile('doc_penilaian');

        $uploadPath = FCPATH . 'uploads/dokumen_pengajuan/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        $dataPengajuan = [
            'nama_program' => $this->request->getPost('jenis_program'),
            'tanggal_mulai' => $this->request->getPost('tgl_mulai'),
            'tanggal_selesai' => $this->request->getPost('tgl_selesai'),
            'jumlah_peserta' => count($this->request->getPost('mhs_nama') ?? []),
            'status' => 'Menunggu'
        ];

        if ($file_proposal && $file_proposal->isValid() && !$file_proposal->hasMoved()) {
            $newName = $file_proposal->getRandomName();
            $file_proposal->move($uploadPath, $newName);
            $dataPengajuan['file_proposal'] = $newName;
        }
        if ($file_pengantar && $file_pengantar->isValid() && !$file_pengantar->hasMoved()) {
            $newName = $file_pengantar->getRandomName();
            $file_pengantar->move($uploadPath, $newName);
            $dataPengajuan['file_surat_pengantar'] = $newName;
        }
        if ($file_logbook && $file_logbook->isValid() && !$file_logbook->hasMoved()) {
            $newName = $file_logbook->getRandomName();
            $file_logbook->move($uploadPath, $newName);
            $dataPengajuan['file_logbook'] = $newName;
        }
        if ($file_panduan && $file_panduan->isValid() && !$file_panduan->hasMoved()) {
            $newName = $file_panduan->getRandomName();
            $file_panduan->move($uploadPath, $newName);
            $dataPengajuan['file_panduan'] = $newName;
        }
        if ($file_daftar_mhs && $file_daftar_mhs->isValid() && !$file_daftar_mhs->hasMoved()) {
            $newName = $file_daftar_mhs->getRandomName();
            $file_daftar_mhs->move($uploadPath, $newName);
            $dataPengajuan['file_daftar_mhs'] = $newName;
        }
        if ($file_kompetensi && $file_kompetensi->isValid() && !$file_kompetensi->hasMoved()) {
            $newName = $file_kompetensi->getRandomName();
            $file_kompetensi->move($uploadPath, $newName);
            $dataPengajuan['file_kompetensi'] = $newName;
        }
        if ($file_sk_pembimbing && $file_sk_pembimbing->isValid() && !$file_sk_pembimbing->hasMoved()) {
            $newName = $file_sk_pembimbing->getRandomName();
            $file_sk_pembimbing->move($uploadPath, $newName);
            $dataPengajuan['file_sk_pembimbing'] = $newName;
        }
        if ($file_bukti_bayar && $file_bukti_bayar->isValid() && !$file_bukti_bayar->hasMoved()) {
            $newName = $file_bukti_bayar->getRandomName();
            $file_bukti_bayar->move($uploadPath, $newName);
            $dataPengajuan['file_bukti_bayar'] = $newName;
        }
        if ($file_dokumen_penilaian && $file_dokumen_penilaian->isValid() && !$file_dokumen_penilaian->hasMoved()) {
            $newName = $file_dokumen_penilaian->getRandomName();
            $file_dokumen_penilaian->move($uploadPath, $newName);
            $dataPengajuan['file_dokumen_penilaian'] = $newName;
        }

        // Update Pengajuan
        $pengajuanModel->update($id, $dataPengajuan);

        // Process Mahasiswa
        $penempatanModel->where('pengajuan_id', $id)->delete();

        $mhsNames = $this->request->getPost('mhs_nama');
        $mhsNims = $this->request->getPost('mhs_nim');
        $mhsTglLahir = $this->request->getPost('mhs_tgl_lahir');
        $mhsJk = $this->request->getPost('mhs_jk');
        $mhsSemester = $this->request->getPost('mhs_semester');
        $mhsHp = $this->request->getPost('mhs_hp');
        $mhsEmail = $this->request->getPost('mhs_email');
        
        $mhsUploadPath = FCPATH . 'uploads/dokumen_mahasiswa/';
        if (!is_dir($mhsUploadPath)) mkdir($mhsUploadPath, 0777, true);

        if ($mhsNames && is_array($mhsNames)) {
            for ($i = 0; $i < count($mhsNames); $i++) {
                $idx = $i + 1;

                $foto = $this->request->getFile('mhs_foto_' . $idx);
                $ijazah = $this->request->getFile('mhs_ijazah_' . $idx);
                $sk = $this->request->getFile('mhs_sk_' . $idx);

                $dataMahasiswa = [
                    'institusi_id' => $institusi_id,
                    'id_profesi' => $this->request->getPost('profesi_id'),
                    'status' => 'Menunggu',
                    'nim' => $mhsNims[$i],
                    'nama_lengkap' => $mhsNames[$i],
                    'jenis_kelamin' => $mhsJk[$i],
                    'jenjang' => 'Profesi',
                    'program_studi' => $this->request->getPost('prodi_asal'),
                    'tanggal_lahir' => $mhsTglLahir[$i],
                    'semester' => $mhsSemester[$i],
                    'no_hp' => $mhsHp[$i],
                    'email' => $mhsEmail[$i]
                ];

                if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                    $fotoName = $foto->getRandomName();
                    $foto->move($mhsUploadPath, $fotoName);
                    $dataMahasiswa['file_foto'] = $fotoName;
                }
                if ($ijazah && $ijazah->isValid() && !$ijazah->hasMoved()) {
                    $ijazahName = $ijazah->getRandomName();
                    $ijazah->move($mhsUploadPath, $ijazahName);
                    $dataMahasiswa['file_ijazah'] = $ijazahName;
                }
                if ($sk && $sk->isValid() && !$sk->hasMoved()) {
                    $skName = $sk->getRandomName();
                    $sk->move($mhsUploadPath, $skName);
                    $dataMahasiswa['file_sk'] = $skName;
                }

                $userModel = new \App\Models\UsersPendidikanModel();
                $existingUser = $userModel->where('email', $mhsEmail[$i])->first();
                if (!$existingUser) {
                    $userModel->insert([
                        'role_id' => 3,
                        'email' => $mhsEmail[$i] ?: ($mhsNims[$i] . '@student.com'),
                        'password' => password_hash(date('dmY', strtotime($mhsTglLahir[$i])), PASSWORD_DEFAULT),
                        'is_active' => 1
                    ]);
                    $dataMahasiswa['user_id'] = $userModel->getInsertID();
                } else {
                    $dataMahasiswa['user_id'] = $existingUser['id'];
                }

                $existingMhs = $mahasiswaModel->where('nim', $mhsNims[$i])->where('institusi_id', $institusi_id)->first();
                if ($existingMhs) {
                    $mahasiswa_id = $existingMhs['id'];
                    $mahasiswaModel->update($mahasiswa_id, $dataMahasiswa);
                } else {
                    $mahasiswaModel->insert($dataMahasiswa);
                    $mahasiswa_id = $mahasiswaModel->getInsertID();
                }

                $penempatanModel->insert([
                    'pengajuan_id' => $id,
                    'mahasiswa_id' => $mahasiswa_id,
                    'stase_id' => null,
                    'status_aktif' => 1
                ]);
            }
        }

        return redirect()->to('pendidikan/institusi/pengajuan/status')->with('success', 'Pengajuan berhasil diperbarui dan sedang menunggu verifikasi ulang.');
    }

    public function mahasiswa()
    {
        $institusi_id = session()->get('institusi_id');

        $db = \Config\Database::connect();
        $builder = $db->table('penempatan_peserta_pendidikan');
        $builder->select('
            mahasiswa_pendidikan.id,
            mahasiswa_pendidikan.nama_lengkap as nama,
            mahasiswa_pendidikan.nim,
            mahasiswa_pendidikan.email,
            mahasiswa_pendidikan.no_hp as hp,
            mahasiswa_pendidikan.tanggal_lahir as dob,
            mahasiswa_pendidikan.program_studi as prodi,
            mahasiswa_pendidikan.status,
            mahasiswa_pendidikan.payment_status,
            mahasiswa_pendidikan.nominal,
            mahasiswa_pendidikan.invoice_file,
            mahasiswa_pendidikan.alasan_penolakan,
            mahasiswa_pendidikan.jenis_kelamin,
            mahasiswa_pendidikan.semester,
            mahasiswa_pendidikan.id_profesi,
            mahasiswa_pendidikan.file_foto,
            mahasiswa_pendidikan.file_ijazah,
            mahasiswa_pendidikan.file_sk,
            MAX(pengajuan_praktik_pendidikan.tanggal_mulai) as tanggal_mulai,
            MAX(pengajuan_praktik_pendidikan.tanggal_selesai) as tanggal_selesai
        ');
        $builder->join('mahasiswa_pendidikan', 'mahasiswa_pendidikan.id = penempatan_peserta_pendidikan.mahasiswa_id');
        $builder->join('pengajuan_praktik_pendidikan', 'pengajuan_praktik_pendidikan.id = penempatan_peserta_pendidikan.pengajuan_id');
        $builder->where('mahasiswa_pendidikan.institusi_id', $institusi_id);
        $builder->where('mahasiswa_pendidikan.status', 'Disetujui');
        $builder->groupBy('mahasiswa_pendidikan.id');
        $builder->orderBy('mahasiswa_pendidikan.nama_lengkap', 'ASC');
        $result = $builder->get()->getResultArray();

        $list_mahasiswa = [];
        $list_mahasiswa = [];
        foreach ($result as $row) {
            $list_mahasiswa[] = [
                'id' => $row['id'],
                'nama' => $row['nama'],
                'nim' => $row['nim'],
                'email' => $row['email'],
                'hp' => $row['hp'],
                'dob' => $row['dob'],
                'prodi' => $row['prodi'],
                'periode' => date('d M Y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($row['tanggal_selesai'])),
                'status' => $row['status'],
                'payment_status' => $row['payment_status'] ?? 'Belum Invoice',
                'nominal' => $row['nominal'] ?? 0,
                'invoice_file' => $row['invoice_file'],
                'alasan_penolakan' => $row['alasan_penolakan'] ?? null,
                'jk' => $row['jenis_kelamin'],
                'semester' => $row['semester'],
                'id_profesi' => $row['id_profesi'],
                'file_foto' => $row['file_foto'],
                'file_ijazah' => $row['file_ijazah'],
                'file_sk' => $row['file_sk']
            ];
        }

        $profesiModel = new \App\Models\Pelatihan\ProfesiPelatihanModel();
        $list_profesi = $profesiModel->findAll();

        $data = [
            'title' => 'Daftar Mahasiswa Diterima',
            'list_mahasiswa' => $list_mahasiswa,
            'list_profesi' => $list_profesi
        ];
        return view('Pendidikan/Institusi/mahasiswa/index', $data);
    }

    public function get_nilai_mahasiswa($id)
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        
        $builder = $db->table('stase_ruangan_ci_pendidikan');
        $builder->select('stase_pendidikan.nama_stase, unit_kerja_pelatihan.nama_unit as nama_ruangan, ci_pendidikan.nama_lengkap as ci_name, stase_ruangan_ci_pendidikan.stase_id, stase_ruangan_ci_pendidikan.ruangan_id');
        $builder->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id');
        $builder->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = stase_ruangan_ci_pendidikan.ruangan_id');
        $builder->join('ci_pendidikan', 'ci_pendidikan.id = stase_ruangan_ci_pendidikan.ci_id', 'left');
        $builder->where("JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '\"" . $id . "\"') OR JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '" . $id . "')");
        $rooms = $builder->get()->getResultArray();

        foreach ($rooms as &$r) {
            $tugas = $db->table('pengumpulan_tugas_pendidikan')
                ->select('pengumpulan_tugas_pendidikan.*, tugas_pendidikan.nama_tugas')
                ->join('tugas_pendidikan', 'tugas_pendidikan.id = pengumpulan_tugas_pendidikan.tugas_id')
                ->where('pengumpulan_tugas_pendidikan.mahasiswa_id', $id)
                ->where('tugas_pendidikan.stase_id', $r['stase_id'])
                ->where('tugas_pendidikan.ruangan_id', $r['ruangan_id'])
                ->get()->getResultArray();

            $r['tugas'] = $tugas;

            $logbooks = $db->table('logbook_pendidikan')
                ->select('logbook_pendidikan.*')
                ->join('penempatan_peserta_pendidikan', 'penempatan_peserta_pendidikan.id = logbook_pendidikan.penempatan_id')
                ->where('penempatan_peserta_pendidikan.mahasiswa_id', $id)
                ->where('logbook_pendidikan.stase_id', $r['stase_id'])
                ->where('logbook_pendidikan.ruangan_id', $r['ruangan_id'])
                ->get()->getResultArray();
            
            $r['logbook'] = $logbooks;
        }

        return $this->response->setJSON(['success' => true, 'data' => $rooms]);
    }

    public function update_mahasiswa($id)
    {
        try {
            $sessionData = session()->get();
            if (!isset($sessionData['institusi_id'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
            }

            $institusi_id = $sessionData['institusi_id'];
            $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
            $mahasiswa = $mahasiswaModel->where('id', $id)->where('institusi_id', $institusi_id)->first();

            if (!$mahasiswa) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data mahasiswa tidak ditemukan.']);
            }

            $dataUpdate = [
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'nim' => $this->request->getPost('nim'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'semester' => $this->request->getPost('semester') ?: null,
                'no_hp' => $this->request->getPost('no_hp') ?: null,
                'email' => $this->request->getPost('email') ?: null,
                'program_studi' => $this->request->getPost('program_studi') ?: null,
                'id_profesi' => $this->request->getPost('id_profesi') ?: null,
            ];

            // Handle file uploads if any
            $mhsUploadPath = FCPATH . 'uploads/dokumen_mahasiswa/';
            if (!is_dir($mhsUploadPath)) mkdir($mhsUploadPath, 0777, true);

            $foto = $this->request->getFile('file_foto');
            $ijazah = $this->request->getFile('file_ijazah');
            $sk = $this->request->getFile('file_sk');

            if ($foto && $foto->isValid() && !$foto->hasMoved()) {
                $fotoName = $foto->getRandomName();
                $foto->move($mhsUploadPath, $fotoName);
                $dataUpdate['file_foto'] = $fotoName;
            }
            if ($ijazah && $ijazah->isValid() && !$ijazah->hasMoved()) {
                $ijazahName = $ijazah->getRandomName();
                $ijazah->move($mhsUploadPath, $ijazahName);
                $dataUpdate['file_ijazah'] = $ijazahName;
            }
            if ($sk && $sk->isValid() && !$sk->hasMoved()) {
                $skName = $sk->getRandomName();
                $sk->move($mhsUploadPath, $skName);
                $dataUpdate['file_sk'] = $skName;
            }

            if (!$mahasiswaModel->update($id, $dataUpdate)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate database: ' . implode(', ', $mahasiswaModel->errors())]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Data mahasiswa berhasil diperbarui.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error System: ' . $e->getMessage()]);
        }
    }

    public function simpan_nilai_akhir()
    {
        try {
            $sessionData = session()->get();
            if (!isset($sessionData['institusi_id'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
            }

            $institusi_id = $sessionData['institusi_id'];
            $mahasiswa_id = $this->request->getPost('id');
            $nilai_akhir = $this->request->getPost('nilai_akhir');

            if (!$mahasiswa_id || $nilai_akhir === null) {
                return $this->response->setJSON(['success' => false, 'message' => 'ID Mahasiswa dan Nilai Akhir wajib diisi.']);
            }

            $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
            $mahasiswa = $mahasiswaModel->where('id', $mahasiswa_id)->where('institusi_id', $institusi_id)->first();

            if (!$mahasiswa) {
                return $this->response->setJSON(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.']);
            }

            $mahasiswaModel->update($mahasiswa_id, [
                'nilai_akhir' => $nilai_akhir,
                'status' => 'Lulus'
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Nilai berhasil disimpan dan mahasiswa telah dipindahkan ke daftar Mahasiswa Lulus.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error System: ' . $e->getMessage()]);
        }
    }

    public function mahasiswa_lulus()
    {
        $institusi_id = session()->get('institusi_id');

        $db = \Config\Database::connect();
        $builder = $db->table('mahasiswa_pendidikan');
        $builder->select('
            mahasiswa_pendidikan.id,
            mahasiswa_pendidikan.nama_lengkap as nama,
            mahasiswa_pendidikan.nim,
            mahasiswa_pendidikan.program_studi as prodi,
            mahasiswa_pendidikan.status,
            mahasiswa_pendidikan.nilai_akhir,
            MAX(penempatan_peserta_pendidikan.id) as penempatan_id,
            MAX(pengajuan_praktik_pendidikan.tanggal_mulai) as tanggal_mulai,
            MAX(pengajuan_praktik_pendidikan.tanggal_selesai) as tanggal_selesai
        ');
        $builder->join('penempatan_peserta_pendidikan', 'penempatan_peserta_pendidikan.mahasiswa_id = mahasiswa_pendidikan.id', 'left');
        $builder->join('pengajuan_praktik_pendidikan', 'pengajuan_praktik_pendidikan.id = penempatan_peserta_pendidikan.pengajuan_id', 'left');
        $builder->where('mahasiswa_pendidikan.institusi_id', $institusi_id);
        $builder->where('mahasiswa_pendidikan.status', 'Lulus');
        $builder->groupBy('mahasiswa_pendidikan.id');
        $builder->orderBy('mahasiswa_pendidikan.nama_lengkap', 'ASC');
        $result = $builder->get()->getResultArray();

        $list_mahasiswa = [];
        foreach ($result as $row) {
            $list_mahasiswa[] = [
                'id' => $row['id'],
                'nama' => $row['nama'],
                'nim' => $row['nim'],
                'prodi' => $row['prodi'],
                'periode' => date('d M Y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($row['tanggal_selesai'])),
                'nilai_akhir' => $row['nilai_akhir'] ?? '-',
                'status' => $row['status']
            ];
        }

        $data = [
            'title' => 'Mahasiswa Lulus',
            'active_menu' => 'mahasiswa_lulus',
            'list_mahasiswa' => $list_mahasiswa
        ];
        return view('Pendidikan/Institusi/mahasiswa/lulus', $data);
    }

    public function cetak_sertifikat($id)
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return redirect()->to('pendidikan/auth')->with('error', 'Silakan login terlebih dahulu.');
        }
        $institusi_id = $sessionData['institusi_id'];

        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->where('id', $id)->where('institusi_id', $institusi_id)->first();

        if (!$mahasiswa || $mahasiswa['status'] !== 'Lulus') {
            return redirect()->to('pendidikan/institusi/mahasiswa/lulus')->with('error', 'Sertifikat tidak tersedia untuk mahasiswa ini.');
        }

        $data = [
            'mahasiswa' => $mahasiswa
        ];

        // Load Dompdf
        $dompdf = new \Dompdf\Dompdf();
        
        // Load view HTML
        $html = view('Pendidikan/Institusi/mahasiswa/sertifikat_pdf', $data);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Sertifikat_Lulus_' . str_replace(' ', '_', $mahasiswa['nama_lengkap']) . '_' . $mahasiswa['nim'];
        $dompdf->stream($filename . ".pdf", ["Attachment" => false]);
    }

    public function submit_payment()
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
        }

        $institusi_id = $sessionData['institusi_id'];
        $mahasiswa_id = $this->request->getPost('mahasiswa_id');
        
        if (!$mahasiswa_id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID Mahasiswa tidak valid.']);
        }

        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->where('id', $mahasiswa_id)->where('institusi_id', $institusi_id)->first();

        if (!$mahasiswa) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.']);
        }

        $file_bukti = $this->request->getFile('bukti_bayar');
        if (!$file_bukti || !$file_bukti->isValid() || $file_bukti->hasMoved()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengunggah file bukti pembayaran.']);
        }

        $uploadPath = FCPATH . 'uploads/dokumen_mahasiswa/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

        $newName = $file_bukti->getRandomName();
        $file_bukti->move($uploadPath, $newName);

        $mahasiswaModel->update($mahasiswa_id, [
            'payment_status' => 'Menunggu Verifikasi',
            'file_bukti_bayar' => $newName,
            'alasan_penolakan' => null
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Bukti Pembayaran berhasil diunggah! Pembayaran sedang diverifikasi.']);
    }
}
