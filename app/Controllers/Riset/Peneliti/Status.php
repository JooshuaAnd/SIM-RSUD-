<?php

namespace App\Controllers\Riset\Peneliti;

use App\Controllers\BaseController;
use App\Models\PengajuanRisetModel;
use App\Models\PublikasiRisetModel;
use App\Models\DokumenRisetModel;

class Status extends BaseController
{
    protected PengajuanRisetModel $pengajuanModel;
    protected PublikasiRisetModel $publikasiModel;
    protected DokumenRisetModel $dokumenModel;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanRisetModel();
        $this->publikasiModel = new PublikasiRisetModel();
        $this->dokumenModel = new DokumenRisetModel();
    }

    public function index()
    {
        $userId = session()->get('riset_user_id');

        // Studi Pendahuluan milik user
        $studi_pendahuluan = $this->pengajuanModel
            ->where('user_riset_id', $userId)
            ->where('jenis_pengajuan', 'studi_pendahuluan')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Format tanggal
        foreach ($studi_pendahuluan as &$item) {
            $date = $item['created_at'] ?? 'now';
            $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
            $item['tanggal'] = date('d', strtotime($date)) . ' ' . $bulanIndo[(int)date('m', strtotime($date))] . ' ' . date('Y', strtotime($date));
        }

        // Izin Penelitian milik user
        $izin_penelitian = $this->pengajuanModel
            ->where('user_riset_id', $userId)
            ->where('jenis_pengajuan', 'penelitian')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        foreach ($izin_penelitian as &$item) {
            $date = $item['created_at'] ?? 'now';
            $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
            $item['tanggal'] = date('d', strtotime($date)) . ' ' . $bulanIndo[(int)date('m', strtotime($date))] . ' ' . date('Y', strtotime($date));
        }

        // Publikasi milik user
        $publikasi = $this->publikasiModel
            ->where('user_riset_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        foreach ($publikasi as &$item) {
            $date = $item['created_at'] ?? 'now';
            $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
            $item['tanggal'] = date('d', strtotime($date)) . ' ' . $bulanIndo[(int)date('m', strtotime($date))] . ' ' . date('Y', strtotime($date));
        }

        $pengaturanModel = new \App\Models\PengaturanSuratRisetModel();
        $pengaturan = $pengaturanModel->first();

        return view('Riset/peneliti/status/index', [
            'title'              => 'Status Pengajuan',
            'active_menu'        => 'status',
            'studi_pendahuluan'  => $studi_pendahuluan,
            'izin_penelitian'    => $izin_penelitian,
            'publikasi'          => $publikasi,
            'pengaturan'         => $pengaturan
        ]);
    }

    public function detail($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (!$pengajuan) {
            return redirect()->to(base_url('riset/peneliti/status'))->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)->findAll();
        $pengajuan['dokumen_upload'] = array_column($dokumen, 'file_path');
        $pengajuan['tanggal'] = date('d F Y, H:i', strtotime($pengajuan['created_at'] ?? 'now')) . ' WIB';

        return view('Riset/peneliti/status/detail', [
            'title'       => 'Detail Pengajuan',
            'active_menu' => 'status',
            'data'        => $pengajuan
        ]);
    }
}
