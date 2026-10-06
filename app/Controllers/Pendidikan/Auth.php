<?php

namespace App\Controllers\Pendidikan;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        session()->destroy();
        return view('Pendidikan/auth/login');
    }

    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new \App\Models\UserPendidikanModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify((string)$password, $user['password'])) {
            if ($user['is_active'] != 1) {
                return redirect()->back()->with('error', 'Akun Anda tidak aktif!');
            }

            $sessionData = [
                'isLoggedIn' => true,
                'user_id'    => $user['id'],
                'role_id'    => $user['role_id'],
            ];

            switch ($user['role_id']) {
                case 1:
                    $sessionData['role'] = 'diklat';
                    $sessionData['name'] = 'Admin Diklat';
                    session()->set($sessionData);
                    log_message('error', 'Login Success Diklat. Session ID: ' . session_id() . ' | Data set: ' . print_r($sessionData, true));
                    return redirect()->to(base_url('pendidikan/admin/diklat/dashboard'));
                case 2:
                    $institusiModel = new \App\Models\InstitusiPendidikanModel();
                    $institusi = $institusiModel->where('user_id', $user['id'])->first();
                    
                    if ($institusi && !empty($institusi['tgl_selesai_mou'])) {
                        $expiryDate = strtotime($institusi['tgl_selesai_mou']);
                        if (time() > $expiryDate) {
                            return redirect()->back()->with('error', 'Masa aktif MoU Institusi Anda telah habis. Akun Anda dinonaktifkan sementara.');
                        }
                    }

                    $sessionData['role'] = 'institusi';
                    $sessionData['name'] = $institusi ? $institusi['nama_institusi'] : 'Institusi';
                    $sessionData['account_status'] = $institusi ? $institusi['status_verifikasi'] : 'pending';
                    $sessionData['institusi_id'] = $institusi ? $institusi['id'] : null;
                    
                    session()->set($sessionData);
                    return redirect()->to('/pendidikan/institusi/dashboard');
                case 3:
                    $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
                    $mahasiswa = $mahasiswaModel->where('user_id', $user['id'])->first();
                    
                    if (!$mahasiswa) {
                        return redirect()->back()->with('error', 'Data Mahasiswa tidak ditemukan.');
                    }

                    if (!in_array($mahasiswa['status'], ['Disetujui', 'Lulus'])) {
                        return redirect()->back()->with('error', 'Akun Anda belum aktif. Silakan tunggu persetujuan pengajuan dari Admin.');
                    }

                    $sessionData['role'] = 'mahasiswa';
                    $sessionData['name'] = $mahasiswa['nama_lengkap'];
                    $sessionData['mahasiswa_id'] = $mahasiswa['id'];
                    session()->set($sessionData);
                    return redirect()->to('/pendidikan/mahasiswa/dashboard');
                case 4:
                    $ciModel = new \App\Models\CiPendidikanModel();
                    $ci = $ciModel->where('user_id', $user['id'])->first();
                    $sessionData['role'] = 'ci';
                    $sessionData['name'] = $ci ? $ci['nama_lengkap'] : 'CI';
                    $sessionData['ci_id'] = $ci ? $ci['id'] : null;
                    session()->set($sessionData);
                    return redirect()->to('/pendidikan/ci/dashboard');
                case 5:
                    $sessionData['role'] = 'superadmin';
                    $sessionData['name'] = 'Super Admin';
                    session()->set($sessionData);
                    return redirect()->to('/superadmin/dashboard');
                default:
                    return redirect()->back()->with('error', 'Role tidak dikenali!');
            }
        }

        return redirect()->back()->with('error', 'Username atau Password salah!');
    }

    public function register()
    {
        return view('Pendidikan/auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/pendidikan/login');
    }

    public function forgotPassword()
    {
        return view('Pendidikan/auth/forgot_password');
    }

    public function processForgotPassword()
    {
        $emailInput = $this->request->getPost('email');
        if (empty($emailInput)) {
            return redirect()->back()->with('error', 'Email tidak boleh kosong!');
        }

        $userModel = new \App\Models\UserPendidikanModel();
        $user = $userModel->where('email', $emailInput)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar!');
        }

        try {
            // Generate random password
            $passwordBaru = bin2hex(random_bytes(4)); // 8 characters
            $hashPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

            // Update database
            $userModel->update($user['id'], ['password' => $hashPassword]);

            // Kirim Email
            $email = \Config\Services::email();
            $email->setFrom('ruskia335@gmail.com', 'Super Admin SIM Diklat');
            $email->setTo($emailInput);
            $email->setSubject('Reset Password Pendidikan SIM Diklat');
            
            $pesan = "Halo Pengguna Pendidikan,<br><br>";
            $pesan .= "Password Anda telah direset.<br>";
            $pesan .= "Berikut adalah password baru Anda: <b>{$passwordBaru}</b><br><br>";
            $pesan .= "Silakan login menggunakan password tersebut dan segera ganti password Anda demi keamanan.<br><br>";
            $pesan .= "Terima kasih.";

            $email->setMessage($pesan);

            if ($email->send()) {
                return redirect()->to('/pendidikan/login')->with('success', "Password baru berhasil dikirim ke {$emailInput}!");
            } else {
                $errorMsg = $email->printDebugger(['headers']);
                log_message('error', 'Gagal mengirim email reset password pendidikan: ' . $errorMsg);
                return redirect()->back()->with('error', 'Gagal mengirim email. Pastikan koneksi internet server stabil.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }

    public function processRegister()
    {
        $password = (string) $this->request->getPost('password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');
        $email = trim((string) $this->request->getPost('email_institusi'));
        $emailPj = trim((string) $this->request->getPost('email_pj'));
        $telpInstitusi = trim((string) $this->request->getPost('telp_institusi'));
        $hpPj = trim((string) $this->request->getPost('hp_pj'));
        $namaPj = trim((string) $this->request->getPost('nama_pj'));
        $tglMulaiMou = $this->request->getPost('tgl_mulai_mou') ?: null;
        $tglSelesaiMou = $this->request->getPost('tgl_selesai_mou') ?: null;
        
        if ($password !== $confirmPassword) {
            return redirect()->back()->withInput()->with('error', 'Konfirmasi password tidak cocok!');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !filter_var($emailPj, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Email institusi dan email penanggung jawab harus menggunakan format email yang valid.');
        }

        if (!preg_match('/^[0-9]+$/D', $telpInstitusi) || !preg_match('/^[0-9]+$/D', $hpPj)) {
            return redirect()->back()->withInput()->with('error', 'Nomor telepon dan nomor HP/WhatsApp hanya boleh berisi angka.');
        }

        if (!preg_match("/^[\\p{L}][\\p{L}\\s.,'-]*$/u", $namaPj)) {
            return redirect()->back()->withInput()->with('error', 'Nama penanggung jawab hanya boleh berisi huruf, spasi, titik, koma, apostrof, atau tanda hubung.');
        }

        if (!$tglMulaiMou || !$tglSelesaiMou) {
            return redirect()->back()->withInput()->with('error', 'Tanggal mulai dan tanggal selesai MoU wajib diisi.');
        }

        if ($tglSelesaiMou < $tglMulaiMou) {
            return redirect()->back()->withInput()->with('error', 'Tanggal selesai MoU tidak boleh lebih awal dari tanggal mulai MoU.');
        }
        
        // Pengecekan email apakah sudah ada
        $userModel = new \App\Models\UsersPendidikanModel();
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('error', 'Email institusi sudah terdaftar!');
        }

        // Handle File Uploads
        $fileMou = $this->request->getFile('file_mou');
        $filePermohonan = $this->request->getFile('file_permohonan');

        if (!$fileMou || $fileMou->getError() === UPLOAD_ERR_NO_FILE) {
            return redirect()->back()->withInput()->with('error', 'Dokumen MoU / PKS wajib diunggah.');
        }

        if (!$filePermohonan || $filePermohonan->getError() === UPLOAD_ERR_NO_FILE) {
            return redirect()->back()->withInput()->with('error', 'Surat Permohonan Kerja Sama wajib diunggah.');
        }

        foreach ([
            'Dokumen MoU / PKS' => $fileMou,
            'Surat Permohonan Kerja Sama' => $filePermohonan,
        ] as $label => $file) {
            if (!$file->isValid() || $file->hasMoved()) {
                return redirect()->back()->withInput()->with('error', $label . ' tidak dapat diunggah.');
            }

            if ($file->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', $label . ' harus berupa file PDF.');
            }
        }

        $fileLainnya = $this->request->getFile('file_lainnya');
        if ($fileLainnya && $fileLainnya->getError() !== UPLOAD_ERR_NO_FILE) {
            if (!$fileLainnya->isValid() || $fileLainnya->hasMoved()) {
                return redirect()->back()->withInput()->with('error', 'Dokumen pendukung lainnya tidak dapat diunggah.');
            }

            if ($fileLainnya->getMimeType() !== 'application/pdf') {
                return redirect()->back()->withInput()->with('error', 'Dokumen pendukung lainnya harus berupa file PDF.');
            }
        }
        
        $uploadPath = WRITEPATH . 'uploads/dokumen_institusi';
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
            return redirect()->back()->withInput()->with('error', 'Folder dokumen tidak dapat disiapkan.');
        }

        $mouName = null;
        if ($fileMou && $fileMou->isValid() && !$fileMou->hasMoved()) {
            $mouName = $fileMou->getRandomName();
            $fileMou->move($uploadPath, $mouName);
        }

        $permohonanName = null;
        if ($filePermohonan && $filePermohonan->isValid() && !$filePermohonan->hasMoved()) {
            $permohonanName = $filePermohonan->getRandomName();
            $filePermohonan->move($uploadPath, $permohonanName);
        }

        $lainnyaName = null;
        if ($fileLainnya && $fileLainnya->isValid() && !$fileLainnya->hasMoved()) {
            $lainnyaName = $fileLainnya->getRandomName();
            $fileLainnya->move($uploadPath, $lainnyaName);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Create User (Role ID 2 = Institusi)
        $userData = [
            'role_id'   => 2, 
            'email'     => $email, // Dari email institusi (login credentials)
            'password'  => password_hash((string)$password, PASSWORD_DEFAULT),
            'is_active' => 1 // Atur 1 agar bisa login, tapi status masih pending
        ];
        $userModel->insert($userData);
        $userId = $userModel->insertID();

        // 2. Create Institusi Profile
        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $institusiData = [
            'user_id'           => $userId,
            'nama_institusi'    => $this->request->getPost('nama_institusi'),
            'jenis_institusi'   => $this->request->getPost('jenis_institusi'),
            'alamat'            => $this->request->getPost('alamat_institusi'),
            'no_telp'           => $telpInstitusi,
            'nama_kontak'       => $namaPj,
            'jabatan_pj'        => $this->request->getPost('jabatan_pj'),
            'hp_pj'             => $hpPj,
            'email_pj'          => $emailPj,
            'file_mou'          => $mouName,
            'file_permohonan'   => $permohonanName,
            'tgl_mulai_mou'     => $tglMulaiMou,
            'tgl_selesai_mou'   => $tglSelesaiMou,
            'file_lainnya'      => $lainnyaName,
            'status_verifikasi' => 'pending'
        ];
        $institusiModel->insert($institusiData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.');
        }

        return redirect()->to('/pendidikan/login')->with('success', 'Registrasi berhasil! Silakan login untuk melihat status verifikasi.');
    }
}
