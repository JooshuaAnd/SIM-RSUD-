<?php

namespace App\Controllers\Riset\Peneliti;

use App\Controllers\BaseController;
use App\Models\PublikasiRisetModel;
use App\Models\DokumenRisetModel;

class Publikasi extends BaseController
{
    protected PublikasiRisetModel $publikasiModel;
    protected DokumenRisetModel $dokumenModel;
    protected \App\Models\UserRisetModel $userModel;

    public function __construct()
    {
        $this->publikasiModel = new PublikasiRisetModel();
        $this->dokumenModel = new DokumenRisetModel();
        $this->userModel = new \App\Models\UserRisetModel();
    }

    public function index()
    {
        $revisi = $this->request->getGet('revisi');
        $id = $this->request->getGet('id');
        $data = null;
        $user = $this->userModel->find(session()->get('riset_user_id'));

        // Fetch completed research permits for this researcher
        $pengajuanModel = new \App\Models\PengajuanRisetModel();
        $riwayat_riset = $pengajuanModel
            ->where('user_riset_id', session()->get('riset_user_id'))
            ->where('jenis_pengajuan', 'penelitian')
            ->where('status', 'selesai')
            ->findAll();

        if ($revisi && $id) {
            $data = $this->publikasiModel->find($id);
        }

        return view('Riset/peneliti/publikasi/index', [
            'title'         => ($revisi) ? 'Revisi Izin Publikasi' : 'Pengajuan Publikasi',
            'active_menu'   => 'publikasi',
            'data'          => $data,
            'user'          => $user,
            'riwayat_riset' => $riwayat_riset
        ]);
    }

    public function form()
    {
        return $this->index();
    }

    public function submit()
    {
        $is_revisi = $this->request->getPost('is_revisi');
        $id = $this->request->getPost('id');
        $userId = session()->get('riset_user_id');

        $riset_id = $this->request->getPost('riset_id');
        $judul = '';
        if ($riset_id && $riset_id !== 'other') {
            $pengajuanModel = new \App\Models\PengajuanRisetModel();
            $pengajuan = $pengajuanModel->find($riset_id);
            $judul = $pengajuan ? $pengajuan['judul'] : '';
        } else {
            $judul = $this->request->getPost('judul_penelitian');
            $riset_id = null;
        }

        $data = [
            'user_riset_id'      => $userId,
            'pengajuan_riset_id' => $riset_id,
            'tujuan_laporan'     => $this->request->getPost('tujuan_laporan') ?? 'izin',
            'judul'              => $judul,
            'waktu_mulai'        => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'      => $this->request->getPost('waktu_selesai'),
            'nama'               => $this->request->getPost('nama'),
            'identitas'          => $this->request->getPost('identitas'),
            'prodi'              => $this->request->getPost('prodi'),
            'institusi'          => $this->request->getPost('institusi'),
            'jenis_jurnal'       => $this->request->getPost('jenis_jurnal'),
            'nama_publikasi'     => $this->request->getPost('nama_publikasi'),
            'kategori_jurnal'    => $this->request->getPost('kategori_jurnal'),
            'issn'               => $this->request->getPost('issn'),
            'scope'              => $this->request->getPost('scope'),
            'alamat_web'         => $this->request->getPost('alamat_web'),
            'abstrak'            => $this->request->getPost('abstrak'),
            'status'             => 'dalam review'
        ];

        // Handle file upload (dokumen)
        $fileInputs = ['permohonan_izin', 'salinan_izin_penelitian', 'draft_artikel', 'pernyataan_anonimitas'];
        
        $validationRules = [];
        foreach ($fileInputs as $input) {
            $f = $this->request->getFile($input);
            if ($f && $f->isValid() && !$f->hasMoved()) {
                $validationRules[$input] = "ext_in[{$input},pdf,doc,docx,jpg,jpeg,png]";
            }
        }
        
        if (!empty($validationRules) && !$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Format file dokumen publikasi tidak diizinkan. Harap unggah format PDF, DOCX, atau Gambar.');
        }

        $uploadedFilesData = [];
        foreach ($fileInputs as $input) {
            $f = $this->request->getFile($input);
            if ($f && $f->isValid() && !$f->hasMoved()) {
                $uploadedFilesData[] = [
                    'file' => $f,
                    'jenis' => $input
                ];
            }
        }
        
        if ($is_revisi && $id) {
            $this->publikasiModel->update($id, $data);

            // Upload dokumen baru jika ada
            if (!empty($uploadedFilesData)) {
                foreach ($uploadedFilesData as $fileData) {
                    $file = $fileData['file'];
                    $jenis = $fileData['jenis'];
                    $newName = str_replace(' ', '_', $file->getClientName());
                    $file->move(FCPATH . 'uploads/riset/publikasi', $newName);
                    
                    // Hapus dokumen lama untuk jenis ini jika ada (termasuk legacy bug 'publikasi')
                    $this->dokumenModel->where('pengajuan_riset_id', $id)
                                       ->groupStart()
                                           ->where('jenis_dokumen', $jenis)
                                           ->orWhere('jenis_dokumen', 'publikasi')
                                       ->groupEnd()
                                       ->delete();

                    $this->dokumenModel->insert([
                        'pengajuan_riset_id' => $id,
                        'jenis_dokumen'      => $jenis, // Save the actual document type
                        'file_path'          => 'uploads/riset/publikasi/' . $newName,
                        'status_dokumen'     => 'pending'
                    ]);
                }
            }

            return redirect()->to(base_url('riset/peneliti/status'))->with('success', "Revisi izin publikasi berhasil dikirim kembali ke admin.");
        }

        $insertId = $this->publikasiModel->insert($data);

        // Upload dokumen
        if (!empty($uploadedFilesData)) {
            foreach ($uploadedFilesData as $fileData) {
                $file = $fileData['file'];
                $jenis = $fileData['jenis'];
                $newName = str_replace(' ', '_', $file->getClientName());
                $file->move(FCPATH . 'uploads/riset/publikasi', $newName);
                $this->dokumenModel->insert([
                    'pengajuan_riset_id' => $insertId,
                    'jenis_dokumen'      => $jenis, // Save the actual document type
                    'file_path'          => 'uploads/riset/publikasi/' . $newName,
                    'status_dokumen'     => 'pending'
                ]);
            }
        }

        return redirect()->to(base_url('riset/peneliti/status'))->with('success', 'Pengajuan izin publikasi baru berhasil dikirim. Mohon tunggu review dari Admin Diklat.');
    }

