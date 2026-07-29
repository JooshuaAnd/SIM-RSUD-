<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\UnitKerjaPelatihanModel;
use App\Models\Pelatihan\ProfesiPelatihanModel;

class ManajemenPeserta extends BaseController
{
    public function index()
    {
        $userModel = new UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $db = \Config\Database::connect();

        // Auto update status to Selesai if the end date and time have passed
        $db->query("UPDATE master_pelatihan SET status = 'Selesai' WHERE status != 'Selesai' AND jadwal_selesai IS NOT NULL AND jam_selesai IS NOT NULL AND LENGTH(jadwal_selesai) > 5 AND LENGTH(jam_selesai) > 3 AND CONCAT(jadwal_selesai, ' ', jam_selesai) <= NOW()");

        $selectedYear = $this->request->getVar('tahun') ?? date('Y');
        
        $dbUsers = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.kategori_target as kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi, unit_kerja_pelatihan.nama_unit as ruangan, users_pelatihan.id_profesi')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta')
            ->findAll();

        $targetKelompok = $this->session->get('target_kelompok') ?? [
            'Named' => 20,
            'Non-Named' => 20
        ];
        
        $stats = [];
        foreach ($dbUsers as $u) {
            // Get last training name
            $lastPelat = $pesertaPelatihanModel->select('master_pelatihan.nama')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->where('peserta_pelatihan.user_id', $u['nik'])
                ->orderBy('peserta_pelatihan.id', 'DESC')
                ->first();
            $lastPelatName = $lastPelat ? $lastPelat['nama'] : 'Belum Ada';

            // Get total JPL completed by user (where status is Lulus) IN THE SELECTED YEAR, only if certificate published
            $myCompletedPelat = $pesertaPelatihanModel->select('master_pelatihan.jpl, master_pelatihan.nama, master_pelatihan.jadwal_selesai')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->where('peserta_pelatihan.user_id', $u['nik'])
                ->where('peserta_pelatihan.status_peserta', 'Lulus')
                ->where('master_pelatihan.cert_published', 1)
                ->findAll();

            // Get approved external certificates
            $myApprovedCerts = $db->table('sertifikat_pelatihan')
                ->where('user_id', $u['nik'])
                ->where('verifikasi', 'approved')
                ->where('jenis_dokumen !=', 'rsud')
                ->get()->getResultArray();

            // Get approved RSUD certificates that do NOT have a corresponding peserta_pelatihan Lulus record
            $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
                ->select('sp.skp, sp.tgl_selesai, sp.judul')
                ->where('sp.user_id', $u['nik'])
                ->where('sp.verifikasi', 'approved')
                ->where('sp.jenis_dokumen', 'rsud')
                ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
                ->get()->getResultArray();
            
            $completedJpl = 0;
            $history = [];
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($cp['jpl'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Internal] ' . $cp['nama'],
                    'jpl' => $cp['jpl'],
                    'tanggal' => !empty($cp['jadwal_selesai']) ? date('d M Y', strtotime($cp['jadwal_selesai'])) : '-'
                ];
            }

            foreach ($myApprovedCerts as $ac) {
                $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($ac['skp'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Eksternal] ' . $ac['judul'],
                    'jpl' => $ac['skp'],
                    'tanggal' => !empty($ac['tgl_selesai']) ? date('d M Y', strtotime($ac['tgl_selesai'])) : '-'
                ];
            }

            foreach ($rsudFallbackCerts as $rc) {
                $yearOfTraining = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($rc['skp'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Internal/Fallback] ' . $rc['judul'],
                    'jpl' => $rc['skp'],
                    'tanggal' => !empty($rc['tgl_selesai']) ? date('d M Y', strtotime($rc['tgl_selesai'])) : '-'
                ];
            }

            // Sync static column only for current year calculations
            if ($selectedYear == date('Y')) {
                $userModel->update($u['nik'], ['capaian_jpl' => $completedJpl]);
            }

            // Get registration status of the last followed training
            $lastReg = $pesertaPelatihanModel->where('user_id', $u['nik'])
                ->orderBy('id', 'DESC')
                ->first();
            $regStatus = $lastReg ? $lastReg['status_peserta'] : 'Tidak Ada';

            // Calculate progress based on sessions attended vs total sessions in the last class
            $progressVal = 0;
            if ($lastReg) {
                $totalSesi = $db->table('sesi_interaktif_pelatihan')
                    ->where('pelatihan_id', $lastReg['pelatihan_id'])
                    ->countAllResults();
                
                if ($totalSesi > 0) {
                    $hadirSesi = $db->table('peserta_presensi_pelatihan')
                        ->where('peserta_pelat_id', $lastReg['id'])
                        ->where('status_hadir', 'Hadir')
                        ->countAllResults();
                    $progressVal = ($hadirSesi / $totalSesi) * 100;
                }
            }

            // Target mapping per profesi
            $kategoriTarget = $u['kategori_target'] ?: 'Non-Named';
            $targetJPLKaryawan = $u['target_jpl_profesi'] ?? 20;
            
