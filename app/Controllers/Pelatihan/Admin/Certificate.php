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

    public function index()
    {
        $pelatihan = $this->masterPelatihanModel->orderBy('nama', 'ASC')->findAll();
        
        // Auto-create default template record for each training if not exists
        foreach ($pelatihan as $p) {
            $exist = $this->templateModel->where('pelatihan_id', $p['id'])->first();
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
            'templates' => $templates
        ];
        return view('pelatihan/admin/sertifikat/index', $data);
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

        // Mark training published
        $this->masterPelatihanModel->update($id, [
            'cert_published' => 1
        ]);

        $masterPelat = $this->masterPelatihanModel->find($id);

        $noSertifTemplate = $template['no_sertifikat'] ?? $this->generateNoSertifikat($masterPelat ?? ['ranah_skp' => 'Pembelajaran', 'jadwal_selesai' => date('Y-m-d'), 'penyelenggara' => 'RSUD']);

        // Fetch passed participants
        $passedPeserta = $this->pesertaModel->where('pelatihan_id', $id)
            ->where('status_peserta', 'Lulus')
            ->findAll();

        foreach ($passedPeserta as $p) {
            $u = $this->userModel->find($p['user_id']);
            if (!$u) continue;

            $exist = $this->certModel->where('user_id', $u['nik'])
                ->where('pelatihan_id', $id)
                ->where('jenis_dokumen', 'rsud')
                ->first();

            if (!$exist) {
                $noSertifikat = str_replace('{id}', str_pad($p['id'], 4, '0', STR_PAD_LEFT), $noSertifTemplate);

                $this->certModel->insert([
                    'user_id' => $u['nik'],
                    'user_nama' => $u['nama_lengkap'],
                    'user_profesi' => $u['id_profesi'] ? 'Tenaga Kesehatan' : 'Staff Umum', // fallback
                    'judul' => $masterPelat['nama'],
                    'ranah' => 'Pembelajaran',
                    'kategori_kegiatan' => 'Peserta Pelatihan',
                    'skp' => $masterPelat['jpl'],
                    'tgl_mulai' => $masterPelat['jadwal_mulai'],
                    'tgl_selesai' => $masterPelat['jadwal_selesai'],
                    'penerbit' => 'RSUD Kota Yogyakarta',
                    'jenis_dokumen' => 'rsud',
                    'verifikasi' => 'approved',
                    'tgl_upload' => date('Y-m-d H:i:s'),
                    'tgl_verifikasi' => date('Y-m-d H:i:s'),
                    'pelatihan_id' => $id,
                    'no_sertifikat' => $noSertifikat,
                ]);

                // Tambahkan notifikasi
                $db = \Config\Database::connect();
                $db->table('notifikasi_pelatihan')->insert([
                    'user_id' => $u['nik'],
                    'title' => 'Sertifikat Diterbitkan',
                    'message' => 'Sertifikat untuk pelatihan ' . $masterPelat['nama'] . ' telah diterbitkan. Silakan unduh di menu Sertifikat Saya.',
                    'type' => 'success',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            // Recalculate JPL
            $this->userModel->recalculateJpl($u['nik']);
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
        return view('pelatihan/admin/sertifikat/template/preview', $data);
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
        return view('pelatihan/admin/sertifikat/template/preview', $data);
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
        return view('pelatihan/admin/sertifikat/template/preview', $data);
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
                $postTest = $db->table('peserta_ujian_pelatihan')
                    ->where('peserta_pelat_id', $p['id'])
                    ->where('tipe_ujian', 'post_test')
                    ->orderBy('id', 'DESC')
                    ->get()->getRowArray();
                    
                if ($postTest) {
                    $p['status_peserta'] = $postTest['status_lulus'];
                    
                    if ($postTest['status_lulus'] === 'Lulus') {
                        $db->table('peserta_pelatihan')
                           ->where('id', $p['id'])
                           ->update(['status_peserta' => 'Lulus']);
                    } else if ($postTest['status_lulus'] === 'Tidak Lulus') {
                        $db->table('peserta_pelatihan')
                           ->where('id', $p['id'])
                           ->update(['status_peserta' => 'Tidak Lulus']);
                    }
                }
            }
        }
        
        return $this->response->setJSON($list);
    }
}
