<?php

namespace App\Controllers\Pelatihan;

use App\Controllers\BaseController;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\UnitKerjaPelatihanModel;
use App\Models\Pelatihan\ProfesiPelatihanModel;

class Auth extends BaseController
{
    public function index()
    {
        session()->remove(['logged_in', 'role', 'user_id', 'nik', 'email']);
        session()->destroy();
        return view('pelatihan/auth/login');
    }

    public function login()
    {
        $nik = $this->request->getPost('nik');
        $password = $this->request->getPost('password');
        
        $rules = [
            'nik'      => 'required|numeric|exact_length[16]',
            'password' => 'required',
        ];

        $customErrors = [
            'nik' => [
                'numeric'      => 'NIK harus berupa angka murni.',
                'exact_length' => 'NIK harus tepat 16 digit.'
            ]
        ];

        if (!$this->validate($rules, $customErrors)) {
            $errors = $this->validator->getErrors();
            $errorString = implode('<br>', $errors);
            return redirect()->back()->withInput()->with('error', $errorString);
        }

        $userModel = new UserPelatihanModel();
        
        // Find user by NIK only
        $user = $userModel->where('nik', $nik)->first();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'NIK atau Password salah!');
        }

        if (($user['status'] ?? 'aktif') !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
        }

        // Get role directly from database
        $dbRole = $user['role'];

        // Set session data
        $this->session->set([
            'user_id'       => $user['nik'],
            'nama'          => $user['nama_lengkap'],
            'email'         => $user['email'],
            'role'          => $user['role'],
            'jenis_peserta' => $user['jenis_peserta'],
            'logged_in'     => true
        ]);

        // Redirect to respective dashboard based on database role
        if ($dbRole === 'admin') {
            return redirect()->to('/pelatihan/admin/dashboard')->with('success', 'Selamat datang, ' . $user['nama_lengkap']);
        } elseif ($dbRole === 'admin_pengabdian') {
            return redirect()->to('/pelatihan/admin_pengabdian/sertifikat')->with('success', 'Selamat datang, ' . $user['nama_lengkap']);
        } else {
            return redirect()->to('/pelatihan/peserta/beranda')->with('success', 'Selamat datang, ' . $user['nama_lengkap']);
        }
    }

    public function register()
    {
        $unitKerjaModel = new UnitKerjaPelatihanModel();
        $profesiModel = new ProfesiPelatihanModel();

        $data = [
            'unit_kerja' => $unitKerjaModel->findAll(),
            'profesi'    => $profesiModel->findAll(),
        ];

        return view('pelatihan/auth/register', $data);
    }

    public function processRegister()
    {
        $rules = [
            'nik'      => 'required|numeric|exact_length[16]|is_unique[users_pelatihan.nik]',
            'nama'     => 'required|min_length[3]|max_length[150]|regex_match[/^[a-zA-Z\s.,\']+$/]',
            'email'    => 'required|valid_email|regex_match[/^[a-zA-Z0-9._%+-]+@(gmail\.com|students\.ukcw\.ac\.id|[a-zA-Z0-9.-]+\.go\.id)$/i]|is_unique[users_pelatihan.email]',
            'phone'    => 'required|numeric|min_length[10]|max_length[15]',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]+$/]',
            'role'     => 'required|in_list[named,nonnamed]',
        ];

        $customErrors = [
            'nik' => [
                'numeric'      => 'NIK harus berupa angka murni.',
                'exact_length' => 'NIK harus tepat 16 digit.',
                'is_unique'    => 'NIK sudah terdaftar.'
            ],
            'nama' => [
                'regex_match'  => 'Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip.'
            ],
            'email' => [
                'valid_email'  => 'Format email harus valid.',
                'regex_match'  => 'Email harus menggunakan domain @gmail.com, @students.ukcw.ac.id, atau domain instansi pemerintah (.go.id).',
                'is_unique'    => 'Email sudah terdaftar.'
            ],
            'phone' => [
                'numeric'      => 'No. WhatsApp harus berupa angka murni.',
                'min_length'   => 'No. WhatsApp minimal 10 digit.',
                'max_length'   => 'No. WhatsApp maksimal 15 digit.'
            ],
            'password' => [
                'regex_match'  => 'Password harus berupa kombinasi huruf dan angka saja (tanpa simbol).'
            ]
        ];

        if (!$this->validate($rules, $customErrors)) {
            $errors = $this->validator->getErrors();
            // Merge errors into a single string for easier flash display
            $errorString = implode('<br>', $errors);
            return redirect()->back()->withInput()->with('error', $errorString);
        }

        $jenisPesertaForm = $this->request->getPost('role'); // named or nonnamed
        $jenisPeserta = ($jenisPesertaForm === 'nonnamed') ? 'non_named' : 'named';

        $idUnitKerja = $this->request->getPost('id_unit_kerja');
        $idProfesi = $this->request->getPost('id_profesi');

        $userData = [
            'nik'           => $this->request->getPost('nik'),
            'nama_lengkap'  => $this->request->getPost('nama'),
            'email'         => $this->request->getPost('email'),
            'no_wa'         => $this->request->getPost('phone'),
            'jenis_peserta' => $jenisPeserta,
            'role'          => 'peserta', // Default role for registration is always peserta
            'id_unit_kerja' => $idUnitKerja ?: null,
            'id_profesi'    => $idProfesi ?: null,
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ];

        $userModel = new UserPelatihanModel();
        if ($userModel->insert($userData)) {
            return redirect()->to('/pelatihan/login')->with('success', 'Pendaftaran Berhasil! Silakan login.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.');
        }
    }

    public function logout()
    {
        $this->session->remove(['logged_in', 'role', 'user_id', 'nik', 'email']);
        $this->session->destroy();
        return redirect()->to('/pelatihan/login');
    }

    public function forgotPassword()
    {
        return view('pelatihan/auth/forgot_password', [
            'title' => 'Lupa Password | Modul Pelatihan'
        ]);
    }

    public function forgotPasswordSubmit()
    {
        $email = $this->request->getPost('email');
        $userModel = new UserPelatihanModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan di sistem kami.')->withInput();
        }

        // Generate random password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $newPassword = substr(str_shuffle($characters), 0, 10); // 10 chars random password

        $userModel->update($user['nik'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        // Send Email
        $emailService = \Config\Services::email();
        $emailService->setTo($user['email']);
        $emailService->setFrom('noreply@rsudjogja.id', 'RSUD Yogyakarta');
        $emailService->setSubject('Password Baru Modul Pelatihan SIM DIKLAT RSUD Yogyakarta');
        
        $message = "
        <html>
        <head>
            <title>Password Baru Anda</title>
        </head>
        <body>
            <p>Halo " . esc($user['nama_lengkap']) . ",</p>
            <p>Sesuai dengan permintaan Anda, sistem telah mereset password akun Modul Pelatihan SIM DIKLAT RSUD Yogyakarta Anda.</p>
            <p>Berikut adalah password baru Anda untuk login:</p>
            <h3 style='background-color:#f1f1f1; padding: 10px; display:inline-block; border-radius: 5px; letter-spacing: 2px;'>" . $newPassword . "</h3>
            <p>Silakan login menggunakan NIK dan password di atas. Kami sangat menyarankan agar Anda segera mengubah password ini melalui menu pengaturan profil di sistem setelah berhasil login.</p>
            <p>Terima kasih,<br>Tim SIM DIKLAT RSUD Yogyakarta</p>
        </body>
        </html>
        ";
        
        $emailService->setMessage($message);
        $emailService->setMailType('html');

        if ($emailService->send()) {
            return redirect()->to(base_url('pelatihan/login'))->with('success', 'Password baru telah dikirim ke email Anda. Silakan periksa kotak masuk atau folder spam untuk login.');
        } else {
            // Log error or display to user for debug (in production, just generic error)
            $err = $emailService->printDebugger(['headers']);
            log_message('error', 'Email reset password gagal dikirim: ' . $err);
            return redirect()->back()->with('error', 'Gagal mengirim email berisi password baru. Pastikan konfigurasi SMTP di server telah diatur.');
        }
    }
}

