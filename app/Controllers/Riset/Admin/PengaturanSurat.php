<?php

namespace App\Controllers\Riset\Admin;

use App\Controllers\BaseController;
use App\Models\PengaturanSuratRisetModel;

class PengaturanSurat extends BaseController
{
    protected $pengaturanSuratModel;

    public function __construct()
    {
        $this->pengaturanSuratModel = new PengaturanSuratRisetModel();
    }

    public function index()
    {
        $pengaturan = $this->pengaturanSuratModel->first();
        
        $data = [
            'title' => 'Pengaturan Surat',
            'active_menu' => 'pengaturan_surat',
            'pengaturan' => $pengaturan
        ];

        return view('Riset/admin/pengaturan_surat/index', $data);
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        
        $rules = [
            'nama_pejabat' => 'required',
            'nip_pejabat' => 'required',
            'jabatan' => 'required',
            'logo_kop' => 'max_size[logo_kop,2048]|is_image[logo_kop]|mime_in[logo_kop,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataToSave = [
            'nama_pejabat' => $this->request->getPost('nama_pejabat'),
            'nip_pejabat' => $this->request->getPost('nip_pejabat'),
            'jabatan' => $this->request->getPost('jabatan'),
            'pangkat' => $this->request->getPost('pangkat'),
            'nama_bank' => $this->request->getPost('nama_bank'),
            'nomor_rekening' => $this->request->getPost('nomor_rekening'),
            'nama_rekening' => $this->request->getPost('nama_rekening'),
        ];

        $uploadPath = FCPATH . 'uploads/riset/pengaturan';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $pengaturanLama = null;
        if (!empty($id)) {
            $pengaturanLama = $this->pengaturanSuratModel->find($id);
        }

        // Handle File Upload for Logo
        $logo = $this->request->getFile('logo_kop');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            if ($pengaturanLama && !empty($pengaturanLama['logo_kop']) && file_exists($uploadPath . '/' . $pengaturanLama['logo_kop'])) {
                unlink($uploadPath . '/' . $pengaturanLama['logo_kop']);
            }
            $newName = $logo->getRandomName();
            $logo->move($uploadPath, $newName);
            $dataToSave['logo_kop'] = $newName;
        }



        if (empty($id)) {
            $this->pengaturanSuratModel->insert($dataToSave);
        } else {
            $this->pengaturanSuratModel->update($id, $dataToSave);
        }

        return redirect()->to('riset/admin/pengaturan-surat')->with('success', 'Pengaturan surat berhasil disimpan.');
    }
}
