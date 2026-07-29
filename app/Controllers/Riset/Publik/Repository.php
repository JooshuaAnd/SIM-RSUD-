<?php

namespace App\Controllers\Riset\Publik;

use App\Controllers\BaseController;
use App\Models\PublikasiRisetModel;

class Repository extends BaseController
{
    protected PublikasiRisetModel $publikasiModel;

    public function __construct()
    {
        $this->publikasiModel = new PublikasiRisetModel();
    }

    public function catalog()
    {
        // Ambil semua publikasi yang statusnya selesai untuk ditampilkan ke publik
        $publications = $this->publikasiModel
            ->where('status', 'selesai')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('Riset/publik/repository/catalog', [
            'title'        => 'Katalog Penelitian',
            'publications' => $publications
        ]);
    }

    public function detail($id = null)
    {
        $publikasi = $this->publikasiModel->find($id);
        
        if (!$publikasi || $publikasi['status'] != 'selesai') {
            return redirect()->to(base_url('repository/catalog'))->with('error', 'Publikasi tidak ditemukan.');
        }

        return view('Riset/publik/repository/detail', [
            'title' => 'Detail Publikasi',
            'data'  => $publikasi
        ]);
    }

    public function request()
    {
        return redirect()->back()->with('success', 'Permintaan akses Anda telah dikirim. Admin akan memverifikasi permintaan Anda melalui email.');
    }
}
