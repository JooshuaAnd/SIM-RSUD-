<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $userModel = new \App\Models\Pelatihan\UserPelatihanModel();
        $pelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $sertifikatModel = new \App\Models\Pelatihan\SertifTerbitPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();

        $totalPeserta = $userModel->where('role', 'peserta')->countAllResults();
        $pesertaBulanIni = $userModel->where('role', 'peserta')
            ->where('created_at >=', date('Y-m-01 00:00:00'))
            ->countAllResults();
        $totalPelatihan = $pelatihanModel->where('status', 'Aktif')->countAllResults();
        $pelatihanSelesaiHariIni = $pelatihanModel->where('status', 'Aktif')
            ->where('jadwal_selesai', date('Y-m-d'))
            ->countAllResults();
        $totalSertifikat = $sertifikatModel->countAllResults();
        $sertifikatMasuk = $db->table('sertifikat_pelatihan')
            ->where('verifikasi', 'pending')
            ->countAllResults();
        $sertifikatPengabdianMasuk = $db->table('sertifikat_pelatihan')
            ->where('verifikasi', 'pending')
            ->groupStart()
                ->where("LOWER(ranah) = 'pengabdian'", null, false)
                ->orWhere("LOWER(jenis_dokumen) = 'pengabdian'", null, false)
            ->groupEnd()
            ->countAllResults();
        $pendaftaranPending = $pesertaModel->groupStart()
                ->where('status_akses', 'Pending')
                ->orWhere('status_pembayaran', 'Pending')
            ->groupEnd()
            ->countAllResults();
        $verifikasiBaru = $pesertaModel->groupStart()
                ->where('status_akses', 'Pending')
                ->orWhere('status_pembayaran', 'Pending')
            ->groupEnd()
            ->where('DATE(waktu_daftar) = ' . $db->escape(date('Y-m-d')), null, false)
            ->countAllResults();
        $pembayaranPending = $pesertaModel->where('status_pembayaran', 'Pending')->countAllResults();

        // Recent users
        $recentUsers = $userModel->where('role', 'peserta')->orderBy('created_at', 'DESC')->limit(5)->findAll();

        $recentPendaftaranRaw = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.id_unit_kerja as unit_kerja_id, master_pelatihan.nama as pelatihan_nama')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id', 'left')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id', 'left')
            ->groupStart()
                ->where('peserta_pelatihan.status_akses', 'Pending')
                ->orWhere('peserta_pelatihan.status_pembayaran', 'Pending')
            ->groupEnd()
            ->orderBy('peserta_pelatihan.waktu_daftar', 'DESC')
            ->limit(5)
            ->findAll();

        $recentPendaftaran = array_map(function($p) {
            return [
                'nama' => $p['nama'] ?? 'Peserta',
                'instansi' => 'RSUD KOTA JOGJA',
                'pelatihan_nama' => $p['pelatihan_nama'] ?? 'Pelatihan',
                'tanggal' => $p['waktu_daftar'] ?? date('Y-m-d'),
                'status_peserta' => $p['status_peserta'],
                'status_pembayaran' => $p['status_pembayaran']
            ];
        }, $recentPendaftaranRaw);

        // Distribusi Unit Kerja
        $distribusiUnitKerja = $userModel->select('unit_kerja_pelatihan.nama_unit as nama, COUNT(users_pelatihan.nik) as total')
                                         ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
                                         ->where('users_pelatihan.role', 'peserta')
                                         ->groupBy('users_pelatihan.id_unit_kerja')
                                         ->orderBy('total', 'DESC')
                                         ->limit(5)
                                         ->findAll();
        // Calculate percentages
        if ($totalPeserta > 0) {
            foreach($distribusiUnitKerja as &$duk) {
                $duk['persen'] = round(($duk['total'] / $totalPeserta) * 100);
            }
        }

        $activeTrainings = $db->table('master_pelatihan mp')
            ->select("mp.*, COUNT(pp.id) as total_daftar, SUM(CASE WHEN pp.status_peserta = 'Lulus' THEN 1 ELSE 0 END) as total_lulus")
            ->join('peserta_pelatihan pp', 'pp.pelatihan_id = mp.id', 'left')
            ->where('mp.status', 'Aktif')
            ->groupBy('mp.id')
            ->orderBy('mp.jadwal_mulai', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();

        foreach ($activeTrainings as &$training) {
            $kuota = max((int)($training['kuota'] ?? 0), 1);
            $daftar = (int)($training['total_daftar'] ?? 0);
            $lulus = (int)($training['total_lulus'] ?? 0);
            $training['persen_daftar'] = min(100, round(($daftar / $kuota) * 100));
            $training['persen_lulus'] = $daftar > 0 ? round(($lulus / $daftar) * 100) : 0;
            $training['hari_reg_tutup'] = !empty($training['reg_tutup_tgl']) ? (int) floor((strtotime($training['reg_tutup_tgl']) - strtotime(date('Y-m-d'))) / 86400) : null;
            $training['hari_mulai'] = !empty($training['jadwal_mulai']) ? (int) floor((strtotime($training['jadwal_mulai']) - strtotime(date('Y-m-d'))) / 86400) : null;
        }
        unset($training);

        $agendaTerdekat = array_values(array_filter($activeTrainings, function ($training) {
            return ($training['hari_mulai'] ?? 999) >= 0 || ($training['hari_reg_tutup'] ?? 999) >= 0;
        }));

        $recentSertifikat = $db->table('sertifikat_pelatihan sp')
            ->select('sp.*, users_pelatihan.nama_lengkap as nama_peserta, master_pelatihan.nama as pelatihan_nama')
            ->join('users_pelatihan', 'users_pelatihan.nik = sp.user_id', 'left')
            ->join('master_pelatihan', 'master_pelatihan.id = sp.pelatihan_id', 'left')
            ->orderBy('sp.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $notifications = [];
        if ($pendaftaranPending > 0) {
            $notifications[] = [
                'title' => 'Registrasi baru menunggu verifikasi',
                'message' => $pendaftaranPending . ' pendaftaran perlu dicek akses atau pembayarannya.',
                'url' => base_url('pelatihan/admin/verifikasi_pendaftaran'),
                'type' => 'danger'
            ];
        }
        if ($verifikasiBaru > 0) {
            $notifications[] = [
                'title' => 'Verifikasi baru hari ini',
                'message' => $verifikasiBaru . ' peserta masuk antrean hari ini.',
                'url' => base_url('pelatihan/admin/verifikasi_pendaftaran'),
                'type' => 'warning'
            ];
        }
        if ($sertifikatMasuk > 0) {
            $notifications[] = [
                'title' => 'Sertifikat masuk',
                'message' => $sertifikatMasuk . ' sertifikat eksternal menunggu keputusan admin.',
                'url' => base_url('pelatihan/admin/sertifikat'),
                'type' => 'primary'
            ];
        }
        if ($sertifikatPengabdianMasuk > 0) {
            $notifications[] = [
                'title' => 'Sertifikat pengabdian masuk',
                'message' => $sertifikatPengabdianMasuk . ' pengajuan pengabdian menunggu admin pengabdian.',
                'url' => base_url('pelatihan/admin_pengabdian/sertifikat'),
                'type' => 'success'
            ];
        }
        foreach ($activeTrainings as $training) {
            if ($training['hari_reg_tutup'] !== null && $training['hari_reg_tutup'] >= 0 && $training['hari_reg_tutup'] <= 3) {
                $notifications[] = [
                    'title' => $training['hari_reg_tutup'] === 0 ? 'Registrasi ditutup hari ini' : $training['hari_reg_tutup'] . ' hari lagi registrasi berakhir',
                    'message' => $training['nama'] . ' sudah terisi ' . ($training['total_daftar'] ?? 0) . '/' . ($training['kuota'] ?? 0) . ' peserta.',
                    'url' => base_url('pelatihan/admin/pelatihan/kelola/' . $training['id']),
                    'type' => 'dark'
                ];
            }
            if ($training['hari_mulai'] !== null && $training['hari_mulai'] >= 0 && $training['hari_mulai'] <= 3) {
                $notifications[] = [
                    'title' => $training['hari_mulai'] === 0 ? 'Pelatihan dimulai hari ini' : $training['hari_mulai'] . ' hari lagi pelatihan aktif',
                    'message' => $training['nama'] . ' mulai ' . date('d M Y', strtotime($training['jadwal_mulai'])) . ' jam ' . ($training['jam_mulai'] ?? '-'),
                    'url' => base_url('pelatihan/admin/pelatihan/kelola/' . $training['id']),
                    'type' => 'danger'
                ];
            }
        }

        $data = [
            'title' => 'Dashboard Admin',
            'total_peserta' => $totalPeserta,
            'peserta_bulan_ini' => $pesertaBulanIni,
            'total_pelatihan' => $totalPelatihan,
            'pelatihan_selesai_hari_ini' => $pelatihanSelesaiHariIni,
            'total_sertifikat' => $totalSertifikat,
            'sertifikat_masuk' => $sertifikatMasuk,
            'sertifikat_pengabdian_masuk' => $sertifikatPengabdianMasuk,
            'pendaftaran_pending' => $pendaftaranPending,
            'verifikasi_baru' => $verifikasiBaru,
            'pembayaran_pending' => $pembayaranPending,
            'pelatihan' => $activeTrainings,
            'recent_users' => $recentUsers,
            'recent_pendaftaran' => $recentPendaftaran,
            'recent_sertifikat' => $recentSertifikat,
            'distribusi_unit_kerja' => $distribusiUnitKerja,
            'agenda_terdekat' => array_slice($agendaTerdekat, 0, 5),
            'notifications' => array_slice($notifications, 0, 8)
        ];

        return view('Pelatihan/admin/dashboard/index', $data);
    }
}
