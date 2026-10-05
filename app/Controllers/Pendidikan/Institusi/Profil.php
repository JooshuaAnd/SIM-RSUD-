<?php

namespace App\Controllers\Pendidikan\Institusi;

use App\Controllers\BaseController;
use App\Models\InstitusiPendidikanModel;
use App\Models\UserPendidikanModel;

class Profil extends BaseController
{
    public function index()
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return redirect()->to('pendidikan/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $institusiModel = new InstitusiPendidikanModel();
        $userModel = new UserPendidikanModel();

        $institusi = $institusiModel->find($sessionData['institusi_id']);
        $user = $userModel->find($sessionData['user_id']);

        if (!$institusi) {
            return redirect()->to('pendidikan/login')->with('error', 'Data institusi tidak ditemukan.');
        }

        $data = [
            'title' => 'Profil Institusi',
            'institusi' => $institusi,
            'user' => $user
        ];

        return view('Pendidikan/Institusi/profil/index', $data);
    }

    public function update()
    {
        $sessionData = session()->get();
        if (!isset($sessionData['institusi_id'])) {
            return redirect()->to('pendidikan/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $institusiModel = new InstitusiPendidikanModel();
        $institusi_id = $sessionData['institusi_id'];
        $institusi_lama = $institusiModel->find($institusi_id);

        if (!$institusi_lama) {
            return redirect()->to('pendidikan/login')->with('error', 'Data institusi tidak ditemukan.');
        }

        $namaInstitusi = trim((string) $this->request->getPost('nama_institusi'));
        $namaKontak = trim((string) $this->request->getPost('nama_kontak'));
        $noTelp = trim((string) $this->request->getPost('no_telp'));
        if ($namaInstitusi === '' || $namaKontak === '' || !preg_match("/^[\\p{L}][\\p{L}\\s.,'-]*$/u", $namaInstitusi) || !preg_match("/^[\\p{L}][\\p{L}\\s.,'-]*$/u", $namaKontak)) {
            return redirect()->back()->withInput()->with('error', 'Nama hanya boleh berisi huruf, spasi, titik, koma, apostrof, atau tanda hubung.');
        }
        if (!preg_match('/^[0-9]+$/D', $noTelp)) {
            return redirect()->back()->withInput()->with('error', 'Nomor telepon hanya boleh berisi angka.');
        }

        $dataUpdate = [
            'nama_institusi' => $namaInstitusi,
            'alamat'         => trim((string) $this->request->getPost('alamat')),
            'no_telp'        => $noTelp,
            'nama_kontak'    => $namaKontak,
        ];

        $uploadPath = WRITEPATH . 'uploads/dokumen_institusi/';
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0775, true) && !is_dir($uploadPath)) {
            return redirect()->back()->withInput()->with('error', 'Folder dokumen tidak dapat disiapkan.');
        }

        foreach (['file_mou', 'file_permohonan'] as $field) {
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

            $oldName = $institusi_lama[$field] ?? null;
            foreach ([WRITEPATH . 'uploads/dokumen_institusi/' . $oldName, FCPATH . 'uploads/institusi/' . $oldName] as $oldPath) {
                if ($oldName && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

        $institusiModel->update($institusi_id, $dataUpdate);

        return redirect()->to('pendidikan/institusi/profil')->with('success', 'Profil institusi berhasil diperbarui.');
    }

    public function update_password()
    {
        $session = session();
        $userId = $session->get('user_id');
        
        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password baru tidak cocok.');
        }

        $userModel = new UserPendidikanModel();
        $user = $userModel->find($userId);

        if (!$user || !password_verify((string)$oldPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        $userModel->update($userId, [
            'password' => password_hash((string)$newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }
}
