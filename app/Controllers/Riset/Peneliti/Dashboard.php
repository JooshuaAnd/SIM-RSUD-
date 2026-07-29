<?php

namespace App\Controllers\Riset\Peneliti;

use App\Controllers\BaseController;
use App\Models\UserRisetModel;
use App\Models\PengajuanRisetModel;
use App\Models\PublikasiRisetModel;

class Dashboard extends BaseController
{
    protected UserRisetModel $userModel;
    protected PengajuanRisetModel $pengajuanModel;
    protected PublikasiRisetModel $publikasiModel;

    public function __construct()
    {
        $this->userModel = new UserRisetModel();
        $this->pengajuanModel = new PengajuanRisetModel();
        $this->publikasiModel = new PublikasiRisetModel();
    }

    public function index()
    {
        $userId = session()->get('riset_user_id');

        // Statistik
        $totalPengajuan = $this->pengajuanModel->where('user_riset_id', $userId)->countAllResults() 
                        + $this->publikasiModel->where('user_riset_id', $userId)->countAllResults();
        
        $dalamProses = $this->pengajuanModel->where('user_riset_id', $userId)->whereIn('status', ['dalam review', 'menunggu_verifikasi', 'menunggu_pembayaran', 'direvisi'])->countAllResults()
                     + $this->publikasiModel->where('user_riset_id', $userId)->whereIn('status', ['dalam review', 'menunggu_verifikasi', 'menunggu_pembayaran', 'direvisi'])->countAllResults();
                     
        $selesai = $this->pengajuanModel->where('user_riset_id', $userId)->where('status', 'selesai')->countAllResults()
                 + $this->publikasiModel->where('user_riset_id', $userId)->where('status', 'selesai')->countAllResults();

        // Riwayat Terbaru
        $riwayatPengajuan = $this->pengajuanModel->where('user_riset_id', $userId)->findAll();
        $riwayatPublikasi = $this->publikasiModel->where('user_riset_id', $userId)->findAll();
        
        // Tambahkan identifier jenis untuk publikasi agar tidak error di view
        foreach($riwayatPublikasi as &$pub) {
            $pub['jenis_pengajuan'] = 'publikasi';
        }
        
        // Gabungkan dan urutkan berdasarkan waktu terbaru
        $riwayat = array_merge($riwayatPengajuan, $riwayatPublikasi);
        usort($riwayat, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Ambil 5 data terbaru
        $riwayat = array_slice($riwayat, 0, 5);

        // Arsip Dokumen (Hanya yang berstatus selesai)
        $arsipIzin = $this->pengajuanModel->where('user_riset_id', $userId)->where('status', 'selesai')->where('jenis_pengajuan', 'penelitian')->findAll(3);
        $arsipPublikasi = $this->publikasiModel->where('user_riset_id', $userId)->where('status', 'selesai')->where('no_surat_izin IS NOT NULL')->findAll(3);

        return view('Riset/peneliti/dashboard/index', [
            'title'       => 'Dashboard Peneliti',
            'active_menu' => 'dashboard',
            'stats'       => [
                'total_pengajuan' => $totalPengajuan,
                'dalam_proses'    => $dalamProses,
                'selesai'         => $selesai,
            ],
            'riwayat'        => $riwayat,
            'arsip_izin'     => $arsipIzin,
            'arsip_publikasi'=> $arsipPublikasi,
        ]);
    }

    public function profil()
    {
        $userId = session()->get('riset_user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('riset/login'))->with('error', 'Sesi tidak valid, silakan login ulang.');
        }

        return view('Riset/peneliti/profil/profil', [
            'title'       => 'Profil Peneliti',
            'active_menu' => 'profil',
            'user'        => $user
        ]);
    }

    public function profil_edit()
    {
        $userId = session()->get('riset_user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('riset/login'))->with('error', 'Sesi tidak valid, silakan login ulang.');
        }

        return view('Riset/peneliti/profil/profil_edit', [
            'title'       => 'Edit Profil Peneliti',
            'active_menu' => 'profil',
            'user'        => $user
        ]);
    }

    public function profil_update()
    {
        $userId = session()->get('riset_user_id');

        $data = [
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'institusi' => $this->request->getPost('institusi'),
            'no_telp'   => $this->request->getPost('no_telp'),
            'identitas'       => $this->request->getPost('identitas'),
            'prodi'     => $this->request->getPost('prodi'),
            'alamat'    => $this->request->getPost('alamat'),
        ];

        // Jika user mengisi password baru
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Handle foto_profil upload
        $foto = $this->request->getFile('foto_profil');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $newName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/riset/profil', $newName);
            
            // Hapus foto lama
            $oldUser = $this->userModel->find($userId);
            if (!empty($oldUser['foto_profil']) && file_exists(FCPATH . 'uploads/riset/profil/' . $oldUser['foto_profil'])) {
                unlink(FCPATH . 'uploads/riset/profil/' . $oldUser['foto_profil']);
            }
            
            $data['foto_profil'] = $newName;
        }

        $this->userModel->update($userId, $data);

        // Fetch fresh data after update to ensure session is perfectly synced
        $freshUser = $this->userModel->find($userId);
        
        session()->set([
            'riset_user_nama'  => $freshUser['nama'],
            'riset_user_email' => $freshUser['email'],
            'riset_user_foto'  => $freshUser['foto_profil'],
        ]);

        return redirect()->to(base_url('riset/peneliti/profil'))->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function update_password()
    {
        $userId = session()->get('riset_user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('riset/login'))->with('error', 'Sesi tidak valid, silakan login ulang.');
        }

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!password_verify($oldPassword, $user['password'])) {
            return redirect()->to(base_url('riset/peneliti/profil'))->with('error', 'Gagal mengganti password: Password saat ini yang Anda masukkan salah.');
        }

        if (strlen($newPassword) < 8) {
            return redirect()->to(base_url('riset/peneliti/profil'))->with('error', 'Gagal mengganti password: Password baru harus minimal 8 karakter.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->to(base_url('riset/peneliti/profil'))->with('error', 'Gagal mengganti password: Konfirmasi password tidak cocok dengan password baru.');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->to(base_url('riset/peneliti/profil'))->with('success', 'Password Anda berhasil diperbarui.');
    }
}
