<?php

namespace App\Controllers\Pendidikan\Institusi;

use App\Controllers\BaseController;
use App\Models\DokumenInstitusiModel;
use App\Models\InstitusiPendidikanModel;

class Dokumen extends BaseController
{
    protected $dokumenModel;
    protected $institusiModel;

    public function __construct()
    {
        $this->dokumenModel = new DokumenInstitusiModel();
        $this->institusiModel = new InstitusiPendidikanModel();
    }

    public function upload()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu'
            ])->setStatusCode(401);
        }

        $institusi = $this->institusiModel->where('user_id', $userId)->first();
        if (!$institusi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $judul = $this->request->getPost('judul');
        if (empty($judul)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Judul dokumen harus diisi'
            ])->setStatusCode(400);
        }

        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid atau tidak terupload'
            ])->setStatusCode(400);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $uploadPath = WRITEPATH . 'uploads/dokumen_institusi';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            $this->dokumenModel->insert([
                'institusi_id'  => $institusi['id'],
                'judul'         => $judul,
                'nama_file'     => $newName,
                'original_name' => $file->getClientName(),
                'tipe_file'     => $file->getMimeType(),
                'ukuran_file'   => $file->getSize(),
                'status'        => 'pending',
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Dokumen berhasil diupload',
                'data' => [
                    'id' => $this->dokumenModel->insertID(),
                    'judul' => $judul,
                    'original_name' => $file->getClientName(),
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupload file'
        ])->setStatusCode(500);
    }

    public function daftar()
    {
        $userId = session()->get('user_id');
        $institusi = $this->institusiModel->where('user_id', $userId)->first();

        if (!$institusi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $dokumen = $this->dokumenModel
            ->where('institusi_id', $institusi['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $dokumen
        ]);
    }

    public function file($jenis)
    {
        $institusiId = session()->get('institusi_id');
        if (!$institusiId) {
            return redirect()->to('pendidikan/login')->with('error', 'Sesi institusi tidak valid.');
        }

        $fieldMap = [
            'mou' => 'file_mou',
            'permohonan' => 'file_permohonan',
            'lainnya' => 'file_lainnya',
        ];
        if (!isset($fieldMap[$jenis])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $institusi = $this->institusiModel->find($institusiId);
        $namaFile = $institusi[$fieldMap[$jenis]] ?? null;
        if (!$namaFile) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        foreach ([
            WRITEPATH . 'uploads/dokumen_institusi/' . $namaFile,
            FCPATH . 'uploads/institusi/' . $namaFile,
        ] as $path) {
            if (is_file($path)) {
                return $this->response->download($path, null, true)->inline();
            }
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
}
