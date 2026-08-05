<?php

namespace App\Controllers\Pelatihan\Peserta;

use App\Controllers\BaseController;

class Home extends BaseController
{
    private function createNotification($db, $userId, $title, $message, $type = 'info')
    {
        $today = date('Y-m-d');
        $exists = $db->table('notifikasi_pelatihan')
            ->where('user_id', $userId)
            ->where('title', $title)
            ->like('message', $message)
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        if ($exists == 0) {
            $db->table('notifikasi_pelatihan')->insert([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function index()
    {
        $userId = $this->session->get('user_id');
        $db = \Config\Database::connect();
        
        // Get user from users_pelatihan based on session user_id
        $userDb = $db->table('users_pelatihan')->where('nik', $userId)->get()->getRowArray();
        
        $user = [
            'id' => $userId ?? 2,
            'nama' => $this->session->get('nama') ?? ($userDb['nama_lengkap'] ?? 'User'),
            'email' => $userDb['email'] ?? 'user@gmail.com',
            'profesi' => $userDb['profesi'] ?? 'Umum',
            'instansi' => $userDb['instansi'] ?? 'RSUD',
            'target_jpl' => $userDb['target_jpl'] ?? 20,
            'total_jpl' => $userDb['capaian_jpl'] ?? 0
        ];

        $myProgress = $db->table('peserta_pelatihan')->where('user_id', $userId)->get()->getResultArray();
        
        $jadwal = [];
        $activePelatihan = $db->table('master_pelatihan')->whereIn('status', ['Publish', 'Aktif'])->get()->getResultArray();
        
        $activeIds = array_column($activePelatihan, 'id');
        $sesiGrouped = [];
        if (!empty($activeIds)) {
            $allSesi = $db->table('sesi_interaktif_pelatihan')->whereIn('pelatihan_id', $activeIds)->get()->getResultArray();
            foreach ($allSesi as $s) {
                $sesiGrouped[$s['pelatihan_id']][] = $s;
            }
        }

        $myProgressMap = [];
        foreach ($myProgress as $p) {
            $myProgressMap[$p['pelatihan_id']] = $p;
        }

        foreach ($activePelatihan as $ap) {
            $statusEnrollment = 'belum_daftar';
            if (isset($myProgressMap[$ap['id']])) {
                $p = $myProgressMap[$ap['id']];
                if ($p['status_peserta'] == 'Lulus' || $p['status_peserta'] == 'Selesai') {
                    $statusEnrollment = 'selesai';
                } elseif ($p['status_peserta'] == 'Aktif') {
                    $statusEnrollment = 'lanjutkan';
                } else {
                    $statusEnrollment = 'terdaftar';
                }
            }
            $jadwal[] = [
                'tanggal' => $ap['jadwal_mulai'], 
                'end' => $ap['jadwal_selesai'],
                'event' => 'Mulai: ' . $ap['nama'], 
                'tipe' => 'pelatihan',
                'reg_buka' => $ap['reg_buka_tgl'],
                'reg_tutup' => $ap['reg_tutup_tgl'],
                'status_enrollment' => $statusEnrollment
            ];
            
            $sesiList = $sesiGrouped[$ap['id']] ?? [];
            foreach ($sesiList as $s) {
                $jadwal[] = [
                    'tanggal' => $s['tanggal'], 
                    'event' => 'Sesi: ' . $s['nama_sesi'], 
                    'tipe' => 'sesi',
                    'jam' => $s['waktu'],
                    'status_enrollment' => $statusEnrollment
                ];
            }
        }

        // Diklat Aktif (Sedang Berjalan)
        $diklat_aktif = $db->query("
            SELECT m.*, p.status_peserta
            FROM peserta_pelatihan p
            JOIN master_pelatihan m ON p.pelatihan_id = m.id
            WHERE p.user_id = ? AND p.status_pembayaran = 'Verified' AND p.status_peserta = 'Aktif'
        ", [$userId])->getResultArray();

        // Kategori Unik
        $kategoriList = $db->query("SELECT DISTINCT kategori FROM master_pelatihan WHERE status IN ('Publish', 'Aktif')")->getResultArray();
        $kategori = [];
        $colors = ['#ce2127', '#111111', '#c62828', '#212529'];
        $icons = ['fa-briefcase-medical', 'fa-laptop-code', 'fa-tasks', 'fa-globe'];
        foreach ($kategoriList as $i => $cat) {
            if (empty($cat['kategori'])) continue;
            $kategori[] = [
                'nama' => $cat['kategori'],
                'icon' => $icons[$i % count($icons)],
                'color' => $colors[$i % count($colors)]
            ];
        }

        // Metode Unik
        $metodeList = $db->query("SELECT DISTINCT mekanisme FROM master_pelatihan WHERE status IN ('Publish', 'Aktif')")->getResultArray();
        $metode = [];
        foreach ($metodeList as $m) {
            if (empty($m['mekanisme'])) continue;
            $metode[] = [
                'nama' => 'Pembelajaran ' . $m['mekanisme'],
                'icon' => $m['mekanisme'] == 'Online' ? 'fa-mouse-pointer' : ($m['mekanisme'] == 'Offline' ? 'fa-chalkboard-teacher' : 'fa-layer-group')
            ];
        }

        // Pelatihan Populer
        $pelatihan_populer = $db->query("
            SELECT m.*, COUNT(p.id) as peserta, m.biaya, m.kategori, m.mekanisme, m.nama
            FROM master_pelatihan m
            LEFT JOIN peserta_pelatihan p ON p.pelatihan_id = m.id
            WHERE m.status IN ('Publish', 'Aktif')
            GROUP BY m.id
            ORDER BY peserta DESC
            LIMIT 4
        ")->getResultArray();

        $rating_institusi = [
            ['nama' => 'RSUD Kota Yogyakarta', 'rating' => 4.9, 'ulasan' => 1250, 'logo' => 'https://ui-avatars.com/api/?name=RSUD+YK&background=ce2127&color=fff'],
        ];

        $selesaiCount = count(array_filter($myProgress, fn($p) => $p['status_peserta'] == 'Lulus'));

        $notifList = $db->table('notifikasi_pelatihan')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $today = date('Y-m-d');
        $registeredTrainings = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->whereIn('status_pembayaran', ['Verified', 'Gratis'])
            ->get()->getResultArray();

        $registeredPelatihanIds = array_column($registeredTrainings, 'pelatihan_id');
        $masterTrainings = [];
        if (!empty($registeredPelatihanIds)) {
            $trainingsRaw = $db->table('master_pelatihan')->whereIn('id', $registeredPelatihanIds)->get()->getResultArray();
            foreach ($trainingsRaw as $t) {
                $masterTrainings[$t['id']] = $t;
            }
        }

        foreach ($registeredTrainings as $reg) {
            $training = $masterTrainings[$reg['pelatihan_id']] ?? null;
            if (!$training) continue;

            $regOpen = $training['reg_buka_tgl'] . ' ' . ($training['reg_buka_jam'] ?: '00:00:00');
            $regClose = $training['reg_tutup_tgl'] . ' ' . ($training['reg_tutup_jam'] ?: '23:59:59');
            $start = $training['jadwal_mulai'] . ' ' . ($training['jam_mulai'] ?: '00:00:00');
            $end = $training['jadwal_selesai'] . ' ' . ($training['jam_selesai'] ?: '23:59:59');

            $daysLeft = (int)floor((strtotime($regClose) - time()) / 86400);
            if ($daysLeft >= 0 && in_array($daysLeft, [3, 2, 1, 0], true)) {
                $this->createNotification($db, $userId, 'Registrasi Segera Ditutup', 'Registrasi pelatihan ' . $training['nama'] . ' akan ditutup ' . ($daysLeft == 0 ? 'hari ini' : 'dalam ' . $daysLeft . ' hari') . '.', 'info');
            }

            if ($today === date('Y-m-d', strtotime($start))) {
                $this->createNotification($db, $userId, 'Pelatihan Hari Ini', 'Pelatihan ' . $training['nama'] . ' dimulai hari ini' . (!empty($training['jam_mulai']) ? ' pukul ' . $training['jam_mulai'] . ' WIB' : '') . '. Silakan akses ruang belajar Anda.', 'info');
            }

            if ($today === date('Y-m-d', strtotime($end))) {
                $this->createNotification($db, $userId, 'Pelatihan Selesai Hari Ini', 'Pelatihan ' . $training['nama'] . ' berakhir hari ini pukul ' . ($training['jam_selesai'] ?: '23:59:59') . ' WIB.', 'info');
            }
        }

        // Re-fetch after possible auto-notifications to get updated unread count
        $notifList = $db->table('notifikasi_pelatihan')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Beranda',
            'user' => $user,
            'total_belajar' => count($myProgress),
            'selesai' => $selesaiCount,
            'total_skp' => 0,
            'total_jpl' => $user['total_jpl'] ?? 0,
            'target_skp' => 0,
            'target_jpl' => $user['target_jpl'] ?? 20,
            'pelatihan_populer' => $pelatihan_populer,
            'diklat_aktif' => $diklat_aktif,
            'jadwal' => $jadwal,
            'notifikasi' => $notifList
        ];

        return view('Pelatihan/peserta/home/index', $data);
    }

    public function notifikasi()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        
        $notif = $db->table('notifikasi_pelatihan')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Notifikasi',
            'notifikasi' => $notif
        ];

        return view('Pelatihan/peserta/home/notifikasi', $data);
    }

    public function mark_read($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return $this->response->setJSON(['success' => false]);

        $db = \Config\Database::connect();
        $db->table('notifikasi_pelatihan')
           ->where('id', $id)
           ->where('user_id', $userId)
           ->update(['is_read' => 1]);

        return $this->response->setJSON(['success' => true]);
    }
    
    public function mark_all_read()
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return $this->response->setJSON(['success' => false]);

        $db = \Config\Database::connect();
        $db->table('notifikasi_pelatihan')
           ->where('user_id', $userId)
           ->where('is_read', 0)
           ->update(['is_read' => 1]);

        return $this->response->setJSON(['success' => true]);
    }
}
