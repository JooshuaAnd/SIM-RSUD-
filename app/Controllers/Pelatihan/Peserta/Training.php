<?php
namespace App\Controllers\Pelatihan\Peserta;
use App\Controllers\BaseController;

class Training extends BaseController
{
    public function daftar($id)
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/pelatihan/login');
        }

        $db = \Config\Database::connect();
        
        $item = $db->table('master_pelatihan')
            ->where('id', $id)
            ->where('status !=', 'Batal')
            ->get()->getRowArray();
        if (!$item) return redirect()->to('/pelatihan/peserta/pembelajaran')->with('error', 'Pelatihan sudah dibatalkan atau tidak tersedia.');

        if ((int) ($item['cert_published'] ?? 0) === 1) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/' . $id)
                ->with('error', 'Pendaftaran pelatihan sudah ditutup karena sertifikat telah diterbitkan.');
        }

        $now = date('Y-m-d H:i:s');
        $regBuka = $item['reg_buka_tgl'] . ' ' . ($item['reg_buka_jam'] ?: '00:00:00');
        $regTutup = $item['reg_tutup_tgl'] . ' ' . ($item['reg_tutup_jam'] ?: '23:59:59');
        if ($now < $regBuka || $now > $regTutup) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('error', 'Pendaftaran sedang ditutup.');
        }

        $statusPeserta = 'Daftar';
        $statusPembayaran = 'Gratis';
        $statusAkses = 'Terbuka';

        if ($item['biaya_nominal'] > 0) {
            $statusPembayaran = 'Pending';
        }

        if ($item['mekanisme'] == 'Tertutup') {
            $statusAkses = 'Pending';
        }

        // Check if already registered
        $exists = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        if ($exists) {
            $db->table('peserta_pelatihan')->where('id', $exists['id'])->update([
                'status_peserta' => $statusPeserta,
                'status_pembayaran' => $statusPembayaran,
                'status_akses' => $statusAkses,
                'bukti_bayar' => null,
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => $statusPeserta,
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'status_pembayaran' => $statusPembayaran,
                'status_akses' => $statusAkses,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($statusPembayaran == 'Gratis' && $statusAkses == 'Terbuka') {
                $now = date('Y-m-d H:i:s');
                $jadwalMulai = $item['jadwal_mulai'] . ' ' . ($item['jam_mulai'] ?: '00:00:00');
                if ($now >= $jadwalMulai) {
                    $msg = 'Pendaftaran pelatihan telah berhasil. Pelatihan langsung bisa diakses, silakan menuju menu Diklat Saya dan klik Mulai Belajar.';
                } else {
                    $tglMulai = date('d M Y', strtotime($item['jadwal_mulai']));
                    $msg = 'Pendaftaran pelatihan telah berhasil. Pelatihan akan dapat diakses mulai tanggal ' . $tglMulai . '.';
                }

                $db->table('notifikasi_pelatihan')->insert([
                    'user_id' => $userId,
                    'title' => 'Pendaftaran Berhasil: ' . $item['nama'],
                    'message' => $msg,
                    'type' => 'success',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Notify admin about new registration (or updated registration)
        $user = $db->table('users_pelatihan')->where('nik', $userId)->get()->getRowArray();
        $namaUser = $user ? $user['nama_lengkap'] : $userId;
        
        $db->table('notifikasi_pelatihan')->insert([
            'user_id' => 'admin',
            'title' => 'Pendaftaran Baru',
            'message' => $namaUser . ' telah mendaftar di ' . $item['nama'] . ' (Akses: '.$statusAkses.', Bayar: '.$statusPembayaran.').',
            'type' => 'primary',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($statusPembayaran == 'Pending' || $statusAkses == 'Pending') {
            $msg = 'Pendaftaran dikirim. Mohon menunggu verifikasi admin untuk pembayaran/akses.';
        } else {
            $msg = 'Pendaftaran Berhasil! Anda sudah bisa mengakses ruang belajar.';
        }

        $redirect = redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('success', $msg);

        // Show upload popup if payment is pending. We just reset or created the record, so proof is always empty here.
        if ($statusPembayaran == 'Pending') {
            $redirect = $redirect->with('show_upload_popup', true);
        }

        return $redirect;
    }

    public function upload_bukti_bayar($id)
    {
        helper('upload_security');
        if (!is_safe_upload($this->request->getFile('bukti_bayar'))) {
            return redirect()->back()->with('error', 'Keamanan: File Bukti Pembayaran tidak valid atau mengandung ekstensi berbahaya.');
        }
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/pelatihan/login');

        $db = \Config\Database::connect();
        $pelatihan = $db->table('master_pelatihan')
            ->where('id', $id)
            ->where('status !=', 'Batal')
            ->get()->getRowArray();
        if (!$pelatihan) {
            return redirect()->to('/pelatihan/peserta/pembelajaran')->with('error', 'Pelatihan sudah dibatalkan atau tidak tersedia.');
        }
        
        $file = $this->request->getFile('bukti_bayar');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/bukti_bayar')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/bukti_bayar', 0777, true);
            }
            
            $user = $db->table('users_pelatihan')->where('nik', $userId)->get()->getRowArray();
            $namaPelatihan = preg_replace('/[^A-Za-z0-9]/', '_', $pelatihan['nama'] ?? 'Pelatihan');
            $namaUser = preg_replace('/[^A-Za-z0-9]/', '_', $user['nama_lengkap'] ?? 'User');
            
            $newName = "BuktiBayar_{$namaPelatihan}_{$namaUser}_" . date('Ymd_His') . "." . $file->getExtension();
            $file->move(ROOTPATH . 'public/uploads/pelatihan/bukti_bayar', $newName);

            $db->table('peserta_pelatihan')
                ->where('user_id', $userId)
                ->where('pelatihan_id', $id)
                ->update([
                    'bukti_bayar' => 'bukti_bayar/' . $newName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            // Notify admin about payment upload
            $db->table('notifikasi_pelatihan')->insert([
                'user_id' => 'admin',
                'title' => 'Bukti Bayar Diunggah',
                'message' => ($user['nama_lengkap'] ?? $userId) . ' telah mengunggah bukti bayar untuk ' . ($pelatihan['nama'] ?? 'Pelatihan') . '.',
                'type' => 'info',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('success', 'Bukti pembayaran berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }

    public function belajar($id)
    {
        helper('pelatihan');
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/pelatihan/login');
        }

        $db = \Config\Database::connect();
        $item = $db->table('master_pelatihan')
            ->where('id', $id)
            ->where('status !=', 'Batal')
            ->get()->getRowArray();
        if (!$item) return redirect()->to('/pelatihan/peserta/pembelajaran')->with('error', 'Pelatihan sudah dibatalkan atau tidak tersedia.');

        $now = date('Y-m-d H:i:s');
        $nowTs = strtotime($now);
        $jadwalMulai = $item['jadwal_mulai'] . ' ' . ($item['jam_mulai'] ?: '00:00:00');
        $jadwalSelesai = $item['jadwal_selesai'] . ' ' . ($item['jam_selesai'] ?: '23:59:59');
        if ($now < $jadwalMulai || $now > $jadwalSelesai) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('error', 'Masa pelatihan belum dimulai atau sudah berakhir.');
        }

        $reg = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        $isPayApproved = $reg && in_array($reg['status_pembayaran'], ['Verified', 'Gratis']);
        $isAccessApproved = $reg && in_array($reg['status_akses'], ['Approved', 'Terbuka']);

        if (!$reg || !$isPayApproved || !$isAccessApproved) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('error', 'Akses ditolak. Mohon tunggu verifikasi admin.');
        }

        $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();
        // Auto-create peserta_pelatihan if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();
        }
        $completed_steps = $pesertaRecord ? (json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? []) : [];
        $pg = $pesertaRecord ? ['progress' => $pesertaRecord['progress'] ?? 0, 'completed_steps' => $completed_steps] : null;
        
        $konten = [];
        $stepCounter = 1;
        $preTestQuestions = [];
        $postTestQuestions = [];
        $examConfiguration = $this->getExamConfiguration($db, (int) $id);
        $examDefinitions = $examConfiguration['tests'];
        $testsBySession = $examConfiguration['by_session'];
        $usesLegacyExamFlow = $examConfiguration['legacy'];
        $examStates = [];
        if ($pesertaRecord) {
            foreach ($examDefinitions as $examDefinition) {
                $examStates[(int) $examDefinition['id']] = $this->getExamAttemptState(
                    $db,
                    (int) $pesertaRecord['id'],
                    $examDefinition,
                    $usesLegacyExamFlow
                );
            }
        }

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $legacyPreTest = $usesLegacyExamFlow ? ($examConfiguration['by_type']['pre_test'][0] ?? null) : null;
        $legacyPostTest = $usesLegacyExamFlow ? ($examConfiguration['by_type']['post_test'][0] ?? null) : null;
        if ($legacyPreTest) {
            $konten[] = [
                'id' => $stepCounter++,
                'tipe' => 'pre_test',
                'judul' => 'Pre-Test',
                'sesi_id' => null,
                'ujian_id' => $legacyPreTest['id'],
            ];
        }
            
        $presensiList = [];
        $presensiStatusList = [];
        if ($pesertaRecord) {
            $pData = $db->table('peserta_presensi_pelatihan')->where('peserta_pelat_id', $pesertaRecord['id'])->get()->getResultArray();
            foreach($pData as $pd) {
                $presensiList[$pd['sesi_id']] = $pd['waktu_absen'];
                $presensiStatusList[$pd['sesi_id']] = $pd['status_hadir'];
            }
        }
        
        $statsSesiTotal = count($sesi);
        $statsSesiHadir = 0;
        $materiAlfa = [];
        foreach ($sesi as $s) {
            $status = $presensiStatusList[$s['id']] ?? null;
            if ($status === 'Hadir') {
                $statsSesiHadir++;
            } elseif ($status === 'Alfa') {
                $mats = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->get()->getResultArray();
                foreach ($mats as $m) {
                    $materiAlfa[] = $m['judul'] . ' (Sesi: ' . $s['nama_sesi'] . ')';
                }
            }
        }

        foreach ($sesi as $s) {
            $preTestSesi = !$usesLegacyExamFlow
                ? ($testsBySession[(int) $s['id']]['pre_test'] ?? null)
                : null;
            if ($preTestSesi) {
                $konten[] = [
                    'id' => $stepCounter++,
                    'tipe' => 'pre_test',
                    'judul' => 'Pre-Test: ' . $s['nama_sesi'],
                    'sesi_id' => $s['id'],
                    'ujian_id' => $preTestSesi['id'],
                ];
            }

            $sessionOpenAt = !empty($s['tanggal']) && !empty($s['waktu']) ? strtotime($s['tanggal'] . ' ' . $s['waktu']) : null;
            $sessionCloseAt = !empty($s['tanggal']) && !empty($s['jam_tutup']) ? strtotime($s['tanggal'] . ' ' . $s['jam_tutup']) : (!empty($s['tanggal']) ? strtotime($s['tanggal'] . ' 23:59:59') : $sessionOpenAt);
            $sessionAvailable = $sessionOpenAt === null || ($nowTs >= $sessionOpenAt && ($sessionCloseAt === null || $nowTs <= $sessionCloseAt));

            $tipeSesiLabel = ucfirst(strtolower($s['tipe_sesi'] ?? 'online'));
            if (strtolower($s['tipe_sesi'] ?? '') == 'offline') {
                $konten[] = [
                    'id' => $stepCounter++,
                    'tipe' => 'presensi',
                    'judul' => 'Sesi ' . $tipeSesiLabel . ': ' . $s['nama_sesi'],
                    'sesi_id' => $s['id'],
                    'waktu' => $s['waktu'] ?? '',
                    'jam_tutup' => $s['jam_tutup'] ?? '',
                    'tanggal' => $s['tanggal'] ?? '',
                    'tempat' => $s['tempat'] ?? '',
                    'alamat' => $s['alamat'] ?? '',
                    'lokasi_ruang' => $s['lokasi_ruang'] ?? '',
                    'maps_url' => $s['maps_url'] ?? '',
                    'available' => $sessionAvailable,
                    'open_at' => $sessionOpenAt ? date('Y-m-d H:i:s', $sessionOpenAt) : null,
                    'close_at' => $sessionCloseAt ? date('Y-m-d H:i:s', $sessionCloseAt) : null,
                    'is_attended' => isset($presensiList[$s['id']]),
                    'attended_at' => isset($presensiList[$s['id']]) ? $presensiList[$s['id']] : null,
                    'status_hadir' => $presensiStatusList[$s['id']] ?? null,
                    'tipe_sesi' => $s['tipe_sesi'] ?? '',
                    'meeting_link' => $s['meeting_link'] ?? '',
                    'meeting_pass' => $s['meeting_pass'] ?? ''
                ];
            } else {
                $konten[] = [
                    'id' => $stepCounter++,
                    'tipe' => 'sesi',
                    'judul' => 'Sesi ' . $tipeSesiLabel . ': ' . $s['nama_sesi'],
                    'sesi_id' => $s['id'],
                    'meeting_link' => $s['meeting_link'] ?? '',
                    'meeting_pass' => $s['meeting_pass'] ?? '',
                    'waktu' => $s['waktu'] ?? '',
                    'jam_tutup' => $s['jam_tutup'] ?? '',
                    'tanggal' => $s['tanggal'] ?? '',
                    'available' => $sessionAvailable,
                    'open_at' => $sessionOpenAt ? date('Y-m-d H:i:s', $sessionOpenAt) : null,
                    'close_at' => $sessionCloseAt ? date('Y-m-d H:i:s', $sessionCloseAt) : null,
                    'tipe_sesi' => $s['tipe_sesi'] ?? '',
                    'is_attended' => isset($presensiList[$s['id']]),
                    'attended_at' => isset($presensiList[$s['id']]) ? $presensiList[$s['id']] : null,
                    'status_hadir' => $presensiStatusList[$s['id']] ?? null
                ];
            }
            
            $sessionOpenAt = !empty($s['tanggal']) && !empty($s['waktu']) ? strtotime($s['tanggal'] . ' ' . $s['waktu']) : null;
            $sessionCloseAt = !empty($s['tanggal']) && !empty($s['jam_tutup']) ? strtotime($s['tanggal'] . ' ' . $s['jam_tutup']) : $sessionOpenAt;
            $sessionAvailable = $sessionOpenAt === null || ($nowTs >= $sessionOpenAt && ($sessionCloseAt === null || $nowTs <= $sessionCloseAt));

            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->orderBy('segmen', 'ASC')->orderBy('urutan', 'ASC')->get()->getResultArray();
            $groupedMateri = [];
            foreach ($materi as $m) {
                $seg = $m['segmen'] ?: 1;
                $groupedMateri[$seg][] = $m;
            }
            
            // Materi & evaluasi sesi tetap bisa diakses jika Hadir atau Izin, meski sesi sudah tutup
            $sesiPresensiStatus = $presensiStatusList[$s['id']] ?? null;
            $materiAccessible = $sessionAvailable || in_array($sesiPresensiStatus, ['Hadir', 'Izin']);

            foreach ($groupedMateri as $seg => $materiList) {
                $konten[] = [
                    'id' => $stepCounter++, 
                    'tipe' => 'materi_segmen', 
                    'judul' => 'Materi Sesi ' . $s['nama_sesi'] . ' (Segmen ' . $seg . ')',
                    'sesi_id' => $s['id'],
                    'segmen' => $seg,
                    'materi_list' => $materiList,
                    'available' => $materiAccessible,
                    'open_at' => $sessionOpenAt ? date('Y-m-d H:i:s', $sessionOpenAt) : null,
                    'close_at' => $sessionCloseAt ? date('Y-m-d H:i:s', $sessionCloseAt) : null,
                    'tipe_sesi' => $s['tipe_sesi'] ?? ''
                ];
            }

            $postTestSesi = !$usesLegacyExamFlow
                ? ($testsBySession[(int) $s['id']]['post_test'] ?? null)
                : null;
            if ($postTestSesi) {
                $konten[] = [
                    'id' => $stepCounter++,
                    'tipe' => 'post_test',
                    'judul' => 'Post-Test: ' . $s['nama_sesi'],
                    'sesi_id' => $s['id'],
                    'ujian_id' => $postTestSesi['id'],
                ];
            }

            $konten[] = [
                'id'      => $stepCounter++,
                'tipe'    => 'evaluasi_sesi',
                'judul'   => 'Evaluasi Sesi: ' . $s['nama_sesi'],
                'sesi_id' => $s['id'],
            ];
        }
        $postTestQuestions = [];
        $postTestStepId = null;
        $postTest = $legacyPostTest;
        if ($postTest) {
            $allPostSoal = $db->table('ujian_soal_pelatihan')
                ->select('ujian_soal_pelatihan.*, materi_pelatihan.sesi_id as soal_sesi_id')
                ->join('materi_pelatihan', 'materi_pelatihan.id = ujian_soal_pelatihan.materi_id', 'left')
                ->where('ujian_soal_pelatihan.ujian_id', $postTest['id'])
                ->get()->getResultArray();
            // Tampilkan soal jika: materi_id null, atau sesi materi bukan Alfa
            foreach ($allPostSoal as $soal) {
                $soalSesiId = $soal['soal_sesi_id'] ?? null;
                if ($soalSesiId === null) {
                    // Soal tidak terkait materi → selalu tampil
                    $postTestQuestions[] = $soal;
                } else {
                    $soalSesiStatus = $presensiStatusList[$soalSesiId] ?? null;
                    if ($soalSesiStatus !== 'Alfa') {
                        // Hadir, Izin, atau belum presensi → tampil
                        $postTestQuestions[] = $soal;
                    }
                    // Alfa → skip soal ini
                }
            }
            $postTestStepId = $stepCounter;
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'post_test', 'judul' => 'Post-Test', 'soal' => count($postTestQuestions), 'ujian_id' => $postTest['id']];
        }
        
        $evalIndex = $stepCounter++;
        $certIndex = $stepCounter++;
        $konten[] = ['id' => $evalIndex, 'tipe' => 'evaluasi', 'judul' => 'Evaluasi Pelatihan'];
        $konten[] = ['id' => $certIndex, 'tipe' => 'sertifikat', 'judul' => 'Sertifikat Kelulusan'];

        $this->session->set('total_steps_'.$id, $stepCounter - 1);
        $this->session->set('eval_step_'.$id, $evalIndex);
        $this->session->set('cert_step_'.$id, $certIndex);

        $completed_steps = $pg ? ($pg['completed_steps'] ?? []) : [];
        $active_step_id = (int) ($this->request->getGet('step') ?? 1);
        $filtered_active = array_filter($konten, fn($k) => (int) $k['id'] === $active_step_id);
        $activeTestStep = !empty($filtered_active) ? reset($filtered_active) : null;
        $activeExam = !empty($activeTestStep['ujian_id'])
            ? ($examConfiguration['by_id'][(int) $activeTestStep['ujian_id']] ?? null)
            : null;

        $emptyExamState = [
            'attempted' => false,
            'attempts' => 0,
            'score' => 0,
            'status' => 'Belum Dikerjakan',
            'benar' => 0,
            'salah' => 0,
            'total' => 0,
        ];
        $lastPreExam = null;
        $lastPostExam = null;
        foreach ($examDefinitions as $examDefinition) {
            if ($this->examTypeToAttempt($examDefinition['tipe_evaluasi'] ?? '') === 'pre_test') {
                $lastPreExam = $examDefinition;
            }
            if ($this->examTypeToAttempt($examDefinition['tipe_evaluasi'] ?? '') === 'post_test') {
                $lastPostExam = $examDefinition;
            }
        }

        $activeSesiId = $activeTestStep['sesi_id'] ?? null;
        $preTestExam = $activeSesiId !== null
            ? ($testsBySession[(int) $activeSesiId]['pre_test'] ?? null)
            : $lastPreExam;
        $postTestExam = $activeSesiId !== null
            ? ($testsBySession[(int) $activeSesiId]['post_test'] ?? null)
            : $lastPostExam;
        if ($activeExam && $this->examTypeToAttempt($activeExam['tipe_evaluasi'] ?? '') === 'pre_test') {
            $preTestExam = $activeExam;
        }
        if ($activeExam && $this->examTypeToAttempt($activeExam['tipe_evaluasi'] ?? '') === 'post_test') {
            $postTestExam = $activeExam;
        }

        $preTestState = $preTestExam ? ($examStates[(int) $preTestExam['id']] ?? $emptyExamState) : $emptyExamState;
        $postTestState = $postTestExam ? ($examStates[(int) $postTestExam['id']] ?? $emptyExamState) : $emptyExamState;
        $preTestAttempted = $preTestState['attempted'];
        $preTestScore = $preTestState['score'];
        $preTestBenar = $preTestState['benar'];
        $preTestSalah = $preTestState['salah'];
        $preTestTotal = $preTestState['total'];
        $postTestAttempts = $postTestState['attempts'];
        $postTestScore = $postTestState['score'];
        $postTestStatus = $postTestState['status'];
        $postTestBenar = $postTestState['benar'];
        $postTestSalah = $postTestState['salah'];
        $postTestTotal = $postTestState['total'];

        $postTestStepIds = [];
        $firstIncompletePostTestStep = null;
        $postTestsCompleted = true;
        $postTestsPassed = true;
        foreach ($konten as $k) {
            if (($k['tipe'] ?? '') === 'pre_test' && !empty($k['ujian_id'])) {
                $state = $examStates[(int) $k['ujian_id']] ?? $emptyExamState;
                if ($state['attempted'] && !in_array((int) $k['id'], $completed_steps)) {
                    $completed_steps[] = (int) $k['id'];
                }
            }

            if (($k['tipe'] ?? '') === 'post_test' && !empty($k['ujian_id'])) {
                $postTestStepIds[] = (int) $k['id'];
                $state = $examStates[(int) $k['ujian_id']] ?? $emptyExamState;
                if (!$state['attempted']) {
                    $postTestsCompleted = false;
                    $postTestsPassed = false;
                    $firstIncompletePostTestStep ??= (int) $k['id'];
                } elseif (strtolower((string) $state['status']) !== 'lulus') {
                    $postTestsPassed = false;
                }
            }
        }
        if (empty($postTestStepIds)) {
            $postTestsCompleted = true;
            $postTestsPassed = true;
        }
        $postTestStepId = $postTestStepIds[0] ?? null;

        // Session evaluation follows its post-test. Redirect direct links to
        // the required post-test as well, not only sidebar navigation.
        if (!$usesLegacyExamFlow
            && ($activeTestStep['tipe'] ?? '') === 'evaluasi_sesi'
            && !empty($activeTestStep['sesi_id'])) {
            $sessionPostTest = $testsBySession[(int) $activeTestStep['sesi_id']]['post_test'] ?? null;
            $sessionPostState = $sessionPostTest
                ? ($examStates[(int) $sessionPostTest['id']] ?? $emptyExamState)
                : null;

            if ($sessionPostTest && !$sessionPostState['attempted']) {
                foreach ($konten as $step) {
                    if (($step['tipe'] ?? '') === 'post_test'
                        && (int) ($step['ujian_id'] ?? 0) === (int) $sessionPostTest['id']) {
                        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step['id'])
                            ->with('error', 'Selesaikan Post-Test sesi ini sebelum mengisi evaluasi sesi.');
                    }
                }
            }
        }

        if ($pesertaRecord) {
            $progressPct = (count($completed_steps) / max(1, $stepCounter - 1)) * 100;
            $storedSteps = json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? [];
            sort($storedSteps);
            $updatedSteps = $completed_steps;
            sort($updatedSteps);
            if ($storedSteps !== $updatedSteps) {
                $db->table('peserta_pelatihan')->where('id', $pesertaRecord['id'])->update([
                    'completed_steps' => json_encode($completed_steps),
                    'progress' => $progressPct,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($activeExam) {
            if ($this->examTypeToAttempt($activeExam['tipe_evaluasi'] ?? '') === 'pre_test') {
                $preTestQuestions = $db->table('ujian_soal_pelatihan')
                    ->where('ujian_id', $activeExam['id'])
                    ->get()->getResultArray();
            } else {
                $allPostSoal = $db->table('ujian_soal_pelatihan')
                    ->select('ujian_soal_pelatihan.*, materi_pelatihan.sesi_id as soal_sesi_id')
                    ->join('materi_pelatihan', 'materi_pelatihan.id = ujian_soal_pelatihan.materi_id', 'left')
                    ->where('ujian_soal_pelatihan.ujian_id', $activeExam['id'])
                    ->get()->getResultArray();
                foreach ($allPostSoal as $soal) {
                    $soalSesiId = $soal['soal_sesi_id'] ?? null;
                    if (!$usesLegacyExamFlow || $soalSesiId === null || ($presensiStatusList[$soalSesiId] ?? null) !== 'Alfa') {
                        $postTestQuestions[] = $soal;
                    }
                }
            }
        }
        
        // Check for closed sessions without presensi & insert Alfa first so status is up-to-date
        if (!empty($filtered_active)) {
            $activeK = reset($filtered_active);
            if (in_array($activeK['tipe'], ['sesi', 'presensi']) && isset($activeK['sesi_id'])) {
                $sesiIdCheck  = $activeK['sesi_id'];
                $statusCheck  = $presensiStatusList[$sesiIdCheck] ?? null;
                $sesiFiltered = array_filter($sesi, fn($s) => $s['id'] == $sesiIdCheck);
                $sesiRowCheck = reset($sesiFiltered);
                $sesiCloseTs  = ($sesiRowCheck && !empty($sesiRowCheck['tanggal']) && !empty($sesiRowCheck['jam_tutup']))
                    ? strtotime($sesiRowCheck['tanggal'] . ' ' . $sesiRowCheck['jam_tutup'])
                    : (($sesiRowCheck && !empty($sesiRowCheck['tanggal'])) ? strtotime($sesiRowCheck['tanggal'] . ' 23:59:59') : null);
                if ($sesiCloseTs !== null && $nowTs > $sesiCloseTs && $statusCheck === null && $pesertaRecord) {
                    $db->table('peserta_presensi_pelatihan')->insert([
                        'peserta_pelat_id' => $pesertaRecord['id'],
                        'sesi_id'          => $sesiIdCheck,
                        'status_hadir'     => 'Alfa',
                        'waktu_absen'      => date('Y-m-d H:i:s'),
                    ]);
                    $presensiStatusList[$sesiIdCheck] = 'Alfa';
                }
            }

            // Auto-skip: jika step aktif adalah materi_segmen atau evaluasi_sesi
            // dari sesi yang Alfa atau sudah tutup tanpa presensi → redirect ke step setelah sesi itu
            $skipTypes = ['materi_segmen', 'evaluasi_sesi', 'pre_test', 'post_test'];
            if (in_array($activeK['tipe'], $skipTypes) && isset($activeK['sesi_id'])) {
                $sesiIdCheck = $activeK['sesi_id'];
                $statusCheck = $presensiStatusList[$sesiIdCheck] ?? null;
                // Cek apakah sesi sudah tutup
                $sesiRowCheck = array_filter($sesi, fn($s) => $s['id'] == $sesiIdCheck);
                $sesiRowCheck = reset($sesiRowCheck);
                $sesiCloseCheck = !empty($sesiRowCheck['tanggal']) && !empty($sesiRowCheck['jam_tutup'])
                    ? strtotime($sesiRowCheck['tanggal'] . ' ' . $sesiRowCheck['jam_tutup'])
                    : ($sesiRowCheck && !empty($sesiRowCheck['tanggal']) ? strtotime($sesiRowCheck['tanggal'] . ' 23:59:59') : null);
                $sesiSudahTutup = $sesiCloseCheck !== null && $nowTs > $sesiCloseCheck;
                $isAlfa = ($statusCheck === 'Alfa');
                $belumPresensi = ($statusCheck === null) && $sesiSudahTutup;
                if ($isAlfa || $belumPresensi) {
                    // Cari step pertama yang bukan bagian dari sesi yang sama
                    $nextStep = null;
                    foreach ($konten as $kStep) {
                        if ($kStep['id'] <= $active_step_id) continue;
                        $kSesiId = $kStep['sesi_id'] ?? null;
                        if ($kSesiId !== $sesiIdCheck) {
                            $nextStep = $kStep['id'];
                            break;
                        }
                    }
                    if ($nextStep) {
                        return redirect()->to(base_url('pelatihan/peserta/belajar/' . $id . '?step=' . $nextStep));
                    }
                }
            }
        }
        
        $user = $db->table('users_pelatihan')->where('nik', $userId)->get()->getRowArray();

        $evalQuestionsRaw = $db->table('kuesioner_master_pelatihan')
            ->select('kuesioner_master_pelatihan.*, kategori_evaluasi_pelatihan.nama_kategori as kategori')
            ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
            ->where('pelatihan_id', $id)
            ->get()->getResultArray();
        $evalQuestions = [];
        foreach ($evalQuestionsRaw as $eq) {
            $evalQuestions[$eq['kategori']][] = $eq;
        }

        $sesiList = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $sertifikat = $db->table('sertifikat_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->where('jenis_dokumen', 'rsud')
            ->get()->getRowArray();

        // Fetch materi, narasumber, penyelenggara for global rating questionnaire
        $materiList = $db->table('materi_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('sesi_id', 'ASC')
            ->orderBy('segmen', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->get()->getResultArray();

        $narasumberList = $db->table('narasumber_pelatihan')
            ->select('narasumber_pelatihan.*, pejabat_ttd_pelatihan.nama_pejabat, pejabat_ttd_pelatihan.gelar_depan, pejabat_ttd_pelatihan.gelar_belakang')
            ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id', 'left')
            ->where('narasumber_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();

        $penyelenggaraList = $db->table('penyelenggara_pelatihan')
            ->select('penyelenggara_pelatihan.*, master_penyelenggara.nama')
            ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id', 'left')
            ->where('penyelenggara_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();

        // Check if peserta already submitted global rating
        $ratingAlreadySubmitted = false;
        if ($pesertaRecord) {
            $ratingAlreadySubmitted = $db->table('peserta_kuesioner_saran_pelatihan')
                ->where('peserta_pelat_id', $pesertaRecord['id'])
                ->countAllResults() > 0;
        }

        $submittedSesiEvaluations = [];
        if ($pesertaRecord) {
            $sesiEvals = $db->table('peserta_kuesioner_rating_pelatihan')
                ->select('sesi_id')
                ->distinct()
                ->where('peserta_pelat_id', $pesertaRecord['id'])
                ->where('sesi_id IS NOT NULL', null, false)
                ->get()->getResultArray();
            foreach ($sesiEvals as $se) {
                $submittedSesiEvaluations[] = (int)$se['sesi_id'];
            }
        }

        $active_step_val = !empty($filtered_active) ? reset($filtered_active) : null;
        $active_sesi_id = ($active_step_val && isset($active_step_val['sesi_id'])) ? $active_step_val['sesi_id'] : null;
        $nextSessionStepId = null;
        if ($active_sesi_id !== null) {
            foreach ($konten as $kStep) {
                if ($kStep['id'] <= $active_step_id) continue;
                $kSesiId = $kStep['sesi_id'] ?? null;
                if ($kSesiId !== $active_sesi_id) {
                    $nextSessionStepId = $kStep['id'];
                    break;
                }
            }
        }
        if (!$nextSessionStepId) {
            $nextSessionStepId = $active_step_id + 1;
        }

        if (!$activeExam) {
            $postTestStatus = $postTestsPassed ? 'Lulus' : 'Tidak Lulus';
        }

        $data = [
            'title' => 'Ruang Belajar',
            'p' => $item,
            'konten' => $konten,
            'completed_steps' => $completed_steps,
            'active_step' => reset($filtered_active),
            'active_id' => $active_step_id,
            'nextSessionStepId' => $nextSessionStepId,
            'pg' => $pg,
            'user' => $user,
            'evalIndex' => $evalIndex,
            'certIndex' => $certIndex,
            'postTestIndex' => $postTestStepId,
            'firstIncompletePostTestStep' => $firstIncompletePostTestStep,
            'post_tests_completed' => $postTestsCompleted,
            'post_tests_passed' => $postTestsPassed,
            'preTestQuestions' => $preTestQuestions,
            'postTestQuestions' => $postTestQuestions,
            'evalQuestions' => $evalQuestions,
            'sesiList' => $sesiList,
            'sertifikat' => $sertifikat,
            'pre_test_attempted' => $preTestAttempted,
            'pre_test_score' => $preTestScore,
            'pre_test_benar' => $preTestBenar,
            'pre_test_salah' => $preTestSalah,
            'pre_test_total' => $preTestTotal,
            'post_test_attempts' => $postTestAttempts,
            'post_test_score' => $postTestScore,
            'post_test_status' => $postTestStatus,
            'post_test_benar' => $postTestBenar,
            'post_test_salah' => $postTestSalah,
            'post_test_total' => $postTestTotal,
            'stats_sesi_total' => $statsSesiTotal,
            'stats_sesi_hadir' => $statsSesiHadir,
            'materi_alfa' => $materiAlfa,
            'max_post_test_attempts' => 3,
            'materiList' => $materiList,
            'narasumberList' => $narasumberList,
            'penyelenggaraList' => $penyelenggaraList,
            'ratingAlreadySubmitted' => $ratingAlreadySubmitted,
            'submittedSesiEvaluations' => $submittedSesiEvaluations,
            'presensiStatusList' => $presensiStatusList,
            'post_test_kkm' => $postTestExam ? ($postTestExam['kkm'] ?? 70) : 70,
        ];
        return view('Pelatihan/peserta/pelatihan/belajar', $data);
    }

    private function examTypeToAttempt(?string $type): string
    {
        $normalized = strtolower(str_replace(['-', ' '], '_', trim((string) $type)));
        $normalized = preg_replace('/_+/', '_', $normalized) ?: '';

        return match ($normalized) {
            'pretest' => 'pre_test',
            'posttest' => 'post_test',
            default => $normalized,
        };
    }

    /**
     * Uses session-level exams whenever at least one has been configured.
     * Older trainings with only a global test remain readable until an admin
     * maps their tests to individual sessions.
     */
    private function getExamConfiguration($db, int $pelatihanId): array
    {
        $scopedTests = $db->table('ujian_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->whereIn('tipe_evaluasi', ['Pre-Test', 'Post-Test'])
            ->where('sesi_id IS NOT NULL', null, false)
            ->orderBy('sesi_id', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $legacy = empty($scopedTests);
        $tests = $scopedTests;
        if ($legacy) {
            $tests = $db->table('ujian_pelatihan')
                ->where('pelatihan_id', $pelatihanId)
                ->whereIn('tipe_evaluasi', ['Pre-Test', 'Post-Test'])
                ->orderBy('id', 'ASC')
                ->get()->getResultArray();
        }

        $bySession = [];
        $byType = [];
        $byId = [];
        foreach ($tests as $test) {
            $type = $this->examTypeToAttempt($test['tipe_evaluasi'] ?? '');
            if (!in_array($type, ['pre_test', 'post_test'], true)) {
                continue;
            }

            $test['tipe_ujian'] = $type;
            $byId[(int) $test['id']] = $test;
            $byType[$type][] = $test;
            if (!$legacy && !empty($test['sesi_id'])) {
                // One test of each type is allowed for every session. Keep the
                // earliest record if historical duplicate rows are present.
                $bySession[(int) $test['sesi_id']][$type] ??= $test;
            }
        }

        return [
            'tests' => array_values($byId),
            'by_session' => $bySession,
            'by_type' => $byType,
            'by_id' => $byId,
            'legacy' => $legacy,
        ];
    }

    private function getExamAttemptState($db, int $pesertaPelatId, array $exam, bool $legacy): array
    {
        $builder = $db->table('peserta_ujian_pelatihan')
            ->where('peserta_pelat_id', $pesertaPelatId);

        if ($legacy) {
            $builder->where('tipe_ujian', $this->examTypeToAttempt($exam['tipe_evaluasi'] ?? ''));
        } else {
            $builder->where('ujian_id', (int) $exam['id']);
        }

        $attempts = $builder
            ->orderBy('created_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();
        $lastAttempt = !empty($attempts) ? $attempts[count($attempts) - 1] : null;
        $benar = 0;
        $total = 0;
        if ($lastAttempt) {
            $answers = $db->table('peserta_jawaban_ujian_pelatihan')
                ->where('peserta_ujian_id', $lastAttempt['id'])
                ->get()->getResultArray();
            $total = count($answers);
            foreach ($answers as $answer) {
                if ((int) ($answer['is_correct'] ?? 0) === 1) {
                    $benar++;
                }
            }
        }

        return [
            'attempted' => $lastAttempt !== null,
            'attempts' => count($attempts),
            'score' => (float) ($lastAttempt['score'] ?? 0),
            'status' => $lastAttempt['status_lulus'] ?? 'Belum Dikerjakan',
            'benar' => $benar,
            'salah' => $total - $benar,
            'total' => $total,
        ];
    }

    private function hasPassedAllPostTests($db, int $pesertaPelatId, int $pelatihanId): bool
    {
        $examConfiguration = $this->getExamConfiguration($db, $pelatihanId);
        $postTests = $examConfiguration['by_type']['post_test'] ?? [];
        if (empty($postTests)) {
            return true;
        }

        foreach ($postTests as $postTest) {
            $state = $this->getExamAttemptState($db, $pesertaPelatId, $postTest, $examConfiguration['legacy']);
            if (strtolower((string) $state['status']) !== 'lulus') {
                return false;
            }
        }

        return true;
    }

    private function _countKontenSteps($db, $pelatihanId): int
    {
        return count($this->_getKontenSteps($db, $pelatihanId));
    }

    private function _findPresensiStepId($db, $pelatihanId, $sesiId): ?int
    {
        foreach ($this->_getKontenSteps($db, $pelatihanId) as $step) {
            if (in_array($step['tipe'], ['presensi', 'sesi'], true) && (int) ($step['sesi_id'] ?? 0) === (int) $sesiId) {
                return (int) $step['id'];
            }
        }

        return null;
    }

    private function _getKontenSteps($db, $pelatihanId): array
    {
        $konten = [];
        $stepCounter = 1;
        $examConfiguration = $this->getExamConfiguration($db, (int) $pelatihanId);
        $testsBySession = $examConfiguration['by_session'];
        $legacy = $examConfiguration['legacy'];
        $legacyPreTest = $legacy ? ($examConfiguration['by_type']['pre_test'][0] ?? null) : null;
        $legacyPostTest = $legacy ? ($examConfiguration['by_type']['post_test'][0] ?? null) : null;
        if ($legacyPreTest) {
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'pre_test', 'ujian_id' => $legacyPreTest['id']];
        }

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        foreach ($sesi as $s) {
            $preTest = !$legacy ? ($testsBySession[(int) $s['id']]['pre_test'] ?? null) : null;
            if ($preTest) {
                $konten[] = ['id' => $stepCounter++, 'tipe' => 'pre_test', 'sesi_id' => $s['id'], 'ujian_id' => $preTest['id']];
            }

            $tipeSesi = strtolower($s['tipe_sesi'] ?? '') == 'offline' ? 'presensi' : 'sesi';
            $konten[] = ['id' => $stepCounter++, 'tipe' => $tipeSesi, 'sesi_id' => $s['id']];

            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->orderBy('segmen', 'ASC')->orderBy('urutan', 'ASC')->get()->getResultArray();
            $groupedSegmen = [];
            foreach ($materi as $m) { $groupedSegmen[$m['segmen'] ?: 1][] = $m; }
            foreach ($groupedSegmen as $seg => $mList) {
                $konten[] = ['id' => $stepCounter++, 'tipe' => 'materi_segmen', 'sesi_id' => $s['id'], 'segmen' => $seg];
            }
            $postTest = !$legacy ? ($testsBySession[(int) $s['id']]['post_test'] ?? null) : null;
            if ($postTest) {
                $konten[] = ['id' => $stepCounter++, 'tipe' => 'post_test', 'sesi_id' => $s['id'], 'ujian_id' => $postTest['id']];
            }
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'evaluasi_sesi', 'sesi_id' => $s['id']];
        }

        if ($legacyPostTest) {
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'post_test', 'ujian_id' => $legacyPostTest['id']];
        }
        $konten[] = ['id' => $stepCounter++, 'tipe' => 'evaluasi'];
        $konten[] = ['id' => $stepCounter++, 'tipe' => 'sertifikat'];

        return $konten;
    }

    public function tandai_selesai($id, $step_id)
    {
        $userId = $this->session->get('user_id');
        $score = $this->request->getGet('score');

        $is_post_test = $this->request->getGet('is_post_test');
        $db = \Config\Database::connect();

        // Compute totalSteps from DB (don't rely on session which may be fresh)
        $totalSteps = $this->session->get('total_steps_'.$id);
        if (!$totalSteps) {
            $totalSteps = $this->_countKontenSteps($db, $id);
            $this->session->set('total_steps_'.$id, $totalSteps);
        }

        $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();

        // Auto-create peserta_pelatihan if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();
        }

        $completed_steps = $pesertaRecord ? (json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? []) : [];

        // Logika evaluasi Post-Test
        if ($is_post_test == '1' && $score !== null) {
            $ujianId = (int) $this->request->getGet('ujian_id');
            $ujian = $db->table('ujian_pelatihan')
                ->where('id', $ujianId)
                ->where('pelatihan_id', $id)
                ->where('tipe_evaluasi', 'Post-Test')
                ->get()->getRowArray();
            if (!$ujian) {
                return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)
                    ->with('error', 'Post-Test tidak valid.');
            }

            $kkm = $ujian['kkm'] ?? 70;
            
            $attempts = 0;
            if ($pesertaRecord) {
                $attempts = $db->table('peserta_ujian_pelatihan')
                    ->where('peserta_pelat_id', $pesertaRecord['id'])
                    ->where('ujian_id', $ujianId)
                    ->countAllResults();
            }
            
            if ($score < $kkm) {
                if ($attempts >= 3) {
                    if ($pesertaRecord) {
                        $db->table('peserta_pelatihan')
                           ->where('id', $pesertaRecord['id'])
                           ->update(['status_peserta' => 'Tidak Lulus', 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }
                return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id.'&error=score_low&last_score='.$score.'&attempts='.$attempts.'&ujian_id='.$ujianId);
            }
        }

        // Tandai step selesai
        if (!in_array((int)$step_id, $completed_steps)) {
            $completed_steps[] = (int)$step_id;
        }
        $progressPct = (count($completed_steps) / max(1, $totalSteps)) * 100;

        if ($pesertaRecord) {
            $db->table('peserta_pelatihan')
               ->where('id', $pesertaRecord['id'])
               ->update([
                   'completed_steps' => json_encode($completed_steps),
                   'progress' => $progressPct,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
        }

        $sesi_id = $this->request->getGet('sesi_id');
        $do_presensi = $this->request->getGet('do_presensi');
        if ($sesi_id && $pesertaRecord && $do_presensi == '1') {
            $existPresensi = $db->table('peserta_presensi_pelatihan')
                                ->where('peserta_pelat_id', $pesertaRecord['id'])
                                ->where('sesi_id', $sesi_id)
                                ->get()->getRowArray();
            if (!$existPresensi) {
                $db->table('peserta_presensi_pelatihan')->insert([
                    'peserta_pelat_id' => $pesertaRecord['id'],
                    'sesi_id' => $sesi_id,
                    'status_hadir' => 'Hadir',
                    'waktu_absen' => date('Y-m-d H:i:s')
                ]);
            } elseif ($existPresensi['status_hadir'] === 'Alfa') {
                // Peserta presensi mandiri (sesi masih aktif) → ubah Alfa ke Hadir
                $db->table('peserta_presensi_pelatihan')
                   ->where('id', $existPresensi['id'])
                   ->update(['status_hadir' => 'Hadir', 'waktu_absen' => date('Y-m-d H:i:s')]);
            }
        }

        $is_ujian = $this->request->getGet('is_ujian');
        if ($is_ujian == '1') {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id.'&success=1');
        }

        $next_step = $this->request->getGet('next_step');
        if ($next_step) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$next_step);
        }

        if ($pesertaRecord) {
            $kontenSteps = $this->_getKontenSteps($db, $id);
            $currStepObj = array_filter($kontenSteps, fn($k) => $k['id'] == $step_id);
            if (!empty($currStepObj)) {
                $cStep = reset($currStepObj);
                $cSesiId = $cStep['sesi_id'] ?? null;
                if ($cSesiId !== null) {
                    $pRow = $db->table('peserta_presensi_pelatihan')
                        ->where('peserta_pelat_id', $pesertaRecord['id'])
                        ->where('sesi_id', $cSesiId)
                        ->get()->getRowArray();
                    if (($pRow['status_hadir'] ?? null) === 'Alfa') {
                        foreach ($kontenSteps as $kStep) {
                            if ($kStep['id'] <= $step_id) continue;
                            if (($kStep['sesi_id'] ?? null) !== $cSesiId) {
                                return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$kStep['id']);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.($step_id + 1));
    }
    public function submit_kuis($id)
    {
        $userId = $this->session->get('user_id');
        $step_id = (int) $this->request->getPost('step_id');
        $tipe_ujian = $this->request->getPost('tipe_ujian'); // 'pre_test' or 'post_test'
        $ujianId = (int) $this->request->getPost('ujian_id');
        $answersJson = $this->request->getPost('answers');
        
        $answers = json_decode($answersJson, true) ?? [];
        $totalQuestions = count($answers);
        $correctCount = 0;

        $db = \Config\Database::connect();
        
        // Cek data peserta
        $pesertaRecord = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        // Auto-create if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')
                ->where('user_id', $userId)
                ->where('pelatihan_id', $id)
                ->get()->getRowArray();
        }
            
        if (!$pesertaRecord) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Data peserta tidak ditemukan.');
        }

        if (!in_array($tipe_ujian, ['pre_test', 'post_test'], true) || $ujianId <= 0) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)
                ->with('error', 'Data tes tidak valid.');
        }

        $dbTipeEvaluasi = $tipe_ujian === 'pre_test' ? 'Pre-Test' : 'Post-Test';
        $ujian = $db->table('ujian_pelatihan')
            ->where('id', $ujianId)
            ->where('pelatihan_id', $id)
            ->where('tipe_evaluasi', $dbTipeEvaluasi)
            ->get()->getRowArray();
        if (!$ujian) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)
                ->with('error', 'Tes tidak ditemukan atau tidak sesuai dengan pelatihan ini.');
        }

        // The posted exam must be the exact test assigned to the current
        // learning step. This prevents a test from one session being used to
        // complete another session's step.
        $step = null;
        foreach ($this->_getKontenSteps($db, $id) as $candidate) {
            if ((int) ($candidate['id'] ?? 0) === $step_id) {
                $step = $candidate;
                break;
            }
        }
        if (!$step
            || ($step['tipe'] ?? '') !== $tipe_ujian
            || (int) ($step['ujian_id'] ?? 0) !== $ujianId) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)
                ->with('error', 'Tes tidak sesuai dengan sesi pembelajaran saat ini.');
        }

        $attempts = $db->table('peserta_ujian_pelatihan')
            ->where('peserta_pelat_id', $pesertaRecord['id'])
            ->where('ujian_id', $ujianId)
            ->countAllResults();

        if ($tipe_ujian == 'pre_test' && $attempts >= 1) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)->with('error', 'Pre-Test hanya dapat dikerjakan 1 kali.');
        }

        if ($tipe_ujian == 'post_test' && $attempts >= 3) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)->with('error', 'Post-Test hanya dapat dikerjakan 3 kali.');
        }

        // Ambil soal dari tes yang dipilih untuk mencocokkan jawaban.
        $soalList = [];
        $soals = $db->table('ujian_soal_pelatihan')->where('ujian_id', $ujian['id'])->get()->getResultArray();
        foreach ($soals as $s) {
            $soalList[$s['id']] = strtolower(trim($s['jawaban_benar']));
        }

        if (empty($soalList)) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)
                ->with('error', 'Soal untuk tes ini belum tersedia.');
        }

        $totalAllQuestions = count($soalList);

        $logJawaban = [];
        $answeredIds = [];
        foreach ($answers as $ans) {
            $sId = (int) ($ans['soal_id'] ?? 0);
            if (!isset($soalList[$sId]) || isset($answeredIds[$sId])) {
                continue;
            }

            $j = strtolower(trim($ans['jawaban'] ?? ''));
            $isCorrect = $soalList[$sId] === $j ? 1 : 0;
            if ($isCorrect) $correctCount++;
            $answeredIds[$sId] = true;
            
            $logJawaban[] = [
                'soal_id' => $sId,
                'jawaban_peserta' => strtoupper($j),
                'is_correct' => $isCorrect
            ];
        }

        // Soal yang di-skip (tidak ada di answers) → log sebagai tidak dijawab
        if ($tipe_ujian == 'post_test') {
            foreach ($soalList as $soalId => $jawaban) {
                if (!isset($answeredIds[$soalId])) {
                    $logJawaban[] = [
                        'soal_id' => $soalId,
                        'jawaban_peserta' => '-',
                        'is_correct' => 0
                    ];
                }
            }
        }

        $score = $totalAllQuestions > 0 ? round(($correctCount / $totalAllQuestions) * 100, 2) : 0;
        
        // Simpan Hasil Ujian
        $ujianIdInserted = null;
        $kkm = $ujian ? ($ujian['kkm'] ?? 70) : 70;
        
        $statusLulus = ($score >= $kkm) ? 'Lulus' : 'Tidak Lulus';

        $db->table('peserta_ujian_pelatihan')->insert([
            'peserta_pelat_id' => $pesertaRecord['id'],
            'ujian_id' => $ujianId,
            'tipe_ujian' => $tipe_ujian,
            'score' => $score,
            'status_lulus' => $statusLulus,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $ujianIdInserted = $db->insertID();

        // Simpan Log Jawaban
        if ($ujianIdInserted && !empty($logJawaban)) {
            foreach ($logJawaban as &$lj) {
                $lj['peserta_ujian_id'] = $ujianIdInserted;
            }
            $db->table('peserta_jawaban_ujian_pelatihan')->insertBatch($logJawaban);
        }

        // Lanjut ke tandai_selesai (menggunakan querystring untuk update status progres dll)
        $isPostTestNum = ($tipe_ujian == 'post_test') ? 1 : 0;
        return redirect()->to('/pelatihan/peserta/tandai_selesai/'.$id.'/'.$step_id.'?score='.$score.'&is_post_test='.$isPostTestNum.'&is_ujian=1&ujian_id='.$ujianId);
    }


    public function submit_evaluasi_sesi($id)
    {
        $userId  = $this->session->get('user_id');
        $stepId  = (int) $this->request->getPost('step_id');
        $sesiId  = (int) $this->request->getPost('sesi_id');

        if (!$sesiId) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Sesi tidak valid.');
        }

        $db = \Config\Database::connect();
        $pesertaRecord = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        // Auto-create if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')
                ->where('user_id', $userId)
                ->where('pelatihan_id', $id)
                ->get()->getRowArray();
        }

        if (!$pesertaRecord) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Data peserta tidak ditemukan.');
        }

        $pesertaPelatId = $pesertaRecord['id'];

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('id', $sesiId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();
        if (!$sesi) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Sesi tidak sesuai dengan pelatihan ini.');
        }

        $examConfiguration = $this->getExamConfiguration($db, (int) $id);
        if (!$examConfiguration['legacy']) {
            $postTest = $examConfiguration['by_session'][$sesiId]['post_test'] ?? null;
            if ($postTest) {
                $postTestState = $this->getExamAttemptState($db, (int) $pesertaPelatId, $postTest, false);
                if (!$postTestState['attempted']) {
                    foreach ($this->_getKontenSteps($db, $id) as $step) {
                        if (($step['tipe'] ?? '') === 'post_test'
                            && (int) ($step['ujian_id'] ?? 0) === (int) $postTest['id']) {
                            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step['id'])
                                ->with('error', 'Selesaikan Post-Test sesi ini sebelum mengisi evaluasi sesi.');
                        }
                    }
                }
            }
        }

        $batchData = [];

        $materiRows = $db->table('materi_pelatihan')->where('sesi_id', $sesiId)->select('id')->get()->getResultArray();
        $materiIds = array_column($materiRows, 'id');

        $ratingsMateri = $this->request->getPost('rating_materi');
        if ($ratingsMateri && is_array($ratingsMateri)) {
            foreach ($ratingsMateri as $materiId => $kuesionerRatings) {
                if (!in_array((int)$materiId, $materiIds)) continue;
                foreach ($kuesionerRatings as $kuesionerId => $nilai) {
                    $batchData[] = [
                        'peserta_pelat_id' => $pesertaPelatId,
                        'kuesioner_id'     => $kuesionerId,
                        'sesi_id'          => $sesiId,
                        'materi_id'        => $materiId,
                        'narasumber_id'    => null,
                        'penyelenggara_id' => null,
                        'nilai_rating'     => (int)$nilai,
                    ];
                }
            }
        }

        $ratingsNarasumber = $this->request->getPost('rating_narasumber');
        if ($ratingsNarasumber && is_array($ratingsNarasumber)) {
            foreach ($ratingsNarasumber as $narasumberId => $kuesionerRatings) {
                foreach ($kuesionerRatings as $kuesionerId => $nilai) {
                    $batchData[] = [
                        'peserta_pelat_id' => $pesertaPelatId,
                        'kuesioner_id'     => $kuesionerId,
                        'sesi_id'          => $sesiId,
                        'materi_id'        => null,
                        'narasumber_id'    => $narasumberId,
                        'penyelenggara_id' => null,
                        'nilai_rating'     => (int)$nilai,
                    ];
                }
            }
        }

        $ratingsPenyelenggara = $this->request->getPost('rating_penyelenggara');
        if ($ratingsPenyelenggara && is_array($ratingsPenyelenggara)) {
            foreach ($ratingsPenyelenggara as $penyelenggaraId => $kuesionerRatings) {
                foreach ($kuesionerRatings as $kuesionerId => $nilai) {
                    $batchData[] = [
                        'peserta_pelat_id' => $pesertaPelatId,
                        'kuesioner_id'     => $kuesionerId,
                        'sesi_id'          => $sesiId,
                        'materi_id'        => null,
                        'narasumber_id'    => null,
                        'penyelenggara_id' => $penyelenggaraId,
                        'nilai_rating'     => (int)$nilai,
                    ];
                }
            }
        }

        $ratingsFasil = $this->request->getPost('rating_fasilitator');
        if ($ratingsFasil && is_array($ratingsFasil) && isset($ratingsFasil[$sesiId])) {
            foreach ($ratingsFasil[$sesiId] as $kuesionerId => $nilai) {
                $batchData[] = [
                    'peserta_pelat_id' => $pesertaPelatId,
                    'kuesioner_id'     => $kuesionerId,
                    'sesi_id'          => $sesiId,
                    'materi_id'        => null,
                    'narasumber_id'    => null,
                    'penyelenggara_id' => null,
                    'nilai_rating'     => $nilai,
                ];
            }
        }

        $ratings = $this->request->getPost('rating');
        if ($ratings && is_array($ratings)) {
            foreach ($ratings as $kuesionerId => $nilai) {
                $batchData[] = [
                    'peserta_pelat_id' => $pesertaPelatId,
                    'kuesioner_id'     => $kuesionerId,
                    'sesi_id'          => $sesiId,
                    'materi_id'        => null,
                    'narasumber_id'    => null,
                    'penyelenggara_id' => null,
                    'nilai_rating'     => $nilai,
                ];
            }
        }

        if (!empty($batchData)) {
            $db->table('peserta_kuesioner_rating_pelatihan')->insertBatch($batchData);
        }

        if ($stepId && $pesertaRecord) {
            $completedSteps = json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? [];
            if (!in_array((int)$stepId, $completedSteps)) {
                $completedSteps[] = (int)$stepId;
            }
            $totalSteps = $this->session->get('total_steps_'.$id);
            if (!$totalSteps) {
                $totalSteps = $this->_countKontenSteps($db, $id);
                $this->session->set('total_steps_'.$id, $totalSteps);
            }
            $db->table('peserta_pelatihan')
               ->where('id', $pesertaPelatId)
               ->update([
                   'completed_steps' => json_encode($completedSteps),
                   'progress' => (count($completedSteps) / $totalSteps) * 100,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
        }

        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.($stepId + 1))
            ->with('success', 'Evaluasi sesi berhasil dikirim!');
    }

    public function submit_evaluasi($id)
    {
        $userId    = $this->session->get('user_id');
        $evalIndex = $this->session->get('eval_step_'.$id) ?? 6;
        $certIndex = $this->session->get('cert_step_'.$id) ?? 7;

        $db = \Config\Database::connect();
        $pesertaRecord = $db->table('peserta_pelatihan')
           ->where('user_id', $userId)
           ->where('pelatihan_id', $id)
           ->get()->getRowArray();

        if ($pesertaRecord) {
            $pesertaPelatId = $pesertaRecord['id'];
            $completedSteps = json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? [];
            if (!in_array($evalIndex, $completedSteps)) $completedSteps[] = $evalIndex;
            if (!in_array($certIndex, $completedSteps)) $completedSteps[] = $certIndex;

            $db->table('peserta_pelatihan')
               ->where('id', $pesertaPelatId)
               ->update([
                   'completed_steps' => json_encode($completedSteps),
                   'progress' => 100,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);

            $ratingUmum = $this->request->getPost('rating_umum') ?? 5;
            $saran      = $this->request->getPost('saran') ?? '';

            $db->table('peserta_kuesioner_saran_pelatihan')->insert([
                'peserta_pelat_id' => $pesertaPelatId,
                'rating_umum'      => $ratingUmum,
                'saran_masukan'    => $saran,
                'waktu_submit'     => date('Y-m-d H:i:s')
            ]);

            if ($this->hasPassedAllPostTests($db, (int) $pesertaPelatId, (int) $id)) {
                $db->table('peserta_pelatihan')
                   ->where('id', $pesertaPelatId)
                   ->update([
                       'status_peserta' => 'Lulus',
                       'updated_at'     => date('Y-m-d H:i:s')
                   ]);
                $msg = 'Pelatihan Selesai! Anda telah resmi Lulus pelatihan ini. Terimakasih atas evaluasi Anda.';
            } else {
                $msg = 'Pelatihan Selesai! Terimakasih atas evaluasi Anda.';
            }
        }

        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$certIndex)->with('success', $msg ?? 'Evaluasi berhasil disimpan.');
    }

    public function approve_and_start($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/pelatihan/login');

        $db = \Config\Database::connect();
        $db->table('peserta_pelatihan')
           ->where('user_id', $userId)
           ->where('pelatihan_id', $id)
           ->update([
               'status_pembayaran' => 'Verified',
               'status_akses' => 'Approved'
           ]);

        return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('success', 'Akses berhasil disetujui.');
    }

    public function reset_simulasi($id = null)
    {
        $userId = $this->session->get('user_id');
        $db = \Config\Database::connect();
        
        if ($id) {
            $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->delete();
        } else {
            $db->table('peserta_pelatihan')->where('user_id', $userId)->delete();
        }
        return redirect()->back()->with('success', 'Reset berhasil.');
    }
}
