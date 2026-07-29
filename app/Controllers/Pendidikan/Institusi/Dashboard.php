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
                'jenis' => '-', // Tidak ada di DB
                'alamat' => $profileData['alamat'],
                'email' => $userData['email'] ?? '-',
                'telp' => $profileData['no_telp'],
                'pj' => $profileData['nama_kontak'],
                'jabatan' => '-', // Tidak ada di DB
                'hp_pj' => '-', // Tidak ada di DB
                'email_pj' => '-' // Tidak ada di DB
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
        // Simulation of updating profile
        session()->setFlashdata('success', 'Data profil institusi berhasil diperbarui. Silakan tunggu verifikasi ulang.');
        return redirect()->to('/pendidikan/institusi/dashboard');
    }
}