    public function detail()
    {
        $id = $this->request->getGet('id');
        $publikasi = $this->publikasiModel->find($id);

        if (!$publikasi) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data publikasi tidak ditemukan.');
        }

        // Ambil dokumen terkait
        // Ambil dokumen terkait (ambil semua dokumen yang terhubung ke pengajuan_riset_id ini)
        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->whereIn('jenis_dokumen', ['publikasi', 'permohonan_izin', 'salinan_izin_penelitian', 'draft_artikel', 'pernyataan_anonimitas', 'Surat Izin Publikasi Resmi'])
                                      ->findAll();
        $publikasi['dokumen'] = $dokumen;
        $date = $publikasi['created_at'] ?? 'now';
        $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        $publikasi['tanggal'] = date('d', strtotime($date)) . ' ' . $bulanIndo[(int)date('m', strtotime($date))] . ' ' . date('Y', strtotime($date));

        // Cek jika baru upload bukti bayar
        $bukti_file = session()->getFlashdata('bukti_file');
        if ($bukti_file) {
            $publikasi['status'] = 'menunggu_verifikasi';
        }

        $pengaturanModel = new \App\Models\PengaturanSuratRisetModel();
        $pengaturan = $pengaturanModel->first();

        return view('Riset/peneliti/publikasi/detail', [
            'title'       => 'Detail Laporan',
            'active_menu' => 'status',
            'data'        => $publikasi,
            'pengaturan'  => $pengaturan
        ]);
    }

    public function print_izin($id = null)
    {
        $publikasi = $this->publikasiModel->find($id);

        if (!$publikasi) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data publikasi tidak ditemukan.');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->where('jenis_dokumen', 'Surat Izin Publikasi Resmi')
                                      ->orderBy('id', 'DESC')
                                      ->first();
                                      
        if ($dokumen && !empty($dokumen['file_path'])) {
            return redirect()->to(base_url($dokumen['file_path']));
        }
        
        return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Surat Izin Publikasi Resmi belum diterbitkan oleh Admin.');
    }

    public function publikasi_bayar()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('bukti_bayar');

        if ($file && $file->isValid()) {
            if (!$this->validate(['bukti_bayar' => 'ext_in[bukti_bayar,pdf,jpg,jpeg,png]'])) {
                return redirect()->back()->with('error', 'Format bukti pembayaran tidak valid. Harap unggah JPG, PNG, atau PDF.');
            }
            $fileName = str_replace(' ', '_', $file->getClientName());
            $file->move(FCPATH . 'uploads/riset/pembayaran', $fileName);

            $this->publikasiModel->update($id, [
                'bukti_file' => 'uploads/riset/pembayaran/' . $fileName,
                'status'     => 'menunggu_verifikasi'
            ]);
        }

        return redirect()->to(base_url('riset/peneliti/publikasi/detail?id=' . $id))
            ->with('success', "Bukti pembayaran untuk publikasi berhasil diunggah. Menunggu verifikasi admin.");
    }
}
