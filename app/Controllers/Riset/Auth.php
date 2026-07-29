<?php

namespace App\Controllers\Riset;

use App\Controllers\BaseController;
use App\Models\UserRisetModel;

class Auth extends BaseController
{
    protected UserRisetModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserRisetModel();
    }

    public function login()
    {
        // Logout user if they navigate back to login page
        session()->destroy();
        
        return view('Riset/auth/login', [
            'title' => 'Login | Modul Riset'
        ]);
    }

    public function authenticate()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Cari user berdasarkan email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Alamat email tidak terdaftar. Silakan periksa kembali atau daftar akun baru.')->withInput();
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Kata sandi yang Anda masukkan salah.')->withInput();
        }

        // Set session
        session()->set([
            'riset_user_id'   => $user['id'],
            'riset_user_nama' => $user['nama'],
            'riset_user_email' => $user['email'],
            'riset_user_foto'  => $user['foto_profil'] ?? null,
            'riset_role'      => $user['role'],
            'riset_logged_in' => true,
        ]);

        // Redirect sesuai role
        if ($user['role'] == 'admin') {
            return redirect()->to(base_url('riset/admin/dashboard'));
        }

        return redirect()->to(base_url('riset/peneliti/dashboard'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('riset/login'));
    }

    public function register()
    {
        return view('Riset/auth/register', [
            'title' => 'Daftar Akun Peneliti'
        ]);
    }

    public function register_submit()
    {
        $rules = [
            'nama'     => [
                'rules'  => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama lengkap minimal 3 karakter.'
                ]
            ],
            'email'    => [
                'rules'  => 'required|valid_email|is_unique[users_riset.email]',
                'errors' => [
                    'required'    => 'Alamat email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.'
                ]
            ],
            'password' => [
                'rules'  => 'required|min_length[8]',
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'min_length' => 'Password minimal harus 8 karakter.'
                ]
            ],
            'institusi' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Institusi atau Universitas wajib diisi.'
                ]
            ],
            'identitas' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'NIM/No. Identitas wajib diisi.'
                ]
            ],
            'prodi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Program Studi wajib diisi.'
                ]
            ],
            'no_telp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor WhatsApp wajib diisi.'
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat lengkap wajib diisi.'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', implode('<br>', $this->validator->getErrors()))->withInput();
        }

        $this->userModel->insert([
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'      => 'peneliti',
            'institusi' => $this->request->getPost('institusi'),
            'identitas'       => $this->request->getPost('identitas'),
            'prodi'     => $this->request->getPost('prodi'),
            'no_telp'   => $this->request->getPost('no_telp'),
            'alamat'    => $this->request->getPost('alamat'),
        ]);

        return redirect()->to(base_url('riset/login'))->with('success', 'Pendaftaran berhasil! Silakan login menggunakan akun Anda.');
    }

    public function forgotPassword()
    {
        return view('Riset/auth/forgot_password', [
            'title' => 'Lupa Password | Modul Riset'
        ]);
    }

    public function forgotPasswordSubmit()
    {
        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan di sistem kami.')->withInput();
        }

        // Generate random password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $newPassword = substr(str_shuffle($characters), 0, 10); // 10 chars random password

        $this->userModel->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_expires_at' => null
        ]);

        // Send Email
        $emailService = \Config\Services::email();
        $emailService->setTo($user['email']);
        $emailService->setFrom('ruskia335@gmail.com', 'RSUD Yogyakarta');
        $emailService->setSubject('Password Baru Modul Riset SIM DIKLAT RSUD Yogyakarta');
        
        $message = "
        <html>
        <head>
            <title>Password Baru Anda</title>
        </head>
        <body>
            <p>Halo " . esc($user['nama']) . ",</p>
            <p>Sesuai dengan permintaan Anda, sistem telah mereset password akun Modul Riset SIM DIKLAT RSUD Yogyakarta Anda.</p>
            <p>Berikut adalah password baru Anda untuk login:</p>
            <h3 style='background-color:#f1f1f1; padding: 10px; display:inline-block; border-radius: 5px; letter-spacing: 2px;'>" . $newPassword . "</h3>
            <p>Silakan login menggunakan email dan password di atas. Kami sangat menyarankan agar Anda segera mengubah password ini melalui menu pengaturan akun di sistem setelah berhasil login.</p>
            <p>Terima kasih,<br>Tim SIM DIKLAT RSUD Yogyakarta</p>
        </body>
        </html>
        ";
        
        $emailService->setMessage($message);
        $emailService->setMailType('html');

        if ($emailService->send()) {
            return redirect()->to(base_url('riset/login'))->with('success', 'Password baru telah dikirim ke email Anda. Silakan periksa kotak masuk atau folder spam untuk login.');
        } else {
            // Log error or display to user for debug (in production, just generic error)
            $err = $emailService->printDebugger(['headers']);
            log_message('error', 'Email reset password gagal dikirim: ' . $err);
            return redirect()->back()->with('error', 'Gagal mengirim email berisi password baru. Pastikan konfigurasi SMTP di server telah diatur.');
        }
    }
}
