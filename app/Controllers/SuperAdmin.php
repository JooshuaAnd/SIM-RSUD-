<?php

namespace App\Controllers;

class SuperAdmin extends BaseController
{
    public function dashboard()
    {
        $db = \Config\Database::connect();
        
        // Ambil data admin dari ketiga modul
        $adminRiset = $db->table('users_riset')->where('role', 'admin')->get()->getResultArray();
        $adminPelatihan = $db->table('users_pelatihan')->whereIn('role', ['admin', 'admin_pengabdian'])->get()->getResultArray();
        $adminPendidikan = $db->table('users_pendidikan')->where('role_id', 1)->get()->getResultArray();

        return view('SuperAdmin/dashboard', [
            'adminRiset' => $adminRiset,
            'adminPelatihan' => $adminPelatihan,
            'adminPendidikan' => $adminPendidikan
        ]);
    }

    public function update_password()
    {
        $userId = session()->get('user_id');
        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password baru tidak cocok.');
        }

        if (strlen((string) $newPassword) < 6) {
            return redirect()->back()->with('error', 'Password baru minimal 6 karakter.');
        }

        $userModel = new \App\Models\UserPendidikanModel();
        $user = $userModel->find($userId);

        if (!$user || !password_verify((string) $oldPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        $userModel->update($userId, [
            'password' => password_hash((string) $newPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->back()->with('success', 'Password Super Admin berhasil diubah.');
    }

    public function create_admin()
    {
        $tipeAdmin = trim((string) $this->request->getPost('tipe_admin'));
        $nik = trim((string) $this->request->getPost('nik'));
        $namaLengkap = trim((string) $this->request->getPost('nama_lengkap'));
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (in_array($tipeAdmin, ['pelatihan', 'admin_pengabdian'], true)
            && !preg_match('/^\d{16}$/', $nik)) {
            return redirect()->back()->with(
                'error',
                'NIK harus tepat 16 digit dan hanya boleh berisi angka.'
            );
        }

        if (in_array($tipeAdmin, ['riset', 'pelatihan', 'admin_pengabdian'], true)
            && !preg_match('/^\p{L}+(?:[ \p{L}]+)*$/u', $namaLengkap)) {
            return redirect()->back()->with(
                'error',
                'Nama lengkap hanya boleh berisi huruf dan spasi.'
            );
        }

        $db = \Config\Database::connect();

        try {
            if ($tipeAdmin == 'riset') {
                $builder = $db->table('users_riset');
                if ($builder->where('email', $email)->countAllResults() > 0) {
                    return redirect()->back()->with('error', 'Email sudah terdaftar di modul Riset!');
                }
                $builder->insert([
                    'nama' => $namaLengkap,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'admin',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            } elseif ($tipeAdmin == 'pelatihan' || $tipeAdmin == 'admin_pengabdian') {
                $roleToInsert = ($tipeAdmin == 'admin_pengabdian') ? 'admin_pengabdian' : 'admin';
                $builder = $db->table('users_pelatihan');
                if ($builder->where('email', $email)->countAllResults() > 0) {
                    return redirect()->back()->with('error', 'Email sudah terdaftar di modul Pelatihan!');
                }
                if ($builder->where('nik', $nik)->countAllResults() > 0) {
                    return redirect()->back()->with('error', 'NIK sudah terdaftar di modul Pelatihan!');
                }
                $builder->insert([
                    'nik' => $nik,
                    'nama_lengkap' => $namaLengkap,
                    'email' => $email,
                    'no_wa' => '-', 
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $roleToInsert,
                    'status' => 'aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            } elseif ($tipeAdmin == 'pendidikan') {
                $builder = $db->table('users_pendidikan');
                if ($builder->where('email', $email)->countAllResults() > 0) {
                    return redirect()->back()->with('error', 'Email sudah terdaftar di modul Pendidikan!');
                }
                $builder->insert([
                    'role_id' => 1,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                return redirect()->back()->with('error', 'Tipe admin tidak dikenali!');
            }

            return redirect()->back()->with('success', 'Berhasil membuat Admin ' . ucfirst($tipeAdmin));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat admin: ' . $e->getMessage());
        }
    }



    public function reset_password_email()
    {
        $id = $this->request->getPost('id');
        $modul = $this->request->getPost('modul');

        if (empty($id) || empty($modul)) {
            return redirect()->back()->with('error', 'Data tidak lengkap!');
        }

        $db = \Config\Database::connect();
        $emailTujuan = '';
        $namaTujuan = '';

        try {
            if ($modul == 'riset') {
                $user = $db->table('users_riset')->where('id', $id)->get()->getRowArray();
                if($user) { $emailTujuan = $user['email']; $namaTujuan = $user['nama']; }
            } elseif ($modul == 'pelatihan') {
                $user = $db->table('users_pelatihan')->where('nik', $id)->get()->getRowArray();
                if($user) { $emailTujuan = $user['email']; $namaTujuan = $user['nama_lengkap']; }
            } elseif ($modul == 'pendidikan') {
                $user = $db->table('users_pendidikan')->where('id', $id)->get()->getRowArray();
                if($user) { $emailTujuan = $user['email']; $namaTujuan = 'Admin Pendidikan'; }
            } else {
                return redirect()->back()->with('error', 'Modul tidak dikenali!');
            }

            if(empty($emailTujuan)) {
                return redirect()->back()->with('error', 'Data admin tidak ditemukan atau admin tidak memiliki email!');
            }

            // Generate random password
            $passwordBaru = bin2hex(random_bytes(4)); // 8 characters

            $hashPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

            // Update database
            if ($modul == 'riset') {
                $db->table('users_riset')->where('id', $id)->update(['password' => $hashPassword]);
            } elseif ($modul == 'pelatihan') {
                $db->table('users_pelatihan')->where('nik', $id)->update(['password' => $hashPassword]);
            } elseif ($modul == 'pendidikan') {
                $db->table('users_pendidikan')->where('id', $id)->update(['password' => $hashPassword]);
            }

            // Kirim Email
            $email = \Config\Services::email();
            
            // Bypass seluruh .env cache dengan initialize manual
            $config = [
                'protocol'   => 'smtp',
                'SMTPHost'   => 'smtp.gmail.com',
                'SMTPUser'   => 'ruskia335@gmail.com',
                'SMTPPass'   => 'dncolbjpkdjgennh',
                'SMTPPort'   => 465,
                'SMTPCrypto' => 'ssl',
                'mailType'   => 'html',
                'CRLF'       => "\r\n",
                'newline'    => "\r\n"
            ];
            $email->initialize($config);

            $email->setFrom('ruskia335@gmail.com', 'Super Admin SIM Diklat');
            $email->setTo($emailTujuan);
            $email->setSubject('Reset Password Admin SIM Diklat');
            
            $pesan = "Halo {$namaTujuan},<br><br>";
            $pesan .= "Password Anda telah direset oleh Super Admin.<br>";
            $pesan .= "Berikut adalah password baru Anda: <b>{$passwordBaru}</b><br><br>";
            $pesan .= "Silakan login menggunakan password tersebut dan segera ganti password Anda demi keamanan.<br><br>";
            $pesan .= "Terima kasih.";

            $email->setMessage($pesan);

            if ($email->send()) {
                return redirect()->back()->with('success', "Password baru berhasil dikirim ke {$emailTujuan}!");
            } else {
                $errorMsg = $email->printDebugger(['headers']);
                log_message('error', 'Gagal mengirim email: ' . $errorMsg);
                return redirect()->back()->with('error', 'Gagal mengirim email. Pastikan koneksi internet server stabil.');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }
}
