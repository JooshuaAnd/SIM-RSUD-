<?php
namespace App\Controllers\Pelatihan\Admin;

use App\Controllers\BaseController;
use App\Models\Pelatihan\SertifikatPelatihanModel;
use App\Models\Pelatihan\MasterPelatihanModel;
use App\Models\Pelatihan\PejabatTtdPelatihanModel;
use App\Models\Pelatihan\SertifTerbitPelatihanModel;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\PesertaPelatihanModel;

class Certificate extends BaseController
{
    protected $certModel;
    protected $masterPelatihanModel;
    protected $pejabatModel;
    protected $templateModel;
    protected $userModel;
    protected $pesertaModel;

    public function __construct()
    {
        $this->certModel = new SertifikatPelatihanModel();
        $this->masterPelatihanModel = new MasterPelatihanModel();
        $this->pejabatModel = new PejabatTtdPelatihanModel();
        $this->templateModel = new SertifTerbitPelatihanModel();
        $this->userModel = new UserPelatihanModel();
        $this->pesertaModel = new PesertaPelatihanModel();
    }

    private function monthToRoman($month)
    {
        $romans = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[(int)$month] ?? 'I';
    }

    private function generateNoSertifikat($pelatihan)
    {
        $totalTemplates = $this->templateModel->countAllResults(false);
        $autoNum = str_pad($totalTemplates + 1, 3, '0', STR_PAD_LEFT);

        $ranah = strtoupper(substr($pelatihan['ranah_skp'] ?? 'Pembelajaran', 0, 3));
        $penyelenggara = strtoupper(preg_replace('/[^A-Za-z]/', '', substr($pelatihan['penyelenggara'] ?? 'RSUD', 0, 3)));
        $kodePenyelenggara = $penyelenggara . '-' . $ranah;

        $tglRef = $pelatihan['jadwal_selesai'] ?? $pelatihan['jadwal_mulai'] ?? date('Y-m-d');
        $bulanRomawi = $this->monthToRoman((int)date('n', strtotime($tglRef)));
        $tahun = date('Y', strtotime($tglRef));

        return "{$autoNum}/SERT/{$kodePenyelenggara}/{$bulanRomawi}/{$tahun}";
    }

