<?php

namespace App\Controllers\Pendidikan\Institusi;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $institusi_id = session()->get('institusi_id');

        if (!$institusi_id) {
            return redirect()->to('/pendidikan/login')->with('error', 'Sesi institusi tidak valid.');
        }

        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $profileData = $institusiModel->find($institusi_id);
        
        $userModel = new \App\Models\UserPendidikanModel();
        $userData = $userModel->find(session()->get('user_id'));

        if (!$profileData) {
            return redirect()->to('/pendidikan/login')->with('error', 'Data institusi tidak ditemukan.');
        }

        $account_status = $profileData['status_verifikasi'];
        if ($account_status === 'Pending') $account_status = 'pending';
        if ($account_status === 'Verified') $account_status = 'approved';
        if ($account_status === 'Revision') $account_status = 'revision';
        if ($account_status === 'Rejected') $account_status = 'rejected';

        // Sinkronisasi session agar navbar/sidebar langsung terbuka
        if (session()->get('account_status') !== $account_status) {
            session()->set('account_status', $account_status);
        }

        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        $allPengajuan = $pengajuanModel->where('institusi_id', $institusi_id)->findAll();

        $total_pengajuan = count($allPengajuan);
        $menunggu = 0;
        $disetujui = 0;
        $ditolak = 0;

        foreach ($allPengajuan as $p) {
            if ($p['status'] === 'Menunggu') $menunggu++;
            if ($p['status'] === 'Disetujui') $disetujui++;
            if ($p['status'] === 'Ditolak') $ditolak++;
        }

        $data = [
            'title' => 'Dashboard Institusi',
            'status' => $account_status,
            'stats' => [
                'total_pengajuan' => $total_pengajuan,
                'menunggu' => $menunggu,
                'disetujui' => $disetujui,
                'ditolak' => $ditolak
            ],
            'notes' => $profileData['catatan_revisi'] ?? 'Tolong perbarui dokumen Anda dengan versi terbaru yang sudah ditandatangani.',
            'alasan_penolakan' => $profileData['alasan_penolakan'] ?? 'Akun Anda belum memenuhi syarat kerjasama saat ini.',
            'profile' => [
                'nama' => $profileData['nama_institusi'],
                'jenis' => $profileData['jenis_institusi'] ?? '-',
                'alamat' => $profileData['alamat'],
                'email' => $userData['email'] ?? '-',
                'telp' => $profileData['no_telp'],
                'pj' => $profileData['nama_kontak'],
                'jabatan' => $profileData['jabatan_pj'] ?? '-',
                'hp_pj' => $profileData['hp_pj'] ?? '-',
                'email_pj' => $profileData['email_pj'] ?? '-',
                'file_mou' => $profileData['file_mou'] ?? null,
                'file_permohonan' => $profileData['file_permohonan'] ?? null,
                'file_lainnya' => $profileData['file_lainnya'] ?? null,
            ]
        ];

        if ($account_status === 'approved') {
            return view('Pendidikan/Institusi/dashboard/index', $data);
        } elseif ($account_status === 'revision') {
            return view('Pendidikan/Institusi/dashboard/revision', $data);
        } elseif ($account_status === 'rejected') {
            return view('Pendidikan/Institusi/dashboard/rejected', $data);
        } else {
            // Default to pending
            return view('Pendidikan/Institusi/dashboard/pending', $data);
        }
    }

    public function update_profile()
    {
        $institusiId = session()->get('institusi_id');
        $userId = session()->get('user_id');
        if (!$institusiId || !$userId) {
            return redirect()->to('/pendidikan/login')->with('error', 'Sesi institusi tidak valid.');
        }

        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $userModel = new \App\Models\UserPendidikanModel();
        $institusi = $institusiModel->find($institusiId);
        if (!$institusi) {
            return redirect()->to('/pendidikan/login')->with('error', 'Data institusi tidak ditemukan.');
        }

        $namaInstitusi = trim((string) $this->request->getPost('nama'));
        $namaPjInput = $this->request->getPost('pj');
        if ($namaPjInput === null || $namaPjInput === '') {
            $namaPjInput = $this->request->getPost('nama_pj');
        }
        $namaPj = trim((string) $namaPjInput);
        $emailInstitusi = trim((string) $this->request->getPost('email_institusi'));
        $emailPj = trim((string) $this->request->getPost('email_pj'));
        $telpInstitusi = trim((string) $this->request->getPost('telp_institusi'));
        $hpPj = trim((string) $this->request->getPost('hp_pj'));

        if ($namaInstitusi === '' || $namaPj === '' || !preg_match("/^[\\p{L}][\\p{L}\\s.,'-]*$/u", $namaInstitusi) || !preg_match("/^[\\p{L}][\\p{L}\\s.,'-]*$/u", $namaPj)) {
            return redirect()->back()->withInput()->with('error', 'Nama hanya boleh berisi huruf, spasi, titik, koma, apostrof, atau tanda hubung.');
        }
        if (!filter_var($emailInstitusi, FILTER_VALIDATE_EMAIL) || !filter_var($emailPj, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Email institusi dan email penanggung jawab harus valid.');
        }
        if (!preg_match('/^[0-9]+$/D', $telpInstitusi) || !preg_match('/^[0-9]+$/D', $hpPj)) {
            return redirect()->back()->withInput()->with('error', 'Nomor telepon dan nomor HP/WhatsApp hanya boleh berisi angka.');
        }

        $emailOwner = $userModel->where('email', $emailInstitusi)->first();
        if ($emailOwner && (int) $emailOwner['id'] !== (int) $userId) {
            return redirect()->back()->withInput()->with('error', 'Email institusi sudah digunakan akun lain.');
        }

        $dataUpdate = [
            'nama_institusi' => $namaInstitusi,
            'jenis_institusi' => $this->request->getPost('jenis'),
            'alamat' => trim((string) $this->request->getPost('alamat')),
            'no_telp' => $telpInstitusi,
            'nama_kontak' => $namaPj,
            'jabatan_pj' => trim((string) $this->request->getPost('jabatan_pj')),
            'hp_pj' => $hpPj,
            'email_pj' => $emailPj,
        ];

        $uploadPath = WRITEPATH . 'uploads/dokumen_institusi';
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
            return redirect()->back()->withInput()->with('error', 'Folder dokumen tidak dapat disiapkan.');
        }

        foreach (['file_mou', 'file_permohonan', 'file_lainnya'] as $field) {
            $file = $this->request->getFile($field);
            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if (!$file->isValid() || $file->hasMoved() || $file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', 'Dokumen yang diunggah harus berupa file PDF yang valid.');
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $dataUpdate[$field] = $newName;

            $oldName = $institusi[$field] ?? null;
            foreach ([WRITEPATH . 'uploads/dokumen_institusi/' . $oldName, FCPATH . 'uploads/institusi/' . $oldName] as $oldPath) {
                if ($oldName && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $institusiModel->update($institusiId, $dataUpdate);
        $userModel->update($userId, ['email' => $emailInstitusi]);

        if ($institusi['status_verifikasi'] === 'revision') {
            $institusiModel->update($institusiId, ['revisi_dikirim_at' => date('Y-m-d H:i:s')]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Data gagal diperbarui. Silakan coba lagi.');
        }

        session()->set('name', $namaInstitusi);
        session()->set('account_status', $institusi['status_verifikasi'] === 'revision' ? 'revision' : session()->get('account_status'));
        session()->setFlashdata('success', $institusi['status_verifikasi'] === 'revision'
            ? 'Revisi telah dilakukan. Silakan menunggu persetujuan admin.'
            : 'Data registrasi institusi berhasil diperbarui.');
        return redirect()->to('/pendidikan/institusi/dashboard');
    }
}
