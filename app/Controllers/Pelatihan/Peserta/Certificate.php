<?php

namespace App\Controllers\Pelatihan\Peserta;

use App\Controllers\BaseController;
use App\Models\Pelatihan\SertifikatPelatihanModel;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\MasterKategoriSkpPelatihanModel;

class Certificate extends BaseController
{
    protected $certModel;
    protected $userModel;

    public function __construct()
    {
        $this->certModel = new SertifikatPelatihanModel();
        $this->userModel = new UserPelatihanModel();
    }

    public function index()
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);
        
        // Recalculate just in case
        $this->userModel->recalculateJpl($userId);
        
        $myCerts = $this->certModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Calculate Target
        $targetJpl = $user['target_jpl'] ?? 20;
        if (empty($targetJpl) && $user['id_profesi']) {
            $db = \Config\Database::connect();
            $prof = $db->table('profesi_pelatihan')->where('id_profesi', $user['id_profesi'])->get()->getRowArray();
            $targetJpl = $prof['target_jpl'] ?? 20;
        }

        $data = [
            'title' => 'Sertifikat Saya',
            'sertifikat' => $myCerts,
            'user' => $user,
            'target_jpl' => $targetJpl,
            'capaian_jpl' => $user['capaian_jpl'] ?? 0
        ];
        return view('Pelatihan/peserta/sertifikat/index', $data);
    }

    public function upload()
    {
        $kategoriModel = new MasterKategoriSkpPelatihanModel();
        $allKategori = $kategoriModel->orderBy('ranah', 'ASC')->orderBy('nama_kategori', 'ASC')->findAll();

        $categories = [];
        foreach ($allKategori as $k) {
            $ranahKey = strtolower($k['ranah']);
            $categories[$ranahKey][] = $k['nama_kategori'];
        }

        $ranah_list = array_unique(array_map(function($k) { return $k['ranah']; }, $allKategori));
        sort($ranah_list);

        return view('Pelatihan/peserta/sertifikat/upload', [
            'title'      => 'Upload Sertifikat Mandiri / Surat Tugas / Pengabdian',
            'categories' => $categories,
            'ranah_list' => $ranah_list,
        ]);
    }

    public function submit_upload()
    {
        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        $db = \Config\Database::connect();
        $prof = $user['id_profesi'] ? $db->table('profesi_pelatihan')->where('id_profesi', $user['id_profesi'])->get()->getRowArray() : null;

        $judulAsli = $this->request->getPost('judul') ?: $this->request->getPost('kategori_kegiatan');
        $judulSafe = preg_replace('/[^A-Za-z0-9]/', '_', $judulAsli ?: 'Sertifikat');
        $namaUser = preg_replace('/[^A-Za-z0-9]/', '_', $user['nama_lengkap'] ?? 'User');

        $file = $this->request->getFile('dokumen');
        $filePath = '';
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/sertifikat')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/sertifikat', 0777, true);
            }
            $newName = "Sertifikat_{$judulSafe}_{$namaUser}_" . date('Ymd_His') . "." . $file->getExtension();
            $file->move(ROOTPATH . 'public/uploads/pelatihan/sertifikat', $newName);
            $filePath = 'uploads/pelatihan/sertifikat/' . $newName;
        }

        $fileSt = $this->request->getFile('dokumen_st');
        $suratTugasPath = '';
        if ($fileSt && $fileSt->isValid() && !$fileSt->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/surat_tugas')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/surat_tugas', 0777, true);
            }
            $newNameSt = "SuratTugas_{$judulSafe}_{$namaUser}_" . date('Ymd_His') . "." . $fileSt->getExtension();
            $fileSt->move(ROOTPATH . 'public/uploads/pelatihan/surat_tugas', $newNameSt);
            $suratTugasPath = 'uploads/pelatihan/surat_tugas/' . $newNameSt;
        }

        $jenisDokumen = $this->request->getPost('jenis_dokumen') ?: 'Mandiri';
        // If ranah indicates pengabdian, force jenis_dokumen to pengabdian
        $ranah = strtolower($this->request->getPost('ranah') ?? '');
        if ($ranah === 'pengabdian') {
            $jenisDokumen = 'pengabdian';
        }
        $judul = $judulAsli;

        $this->certModel->insert([
            'user_id'           => $userId,
            'user_nama'         => $user['nama_lengkap'],
            'user_profesi'      => $prof ? $prof['nama_profesi'] : 'Staff Umum',
            'judul'             => $judul,
            'ranah'             => $this->request->getPost('ranah') ?: 'Pembelajaran',
            'kategori_kegiatan' => $this->request->getPost('kategori_kegiatan'),
            'skp'               => (float)($this->request->getPost('jpl') ?? 0),
            'tgl_mulai'         => $this->request->getPost('tgl_mulai') ?: null,
            'tgl_selesai'       => $this->request->getPost('tgl_selesai') ?: null,
            'penerbit'          => $this->request->getPost('penerbit'),
            'jenis_dokumen'     => $jenisDokumen,
            'verifikasi'        => 'pending',
            'tgl_upload'        => date('Y-m-d H:i:s'),
            'file_path'         => $filePath,
            'surat_tugas_path'  => $suratTugasPath,
            'no_sertifikat'     => $this->request->getPost('no_sertifikat') ?: null,
        ]);

        return redirect()->to(site_url('pelatihan/peserta/sertifikat'))->with('success', 'Sertifikat kegiatan berhasil diunggah.');
    }

    public function edit($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $cert = $this->certModel->where('id', $id)->where('user_id', $userId)->first();
        if (!$cert) return redirect()->to('pelatihan/peserta/sertifikat')->with('error', 'Sertifikat tidak ditemukan.');

        $kategoriModel = new \App\Models\Pelatihan\MasterKategoriSkpPelatihanModel();
        $allKategori = $kategoriModel->orderBy('ranah', 'ASC')->orderBy('nama_kategori', 'ASC')->findAll();

        $categories = [];
        foreach ($allKategori as $k) {
            $ranahKey = strtolower($k['ranah']);
            $categories[$ranahKey][] = $k['nama_kategori'];
        }

        helper('pelatihan');
        return view('Pelatihan/peserta/sertifikat/edit', [
            'title' => 'Edit Sertifikat Kegiatan',
            'categories' => $categories,
            'cert' => $cert
        ]);
    }

    public function submit_edit($id)
    {
        $userId = $this->session->get('user_id');
        $cert = $this->certModel->where('id', $id)->where('user_id', $userId)->first();
        if (!$cert) return redirect()->to('pelatihan/peserta/sertifikat')->with('error', 'Sertifikat tidak ditemukan.');

        $jenisDokumen = $this->request->getPost('jenis_dokumen') ?: 'Mandiri';
        $ranah = strtolower($this->request->getPost('ranah') ?? '');
        if ($ranah === 'pengabdian') {
            $jenisDokumen = 'pengabdian';
        }
        $judul = $this->request->getPost('judul') ?: $this->request->getPost('kategori_kegiatan');

        $updateData = [
            'judul'             => $judul,
            'ranah'             => $this->request->getPost('ranah') ?: 'Pembelajaran',
            'kategori_kegiatan' => $this->request->getPost('kategori_kegiatan'),
            'skp'               => (float)($this->request->getPost('jpl') ?? 0),
            'tgl_mulai'         => $this->request->getPost('tgl_mulai') ?: null,
            'tgl_selesai'       => $this->request->getPost('tgl_selesai') ?: null,
            'penerbit'          => $this->request->getPost('penerbit'),
            'jenis_dokumen'     => $jenisDokumen,
            'no_sertifikat'     => $this->request->getPost('no_sertifikat') ?: null,
            'verifikasi'        => 'pending' // Reset status verifikasi saat di-edit
        ];

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);
        
        $judulAsli = $this->request->getPost('judul') ?: $this->request->getPost('kategori_kegiatan');
        $judulSafe = preg_replace('/[^A-Za-z0-9]/', '_', $judulAsli ?: 'Sertifikat');
        $namaUser = preg_replace('/[^A-Za-z0-9]/', '_', $user['nama_lengkap'] ?? 'User');

        $file = $this->request->getFile('dokumen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/sertifikat')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/sertifikat', 0777, true);
            }
            $newName = "Sertifikat_{$judulSafe}_{$namaUser}_" . date('Ymd_His') . "." . $file->getExtension();
            $file->move(ROOTPATH . 'public/uploads/pelatihan/sertifikat', $newName);
            $updateData['file_path'] = 'uploads/pelatihan/sertifikat/' . $newName;
        }

        $fileSt = $this->request->getFile('dokumen_st');
        if ($fileSt && $fileSt->isValid() && !$fileSt->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/surat_tugas')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/surat_tugas', 0777, true);
            }
            $newNameSt = "SuratTugas_{$judulSafe}_{$namaUser}_" . date('Ymd_His') . "." . $fileSt->getExtension();
            $fileSt->move(ROOTPATH . 'public/uploads/pelatihan/surat_tugas', $newNameSt);
            $updateData['surat_tugas_path'] = 'uploads/pelatihan/surat_tugas/' . $newNameSt;
        }

        $this->certModel->update($id, $updateData);

        return redirect()->to(site_url('pelatihan/peserta/sertifikat'))->with('success', 'Sertifikat kegiatan berhasil diperbarui.');
    }

    public function download($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        // Verify the certificate belongs to the user and is published by RSUD
        $cert = $this->certModel->where('id', $id)->where('user_id', $userId)->first();
        if (!$cert || $cert['jenis_dokumen'] !== 'rsud') {
            return redirect()->to('pelatihan/peserta/sertifikat')->with('error', 'Sertifikat tidak valid atau bukan terbitan sistem.');
        }

        $db = \Config\Database::connect();
        $pelatihanId = $cert['pelatihan_id'];

        // Get template
        $template = $db->table('sertif_terbit_pelatihan')
            ->select('sertif_terbit_pelatihan.*, p1.nama_pejabat as nama_1, p1.jabatan as jab_1, p1.an_pejabat as an_1, p1.nip_pejabat as nip_1, p1.ttd_image as ttd_1, p2.nama_pejabat as nama_2, p2.jabatan as jab_2, p2.an_pejabat as an_2, p2.nip_pejabat as nip_2, p2.ttd_image as ttd_2')
            ->join('pejabat_ttd_pelatihan p1', 'p1.id = sertif_terbit_pelatihan.pejabat_id_1', 'left')
            ->join('pejabat_ttd_pelatihan p2', 'p2.id = sertif_terbit_pelatihan.pejabat_id_2', 'left')
            ->where('sertif_terbit_pelatihan.pelatihan_id', $pelatihanId)
            ->orderBy("FIELD(sertif_terbit_pelatihan.status, 'diterbitkan', 'draft')", 'ASC', false)
            ->orderBy('sertif_terbit_pelatihan.id', 'DESC')
            ->get()->getRowArray();

        if (!$template) {
            return redirect()->to('pelatihan/peserta/sertifikat')->with('error', 'Template sertifikat tidak ditemukan.');
        }

        $pelatihan = $db->table('master_pelatihan')->where('id', $pelatihanId)->get()->getRowArray();
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Pratinjau Sertifikat',
            'pelatihan' => $pelatihan,
            'users' => [$user],
            'template' => $template,
            'no_sertifikat' => $cert['no_sertifikat'] ?? ($template['no_sertifikat'] ?? 'KT.03.02/F/0001/SER/' . date('Y'))
        ];
        
        return view('Pelatihan/admin/sertifikat/template/preview', $data);
    }
}
