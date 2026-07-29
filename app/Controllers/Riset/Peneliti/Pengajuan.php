<?php

namespace App\Controllers\Riset\Peneliti;

use App\Controllers\BaseController;
use App\Models\PengajuanRisetModel;
use App\Models\DokumenRisetModel;

class Pengajuan extends BaseController
{
    protected PengajuanRisetModel $pengajuanModel;
    protected DokumenRisetModel $dokumenModel;
    protected \App\Models\UserRisetModel $userRisetModel;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanRisetModel();
        $this->dokumenModel = new DokumenRisetModel();
        $this->userRisetModel = new \App\Models\UserRisetModel();
    }

    public function stupen_form()
    {
        $id = $this->request->getGet('id');
        $data = null;
        $user = $this->userRisetModel->find(session()->get('riset_user_id'));

        if ($id) {
            $data = $this->pengajuanModel->find($id);
        }

        return view('Riset/peneliti/pengajuan/stupen_form', [
            'title'       => ($id) ? 'Revisi Studi Pendahuluan' : 'Form Studi Pendahuluan',
            'active_menu' => 'riset',
            'data'        => $data,
            'user'        => $user
        ]);
    }

    public function stupen_detail($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data pengajuan tidak ditemukan');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->whereIn('jenis_dokumen', ['Surat Permohonan', 'Proposal', 'CV', 'Draft Wawancara', 'Surat Izin Resmi'])
                                      ->findAll();
        $pengajuan['dokumen'] = $dokumen;
        
        // Cek jika baru saja upload bukti bayar
        $bukti_file = session()->getFlashdata('bukti_file');
        if ($bukti_file) {
            $pengajuan['status'] = 'menunggu_verifikasi';
        }
        $date = $pengajuan['created_at'] ?? 'now';
        $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        $pengajuan['tanggal'] = date('d', strtotime($date)) . ' ' . $bulanIndo[(int)date('m', strtotime($date))] . ' ' . date('Y', strtotime($date));
        $pengajuan['bukti_file'] = $pengajuan['bukti_file'] ?? $bukti_file;

        $pengaturanModel = new \App\Models\PengaturanSuratRisetModel();
        $pengaturan = $pengaturanModel->first();

        return view('Riset/peneliti/pengajuan/stupen_detail', [
            'title'       => 'Detail Pendaftaran Riset',
            'active_menu' => 'status',
            'data'        => $pengajuan,
            'pengaturan'  => $pengaturan
        ]);
    }

    public function stupen_submit()
    {
        $is_revisi = $this->request->getPost('is_revisi');
        $id = $this->request->getPost('id');
        $userId = session()->get('riset_user_id');

        $data = [
            'user_riset_id'  => $userId,
            'jenis_pengajuan'=> 'studi_pendahuluan',
            'nama'           => $this->request->getPost('nama'),
            'identitas'      => $this->request->getPost('identitas'),
            'prodi'          => $this->request->getPost('prodi'),
            'institusi'      => $this->request->getPost('institusi'),
            'judul'          => $this->request->getPost('judul'),
            'waktu_mulai'    => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'  => $this->request->getPost('waktu_selesai'),
            'status'         => 'dalam review'
        ];

        // Handle file upload dokumen
        $dokumenFields = ['surat_permohonan', 'proposal', 'cv', 'draft_wawancara'];

        $validationRules = [];
        foreach ($dokumenFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $validationRules[$field] = "ext_in[{$field},pdf,doc,docx,jpg,jpeg,png]";
            }
        }
        
        if (!empty($validationRules) && !$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Format file dokumen tidak diizinkan. Harap unggah format PDF, DOCX, atau Gambar.');
        }

        if ($is_revisi && $id) {
            $this->pengajuanModel->update($id, $data);

            foreach ($dokumenFields as $field) {
                $file = $this->request->getFile($field);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newName = str_replace(' ', '_', $file->getClientName());
                    $file->move(FCPATH . 'uploads/riset/dokumen', $newName);
                    $jenisDoc = strtolower($field) == 'cv' ? 'CV' : ucwords(str_replace('_', ' ', $field));
                    
                    // Hapus dokumen lama untuk jenis ini jika ada
                    $this->dokumenModel->where('pengajuan_riset_id', $id)
                                       ->where('jenis_dokumen', $jenisDoc)
                                       ->delete();
                                       
                    $this->dokumenModel->insert([
                        'pengajuan_riset_id' => $id,
                        'jenis_dokumen'      => $jenisDoc,
                        'file_path'          => 'uploads/riset/dokumen/' . $newName,
                        'status_dokumen'     => 'pending'
                    ]);
                }
            }

            return redirect()->to(base_url('riset/peneliti/status'))->with('success', "Revisi Studi Pendahuluan berhasil dikirim. Menunggu verifikasi ulang admin.");
        }

        $insertId = $this->pengajuanModel->insert($data);

        foreach ($dokumenFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = str_replace(' ', '_', $file->getClientName());
                $file->move(FCPATH . 'uploads/riset/dokumen', $newName);
                $this->dokumenModel->insert([
                    'pengajuan_riset_id' => $insertId,
                    'jenis_dokumen'      => strtolower($field) == 'cv' ? 'CV' : ucwords(str_replace('_', ' ', $field)),
                    'file_path'          => 'uploads/riset/dokumen/' . $newName,
                    'status_dokumen'     => 'pending'
                ]);
            }
        }

        return redirect()->to(base_url('riset/peneliti/status'))->with('success', 'Pendaftaran Studi Pendahuluan baru berhasil dikirim. Menunggu review admin.');
    }

    public function surat_izin_print($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (!$pengajuan) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data tidak ditemukan.');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->where('jenis_dokumen', 'Surat Izin Resmi')
                                      ->first();
                                      
        if ($dokumen && !empty($dokumen['file_path'])) {
            return redirect()->to(base_url($dokumen['file_path']));
        }
        
        return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Surat Izin Resmi belum diterbitkan oleh Admin.');
    }

    public function surat_izin_penelitian_print($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (!$pengajuan) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data tidak ditemukan.');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->where('jenis_dokumen', 'Surat Izin Resmi')
                                      ->first();
                                      
        if ($dokumen && !empty($dokumen['file_path'])) {
            return redirect()->to(base_url($dokumen['file_path']));
        }
        
        return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Surat Izin Resmi belum diterbitkan oleh Admin.');
    }

    public function stupen_bayar()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('bukti_bayar');
        if ($file && $file->isValid()) {
            if (!$this->validate(['bukti_bayar' => 'ext_in[bukti_bayar,pdf,jpg,jpeg,png]'])) {
                return redirect()->back()->with('error', 'Format bukti pembayaran tidak valid. Harap unggah JPG, PNG, atau PDF.');
            }
            $fileName = str_replace(' ', '_', $file->getClientName());
            $file->move(FCPATH . 'uploads/riset/pembayaran', $fileName);
            
            $this->pengajuanModel->update($id, [
                'bukti_file' => 'uploads/riset/pembayaran/' . $fileName,
                'status'     => 'menunggu_verifikasi'
            ]);
        }

        return redirect()->to(base_url("riset/peneliti/pengajuan/stupen/detail/{$id}"))
            ->with('success', "Bukti pembayaran untuk pendaftaran riset berhasil diunggah. Menunggu verifikasi admin.");
    }

    public function izin_form()
    {
        $id = $this->request->getGet('id');
        $data = null;
        $user = $this->userRisetModel->find(session()->get('riset_user_id'));

        if ($id) {
            $data = $this->pengajuanModel->find($id);
        }

        return view('Riset/peneliti/pengajuan/izin_form', [
            'title'       => ($id) ? 'Revisi Izin Penelitian' : 'Form Izin Penelitian',
            'active_menu' => 'izin',
            'data'        => $data,
            'user'        => $user
        ]);
    }

    public function izin_detail($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);
        if (!$pengajuan) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data pengajuan tidak ditemukan');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->whereIn('jenis_dokumen', ['Surat Permohonan', 'Proposal', 'CV', 'Draft Wawancara', 'Surat Izin Resmi'])
                                      ->findAll();
        $pengajuan['dokumen'] = $dokumen;

        $bukti_file = session()->getFlashdata('bukti_file');
        if ($bukti_file) {
            $pengajuan['status'] = 'menunggu_verifikasi';
        }
        $date = $pengajuan['created_at'] ?? 'now';
        $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        $pengajuan['tanggal'] = date('d', strtotime($date)) . ' ' . $bulanIndo[(int)date('m', strtotime($date))] . ' ' . date('Y', strtotime($date));
        $pengajuan['bukti_file'] = $pengajuan['bukti_file'] ?? $bukti_file;

        $pengaturanModel = new \App\Models\PengaturanSuratRisetModel();
        $pengaturan = $pengaturanModel->first();

        return view('Riset/peneliti/pengajuan/izin_detail', [
            'title'       => 'Detail Izin Penelitian',
            'active_menu' => 'status',
            'data'        => $pengajuan,
            'pengaturan'  => $pengaturan
        ]);
    }

    public function izin_submit()
    {
        $is_revisi = $this->request->getPost('is_revisi');
        $id = $this->request->getPost('id');
        $userId = session()->get('riset_user_id');

        $data = [
            'user_riset_id'  => $userId,
            'jenis_pengajuan'=> 'penelitian',
            'nama'           => $this->request->getPost('nama'),
            'identitas'      => $this->request->getPost('identitas'),
            'prodi'          => $this->request->getPost('prodi'),
            'institusi'      => $this->request->getPost('institusi'),
            'judul'          => $this->request->getPost('judul'),
            'waktu_mulai'    => $this->request->getPost('waktu_mulai'),
            'waktu_selesai'  => $this->request->getPost('waktu_selesai'),
            'status'         => 'dalam review'
        ];

        // Handle file upload
        $dokumenFields = ['surat_permohonan', 'proposal', 'cv', 'draft_wawancara'];

        $validationRules = [];
        foreach ($dokumenFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $validationRules[$field] = "ext_in[{$field},pdf,doc,docx,jpg,jpeg,png]";
            }
        }
        
        if (!empty($validationRules) && !$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Format file dokumen tidak diizinkan. Harap unggah format PDF, DOCX, atau Gambar.');
        }

        if ($is_revisi && $id) {
            $this->pengajuanModel->update($id, $data);

            foreach ($dokumenFields as $field) {
                $file = $this->request->getFile($field);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newName = str_replace(' ', '_', $file->getClientName());
                    $file->move(FCPATH . 'uploads/riset/dokumen', $newName);
                    $jenisDoc = strtolower($field) == 'cv' ? 'CV' : ucwords(str_replace('_', ' ', $field));
                    
                    // Hapus dokumen lama untuk jenis ini jika ada
                    $this->dokumenModel->where('pengajuan_riset_id', $id)
                                       ->where('jenis_dokumen', $jenisDoc)
                                       ->delete();
                                       
                    $this->dokumenModel->insert([
                        'pengajuan_riset_id' => $id,
                        'jenis_dokumen'      => $jenisDoc,
                        'file_path'          => 'uploads/riset/dokumen/' . $newName,
                        'status_dokumen'     => 'pending'
                    ]);
                }
            }

            return redirect()->to(base_url('riset/peneliti/status'))->with('success', "Revisi Izin Penelitian berhasil dikirim. Menunggu verifikasi ulang admin.");
        }

        $insertId = $this->pengajuanModel->insert($data);

        foreach ($dokumenFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = str_replace(' ', '_', $file->getClientName());
                $file->move(FCPATH . 'uploads/riset/dokumen', $newName);
                $this->dokumenModel->insert([
                    'pengajuan_riset_id' => $insertId,
                    'jenis_dokumen'      => strtolower($field) == 'cv' ? 'CV' : ucwords(str_replace('_', ' ', $field)),
                    'file_path'          => 'uploads/riset/dokumen/' . $newName,
                    'status_dokumen'     => 'pending'
                ]);
            }
        }

        return redirect()->to(base_url('riset/peneliti/status'))->with('success', 'Pendaftaran Izin Penelitian baru berhasil dikirim. Menunggu review admin.');
    }

    public function izin_bayar()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('bukti_bayar');
        if ($file && $file->isValid()) {
            if (!$this->validate(['bukti_bayar' => 'ext_in[bukti_bayar,pdf,jpg,jpeg,png]'])) {
                return redirect()->back()->with('error', 'Format bukti pembayaran tidak valid. Harap unggah JPG, PNG, atau PDF.');
            }
            $fileName = str_replace(' ', '_', $file->getClientName());
            $file->move(FCPATH . 'uploads/riset/pembayaran', $fileName);
            
            $this->pengajuanModel->update($id, [
                'bukti_file' => 'uploads/riset/pembayaran/' . $fileName,
                'status'     => 'menunggu_verifikasi'
            ]);
        }

        return redirect()->to(base_url("riset/peneliti/pengajuan/izin/detail/{$id}"))
            ->with('success', "Bukti pembayaran izin penelitian berhasil diunggah. Menunggu verifikasi admin.");
    }
}