    private function createNotification($userId, $title, $message, $type = 'info')
    {
        if (empty($userId)) {
            return;
        }

        $db = \Config\Database::connect();
        $db->table('notifikasi_pelatihan')->insert([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Normalize the exam type used by the master exam table and the attempt
     * table. The master table uses labels such as "Pre-Test", while attempts
     * currently store values such as "pre_test".
     */
    private function normalizeExamType(?string $type): string
    {
        $normalized = strtolower(trim((string) $type));
        $normalized = str_replace(['-', ' '], '_', $normalized);
        $normalized = preg_replace('/_+/', '_', $normalized) ?: '';

        return match ($normalized) {
            'pretest' => 'pre_test',
            'posttest' => 'post_test',
            'quiz' => 'kuis',
            default => $normalized,
        };
    }

    private function examTypeLabel(string $type): string
    {
        return match ($type) {
            'pre_test' => 'Pre-Test',
            'post_test' => 'Post-Test',
            'kuis' => 'Kuis',
            default => ucwords(str_replace('_', ' ', $type)),
        };
    }

    /**
     * A participant has completed an exam when an attempt has been recorded.
     * Post-Test is allowed up to three attempts; an unsuccessful participant
     * is therefore considered finished only after the final attempt, while a
     * passing attempt finishes the requirement immediately.
     */
    private function isExamCompleted(string $examType, array $attempts): bool
    {
        if (empty($attempts)) {
            return false;
        }

        if ($examType !== 'post_test') {
            return true;
        }

        foreach ($attempts as $attempt) {
            if (strtolower(trim((string) ($attempt['status_lulus'] ?? ''))) === 'lulus') {
                return true;
            }
        }

        return count($attempts) >= 3;
    }

    /**
     * Returns null when a training has no post-test, otherwise whether the
     * participant has passed every configured post-test. Session-level tests
     * are evaluated independently; legacy global tests retain their old flow.
     */
    private function participantPassedAllPostTests(\CodeIgniter\Database\BaseConnection $db, int $pesertaPelatId, int $pelatihanId): ?bool
    {
        $scopedTests = $db->table('ujian_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->where('tipe_evaluasi', 'Post-Test')
            ->where('sesi_id IS NOT NULL', null, false)
            ->get()->getResultArray();
        $legacy = empty($scopedTests);
        $postTests = $scopedTests;
        if ($legacy) {
            $postTests = $db->table('ujian_pelatihan')
                ->where('pelatihan_id', $pelatihanId)
                ->where('tipe_evaluasi', 'Post-Test')
                ->get()->getResultArray();
        }

        if (empty($postTests)) {
            return null;
        }

        foreach ($postTests as $postTest) {
            $attempts = $db->table('peserta_ujian_pelatihan')
                ->where('peserta_pelat_id', $pesertaPelatId);
            if ($legacy) {
                $attempts->where('tipe_ujian', 'post_test');
            } else {
                $attempts->where('ujian_id', $postTest['id']);
            }

            if (!$attempts->where('status_lulus', 'Lulus')->countAllResults()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the server-side readiness state used both by the page and by the
     * publish action. Rejected/cancelled registrations are not active
     * participants and must not make a class impossible to publish forever.
     */
    private function getCertificatePublishReadiness(int $pelatihanId): array
    {
        $db = \Config\Database::connect();

        $tests = $db->table('ujian_pelatihan')
            ->select('ujian_pelatihan.id, ujian_pelatihan.tipe_evaluasi, ujian_pelatihan.sesi_id, sesi_interaktif_pelatihan.nama_sesi')
            ->join('sesi_interaktif_pelatihan', 'sesi_interaktif_pelatihan.id = ujian_pelatihan.sesi_id', 'left')
            ->where('ujian_pelatihan.pelatihan_id', $pelatihanId)
            ->orderBy('ujian_pelatihan.id', 'ASC')
            ->get()
            ->getResultArray();

        $hasSessionScopedTests = !empty(array_filter($tests, static fn (array $test): bool => !empty($test['sesi_id'])));
        $testDefinitions = [];
        $knownTypes = [];
        foreach ($tests as $test) {
            if ($hasSessionScopedTests && empty($test['sesi_id'])) {
                continue;
            }

            $type = $this->normalizeExamType($test['tipe_evaluasi'] ?? null);
            $testKey = $hasSessionScopedTests ? 'ujian_' . (int) $test['id'] : $type;
            if ($type === '' || isset($knownTypes[$testKey])) {
                continue;
            }

            $knownTypes[$testKey] = true;
            $label = $this->examTypeLabel($type);
            if ($hasSessionScopedTests) {
                $label .= ': ' . ($test['nama_sesi'] ?: 'Sesi #' . $test['sesi_id']);
            }
            $testDefinitions[] = [
                'key' => $testKey,
                'type' => $type,
                'label' => $label,
            ];
        }

        $participantRows = $db->table('peserta_pelatihan pp')
            ->select('pp.*, u.nama_lengkap')
            ->join('users_pelatihan u', 'u.nik = pp.user_id', 'left')
            ->where('pp.pelatihan_id', $pelatihanId)
            ->get()
            ->getResultArray();

        // A rejected registration or a participant who cancelled is no longer
        // part of the active class and cannot complete its exams.
        $participants = array_values(array_filter($participantRows, static function (array $participant): bool {
            return ($participant['status_peserta'] ?? null) !== 'Gagal'
                && ($participant['status_pembayaran'] ?? null) !== 'Rejected'
                && ($participant['status_akses'] ?? null) !== 'Rejected';
        }));

        $attemptRows = $db->table('peserta_ujian_pelatihan pue')
            ->select('pue.peserta_pelat_id, pue.ujian_id, pue.tipe_ujian, pue.status_lulus')
            ->join('peserta_pelatihan pp', 'pp.id = pue.peserta_pelat_id')
            ->where('pp.pelatihan_id', $pelatihanId)
            ->get()
            ->getResultArray();

        $attemptsByParticipant = [];
        foreach ($attemptRows as $attempt) {
            $participantId = (int) $attempt['peserta_pelat_id'];
            $type = $this->normalizeExamType($attempt['tipe_ujian'] ?? null);
            $testKey = $hasSessionScopedTests && !empty($attempt['ujian_id'])
                ? 'ujian_' . (int) $attempt['ujian_id']
                : $type;
            $attemptsByParticipant[$participantId][$testKey][] = $attempt;
        }

        $missingParticipants = [];
        $completedParticipantCount = 0;

        foreach ($participants as $participant) {
            $missingTests = [];
            $participantAttempts = $attemptsByParticipant[(int) $participant['id']] ?? [];

            foreach ($testDefinitions as $test) {
                $attempts = $participantAttempts[$test['key']] ?? [];
                if (!$this->isExamCompleted($test['type'], $attempts)) {
                    $missingTests[] = $test['label'];
                }
            }

            if (empty($missingTests)) {
                $completedParticipantCount++;
                continue;
            }

            $missingParticipants[] = [
                'id' => (int) $participant['id'],
                'user_id' => $participant['user_id'],
                'nama' => $participant['nama_lengkap'] ?: $participant['user_id'],
                'missing_tests' => $missingTests,
            ];
        }

        return [
            'ready' => empty($missingParticipants),
            'participant_count' => count($participants),
            'completed_participant_count' => $completedParticipantCount,
            'pending_participant_count' => count($missingParticipants),
            'test_count' => count($testDefinitions),
            'tests' => $testDefinitions,
            'missing_participants' => $missingParticipants,
        ];
    }

    public function index()
    {
        $pelatihan = $this->masterPelatihanModel->orderBy('nama', 'ASC')->findAll();
        
        $templates = $this->templateModel->findAll();
        $templateMap = [];
        foreach ($templates as $t) {
            $templateMap[$t['pelatihan_id']] = $t;
        }

        // Auto-create default template record for each training if not exists
        foreach ($pelatihan as $p) {
            $exist = $templateMap[$p['id']] ?? null;
            if (!$exist) {
                $noSertif = $this->generateNoSertifikat($p);
                $this->templateModel->insert([
                    'pelatihan_id' => $p['id'],
                    'no_sertifikat' => $noSertif,
                    'background_color' => '#ffffff',
                    'logo_header' => 'assets/img/logo_rs.jpg',
                    'pejabat_id_1' => 1,
                    'pejabat_id_2' => null,
                    'status' => 'draft',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } elseif (empty($exist['no_sertifikat'])) {
                $noSertif = $this->generateNoSertifikat($p);
                $this->templateModel->update($exist['id'], [
                    'no_sertifikat' => $noSertif,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $sertifikat = $this->certModel->orderBy('created_at', 'DESC')->findAll();
        $pejabat = $this->pejabatModel->findAll();

        $publishReadiness = [];
        foreach ($pelatihan as $p) {
            $publishReadiness[$p['id']] = $this->getCertificatePublishReadiness((int) $p['id']);
        }
        
        // Join templates with training names
        $db = \Config\Database::connect();
        $templates = $db->table('sertif_terbit_pelatihan')
            ->select('sertif_terbit_pelatihan.*, master_pelatihan.nama as nama_pelatihan, p1.nama_pejabat as nama_pejabat_1, p2.nama_pejabat as nama_pejabat_2')
            ->join('master_pelatihan', 'master_pelatihan.id = sertif_terbit_pelatihan.pelatihan_id')
            ->join('pejabat_ttd_pelatihan p1', 'p1.id = sertif_terbit_pelatihan.pejabat_id_1', 'left')
            ->join('pejabat_ttd_pelatihan p2', 'p2.id = sertif_terbit_pelatihan.pejabat_id_2', 'left')
            ->get()->getResultArray();

        $data = [
            'title' => 'Kelola Sertifikat',
            'sertifikat' => $sertifikat,
            'pelatihan' => $pelatihan,
            'pejabat' => $pejabat,
            'templates' => $templates,
            'publishReadiness' => $publishReadiness,
        ];
        return view('Pelatihan/admin/sertifikat/index', $data);
    }

    public function update()
    {
        $data = $this->request->getPost();
        $id = $data['id'];

        $updateData = [
            'judul' => $data['judul'],
            'ranah' => $data['ranah'] ?? 'Pembelajaran',
            'kategori_kegiatan' => $data['kategori_kegiatan'] ?? '',
            'skp' => (float)($data['skp'] ?? 0),
            'tgl_mulai' => $data['tgl_mulai'] ?? null,
            'tgl_selesai' => $data['tgl_selesai'] ?? null,
            'penerbit' => $data['penerbit'] ?? '',
            'no_sertifikat' => $data['no_sertifikat'] ?? '',
        ];

        $this->certModel->update($id, $updateData);
        
        // Recalculate
        $cert = $this->certModel->find($id);
        if ($cert) {
            $this->userModel->recalculateJpl($cert['user_id']);
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    public function approve($id)
    {
        $jplValue = (float)$this->request->getPost('jpl');
        
        $this->certModel->update($id, [
            'verifikasi' => 'approved',
            'skp' => $jplValue,
            'tgl_verifikasi' => date('Y-m-d H:i:s'),
            'alasan_penolakan' => null
        ]);

        $cert = $this->certModel->find($id);
        if ($cert) {
            $this->userModel->recalculateJpl($cert['user_id']);
            $this->createNotification(
                $cert['user_id'], 
                'Sertifikat Disetujui', 
                'Sertifikat Anda dengan judul "' . $cert['judul'] . '" telah disetujui dan mendapatkan ' . number_format($jplValue, 0) . ' JPL.', 
                'success'
            );
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Sertifikat berhasil disetujui.');
    }

    public function reject($id)
    {
        $reason = $this->request->getPost('alasan_penolakan') ?: 'Berkas tidak sesuai ketentuan.';
        
        $this->certModel->update($id, [
            'verifikasi' => 'rejected',
            'alasan_penolakan' => $reason,
            'tgl_verifikasi' => date('Y-m-d H:i:s')
        ]);

        $cert = $this->certModel->find($id);
        if ($cert) {
            $this->userModel->recalculateJpl($cert['user_id']);
            $this->createNotification(
                $cert['user_id'],
                'Sertifikat Ditolak',
                'Sertifikat Anda dengan judul "' . $cert['judul'] . '" ditolak. Alasan: ' . $reason,
                'danger'
            );
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Sertifikat berhasil ditolak.');
    }

    public function unverify($id)
    {
        $this->certModel->update($id, [
            'verifikasi' => 'pending',
            'tgl_verifikasi' => null
        ]);

        $cert = $this->certModel->find($id);
        if ($cert) {
            $this->userModel->recalculateJpl($cert['user_id']);
            $this->createNotification(
                $cert['user_id'],
                'Verifikasi Dikembalikan',
                'Verifikasi sertifikat Anda dengan judul "' . $cert['judul'] . '" telah dikembalikan ke status pending.',
                'warning'
            );
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Verifikasi sertifikat telah dibatalkan.');
    }

    public function delete($id)
    {
        $cert = $this->certModel->find($id);
        if ($cert) {
            $userId = $cert['user_id'];
            $this->certModel->delete($id);
            $this->userModel->recalculateJpl($userId);
            $this->createNotification(
                $userId,
                'Sertifikat Dihapus',
                'Sertifikat Anda dengan judul "' . $cert['judul'] . '" telah dihapus oleh admin.',
                'danger'
            );
        }
        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Sertifikat berhasil dihapus.');
    }

    public function publish($id)
    {
        // Check template first
        $template = $this->templateModel->where('pelatihan_id', $id)->first();
        if (!$template) {
            return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('error', 'Template sertifikat belum dikonfigurasi untuk pelatihan ini. Silakan buat template terlebih dahulu di tab "Template Sertifikat".');
        }

        $readiness = $this->getCertificatePublishReadiness((int) $id);
        if (!$readiness['ready']) {
            $pendingMessage = $readiness['pending_participant_count'] . ' peserta belum menyelesaikan seluruh ujian. Silakan cek daftar peserta terlebih dahulu.';

            return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('error', $pendingMessage . ' Publish sertifikat belum dapat dilakukan.');
        }

        // Mark training published
        $this->masterPelatihanModel->update($id, [
            'cert_published' => 1
        ]);

        $masterPelat = $this->masterPelatihanModel->find($id);

        // Sertifikat peserta yang lulus biasanya sudah terbit otomatis saat mereka
        // menyelesaikan pelatihan; ini menyusul yang belum (idempoten).
        $issuer = new \App\Libraries\CertificateIssuer();
        $passedPeserta = $this->pesertaModel->where('pelatihan_id', $id)
            ->where('status_peserta', 'Lulus')
            ->findAll();

        foreach ($passedPeserta as $p) {
            $issuer->issue((int) $id, (string) $p['user_id'], false);
            $this->userModel->recalculateJpl($p['user_id']);
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Sertifikat pelatihan resmi diterbitkan.');
    }

    public function unpublish($id)
    {
        // 1. Set master_pelatihan to draft
        $this->masterPelatihanModel->update($id, [
            'cert_published' => 0
        ]);

        // 2. Find all generated RSUD certificates for this pelatihan
        $certs = $this->certModel->where('pelatihan_id', $id)
            ->where('jenis_dokumen', 'rsud')
            ->findAll();

        // 3. Delete them and recalculate JPL for each user
        foreach ($certs as $cert) {
            $userId = $cert['user_id'];
            $this->certModel->delete($cert['id']);
            $this->userModel->recalculateJpl($userId);
            $this->createNotification(
                $userId,
                'Penerbitan Dibatalkan',
                'Penerbitan sertifikat untuk kegiatan "' . $cert['judul'] . '" telah dibatalkan oleh admin.',
                'warning'
            );
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Penerbitan sertifikat dibatalkan dan JPL telah disesuaikan.');
    }

    // CRUD Pejabat Penandatangan
    public function save_pejabat()
    {
        helper('upload_security');
        if (!is_safe_upload($this->request->getFile('ttd_image')) || !is_safe_upload($this->request->getFile('foto'))) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: File TTD atau Foto tidak valid atau mengandung ekstensi berbahaya.');
        }
        $id = $this->request->getPost('id');
        $data = [
            'status'         => $this->request->getPost('status') ?? 'Narasumber',
            'an_pejabat'     => $this->request->getPost('an_pejabat'),
            'jabatan'        => $this->request->getPost('jabatan'),
            'nama_pejabat'   => $this->request->getPost('nama_pejabat'),
            'nip_pejabat'    => $this->request->getPost('nip_pejabat'),
            'gelar_depan'    => $this->request->getPost('gelar_depan') ?? null,
            'gelar_belakang' => $this->request->getPost('gelar_belakang') ?? null,
            'pendidikan'     => $this->request->getPost('pendidikan') ?? null,
            'keahlian'       => $this->request->getPost('keahlian') ?? null,
            'kontak'         => $this->request->getPost('kontak') ?? null,
            'email'          => $this->request->getPost('email') ?? null,
            'riwayat'        => $this->request->getPost('riwayat') ?? null,
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        // Handle ttd_image upload
        $file = $this->request->getFile('ttd_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/ttd')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/ttd', 0777, true);
            }
            $namaPejabat = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('nama_pejabat') ?: 'Pejabat');
            $newName = "TTD_{$namaPejabat}_" . date('Ymd_His') . "." . $file->getExtension();
            $file->move(ROOTPATH . 'public/uploads/pelatihan/ttd', $newName);
            $data['ttd_image'] = 'ttd/' . $newName;
        }

        // Handle foto upload
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $extension = strtolower($fotoFile->getExtension());
            if (in_array($extension, $allowedExtensions, true) && $fotoFile->getSize() <= 2 * 1024 * 1024) {
                if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/foto')) {
                    mkdir(ROOTPATH . 'public/uploads/pelatihan/foto', 0777, true);
                }
                $namaPejabat = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('nama_pejabat') ?: 'Narasumber');
                $newName = "Foto_{$namaPejabat}_" . date('Ymd_His') . "." . $extension;
                $fotoFile->move(ROOTPATH . 'public/uploads/pelatihan/foto', $newName);
                $data['foto'] = 'uploads/pelatihan/foto/' . $newName;
            }
        }

        if ($id) {
            $this->pejabatModel->update($id, $data);
            $msg = 'Pejabat penandatangan berhasil diperbarui.';
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->pejabatModel->insert($data);
            $msg = 'Pejabat penandatangan berhasil ditambahkan.';
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', $msg);
    }

    public function delete_pejabat($id)
    {
        $this->pejabatModel->delete($id);
        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Pejabat penandatangan berhasil dihapus.');
    }

    // CRUD templates / sertif_terbit
    public function save_template()
    {
        helper('upload_security');
        if (!is_safe_upload($this->request->getFile('logo_header'))) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: File Logo Header tidak valid atau mengandung ekstensi berbahaya.');
        }
        $id = $this->request->getPost('id');
        $data = [
            'pelatihan_id' => $this->request->getPost('pelatihan_id'),
            'no_sertifikat' => $this->request->getPost('no_sertifikat'),
            'background_color' => $this->request->getPost('background_color') ?: '#ffffff',
            'pejabat_id_1' => $this->request->getPost('pejabat_id_1') ?: null,
            'pejabat_id_2' => $this->request->getPost('pejabat_id_2') ?: null,
            'status' => 'diterbitkan', // Set to diterbitkan by default
            'custom_an_1' => null,
            'custom_jabatan_1' => null,
            'custom_nama_1' => null,
            'custom_nip_1' => null,
            'custom_qr_1' => null,
            'custom_an_2' => null,
            'custom_jabatan_2' => null,
            'custom_nama_2' => null,
            'custom_nip_2' => null,
            'custom_qr_2' => null,
        ];

        // Logo upload
        $file = $this->request->getFile('logo_header');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/template_sertifikat')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/template_sertifikat', 0777, true);
            }
            $namaTemplate = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('nama_template') ?: 'Template');
            $newName = "TemplateSertifikat_{$namaTemplate}_" . date('Ymd_His') . "." . $file->getExtension();
            $file->move(ROOTPATH . 'public/uploads/pelatihan/template_sertifikat', $newName);
            $data['logo_header'] = 'template_sertifikat/' . $newName;
        }

        if ($id) {
            $this->templateModel->update($id, $data);
            $msg = 'Template sertifikat berhasil diperbarui.';
        } else {
            $this->templateModel->insert($data);
            $msg = 'Template sertifikat berhasil dibuat.';
        }

        // Peserta yang sudah lulus sebelum template ada langsung mendapat sertifikat.
        if (!empty($data['pelatihan_id'])) {
            $issued = (new \App\Libraries\CertificateIssuer())->issueForPassedParticipants((int) $data['pelatihan_id']);
            if ($issued > 0) {
                $msg .= " Sertifikat untuk {$issued} peserta yang sudah lulus diterbitkan otomatis.";
            }
        }

        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', $msg);
    }

    public function delete_template($id)
    {
        $this->templateModel->delete($id);
        return redirect()->to(site_url('pelatihan/admin/sertifikat'))->with('success', 'Template sertifikat berhasil dihapus.');
    }

    public function preview_template($templateId, $userId = null)
    {
        $db = \Config\Database::connect();
        
        // Fetch specific template by its primary key
        $template = $db->table('sertif_terbit_pelatihan')
            ->select('sertif_terbit_pelatihan.*, p1.nama_pejabat as nama_1, p1.jabatan as jab_1, p1.an_pejabat as an_1, p1.nip_pejabat as nip_1, p1.ttd_image as ttd_1, p2.nama_pejabat as nama_2, p2.jabatan as jab_2, p2.an_pejabat as an_2, p2.nip_pejabat as nip_2, p2.ttd_image as ttd_2')
            ->join('pejabat_ttd_pelatihan p1', 'p1.id = sertif_terbit_pelatihan.pejabat_id_1', 'left')
            ->join('pejabat_ttd_pelatihan p2', 'p2.id = sertif_terbit_pelatihan.pejabat_id_2', 'left')
            ->where('sertif_terbit_pelatihan.id', $templateId)
            ->get()->getRowArray();

        if (!$template) {
            return redirect()->back()->with('error', 'Template tidak ditemukan.');
        }

        $pelatihanId = $template['pelatihan_id'];
        
        // Preview generated RSUD cert
        $pelatihan = $this->masterPelatihanModel->find($pelatihanId);
        
        $users = [];
        if ($userId) {
            $u = $this->userModel->find($userId);
            if ($u) $users[] = $u;
        } else {
            // Fetch all passed participants for batch preview
            $passed = $this->pesertaModel->where('pelatihan_id', $pelatihanId)
                                         ->where('status_peserta', 'Lulus')
                                         ->findAll();
            foreach ($passed as $p) {
                $u = $this->userModel->find($p['user_id']);
                if ($u) $users[] = $u;
            }
        }

        $data = [
            'title' => 'Pratinjau Sertifikat',
            'pelatihan' => $pelatihan,
            'users' => $users,
            'template' => $template,
            'no_sertifikat' => $template['no_sertifikat'] ?? $this->generateNoSertifikat($pelatihan ?? ['ranah_skp' => 'Pembelajaran', 'jadwal_selesai' => date('Y-m-d'), 'penyelenggara' => 'RSUD'])
        ];
        return view('Pelatihan/admin/sertifikat/template/preview', $data);
    }

    public function preview_pelatihan($pelatihanId, $userId = null)
    {
        $db = \Config\Database::connect();
        
        // Fetch active template by pelatihan_id
        // We prioritize the one with status 'diterbitkan' or the newest one
        $template = $db->table('sertif_terbit_pelatihan')
            ->select('sertif_terbit_pelatihan.*, p1.nama_pejabat as nama_1, p1.jabatan as jab_1, p1.an_pejabat as an_1, p1.nip_pejabat as nip_1, p1.ttd_image as ttd_1, p2.nama_pejabat as nama_2, p2.jabatan as jab_2, p2.an_pejabat as an_2, p2.nip_pejabat as nip_2, p2.ttd_image as ttd_2')
            ->join('pejabat_ttd_pelatihan p1', 'p1.id = sertif_terbit_pelatihan.pejabat_id_1', 'left')
            ->join('pejabat_ttd_pelatihan p2', 'p2.id = sertif_terbit_pelatihan.pejabat_id_2', 'left')
            ->where('sertif_terbit_pelatihan.pelatihan_id', $pelatihanId)
            ->orderBy("FIELD(sertif_terbit_pelatihan.status, 'diterbitkan', 'draft')", 'ASC', false)
            ->orderBy('sertif_terbit_pelatihan.id', 'DESC')
            ->get()->getRowArray();

        if (!$template) {
            return redirect()->back()->with('error', 'Template tidak ditemukan untuk pelatihan ini.');
        }

        // Preview generated RSUD cert
        $pelatihan = $this->masterPelatihanModel->find($pelatihanId);
        
        $users = [];
        if ($userId) {
            $u = $this->userModel->find($userId);
            if ($u) $users[] = $u;
        } else {
            // Fetch all passed participants for batch preview
            $passed = $this->pesertaModel->where('pelatihan_id', $pelatihanId)
                                         ->where('status_peserta', 'Lulus')
                                         ->findAll();
            foreach ($passed as $p) {
                $u = $this->userModel->find($p['user_id']);
                if ($u) $users[] = $u;
            }
        }

        $data = [
            'title' => 'Pratinjau Sertifikat',
            'pelatihan' => $pelatihan,
            'users' => $users,
            'template' => $template,
            'no_sertifikat' => $template['no_sertifikat'] ?? $this->generateNoSertifikat($pelatihan ?? ['ranah_skp' => 'Pembelajaran', 'jadwal_selesai' => date('Y-m-d'), 'penyelenggara' => 'RSUD'])
        ];
        return view('Pelatihan/admin/sertifikat/template/preview', $data);
    }

    public function preview_external($id)
    {
        // For external/mandiri certs
        $cert = $this->certModel->find($id);
        $user = $this->userModel->find($cert['user_id'] ?? '');

        $data = [
            'title' => 'Pratinjau Sertifikat Eksternal',
            'cert' => $cert,
            'users' => $user ? [$user] : []
        ];
        return view('Pelatihan/admin/sertifikat/template/preview', $data);
    }

    public function peserta_by_pelatihan($pelatihanId)
    {
        $db = \Config\Database::connect();
        $list = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.nik')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->where('peserta_pelatihan.pelatihan_id', $pelatihanId)
            ->get()->getResultArray();
            
        foreach ($list as &$p) {
            if (empty($p['status_peserta']) || $p['status_peserta'] !== 'Lulus') {
                $passedAllPostTests = $this->participantPassedAllPostTests($db, (int) $p['id'], (int) $pelatihanId);
                if ($passedAllPostTests !== null) {
                    $p['status_peserta'] = $passedAllPostTests ? 'Lulus' : 'Tidak Lulus';
                    $db->table('peserta_pelatihan')
                        ->where('id', $p['id'])
                        ->update(['status_peserta' => $p['status_peserta']]);
                }
            }
        }
        
        return $this->response->setJSON($list);
    }
}