            $stats[] = [
                'nik' => $u['nik'],
                'nama' => $u['nama_lengkap'],
                'profesi' => $u['profesi'] ?? '-',
                'kategori_target' => $kategoriTarget,
                'divisi' => $u['ruangan'] ?? 'Umum',
                'pelatihan' => $lastPelatName,
                'status_reg' => $regStatus,
                'progress' => $progressVal,
                'jpl' => $completedJpl,
                'target_jpl' => $targetJPLKaryawan,
                'history' => $history
            ];
        }

        // Room/Ruangan stats calculation
        $unitKerjaList = $db->table('unit_kerja_pelatihan')->get()->getResultArray();
        $ruanganStats = [];
        foreach ($unitKerjaList as $ruang) {
            $employeesInRoom = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.kategori_target as kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi')
                ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
                ->where('id_unit_kerja', $ruang['id_unit_kerja'])
                ->where('role', 'peserta')
                ->findAll();
            $totalEmployees = count($employeesInRoom);
            
            $followedList = [];
            $notFollowedList = [];
            foreach ($employeesInRoom as $emp) {
                // Get total JPL completed by user (where status is Lulus) IN THE SELECTED YEAR
                $myCompletedPelat = $pesertaPelatihanModel->select('master_pelatihan.jpl, master_pelatihan.jadwal_selesai')
                    ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                    ->where('peserta_pelatihan.user_id', $emp['nik'])
                    ->where('peserta_pelatihan.status_peserta', 'Lulus')
                    ->where('master_pelatihan.cert_published', 1)
                    ->findAll();
                
                // Get approved external certificates
                $myApprovedCerts = $db->table('sertifikat_pelatihan')
                    ->where('user_id', $emp['nik'])
                    ->where('verifikasi', 'approved')
                    ->where('jenis_dokumen !=', 'rsud')
                    ->get()->getResultArray();

                // Get approved RSUD certificates that do NOT have a corresponding peserta_pelatihan Lulus record
                $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
                    ->select('sp.skp, sp.tgl_selesai')
                    ->where('sp.user_id', $emp['nik'])
                    ->where('sp.verifikasi', 'approved')
                    ->where('sp.jenis_dokumen', 'rsud')
                    ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
                    ->get()->getResultArray();

                $completedJpl = 0;
                foreach ($myCompletedPelat as $cp) {
                    $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                    if ($yearOfTraining == $selectedYear) {
                        $completedJpl += (int)($cp['jpl'] ?? 0);
                    }
                }
                foreach ($myApprovedCerts as $ac) {
                    $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                    if ($yearOfTraining == $selectedYear) {
                        $completedJpl += (int)($ac['skp'] ?? 0);
                    }
                }
                foreach ($rsudFallbackCerts as $rc) {
                    $yearOfTraining = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                    if ($yearOfTraining == $selectedYear) {
                        $completedJpl += (int)($rc['skp'] ?? 0);
                    }
                }

                $targetJPLKaryawan = $emp['target_jpl_profesi'] ?? 20;
                
                $kurangVal = max(0, $targetJPLKaryawan - $completedJpl);
                $isMet = $completedJpl >= $targetJPLKaryawan;
                
                $empDetails = [
                    'nama' => $emp['nama_lengkap'],
                    'nik' => $emp['nik'],
                    'profesi' => $emp['profesi'] ?? '-',
                    'target' => $targetJPLKaryawan,
                    'jpl' => $completedJpl,
                    'kurang' => $kurangVal,
                    'status' => $isMet ? 'Tercapai' : 'Belum'
                ];

                if ($isMet) {
                    $followedList[] = $empDetails;
                } else {
                    $notFollowedList[] = $empDetails;
                }
            }
            
            $pct = $totalEmployees > 0 ? (count($followedList) / $totalEmployees) * 100 : 0;
            $ruanganStats[] = [
                'nama_unit' => $ruang['nama_unit'],
                'total' => $totalEmployees,
                'persen' => $pct,
                'sudah' => $followedList,
                'belum' => $notFollowedList
            ];
        }

        // Load all professions to let admin classify them
        $profesiList = $db->table('profesi_pelatihan')->get()->getResultArray();

        // Summary data
        $totalKaryawan = count($stats);
        $totalTargetJPL = array_sum(array_column($stats, 'target_jpl'));
        $totalJPLCapaian = array_sum(array_column($stats, 'jpl'));
        $totalKurangJPL = max(0, $totalTargetJPL - $totalJPLCapaian);
        $totalTidakAktif = count(array_filter($stats, fn($s) => $s['pelatihan'] === 'Belum Ada'));

        $data = [
            'title' => 'Monitoring & Matriks JPL',
            'stats' => $stats,
            'selectedYear' => $selectedYear,
            'targetKelompok' => $targetKelompok,
            'profesiList' => $profesiList,
            'ruanganStats' => $ruanganStats,
            'totalKaryawan' => $totalKaryawan,
            'totalTargetJPL' => $totalTargetJPL,
            'totalJPLCapaian' => $totalJPLCapaian,
            'totalKurangJPL' => $totalKurangJPL,
            'totalTidakAktif' => $totalTidakAktif
        ];
        return view('pelatihan/admin/monitoring/index', $data);
    }

    public function set_target($userId)
    {
        // individual target override (if needed)
        return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Konfigurasi target berhasil diperbarui.');
    }

    public function jpl_history(string $userId)
    {
        $db = \Config\Database::connect();
        $selectedYear = $this->request->getGet('tahun') ?? date('Y');

        // 1. Internal completed trainings
        $myCompletedPelat = $db->table('peserta_pelatihan')
            ->select('master_pelatihan.nama as judul, master_pelatihan.jpl as skp, master_pelatihan.jadwal_selesai as tgl_selesai, "rsud" as jenis')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.user_id', $userId)
            ->where('peserta_pelatihan.status_peserta', 'Lulus')
            ->where('master_pelatihan.cert_published', 1)
            ->get()->getResultArray();

        // 2. External / Mandiri
        $myApprovedCerts = $db->table('sertifikat_pelatihan')
            ->select('judul, skp, tgl_selesai, jenis_dokumen as jenis')
            ->where('user_id', $userId)
            ->where('verifikasi', 'approved')
            ->where('jenis_dokumen !=', 'rsud')
            ->get()->getResultArray();

        // 3. Fallback RSUD certificates
        $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
            ->select('sp.judul, sp.skp, sp.tgl_selesai, "rsud" as jenis')
            ->where('sp.user_id', $userId)
            ->where('sp.verifikasi', 'approved')
            ->where('sp.jenis_dokumen', 'rsud')
            ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
            ->get()->getResultArray();

        $allHistory = array_merge($myCompletedPelat, $myApprovedCerts, $rsudFallbackCerts);
        
        $aktif = [];
        $kadaluarsa = [];

        foreach ($allHistory as $h) {
            $year = !empty($h['tgl_selesai']) ? date('Y', strtotime($h['tgl_selesai'])) : date('Y');
            if ($year == $selectedYear) {
                $aktif[] = $h;
            } else {
                $kadaluarsa[] = $h;
            }
        }

        return $this->response->setJSON([
            'aktif' => $aktif,
            'kadaluarsa' => $kadaluarsa
        ]);
    }

    public function remind_individual()
    {
        $json = $this->request->getJSON();
        $nik = $json->nik ?? '';
        $message = $json->message ?? 'Pengingat capaian JPL tahunan Anda.';
        
        if (empty($nik)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'NIK tidak valid.']);
        }
        
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();
        $notifModel->insert([
            'user_id' => $nik,
            'title' => 'Peringatan Capaian JPL',
            'message' => $message,
            'type' => 'warning',
            'is_read' => 0
        ]);
        
        $userModel = new \App\Models\Pelatihan\UserPelatihanModel();
        $user = $userModel->where('nik', $nik)->first();
        if ($user && !empty($user['email'])) {
            $emailService = \Config\Services::email();
            $emailService->setTo($user['email']);
            $emailService->setSubject('Peringatan Capaian JPL');
            $emailService->setMessage($message);
            $emailService->send();
        }
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function broadcast_room()
    {
        $json = $this->request->getJSON();
        $niks = $json->niks ?? [];
        $message = $json->message ?? 'Pengingat capaian JPL tahunan Anda.';
        
        if (empty($niks)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada peserta dipilih.']);
        }
        
        $userModel = new \App\Models\Pelatihan\UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();
        $db = \Config\Database::connect();
        
        $targetKelompok = $this->session->get('target_kelompok') ?? [
            'Named' => 20,
            'Non-Named' => 20
        ];
        
        $sentCount = 0;
        foreach ($niks as $nik) {
            $user = $userModel->select('users_pelatihan.*, profesi_pelatihan.kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi')
                ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
                ->where('users_pelatihan.nik', $nik)
                ->first();
            if (!$user) continue;
            
            $myCompletedPelat = $pesertaPelatihanModel->select('master_pelatihan.jpl, master_pelatihan.jadwal_selesai')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->where('peserta_pelatihan.user_id', $nik)
                ->where('peserta_pelatihan.status_peserta', 'Lulus')
                ->where('master_pelatihan.cert_published', 1)
                ->findAll();
            
            $myApprovedCerts = $db->table('sertifikat_pelatihan')
                ->where('user_id', $nik)
                ->where('verifikasi', 'approved')
                ->where('jenis_dokumen !=', 'rsud')
                ->get()->getResultArray();
            
            $completedJpl = 0;
            $currentYear = date('Y');
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $currentYear) $completedJpl += (int)($cp['jpl'] ?? 0);
            }
            foreach ($myApprovedCerts as $ac) {
                $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $currentYear) $completedJpl += (int)($ac['skp'] ?? 0);
            }
            
            $targetJPL = $user['target_jpl_profesi'] ?? 20;
            $kurangVal = max(0, $targetJPL - $completedJpl);
            
            $personalized = str_replace(
                ['{nama}', '{jpl}', '{target}', '{kurang}'],
                [$user['nama_lengkap'], $completedJpl, $targetJPL, $kurangVal],
                $message
            );
            
            $notifModel->insert([
                'user_id' => $nik,
                'title' => 'Peringatan Capaian JPL - Room Broadcast',
                'message' => $personalized,
                'type' => 'warning',
                'is_read' => 0
            ]);
            
            if (!empty($user['email'])) {
                $emailService = \Config\Services::email();
                $emailService->setTo($user['email']);
                $emailService->setSubject('Peringatan Capaian JPL - Room Broadcast');
                $emailService->setMessage($personalized);
                $emailService->send();
            }
            
            $sentCount++;
        }
        
        return $this->response->setJSON(['status' => 'success', 'count' => $sentCount]);
    }

    public function save_mapping_kelompok()
    {
        $profesiTarget = $this->request->getPost('profesi_target'); // e.g. [1 => 20, 2 => 20]
        $db = \Config\Database::connect();
        
        if (!empty($profesiTarget)) {
            foreach ($profesiTarget as $idProf => $target) {
                $db->table('profesi_pelatihan')
                    ->where('id_profesi', $idProf)
                    ->update(['target_jpl' => $target]);
            }
        }
        
        return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Target JPL profesi berhasil disimpan.')->with('active_tab', 'matriks');
    }

    public function broadcast_notifikasi()
    {
        $divisi = $this->request->getPost('divisi');
        $statusJpl = $this->request->getPost('status_jpl');
        $message = $this->request->getPost('message') ?: 'Pemberitahuan penting mengenai capaian program diklat Anda.';
        
        $userModel = new \App\Models\Pelatihan\UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();

        // Query users based on filters
        $query = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.kategori_target as kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta');
            
        if (!empty($divisi)) {
            $query->where('unit_kerja_pelatihan.nama_unit', $divisi);
        }
        
        $usersList = $query->findAll();
        
        $targetKelompok = $this->session->get('target_kelompok') ?? [
            'Named' => 20,
            'Non-Named' => 20
        ];

        $sentCount = 0;
        foreach ($usersList as $u) {
            // Get total JPL completed by user (where status is Lulus) in current year
            $myCompletedPelat = $pesertaPelatihanModel->select('master_pelatihan.jpl, master_pelatihan.jadwal_selesai')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->where('peserta_pelatihan.user_id', $u['nik'])
                ->where('peserta_pelatihan.status_peserta', 'Lulus')
                ->where('master_pelatihan.cert_published', 1)
                ->findAll();
            
            $completedJpl = 0;
            $currentYear = date('Y');
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $currentYear) {
                    $completedJpl += (int)($cp['jpl'] ?? 0);
                }
            }

            $targetJPLKaryawan = $u['target_jpl_profesi'] ?? 20;
            $isMet = $completedJpl >= $targetJPLKaryawan;
            
            if ($statusJpl === 'Belum Memenuhi' && $isMet) continue;
            if ($statusJpl === 'Terpenuhi' && !$isMet) continue;

            $kurangVal = max(0, $targetJPLKaryawan - $completedJpl);
            
            $personalized = str_replace(
                ['{nama}', '{jpl}', '{target}', '{kurang}'],
                [$u['nama_lengkap'], $completedJpl, $targetJPLKaryawan, $kurangVal],
                $message
            );
            
            $notifModel->insert([
                'user_id' => $u['nik'],
                'title' => 'Broadcast Capaian JPL',
                'message' => $personalized,
                'type' => 'info',
                'is_read' => 0
            ]);
            
            if (!empty($u['email'])) {
                $emailService = \Config\Services::email();
                $emailService->setTo($u['email']);
                $emailService->setSubject('Broadcast Capaian JPL');
                $emailService->setMessage($personalized);
                $emailService->send();
            }
            
            $sentCount++;
        }
        
        return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Broadcast berhasil dikirim ke ' . $sentCount . ' peserta.');
    }

    public function reminder(string $userId)
    {
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();
        $message = 'Capaian JPL tahunan Anda masih di bawah target minimal. Harap segera ikuti program diklat yang tersedia.';
        $notifModel->insert([
            'user_id' => $userId,
            'title' => 'Peringatan Capaian JPL',
            'message' => $message,
            'type' => 'warning',
            'is_read' => 0
        ]);
        
        $userModel = new \App\Models\Pelatihan\UserPelatihanModel();
        $user = $userModel->where('nik', $userId)->first();
        if ($user && !empty($user['email'])) {
            $emailService = \Config\Services::email();
            $emailService->setTo($user['email']);
            $emailService->setSubject('Peringatan Capaian JPL');
            $emailService->setMessage($message);
            $emailService->send();
        }
        
        return redirect()->back()->with('success', 'Pengingat telah dikirim ke notifikasi dan email peserta.');
    }

    public function akun_peserta()
    {
        $userModel = new UserPelatihanModel();
        $unitKerjaModel = new UnitKerjaPelatihanModel();
        $profesiModel = new ProfesiPelatihanModel();

        $allUsers = $userModel->getUserWithRelations();

        $named = [];
        $non_named = [];

        foreach ($allUsers as $u) {
            if (in_array($u['role'], ['admin', 'admin_pengabdian'])) {
                continue;
            }

            $mapped = [
                'id'      => $u['nik'], // Use NIK as identifier
                'nama'    => $u['nama_lengkap'],
                'email'   => $u['email'],
                'nik'     => $u['nik'],
                'wa'      => $u['no_wa'],
                'profesi' => $u['nama_profesi'] ?? '-',
                'ruangan' => $u['nama_unit'] ?? 'Umum',
                'instansi'=> $u['nama_unit'] ?? 'RSUD Kota Yogyakarta',
                'status'  => $u['status'],
                'id_unit_kerja' => $u['id_unit_kerja'],
                'id_profesi'    => $u['id_profesi'],
                'role'          => $u['role'],
                'jenis_peserta' => $u['jenis_peserta'],
                'target_jpl'    => $u['profesi_target_jpl'] ?? 20,
                'capaian_jpl'   => $u['capaian_jpl'] ?? 0
            ];

            if ($u['jenis_peserta'] === 'named') {
                $named[] = $mapped;
            } else {
                $non_named[] = $mapped;
            }
        }

        $data = [
            'title'           => 'Manajemen Akun Peserta',
            'users_named'     => $named,
            'users_non_named' => $non_named,
            'unit_kerja'      => $unitKerjaModel->findAll(),
            'profesi'         => $profesiModel->findAll()
        ];

        return view('pelatihan/admin/manajemen_peserta/index', $data);
    }

    public function tambah_akun()
    {
        $roleInput = $this->request->getPost('role'); // admin, admin_pengabdian, named, nonnamed
        
        $rules = [
            'nik'      => 'required|numeric|exact_length[16]|is_unique[users_pelatihan.nik]',
            'nama'     => 'required|min_length[3]|max_length[150]|regex_match[/^[a-zA-Z\s.,\']+$/]',
            'email'    => 'required|valid_email|regex_match[/^[a-zA-Z0-9._%+-]+@(gmail\.com|students\.ukcw\.ac\.id|[a-zA-Z0-9.-]+\.go\.id)$/i]|is_unique[users_pelatihan.email]',
            'wa'       => 'required|numeric|min_length[10]|max_length[15]',
        ];

        $customErrors = [
            'nik' => [
                'numeric'      => 'NIK harus berupa angka murni.',
                'exact_length' => 'NIK harus tepat 16 digit.',
                'is_unique'    => 'NIK sudah terdaftar.'
            ],
            'nama' => [
                'regex_match'  => 'Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip.'
            ],
            'email' => [
                'valid_email'  => 'Format email harus valid.',
                'regex_match'  => 'Email harus menggunakan domain @gmail.com, @students.ukcw.ac.id, atau domain instansi pemerintah (.go.id).',
                'is_unique'    => 'Email sudah terdaftar.'
            ],
            'wa' => [
                'numeric'      => 'No. WhatsApp harus berupa angka murni.',
                'min_length'   => 'No. WhatsApp minimal 10 digit.',
                'max_length'   => 'No. WhatsApp maksimal 15 digit.'
            ]
        ];

        if (!$this->validate($rules, $customErrors)) {
            $errors = $this->validator->getErrors();
            $errorString = implode('<br>', $errors);
            return redirect()->back()->withInput()->with('error', $errorString);
        }

        // Map role and jenis_peserta based on select choice
        $dbRole = 'peserta';
        $jenisPeserta = 'named';

        if ($roleInput === 'admin') {
            $dbRole = 'admin';
        } elseif ($roleInput === 'admin_pengabdian') {
            $dbRole = 'admin_pengabdian';
        } elseif ($roleInput === 'nonnamed') {
            $jenisPeserta = 'non_named';
        }

        $idUnitKerja = $this->request->getPost('id_unit_kerja');
        $idProfesi = $this->request->getPost('id_profesi');

        if ($jenisPeserta === 'non_named') {
             $idUnitKerja = $this->request->getPost('id_unit_kerja');
             $idProfesi = $this->request->getPost('id_profesi');
        }

        // Generate random password
        $randomPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        $userData = [
            'nik'           => $this->request->getPost('nik'),
            'nama_lengkap'  => $this->request->getPost('nama'),
            'email'         => $this->request->getPost('email'),
            'no_wa'         => $this->request->getPost('wa'),
            'jenis_peserta' => $jenisPeserta,
            'role'          => $dbRole,
            'id_unit_kerja' => $idUnitKerja ?: null,
            'id_profesi'    => $idProfesi ?: null,
            'password'      => password_hash($randomPassword, PASSWORD_BCRYPT),
            'status'        => 'aktif'
        ];

        $userModel = new UserPelatihanModel();
        if ($userModel->insert($userData)) {
            // Send email
            $emailService = \Config\Services::email();
            $emailService->setTo($userData['email']);
            $emailService->setFrom('noreply@rsudjogja.id', 'RSUD Yogyakarta');
            $emailService->setSubject('Informasi Akun SIM Diklat KP');
            
            $message = "<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>";
            $message .= "<h2 style='color: #ce2127;'>Selamat Datang di SIM Diklat KP</h2>";
            $message .= "<p>Halo <b>" . esc($userData['nama_lengkap']) . "</b>,</p>";
            $message .= "<p>Akun Anda telah berhasil didaftarkan oleh Admin. Berikut adalah detail login Anda:</p>";
            $message .= "<table style='border-collapse: collapse; width: 100%; max-width: 400px;'>";
            $message .= "<tr><td style='padding: 8px; border: 1px solid #ddd;'><b>Username / NIK</b></td><td style='padding: 8px; border: 1px solid #ddd; font-family: monospace;'>" . esc($userData['nik']) . "</td></tr>";
            $message .= "<tr><td style='padding: 8px; border: 1px solid #ddd;'><b>Password</b></td><td style='padding: 8px; border: 1px solid #ddd; font-family: monospace;'>" . esc($randomPassword) . "</td></tr>";
            $message .= "</table>";
            $message .= "<p style='margin-top: 20px;'>Harap segera ubah password Anda setelah berhasil login untuk keamanan akun Anda.</p>";
            $message .= "<a href='" . base_url('pelatihan/peserta/login') . "' style='display: inline-block; padding: 10px 20px; color: #fff; background-color: #ce2127; text-decoration: none; border-radius: 5px;'>Login Sekarang</a>";
            $message .= "<p style='margin-top: 20px; font-size: 0.9em; color: #666;'>Terima kasih,<br>Tim Diklat RSUD Kota Yogyakarta</p>";
            $message .= "</div>";
            
            $emailService->setMessage($message);
            $emailService->send();

            return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil ditambahkan dan password telah dikirim ke email peserta.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan akun. Silakan coba lagi.');
        }
    }

    public function edit_akun()
    {
        $id = $this->request->getPost('id'); // original NIK
        $newNik = $this->request->getPost('nik');
        $newEmail = $this->request->getPost('email');

        $userModel = new UserPelatihanModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[3]|max_length[150]|regex_match[/^[a-zA-Z\s.,\']+$/]',
            'wa'       => 'required|numeric|min_length[10]|max_length[15]',
        ];

        // Add conditional rules if NIK or Email has changed
        if ($newNik !== $id) {
            $rules['nik'] = 'required|numeric|exact_length[16]|is_unique[users_pelatihan.nik]';
        } else {
            $rules['nik'] = 'required|numeric|exact_length[16]';
        }

        if ($newEmail !== $user['email']) {
            $rules['email'] = 'required|valid_email|regex_match[/^[a-zA-Z0-9._%+-]+@(gmail\.com|students\.ukcw\.ac\.id|[a-zA-Z0-9.-]+\.go\.id)$/i]|is_unique[users_pelatihan.email]';
        } else {
            $rules['email'] = 'required|valid_email|regex_match[/^[a-zA-Z0-9._%+-]+@(gmail\.com|students\.ukcw\.ac\.id|[a-zA-Z0-9.-]+\.go\.id)$/i]';
        }


        $customErrors = [
            'nik' => [
                'numeric'      => 'NIK harus berupa angka murni.',
                'exact_length' => 'NIK harus tepat 16 digit.',
                'is_unique'    => 'NIK sudah terdaftar.'
            ],
            'nama' => [
                'regex_match'  => 'Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip.'
            ],
            'email' => [
                'valid_email'  => 'Format email harus valid.',
                'regex_match'  => 'Email harus menggunakan domain @gmail.com, @students.ukcw.ac.id, atau domain instansi pemerintah (.go.id).',
                'is_unique'    => 'Email sudah terdaftar.'
            ],
            'wa' => [
                'numeric'      => 'No. WhatsApp harus berupa angka murni.',
                'min_length'   => 'No. WhatsApp minimal 10 digit.',
                'max_length'   => 'No. WhatsApp maksimal 15 digit.'
            ]
        ];

        if (!$this->validate($rules, $customErrors)) {
            $errors = $this->validator->getErrors();
            $errorString = implode('<br>', $errors);
            return redirect()->back()->withInput()->with('error', $errorString);
        }

        $idUnitKerja = $this->request->getPost('id_unit_kerja');
        $idProfesi = $this->request->getPost('id_profesi');
        $jenisPeserta = $this->request->getPost('jenis_peserta');
        $roleInput = $this->request->getPost('role'); // admin, admin_pengabdian, peserta

        $updateData = [
            'nama_lengkap'  => $this->request->getPost('nama'),
            'email'         => $this->request->getPost('email'),
            'no_wa'         => $this->request->getPost('wa'),
            'id_unit_kerja' => $idUnitKerja ?: null,
            'id_profesi'    => $idProfesi ?: null
        ];

        if (!empty($jenisPeserta)) {
            $updateData['jenis_peserta'] = $jenisPeserta;
        }

        if (!empty($roleInput)) {
            // Normalize role value
            $validRoles = ['admin', 'admin_pengabdian', 'peserta'];
            if (in_array($roleInput, $validRoles)) {
                $updateData['role'] = $roleInput;
            }
        }


        // If NIK is updated, CodeIgniter's manual primary key update is safer to handle by transaction
        if ($newNik !== $id) {
            $updateData['nik'] = $newNik;
            $db = \Config\Database::connect();
            $db->transBegin();
            
            // Insert updated user
            $newUser = array_merge($user, $updateData);
            $userModel->insert($newUser);
            
            // Delete old user
            $userModel->delete($id);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal memperbarui NIK.');
            } else {
                $db->transCommit();
                return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil diperbarui.');
            }
        }

        if ($userModel->update($id, $updateData)) {
            return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui akun.');
        }
    }

    public function toggle_status(string $nik)
{
    $userModel = new UserPelatihanModel();
    $user = $userModel->find($nik);

    if (!$user) {
        return redirect()->back()->with('error', 'Akun tidak ditemukan.');
    }

    $newStatus = ($user['status'] === 'aktif') ? 'nonaktif' : 'aktif';

    if ($userModel->update($nik, ['status' => $newStatus])) {
        // HANYA kirim satu pesan sukses
        return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Status akun berhasil diubah.');
    } else {
        return redirect()->back()->with('error', 'Gagal mengubah status akun.');
    }
}

    public function delete_akun(string $nik)
    {
        $userModel = new UserPelatihanModel();
        if ($userModel->delete($nik)) {
            return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus akun.');
        }
    }

    public function monitoring_peserta()
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();

        $pelatihan = $masterPelatihanModel->orderBy('nama', 'ASC')->findAll();
        
        $list = [];
        foreach ($pelatihan as $p) {
            $count = $pesertaModel->where('pelatihan_id', $p['id'])->countAllResults();
            $list[] = array_merge($p, ['total_peserta' => $count]);
        }

        return view('pelatihan/admin/monitoring/kelas', ['title' => 'Monitoring Progress Diklat', 'list' => $list]);
    }

    public function presensi(string $pelatihanId)
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $sesiModel = new \App\Models\Pelatihan\SesiInteraktifPelatihanModel();
        $db = \Config\Database::connect();

        $pelatihan = $masterPelatihanModel->find($pelatihanId);
        if (!$pelatihan) {
            return redirect()->to('/pelatihan/admin/monitoring_peserta');
        }

        // Fetch sessions
        $sesi_list = $sesiModel->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->findAll();

        // Fetch participants (joined with users_pelatihan, profesi_pelatihan, unit_kerja_pelatihan)
        $peserta = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.email, profesi_pelatihan.nama_profesi as profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('peserta_pelatihan.pelatihan_id', $pelatihanId)
            ->findAll();

        // Map presence status
        foreach ($peserta as &$p) {
            $p['kehadiran'] = [];
            $p['hadir_count'] = 0;
            
            foreach ($sesi_list as $s) {
                $presensi = $db->table('peserta_presensi_pelatihan')
                    ->where('peserta_pelat_id', $p['id'])
                    ->where('sesi_id', $s['id'])
                    ->get()->getRowArray();
                
                $status = $presensi ? $presensi['status_hadir'] : 'Alfa';
                $p['kehadiran'][$s['id']] = $status;
                if ($status == 'Hadir' || $status == 'Izin') {
                    $p['hadir_count']++;
                }
            }

            $p['progress'] = $p['progress'] ?? 0;
        }

        $data = [
            'title' => 'Monitoring Presensi',
            'pelatihan' => $pelatihan,
            'sesi_list' => $sesi_list,
            'peserta' => $peserta
        ];

        return view('pelatihan/admin/monitoring/presensi', $data);
    }

    public function toggle_presensi()
    {
        $userId = $this->request->getPost('user_id'); // NIK
        $pelatihanId = $this->request->getPost('pelatihan_id');
        $sesiId = $this->request->getPost('sesi'); // sesi_id
        $rawStatus = $this->request->getPost('status');
        $statusMap = ['hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'];
        $status = $statusMap[$rawStatus] ?? 'Alfa';

        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $peserta = $pesertaModel->where('user_id', $userId)
            ->where('pelatihan_id', $pelatihanId)
            ->first();

        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Peserta tidak ditemukan']);
        }

        $db = \Config\Database::connect();
        $exist = $db->table('peserta_presensi_pelatihan')
            ->where('peserta_pelat_id', $peserta['id'])
            ->where('sesi_id', $sesiId)
            ->get()->getRowArray();

        if ($exist) {
            $db->table('peserta_presensi_pelatihan')
                ->where('id', $exist['id'])
                ->update(['status_hadir' => $status, 'waktu_absen' => date('Y-m-d H:i:s')]);
        } else {
            $db->table('peserta_presensi_pelatihan')->insert([
                'peserta_pelat_id' => $peserta['id'],
                'sesi_id' => $sesiId,
                'status_hadir' => $status,
                'waktu_absen' => date('Y-m-d H:i:s')
            ]);
        }

        $this->_updatePresensiProgress($db, $peserta, $pelatihanId, $sesiId, $status);

        return $this->response->setJSON(['status' => 'success']);
    }

    private function _findPresensiStepId($db, $pelatihanId, $sesiId): ?int
    {
        $stepCounter = 1;
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) $stepCounter++;

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        foreach ($sesi as $s) {
            if ((int)$s['id'] === (int)$sesiId) {
                return $stepCounter;
            }
            $stepCounter++; // presensi or sesi
            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->get()->getResultArray();
            $groupedSegmen = [];
            foreach ($materi as $m) { $groupedSegmen[$m['segmen'] ?: 1][] = $m; }
            $stepCounter += count($groupedSegmen);
            $stepCounter++; // evaluasi_sesi
        }
        return null;
    }

    private function _countTotalSteps($db, $pelatihanId): int
    {
        $stepCounter = 1;
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) $stepCounter++;

        $sesi = $db->table('sesi_interaktif_pelatihan')->where('pelatihan_id', $pelatihanId)->get()->getResultArray();
        foreach ($sesi as $s) {
            $stepCounter++; // presensi or sesi
            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->get()->getResultArray();
            $groupedSegmen = [];
            foreach ($materi as $m) { $groupedSegmen[$m['segmen'] ?: 1][] = $m; }
            $stepCounter += count($groupedSegmen);
            $stepCounter++; // evaluasi_sesi
        }

        $postTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
        if ($postTest) $stepCounter++;
        $stepCounter++; // evaluasi
        $stepCounter++; // sertifikat
        return $stepCounter - 1;
    }

    private function _updatePresensiProgress($db, $peserta, $pelatihanId, $sesiId, $status)
    {
        $stepId = $this->_findPresensiStepId($db, $pelatihanId, $sesiId);
        if (!$stepId) return;

        $completedSteps = json_decode($peserta['completed_steps'] ?? '[]', true) ?? [];
        $totalSteps = $this->_countTotalSteps($db, $pelatihanId);

        if (in_array($status, ['Hadir', 'Izin'])) {
            if (!in_array((int)$stepId, $completedSteps)) {
                $completedSteps[] = (int)$stepId;
            }
        } else {
            $completedSteps = array_values(array_filter($completedSteps, fn($s) => (int)$s !== (int)$stepId));
        }

        $progressPct = $totalSteps > 0 ? (count($completedSteps) / $totalSteps) * 100 : 0;

        $db->table('peserta_pelatihan')
            ->where('id', $peserta['id'])
            ->update([
                'completed_steps' => json_encode($completedSteps),
                'progress' => $progressPct,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    } 
    public function hapus_pendaftaran(string $userId, string $pelatihanId)
    {
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $pesertaModel->where('user_id', $userId)
            ->where('pelatihan_id', $pelatihanId)
            ->delete();

        return redirect()->back()->with('success', 'Peserta berhasil dihapus dari pelatihan.');
    }

    public function add_peserta($pelatihanId = null)
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $userPelatihanModel = new UserPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        
        $pelatihan = $masterPelatihanModel->orderBy('nama', 'ASC')->findAll();
        
        // Fetch users who are NOT admins, with their relations
        $users = $userPelatihanModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta')
            ->findAll();
        
        // Fetch registered NIKs in this training to disable check
        $registeredNiks = [];
        if ($pelatihanId) {
            $registered = $pesertaModel->where('pelatihan_id', $pelatihanId)->findAll();
            $registeredNiks = array_column($registered, 'user_id');
        }
        
        $data = [
            'title' => 'Tambah Peserta Ke Pelatihan',
            'pelatihan' => $pelatihan,
            'users' => $users,
            'selectedId' => $pelatihanId,
            'registeredNiks' => $registeredNiks
        ];
        return view('pelatihan/admin/monitoring/add_peserta', $data);
    }

    public function save_peserta()
    {
        $pelatihanId = $this->request->getPost('pelatihan_id');
        $userIds = $this->request->getPost('user_ids') ?? [];
        
        if (empty($pelatihanId) || empty($userIds)) {
            return redirect()->back()->with('error', 'Silakan pilih pelatihan dan peserta.');
        }
 
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        
        foreach ($userIds as $nik) {
            $exists = $pesertaModel->where('user_id', $nik)
                ->where('pelatihan_id', $pelatihanId)
                ->first();
            
            if (!$exists) {
                $pesertaModel->insert([
                    'user_id' => $nik,
                    'pelatihan_id' => $pelatihanId,
                    'status_peserta' => 'Daftar',
                    'waktu_daftar' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        return redirect()->to('/pelatihan/admin/presensi/'.$pelatihanId)->with('success', count($userIds) . ' Peserta berhasil ditambahkan.');
    }

    public function export_excel(string $pelatihanId)
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $db = \Config\Database::connect();

        $pelatihan = $masterPelatihanModel->find($pelatihanId);
        if (!$pelatihan) {
            return redirect()->to('/pelatihan/admin/monitoring_peserta');
        }

        $countPeserta = $pesertaModel->where('pelatihan_id', $pelatihanId)->countAllResults();

        // Spreadsheet setup
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // === SHEET 1: INFO PELATIHAN ===
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Info Pelatihan');
        
        $sheet1->setCellValue('A1', 'INFORMASI PELATIHAN: ' . $pelatihan['nama']);
        $sheet1->mergeCells('A1:B1');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet1->setCellValue('A3', 'Nama Pelatihan');
        $sheet1->setCellValue('B3', $pelatihan['nama']);
        
        $sheet1->setCellValue('A4', 'Metode & Lokasi');
        $sheet1->setCellValue('B4', ($pelatihan['metode'] ?? '-') . ' - ' . ($pelatihan['lokasi'] ?? '-'));
        
        $sheet1->setCellValue('A5', 'Kuota');
        $sheet1->setCellValue('B5', $pelatihan['kuota']);
        
        $sheet1->setCellValue('A6', 'Jumlah Peserta');
        $sheet1->setCellValue('B6', $countPeserta);
        
        $sheet1->setCellValue('A7', 'Tema / Program');
        $sheet1->setCellValue('B7', $pelatihan['program'] ?? '-');
        
        $sheet1->setCellValue('A8', 'Level & Cakupan');
        $sheet1->setCellValue('B8', ($pelatihan['level_pelatihan'] ?? '-') . ' - ' . ($pelatihan['cakupan'] ?? '-'));
        
        $sheet1->setCellValue('A9', 'Jadwal Pelaksanaan');
        $jadwal = ($pelatihan['jadwal_mulai'] ?? '') . ' s.d ' . ($pelatihan['jadwal_selesai'] ?? '');
        $sheet1->setCellValue('B9', $jadwal);
        
        $sheet1->setCellValue('A10', 'Tujuan');
        $sheet1->setCellValue('B10', strip_tags($pelatihan['tujuan'] ?? '-'));
        
        $sheet1->setCellValue('A11', 'Kompetensi');
        $sheet1->setCellValue('B11', strip_tags($pelatihan['kompetensi'] ?? '-'));

        $narasumberList = $db->table('narasumber_pelatihan')
            ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id')
            ->where('narasumber_pelatihan.pelatihan_id', $pelatihanId)
            ->get()->getResultArray();
        $narasumberText = [];
        foreach($narasumberList as $n) { $narasumberText[] = $n['nama_pejabat']; }
        $sheet1->setCellValue('A12', 'Narasumber');
        $sheet1->setCellValue('B12', implode(', ', $narasumberText) ?: '-');

        // Kurikulum Materi
        $sheet1->setCellValue('A14', 'Kurikulum Materi');
        $sheet1->getStyle('A14')->getFont()->setBold(true);
        $rowK = 15;
        $noK = 1;

        // Pre Test
        $sheet1->setCellValue('A'.$rowK, $noK++);
        $sheet1->setCellValue('B'.$rowK, "Pre Test");
        $rowK++;

        // Sesi
        $sesi = $db->table('sesi_interaktif_pelatihan')->where('pelatihan_id', $pelatihanId)->orderBy('tanggal', 'ASC')->get()->getResultArray();
        foreach ($sesi as $s) {
            $sheet1->setCellValue('A'.$rowK, $noK++);
            $sheet1->setCellValue('B'.$rowK, "Sesi " . ucfirst($s['tipe_sesi']) . ": " . $s['nama_sesi'] . " (" . $s['tanggal'] . ")");
            $rowK++;
        }

        // Materi
        $materi = $db->table('materi_pelatihan')->where('pelatihan_id', $pelatihanId)->orderBy('urutan', 'ASC')->get()->getResultArray();
        foreach ($materi as $m) {
            $sheet1->setCellValue('A'.$rowK, $noK++);
            $sheet1->setCellValue('B'.$rowK, "Materi: " . $m['judul']);
            $rowK++;
        }

        // Post Test
        $sheet1->setCellValue('A'.$rowK, $noK++);
        $sheet1->setCellValue('B'.$rowK, "Post Test");
        $rowK++;

        // Kuesioner
        $kuesionerCount = $db->table('kuesioner_master_pelatihan')->where('pelatihan_id', $pelatihanId)->countAllResults();
        if ($kuesionerCount > 0) {
            $sheet1->setCellValue('A'.$rowK, $noK++);
            $sheet1->setCellValue('B'.$rowK, "Evaluasi Pelatihan (Kuesioner)");
            $rowK++;
        }

        $sheet1->setCellValue('A'.$rowK, $noK++);
        $sheet1->setCellValue('B'.$rowK, "Sertifikat Kelulusan");

        $sheet1->getColumnDimension('A')->setWidth(25);
        $sheet1->getColumnDimension('B')->setWidth(80);
        $sheet1->getStyle('A3:A12')->getFont()->setBold(true);

        // === SHEET 2: PESERTA ===
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Data Peserta');

        $headers = [
            'No', 'Role Sistem', 'Status Aktivasi', 'Nama Lengkap', 'NIK', 'Email', 
            'No WhatsApp', 'Unit Kerja', 'Profesi', 'Target JPL', 'Capaian JPL', 
            'Nilai Pre Test', 'Nilai Post Test', 'Status Kelulusan'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $sheet2->getStyle($col . '1')->getFont()->setBold(true);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $peserta = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.email, users_pelatihan.no_wa, users_pelatihan.role, users_pelatihan.status, users_pelatihan.capaian_jpl, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.target_jpl, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('peserta_pelatihan.pelatihan_id', $pelatihanId)
            ->findAll();

        $row = 2;
        $no = 1;
        foreach ($peserta as $p) {
            // Pre-test
            $pre = $db->table('peserta_ujian_pelatihan')
                      ->where('peserta_pelat_id', $p['id'])
                      ->where('tipe_ujian', 'pre_test')
                      ->get()->getRowArray();
            $nilaiPre = $pre ? $pre['score'] : '-';

            // Post-test
            $post = $db->table('peserta_ujian_pelatihan')
                      ->where('peserta_pelat_id', $p['id'])
                      ->where('tipe_ujian', 'post_test')
                      ->get()->getRowArray();
            $nilaiPost = $post ? $post['score'] : '-';

            $kkm = $pelatihan['kkm'] ?? 70;
            $statusKelulusan = ($nilaiPost !== '-' && $nilaiPost >= $kkm) ? 'LULUS' : 'TIDAK LULUS';
            if ($nilaiPost === '-') $statusKelulusan = 'BELUM UJIAN';

            $sheet2->setCellValue('A'.$row, $no++);
            $sheet2->setCellValue('B'.$row, $p['role'] ?? 'Peserta');
            $sheet2->setCellValue('C'.$row, $p['status'] ?? 'Aktif');
            $sheet2->setCellValue('D'.$row, $p['nama']);
            $sheet2->setCellValueExplicit('E'.$row, $p['user_id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet2->setCellValue('F'.$row, $p['email']);
            $sheet2->setCellValueExplicit('G'.$row, $p['no_wa'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet2->setCellValue('H'.$row, $p['ruangan'] ?? '-');
            $sheet2->setCellValue('I'.$row, $p['profesi'] ?? '-');
            $sheet2->setCellValue('J'.$row, $p['target_jpl'] ?? 0);
            $sheet2->setCellValue('K'.$row, $p['capaian_jpl'] ?? 0);
            $sheet2->setCellValue('L'.$row, $nilaiPre);
            $sheet2->setCellValue('M'.$row, $nilaiPost);
            $sheet2->setCellValue('N'.$row, $statusKelulusan);
            
            $row++;
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Data_Monitoring_Pelatihan_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
