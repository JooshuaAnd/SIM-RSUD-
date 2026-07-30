<?php

namespace App\Controllers\Riset\Admin;

use App\Controllers\BaseController;
use App\Models\PengajuanRisetModel;
use App\Models\DokumenRisetModel;

class Review extends BaseController
{
    protected PengajuanRisetModel $pengajuanModel;
    protected DokumenRisetModel $dokumenModel;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanRisetModel();
        $this->dokumenModel = new DokumenRisetModel();
    }

    public function index()
    {
        $daftar = $this->pengajuanModel
            ->where('jenis_pengajuan', 'studi_pendahuluan')
            ->where('status !=', 'ditolak')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('Riset/admin/review/index', [
            'title'       => 'Review Pengajuan',
            'active_menu' => 'review',
            'daftar'      => $daftar
        ]);
    }

    public function detail($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (!$pengajuan) {
            return redirect()->to(base_url('riset/admin/review'))->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->whereIn('jenis_dokumen', ['Surat Permohonan', 'Proposal', 'CV', 'Draft Wawancara', 'Surat Izin Resmi'])
                                      ->findAll();
        $pengajuan['dokumen'] = $dokumen;

        // Fetch user data for contact info
        $userModel = new \App\Models\UserRisetModel();
        $user = $userModel->find($pengajuan['user_riset_id']);
        $pengajuan['email'] = $user['email'] ?? 'Tidak ada';
        $pengajuan['no_telp'] = $user['no_telp'] ?? 'Tidak ada';
        $pengajuan['tanggal'] = date('d/m/Y', strtotime($pengajuan['created_at'] ?? 'now'));

        // Generate nomor surat berdasarkan angka tertinggi di tahun berjalan khusus studi pendahuluan
        $currentYear = date('Y');
        $existingNumbers = $this->pengajuanModel
            ->where('jenis_pengajuan', 'studi_pendahuluan')
            ->like('nomor_surat', '/' . $currentYear, 'before')
            ->findColumn('nomor_surat');
        
        $maxNumber = 0;
        if ($existingNumbers) {
            foreach ($existingNumbers as $numStr) {
                $parts = explode('/', $numStr);
                if (isset($parts[0]) && is_numeric($parts[0])) {
                    $val = (int)$parts[0];
                    if ($val > $maxNumber) {
                        $maxNumber = $val;
                    }
                }
            }
        }
        
        $increment = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
        $romans = ['01'=>'I', '02'=>'II', '03'=>'III', '04'=>'IV', '05'=>'V', '06'=>'VI', '07'=>'VII', '08'=>'VIII', '09'=>'IX', '10'=>'X', '11'=>'XI', '12'=>'XII'];
        $romanMonth = $romans[date('m')];
        $default_nomor_surat = "{$increment}/SIP-SP/{$romanMonth}/" . $currentYear;

        return view('Riset/admin/review/detail', [
            'title'               => 'Detail Pengajuan',
            'active_menu'         => 'review',
            'id'                  => $id,
            'data'                => $pengajuan,
            'default_nomor_surat' => $default_nomor_surat
        ]);
    }

    public function approve()
    {
        $id               = $this->request->getPost('id');
        $status_validasi  = $this->request->getPost('status_validasi');
        $catatan          = $this->request->getPost('catatan');
        $nominal          = $this->request->getPost('nominal_bayar');
        $nomor_surat      = $this->request->getPost('nomor_surat');
        $waktu_mulai      = $this->request->getPost('waktu_mulai');
        $waktu_selesai    = $this->request->getPost('waktu_selesai');

        $updateData = [];

        if ($status_validasi == 'konfirmasi_dokumen') {
            $updateData['status'] = 'menunggu_pembayaran';
            $updateData['catatan_revisi'] = null;
            if ($nominal) {
                $updateData['nominal_bayar'] = $nominal;
            }
            $message = 'Dokumen disetujui. Menunggu pembayaran dari peneliti.';
        } elseif ($status_validasi == 'konfirmasi_bayar') {
            $updateData['status'] = 'selesai';
            $updateData['catatan_revisi'] = null;
            if ($nomor_surat) {
                $exists = $this->pengajuanModel->where('nomor_surat', $nomor_surat)->where('id !=', $id)->first();
                if ($exists) {
                    return redirect()->back()->with('error', "Gagal memproses: Nomor surat {$nomor_surat} sudah digunakan di pengajuan lain.");
                }
                $updateData['nomor_surat'] = $nomor_surat;
            }
            if ($waktu_mulai) {
                $updateData['waktu_mulai'] = $waktu_mulai;
            }
            if ($waktu_selesai) {
                $updateData['waktu_selesai'] = $waktu_selesai;
            }
            $message = 'Pembayaran divalidasi. Surat Izin berhasil diterbitkan.';
        } elseif ($status_validasi == 'revisi') {
            $updateData['status'] = 'direvisi';
            $updateData['catatan_revisi'] = $catatan;
            $message = 'Status pengajuan berhasil diperbarui menjadi Revisi.';
        } elseif ($status_validasi == 'revisi_bayar') {
            $updateData['status'] = 'menunggu_pembayaran';
            $updateData['catatan_revisi'] = $catatan;
            $message = 'Bukti pembayaran dikembalikan untuk direvisi.';
        } elseif ($status_validasi == 'tolak') {
            $updateData['status'] = 'ditolak';
            $updateData['catatan_penolakan'] = $catatan;
            $message = 'Pengajuan berhasil ditolak.';
        } else {
            $updateData['status'] = 'selesai';
            $message = 'Pendaftaran riset berhasil divalidasi dan disetujui.';
        }

        $this->pengajuanModel->update($id, $updateData);

        return redirect()->to(base_url('riset/admin/review'))
            ->with('success', $message);
    }

    public function print($id = null)
    {
        $pengajuan = $this->pengajuanModel->find($id);

        if (!$pengajuan) {
            return redirect()->to(base_url('riset/admin/review'))->with('error', 'Data tidak ditemukan.');
        }

        $bulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        $mulai = !empty($pengajuan['waktu_mulai']) ? $pengajuan['waktu_mulai'] : null;
        $waktu_mulai_fmt = $mulai ? date('d', strtotime($mulai)) . ' ' . $bulanIndo[(int)date('m', strtotime($mulai))] . ' ' . date('Y', strtotime($mulai)) : '-';
        
        $selesai = !empty($pengajuan['waktu_selesai']) ? $pengajuan['waktu_selesai'] : null;
        $waktu_selesai_fmt = $selesai ? date('d', strtotime($selesai)) . ' ' . $bulanIndo[(int)date('m', strtotime($selesai))] . ' ' . date('Y', strtotime($selesai)) : '-';

        $pengaturanModel = new \App\Models\PengaturanSuratRisetModel();
        $pengaturan = $pengaturanModel->first();

        return view('Riset/peneliti/pengajuan/surat_izin_template', [
            'title'          => 'Cetak Surat Izin Studi Pendahuluan',
            'active_menu'    => 'review',
            'nama_peneliti'  => $pengajuan['nama'] ?? '-',
            'nim'            => $pengajuan['identitas'] ?? '-',
            'prodi'          => $pengajuan['prodi'] ?? '-',
            'institusi'      => $pengajuan['institusi'] ?? '-',
            'judul_riset'    => $pengajuan['judul'] ?? '-',
            'waktu_mulai'    => $waktu_mulai_fmt,
            'waktu_selesai'  => $waktu_selesai_fmt,
            'nomor_surat'    => $pengajuan['nomor_surat'] ?? null,
            'pengaturan'     => $pengaturan
        ]);
    }

    public function uploadSuratIzin()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('surat_izin');

        if ($file && $file->isValid()) {
            $newName = str_replace(' ', '_', $file->getClientName());
            $file->move(FCPATH . 'uploads/riset/dokumen', $newName);

            $this->dokumenModel->where('pengajuan_riset_id', $id)
                               ->where('jenis_dokumen', 'Surat Izin Resmi')
                               ->delete();
                               
            $this->dokumenModel->insert([
                'pengajuan_riset_id' => $id,
                'jenis_dokumen'      => 'Surat Izin Resmi',
                'file_path'          => 'uploads/riset/dokumen/' . $newName,
                'status_dokumen'     => 'valid'
            ]);

            return redirect()->to(base_url("riset/admin/review/detail/{$id}"))->with('success', 'Surat Izin Resmi berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah surat izin.');
    }
}
