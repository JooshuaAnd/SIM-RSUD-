<?php

namespace App\Controllers\Pendidikan;

use App\Controllers\BaseController;
use App\Models\InstitusiPendidikanModel;
use App\Models\MahasiswaPendidikanModel;
use App\Models\DokumenInstitusiModel;
use App\Models\CiPendidikanModel;
use App\Models\StasePendidikanModel;
use App\Models\PengajuanPraktikPendidikanModel;
use App\Models\Pelatihan\ProfesiPelatihanModel;
use App\Models\Pelatihan\UnitKerjaPelatihanModel;
use App\Models\UsersPendidikanModel;
use App\Models\PenempatanPesertaPendidikanModel;

class AdminDiklat extends BaseController
{
    protected $institusiModel;
    protected $mahasiswaModel;
    protected $dokumenModel;
    protected $ciModel;
    protected $staseModel;
    protected $pengajuanModel;
    protected $profesiModel;
    protected $unitKerjaModel;
    protected $usersModel;

    public function __construct()
    {
        $this->institusiModel = new InstitusiPendidikanModel();
        $this->mahasiswaModel = new MahasiswaPendidikanModel();
        $this->dokumenModel = new DokumenInstitusiModel();
        $this->ciModel = new CiPendidikanModel();
        $this->staseModel = new StasePendidikanModel();
        $this->pengajuanModel = new PengajuanPraktikPendidikanModel();
        $this->profesiModel = new ProfesiPelatihanModel();
        $this->unitKerjaModel = new UnitKerjaPelatihanModel();
        $this->usersModel = new UsersPendidikanModel();
    }

    public function index()
    {
        return redirect()->to(base_url('pendidikan/admin/diklat/dashboard'));
    }

    public function dashboard()
    {
        $institusiList = $this->institusiModel->orderBy('created_at', 'DESC')->findAll();
        $totalInstitusi = count($institusiList);
        $pendingInstitusi = $this->institusiModel->where('status_verifikasi', 'pending')->countAllResults();
        $totalMahasiswa = $this->mahasiswaModel->whereIn('status', ['Disetujui', 'Aktif', '1'])->countAllResults();
        $totalKampus = $this->institusiModel->where('status_verifikasi', 'approved')->countAllResults();
        $totalCi = $this->ciModel->countAllResults();
        $totalStase = $this->staseModel->countAllResults();

        $pendingList = array_values(array_filter($institusiList, static function ($institusi) {
            return $institusi['status_verifikasi'] === 'pending'
                || ($institusi['status_verifikasi'] === 'revision' && !empty($institusi['revisi_dikirim_at']));
        }));
        usort($pendingList, static function ($first, $second) {
            $firstTime = $first['revisi_dikirim_at'] ?? $first['created_at'] ?? '';
            $secondTime = $second['revisi_dikirim_at'] ?? $second['created_at'] ?? '';
            return strcmp($secondTime, $firstTime);
        });

        $pendingCount = count($pendingList);
        $pendingList = array_slice($pendingList, 0, 5);

        return view('Pendidikan/AdminDiklat/dashboard', [
            'menu' => 'dashboard',
            'totalInstitusi' => $totalInstitusi,
            'pendingInstitusi' => $pendingInstitusi,
            'totalMahasiswa' => $totalMahasiswa,
            'totalKampus' => $totalKampus,
            'totalCi' => $totalCi,
            'totalStase' => $totalStase,
            'institusiList' => $institusiList,
            'pendingList' => $pendingList,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function update_password()
    {
        $session = session();
        $userId = $session->get('user_id');
        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password baru tidak cocok.');
        }

        $user = $this->usersModel->find($userId);

        if (!$user || !password_verify((string)$oldPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password lama salah.');
        }

        $this->usersModel->update($userId, [
            'password' => password_hash((string)$newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('success', 'Password berhasil diubah.');
    }

    public function institusi()
    {
        $tab = $this->request->getGet('tab') ?? 'inbox';
        $viewMode = $this->request->getGet('view') ?? 'list';

        $allInst = $this->institusiModel->orderBy('created_at', 'DESC')->findAll();
        foreach ($allInst as &$inst) {
            $inst['has_submitted_revision'] = $inst['status_verifikasi'] === 'revision' && !empty($inst['revisi_dikirim_at']);
        }
        unset($inst);

        $statusMap = [
            'inbox' => 'pending',
            'approved' => 'approved',
            'revision' => 'revision',
            'declined' => 'rejected',
        ];

        $filtered = array_filter($allInst, function ($inst) use ($tab, $statusMap) {
            return isset($statusMap[$tab]) && $inst['status_verifikasi'] === $statusMap[$tab];
        });

        $pendingCount = $this->institusiModel->where('status_verifikasi', 'pending')->countAllResults();
        $submittedRevisionCount = $this->institusiModel->where('status_verifikasi', 'revision')->where('revisi_dikirim_at IS NOT NULL', null, false)->countAllResults();
        $counts = [
            'inbox' => $pendingCount + $submittedRevisionCount,
            'approved' => $this->institusiModel->where('status_verifikasi', 'approved')->countAllResults(),
            'revision' => $this->institusiModel->where('status_verifikasi', 'revision')->countAllResults(),
            'revision_submitted' => $submittedRevisionCount,
            'declined' => $this->institusiModel->where('status_verifikasi', 'rejected')->countAllResults(),
        ];

        if ($tab === 'inbox') {
            $institusiList = array_values(array_filter($allInst, static function ($inst) {
                return $inst['status_verifikasi'] === 'pending' || !empty($inst['has_submitted_revision']);
            }));
        } elseif ($tab === 'approved') {
            $institusiList = $this->institusiModel->where('status_verifikasi', 'approved')->orderBy('created_at', 'DESC')->findAll();
        } elseif ($tab === 'revision') {
            $institusiList = $this->institusiModel->where('status_verifikasi', 'revision')->orderBy('created_at', 'DESC')->findAll();
        } elseif ($tab === 'declined') {
            $institusiList = $this->institusiModel->where('status_verifikasi', 'rejected')->orderBy('created_at', 'DESC')->findAll();
        } else {
            $institusiList = $allInst;
        }
        foreach ($institusiList as &$inst) {
            $inst['has_submitted_revision'] = $inst['status_verifikasi'] === 'revision' && !empty($inst['revisi_dikirim_at']);
        }
        unset($inst);

        return view('Pendidikan/AdminDiklat/institusi', [
            'menu' => 'institusi',
            'tab' => $tab,
            'viewMode' => $viewMode,
            'institusiList' => $institusiList,
            'counts' => $counts,
        ]);
    }

    public function institusiDetail($id)
    {
        $institusi = $this->institusiModel
            ->select('institusi_pendidikan.*, users_pendidikan.email')
            ->join('users_pendidikan', 'users_pendidikan.id = institusi_pendidikan.user_id', 'left')
            ->find($id);

        if (!$institusi) {
            return redirect()->to(base_url('pendidikan/admin/diklat/institusi'))->with('error', 'Data tidak ditemukan');
        }
        $institusi['has_submitted_revision'] = $institusi['status_verifikasi'] === 'revision' && !empty($institusi['revisi_dikirim_at']);

        $subTab = $this->request->getGet('tab') ?? 'documents';
        $mahasiswaList = $this->mahasiswaModel->where('institusi_id', $id)->findAll();
        $dokumenList = $this->dokumenModel->where('institusi_id', $id)->findAll();

        // Merge file_mou and file_permohonan from institusi_pendidikan into dokumenList
        $fileFields = [
            'file_mou' => 'MOU / Perjanjian Kerja Sama',
            'file_permohonan' => 'Surat Permohonan Praktik',
        ];
        foreach ($fileFields as $field => $label) {
            if (!empty($institusi[$field])) {
                $dokumenList[] = [
                    'id' => null,
                    'institusi_id' => $id,
                    'judul' => $label,
                    'nama_file' => $institusi[$field],
                    'original_name' => null,
                    'tipe_file' => 'application/pdf',
                    'ukuran_file' => null,
                    'status' => 'verified',
                    'keterangan' => null,
                    'created_at' => $institusi['created_at'] ?? date('Y-m-d H:i:s'),
                    'updated_at' => $institusi['updated_at'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        // Merge pengajuan documents
        $pengajuanList = $this->pengajuanModel->where('institusi_id', $id)->findAll();
        $pengajuanDocFields = [
            'file_proposal' => 'Proposal',
            'file_surat_pengantar' => 'Surat Pengantar',
            'file_logbook' => 'Logbook',
            'file_panduan' => 'Panduan',
            'file_daftar_mhs' => 'Daftar Mahasiswa',
            'file_kompetensi' => 'Kompetensi',
            'file_sk_pembimbing' => 'SK Pembimbing',
            'file_bukti_bayar' => 'Bukti Bayar'
        ];
        foreach ($pengajuanList as $pengajuan) {
            foreach ($pengajuanDocFields as $field => $label) {
                if (!empty($pengajuan[$field])) {
                    $dokumenList[] = [
                        'id' => null,
                        'institusi_id' => $id,
                        'judul' => $label . ' (' . $pengajuan['nama_program'] . ')',
                        'nama_file' => $pengajuan[$field],
                        'original_name' => null,
                        'tipe_file' => 'application/pdf',
                        'ukuran_file' => null,
                        'status' => 'verified',
                        'keterangan' => null,
                        'created_at' => $pengajuan['created_at'] ?? date('Y-m-d H:i:s'),
                        'updated_at' => $pengajuan['updated_at'] ?? date('Y-m-d H:i:s'),
                        'is_pengajuan' => true,
                    ];
                }
            }
        }

        $pendingCount = $this->institusiModel->where('status_verifikasi', 'pending')->countAllResults();
        $submittedRevisionCount = $this->institusiModel->where('status_verifikasi', 'revision')->where('revisi_dikirim_at IS NOT NULL', null, false)->countAllResults();
        $counts = [
            'inbox' => $pendingCount + $submittedRevisionCount,
            'approved' => $this->institusiModel->where('status_verifikasi', 'approved')->countAllResults(),
            'revision' => $this->institusiModel->where('status_verifikasi', 'revision')->countAllResults(),
            'revision_submitted' => $submittedRevisionCount,
            'declined' => $this->institusiModel->where('status_verifikasi', 'rejected')->countAllResults(),
        ];

        return view('Pendidikan/AdminDiklat/institusi', [
            'menu' => 'institusi',
            'tab' => 'inbox',
            'viewMode' => 'detail',
            'detail' => $institusi,
            'subTab' => $subTab,
            'mahasiswaList' => $mahasiswaList,
            'dokumenList' => $dokumenList,
            'institusiList' => [],
            'counts' => $counts,
        ]);
    }

    public function ci()
    {
        $ciList = $this->ciModel
            ->select('ci_pendidikan.*, profesi_pelatihan.nama_profesi, unit_kerja_pelatihan.nama_unit as ruangan_tugas')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = ci_pendidikan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = ci_pendidikan.id_unit_kerja', 'left')
            ->findAll();

        $totalCi = count($ciList);
        $rooms = array_unique(array_filter(array_column($ciList, 'ruangan_tugas')));
        $totalRuangan = count($rooms);
        $profesiList = $this->profesiModel->findAll();
        $unitKerjaList = $this->unitKerjaModel->findAll();

        return view('Pendidikan/AdminDiklat/ci', [
            'menu' => 'ci',
            'ciList' => $ciList,
            'totalCi' => $totalCi,
            'totalRuangan' => $totalRuangan,
            'profesiList' => $profesiList,
            'unitKerjaList' => $unitKerjaList,
        ]);
    }

    public function stase()
    {
        $staseList = $this->staseModel
            ->select('stase_pendidikan.*, ci_pendidikan.nama_lengkap as ci_name, ci_profesi.nama_profesi as ci_profession, profesi_pelatihan.nama_profesi')
            ->join('ci_pendidikan', 'ci_pendidikan.id = stase_pendidikan.ci_id', 'left')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = stase_pendidikan.profesi_id', 'left')
            ->join('profesi_pelatihan ci_profesi', 'ci_profesi.id_profesi = ci_pendidikan.id_profesi', 'left')
            ->orderBy('stase_pendidikan.nama_stase', 'ASC')
            ->findAll();

        $uniqueNames = array_unique(array_filter(array_column($staseList, 'nama_stase')));
        $allRooms = [];
        foreach ($staseList as $s) {
            $rooms = array_filter(array_map('trim', explode(',', $s['ruangan'] ?? '')));
            $allRooms = array_merge($allRooms, $rooms);
        }
        $assigned = count(array_filter($staseList, fn($s) => !empty($s['ci_id'])));

        $stats = [
            'total' => count($staseList),
            'uniqueNames' => count($uniqueNames),
            'uniqueRooms' => count(array_unique($allRooms)),
            'assigned' => $assigned,
        ];

        $profesiList = $this->profesiModel->findAll();
        $unitKerjaList = $this->unitKerjaModel->findAll();

        return view('Pendidikan/AdminDiklat/stase', [
            'menu' => 'stase',
            'staseList' => $staseList,
            'stats' => $stats,
            'profesiList' => $profesiList,
            'unitKerjaList' => $unitKerjaList,
        ]);
    }

    public function user()
    {
        $tab = $this->request->getGet('tab') ?? 'explorer';

        $mahasiswaList = $this->mahasiswaModel
            ->select('mahasiswa_pendidikan.*, institusi_pendidikan.nama_institusi')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = mahasiswa_pendidikan.institusi_id', 'left')
            ->whereIn('mahasiswa_pendidikan.status', ['Disetujui', 'Aktif', '1'])
            ->orderBy('mahasiswa_pendidikan.nama_lengkap', 'ASC')
            ->findAll();

        $institusiList = $this->institusiModel->where('status_verifikasi', 'approved')->findAll();

        $userList = [];
        $profesiList = [];
        $unitKerjaList = [];
        if ($tab === 'ci') {
            $userList = $this->ciModel->findAll();
            $profesiList = $this->profesiModel->findAll();
            $unitKerjaList = $this->unitKerjaModel->findAll();
        }

        return view('Pendidikan/AdminDiklat/user', [
            'menu' => 'user',
            'tab' => $tab,
            'mahasiswaList' => $mahasiswaList,
            'institusiList' => $institusiList,
            'userList' => $userList,
            'profesiList' => $profesiList,
            'unitKerjaList' => $unitKerjaList,
        ]);
    }

    public function userDetail($id)
    {
        $institusi = $this->institusiModel->find($id);
        if (!$institusi) {
            return redirect()->to(base_url('pendidikan/admin/diklat/user'))->with('error', 'Data tidak ditemukan');
        }

        $mahasiswa = $this->mahasiswaModel->where('institusi_id', $id)->whereIn('status', ['Disetujui', 'Aktif', '1'])->findAll();
        $profesiList = array_unique(array_filter(array_column($mahasiswa, 'program_studi')));
        $mahasiswaByProfesi = [];
        foreach ($mahasiswa as $m) {
            $prof = $m['program_studi'] ?? 'Lainnya';
            $mahasiswaByProfesi[$prof][] = $m;
        }

        return view('Pendidikan/AdminDiklat/user_detail', [
            'menu' => 'user',
            'institusi' => $institusi,
            'profesiList' => $profesiList,
            'mahasiswaByProfesi' => $mahasiswaByProfesi,
        ]);
    }

    public function userProfesi($id, $profesi)
    {
        $institusi = $this->institusiModel->find($id);
        if (!$institusi) {
            return redirect()->to(base_url('pendidikan/admin/diklat/user'))->with('error', 'Data tidak ditemukan');
        }

        $mahasiswaList = $this->mahasiswaModel
            ->where('institusi_id', $id)
            ->where('program_studi', $profesi)
            ->findAll();

        return view('Pendidikan/AdminDiklat/user_profesi', [
            'menu' => 'user',
            'institusi' => $institusi,
            'profesi' => urldecode($profesi),
            'mahasiswaList' => $mahasiswaList,
        ]);
    }

    public function pengajuan()
    {
        $pengajuanList = $this->pengajuanModel
            ->select('pengajuan_praktik_pendidikan.*, institusi_pendidikan.nama_institusi')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = pengajuan_praktik_pendidikan.institusi_id', 'left')
            ->whereIn('pengajuan_praktik_pendidikan.status', ['Menunggu', 'Revisi'])
            ->orderBy('pengajuan_praktik_pendidikan.created_at', 'DESC')
            ->findAll();

        foreach ($pengajuanList as &$p) {
            $p['jumlah_mahasiswa'] = $this->mahasiswaModel
                ->where('institusi_id', $p['institusi_id'])
                ->whereIn('status', ['Disetujui', 'Aktif', '1'])
                ->countAllResults();
        }

        return view('Pendidikan/AdminDiklat/pengajuan', [
            'menu' => 'pengajuan',
            'viewMode' => 'list',
            'pengajuanList' => $pengajuanList,
            'totalPengajuan' => count($pengajuanList),
        ]);
    }

    public function pengajuanDetail($id)
    {
        $detail = [
            'pengajuan' => $this->pengajuanModel
                ->select('pengajuan_praktik_pendidikan.*, institusi_pendidikan.nama_institusi')
                ->join('institusi_pendidikan', 'institusi_pendidikan.id = pengajuan_praktik_pendidikan.institusi_id', 'left')
                ->find($id),
        ];

        if (!$detail['pengajuan']) {
            return redirect()->to(base_url('pendidikan/admin/diklat/pengajuan'))->with('error', 'Data tidak ditemukan');
        }

        $db = \Config\Database::connect();

        $builder = $db->table('penempatan_peserta_pendidikan');
        $builder->select('mahasiswa_pendidikan.*');
        $builder->join('mahasiswa_pendidikan', 'mahasiswa_pendidikan.id = penempatan_peserta_pendidikan.mahasiswa_id');
        $builder->where('penempatan_peserta_pendidikan.pengajuan_id', $id);
        $builder->whereIn('mahasiswa_pendidikan.status', ['Disetujui', 'Aktif', '1']);
        $detail['mahasiswa'] = $builder->get()->getResultArray();

        $builder2 = $db->table('penempatan_peserta_pendidikan');
        $builder2->select('mahasiswa_pendidikan.*');
        $builder2->join('mahasiswa_pendidikan', 'mahasiswa_pendidikan.id = penempatan_peserta_pendidikan.mahasiswa_id');
        $builder2->where('penempatan_peserta_pendidikan.pengajuan_id', $id);
        $detail['all_mahasiswa'] = $builder2->get()->getResultArray();

        return view('Pendidikan/AdminDiklat/pengajuan', [
            'menu' => 'pengajuan',
            'viewMode' => 'detail',
            'detail' => $detail,
            'totalPengajuan' => 0,
            'pengajuanList' => [],
        ]);
    }


    public function staseDetail($id)
    {
        $stase = $this->staseModel
            ->select('stase_pendidikan.*, profesi_pelatihan.nama_profesi')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = stase_pendidikan.profesi_id', 'left')
            ->find($id);

        if (!$stase) {
            return redirect()->to(base_url('pendidikan/admin/diklat/stase'))->with('error', 'Stase tidak ditemukan');
        }

        $mappingModel = new \App\Models\StaseRuanganCiModel();
        $mappings = $mappingModel->where('stase_id', $id)->findAll();
        $mappedRooms = [];
        foreach ($mappings as $m) {
            $mappedRooms[$m['ruangan_id']] = [
                'ci_id' => $m['ci_id'],
                'mahasiswa_ids' => $m['mahasiswa_ids'] ? json_decode($m['mahasiswa_ids'], true) : []
            ];
        }

        $ruanganIds = $stase['ruangan'] ? explode(',', $stase['ruangan']) : [];
        $db = \Config\Database::connect();
        $ruanganList = [];
        if (!empty($ruanganIds)) {
            $ruanganList = $db->table('unit_kerja_pelatihan')
                ->whereIn('id_unit_kerja', $ruanganIds)
                ->get()->getResultArray();
        }

        $ciList = $this->ciModel->where('id_profesi', $stase['profesi_id'])->findAll();

        $ciList = array_map(function ($ci) use ($stase) {
            $hasOverlap = false;
            if ($stase['tanggal_mulai'] && $stase['tanggal_akhir']) {
                $db = \Config\Database::connect();
                $countMapping = $db->table('stase_ruangan_ci_pendidikan')
                    ->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id')
                    ->where('stase_ruangan_ci_pendidikan.ci_id', $ci['id'])
                    ->where('stase_ruangan_ci_pendidikan.stase_id !=', $stase['id'])
                    ->where('stase_pendidikan.tanggal_mulai <=', $stase['tanggal_akhir'])
                    ->where('stase_pendidikan.tanggal_akhir >=', $stase['tanggal_mulai'])
                    ->countAllResults();
                $countDirect = $db->table('stase_pendidikan')
                    ->where('ci_id', $ci['id'])
                    ->where('id !=', $stase['id'])
                    ->where('tanggal_mulai <=', $stase['tanggal_akhir'])
                    ->where('tanggal_akhir >=', $stase['tanggal_mulai'])
                    ->countAllResults();
                $hasOverlap = ($countMapping + $countDirect) > 0;
            }
            $ci['available'] = !$hasOverlap;
            $ci['has_overlap'] = $hasOverlap;
            return $ci;
        }, $ciList);

        $mahasiswaModel = new \App\Models\MahasiswaPendidikanModel();
        $mahasiswaList = $mahasiswaModel->where('id_profesi', $stase['profesi_id'])
            ->whereIn('status', ['Disetujui', 'Aktif', '1'])->findAll();

        $overlappingMhsIds = $stase['tanggal_mulai'] && $stase['tanggal_akhir']
            ? $this->getOverlappingMahasiswaIds($stase['id'])
            : [];
        $mahasiswaList = array_map(function ($m) use ($overlappingMhsIds) {
            $m['has_overlap'] = in_array($m['id'], $overlappingMhsIds);
            return $m;
        }, $mahasiswaList);

        $institusiModel = new \App\Models\InstitusiPendidikanModel();
        $institusiList = $institusiModel->findAll();

        return view('Pendidikan/AdminDiklat/stase_detail', [
            'menu' => 'stase',
            'stase' => $stase,
            'ruanganList' => $ruanganList,
            'ciList' => $ciList,
            'mahasiswaList' => $mahasiswaList,
            'institusiList' => $institusiList,
            'mappedRooms' => $mappedRooms
        ]);
    }
    public function list()
    {
        $institusi = $this->institusiModel
            ->select('institusi_pendidikan.*, users_pendidikan.email')
            ->join('users_pendidikan', 'users_pendidikan.id = institusi_pendidikan.user_id', 'left')
            ->orderBy('institusi_pendidikan.created_at', 'DESC')
            ->findAll();

        foreach ($institusi as &$inst) {
            $inst['has_submitted_revision'] = $inst['status_verifikasi'] === 'revision' && !empty($inst['revisi_dikirim_at']);
            $inst['file_mou_url'] = $inst['file_mou']
                ? base_url('pendidikan/admin/diklat/api/institusi/file/' . $inst['id'] . '/mou')
                : null;
            $inst['file_permohonan_url'] = $inst['file_permohonan']
                ? base_url('pendidikan/admin/diklat/api/institusi/file/' . $inst['id'] . '/permohonan')
                : null;

            $inst['dokumen'] = $this->dokumenModel
                ->where('institusi_id', $inst['id'])
                ->findAll();
        }

        return $this->response->setJSON($institusi);
    }

    public function detail($id)
    {
        $institusi = $this->institusiModel
            ->select('institusi_pendidikan.*, users_pendidikan.email')
            ->join('users_pendidikan', 'users_pendidikan.id = institusi_pendidikan.user_id', 'left')
            ->find($id);

        if (!$institusi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $institusi['has_submitted_revision'] = $institusi['status_verifikasi'] === 'revision' && !empty($institusi['revisi_dikirim_at']);

        $institusi['file_mou_url'] = $institusi['file_mou']
            ? base_url('pendidikan/admin/diklat/api/institusi/file/' . $id . '/mou')
            : null;
        $institusi['file_permohonan_url'] = $institusi['file_permohonan']
            ? base_url('pendidikan/admin/diklat/api/institusi/file/' . $id . '/permohonan')
            : null;

        $mahasiswa = $this->mahasiswaModel
            ->where('institusi_id', $id)
            ->whereIn('status', ['Disetujui', 'Aktif', '1'])
            ->findAll();

        $dokumen = $this->dokumenModel
            ->where('institusi_id', $id)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'institusi' => $institusi,
                'mahasiswa' => $mahasiswa,
                'dokumen' => $dokumen
            ]
        ]);
    }

    public function approve($id)
    {
        if (!$this->institusiModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $this->institusiModel->update($id, [
            'status_verifikasi' => 'approved',
            'catatan_revisi' => null,
            'alasan_penolakan' => null,
        ]);

        // Approve all pending students belonging to this institution
        $this->mahasiswaModel->where('institusi_id', $id)
            ->where('status', 'Menunggu')
            ->set(['status' => 'Disetujui'])
            ->update();

        // Also update pengajuan status if needed
        $pengajuanModel = new \App\Models\PengajuanPraktikPendidikanModel();
        $pengajuanModel->where('institusi_id', $id)
            ->where('status', 'Menunggu')
            ->set(['status' => 'Disetujui'])
            ->update();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Institusi dan mahasiswa berhasil disetujui'
        ]);
    }

    public function decline($id)
    {
        if (!$this->institusiModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $alasan = $this->request->getPost('alasan_penolakan') ?? '';

        $this->institusiModel->update($id, [
            'status_verifikasi' => 'rejected',
            'alasan_penolakan' => $alasan,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Institusi ditolak'
        ]);
    }

    public function revision($id)
    {
        if (!$this->institusiModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $catatan = $this->request->getPost('catatan_revisi');
        if (empty($catatan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Catatan revisi harus diisi'
            ])->setStatusCode(400);
        }

        $this->institusiModel->update($id, [
            'status_verifikasi' => 'revision',
            'catatan_revisi' => $catatan,
            'revisi_dikirim_at' => null,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Revisi berhasil diminta'
        ]);
    }

    public function resubmit($id)
    {
        $institusi = $this->institusiModel->find($id);
        if (!$institusi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        if ($institusi['status_verifikasi'] !== 'revision' || empty($institusi['revisi_dikirim_at'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Belum ada revisi institusi yang menunggu peninjauan.'
            ])->setStatusCode(400);
        }

        $this->institusiModel->update($id, [
            'status_verifikasi' => 'pending',
            'catatan_revisi' => null,
            'revisi_dikirim_at' => null,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Revisi telah ditinjau. Data institusi dipindahkan ke inbox untuk keputusan admin.'
        ]);
    }

    public function file($id, $jenis)
    {
        $institusi = $this->institusiModel->find($id);
        if (!$institusi) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data institusi tidak ditemukan'
            ])->setStatusCode(404);
        }

        $kolom = ($jenis === 'mou') ? 'file_mou' : 'file_permohonan';
        $namaFile = $institusi[$kolom] ?? null;

        if (!$namaFile) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ])->setStatusCode(404);
        }

        // Check both possible locations
        $paths = [
            FCPATH . 'uploads/institusi/' . $namaFile,
            WRITEPATH . 'uploads/dokumen_institusi/' . $namaFile,
        ];
        $path = null;
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }
        if (!$path) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak ditemukan di server'
            ])->setStatusCode(404);
        }

        $download = $this->response->download($path, null, true);
        if ($this->request->getGet('download') !== '1') {
            $download->inline();
        }
        return $download;
    }

    public function viewDokumen($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ])->setStatusCode(404);
        }

        $path = WRITEPATH . 'uploads/dokumen_institusi/' . $dokumen['nama_file'];
        if (!file_exists($path)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak ditemukan di server'
            ])->setStatusCode(404);
        }

        return $this->response->download($path, null, true)
            ->inline();
    }

    public function downloadDokumen($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ])->setStatusCode(404);
        }

        $path = WRITEPATH . 'uploads/dokumen_institusi/' . $dokumen['nama_file'];
        if (!file_exists($path)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak ditemukan di server'
            ])->setStatusCode(404);
        }

        return $this->response->download($path, null, true);
    }

    public function verifikasiDokumen($id)
    {
        $dokumen = $this->dokumenModel->find($id);
        if (!$dokumen) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ])->setStatusCode(404);
        }

        $status = $this->request->getPost('status');
        $keterangan = $this->request->getPost('keterangan');

        if (!in_array($status, ['verified', 'revision'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid'
            ])->setStatusCode(400);
        }

        $this->dokumenModel->update($id, [
            'status' => $status,
            'keterangan' => $keterangan,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status dokumen berhasil diperbarui'
        ]);
    }

    public function pengajuanList()
    {
        $pengajuanModel = new PengajuanPraktikPendidikanModel();

        $pengajuan = $pengajuanModel
            ->select('pengajuan_praktik_pendidikan.*, institusi_pendidikan.nama_institusi')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = pengajuan_praktik_pendidikan.institusi_id', 'left')
            ->whereIn('pengajuan_praktik_pendidikan.status', ['Menunggu', 'Revisi'])
            ->orderBy('pengajuan_praktik_pendidikan.created_at', 'DESC')
            ->findAll();

        $mahasiswaModel = new MahasiswaPendidikanModel();
        foreach ($pengajuan as &$p) {
            $p['jumlah_mahasiswa'] = $mahasiswaModel
                ->where('institusi_id', $p['institusi_id'])
                ->whereIn('status', ['Disetujui', 'Aktif', '1'])
                ->countAllResults();
        }

        return $this->response->setJSON($pengajuan);
    }

    public function apiPengajuanDetail($id)
    {
        $pengajuanModel = new PengajuanPraktikPendidikanModel();

        $pengajuan = $pengajuanModel
            ->select('pengajuan_praktik_pendidikan.*, institusi_pendidikan.nama_institusi')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = pengajuan_praktik_pendidikan.institusi_id', 'left')
            ->find($id);

        if (!$pengajuan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ])->setStatusCode(404);
        }

        $mahasiswaModel = new MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel
            ->where('institusi_id', $pengajuan['institusi_id'])
            ->whereIn('status', ['Disetujui', 'Aktif', '1'])
            ->findAll();

        $allMahasiswa = $mahasiswaModel
            ->where('institusi_id', $pengajuan['institusi_id'])
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'pengajuan' => $pengajuan,
                'mahasiswa' => $mahasiswa,
                'all_mahasiswa' => $allMahasiswa
            ]
        ]);
    }

    public function pengajuanApprove($id)
    {
        $pengajuanModel = new PengajuanPraktikPendidikanModel();

        if (!$pengajuanModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ])->setStatusCode(404);
        }

        $pengajuanModel->update($id, [
            'status' => 'Disetujui',
            'catatan_admin' => null,
        ]);

        // Update status semua mahasiswa dalam pengajuan ini menjadi 'Disetujui'
        $penempatanModel = new \App\Models\PenempatanPesertaPendidikanModel();
        $mahasiswaList = $penempatanModel
            ->select('mahasiswa_id')
            ->where('pengajuan_id', $id)
            ->findAll();

        $mahasiswaIds = array_column($mahasiswaList, 'mahasiswa_id');
        if (!empty($mahasiswaIds)) {
            $this->mahasiswaModel
                ->whereIn('id', $mahasiswaIds)
                ->set(['status' => 'Disetujui'])
                ->update();
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengajuan berhasil disetujui'
        ]);
    }

    public function pengajuanDecline($id)
    {
        $pengajuanModel = new PengajuanPraktikPendidikanModel();

        $pengajuan = $pengajuanModel->find($id);
        if (!$pengajuan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ])->setStatusCode(404);
        }

        $alasan = $this->request->getPost('alasan_penolakan') ?? '';

        $pengajuanModel->update($id, [
            'status' => 'Ditolak',
            'catatan_admin' => $alasan,
        ]);

        // Update status semua mahasiswa dalam pengajuan ini menjadi 'Ditolak'
        $penempatanModel = new \App\Models\PenempatanPesertaPendidikanModel();
        $mahasiswaList = $penempatanModel
            ->select('mahasiswa_id')
            ->where('pengajuan_id', $id)
            ->findAll();

        $mahasiswaIds = array_column($mahasiswaList, 'mahasiswa_id');
        if (!empty($mahasiswaIds)) {
            $this->mahasiswaModel
                ->whereIn('id', $mahasiswaIds)
                ->set(['status' => 'Ditolak'])
                ->update();
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Pengajuan ditolak'
        ]);
    }

    public function pengajuanRevision($id)
    {
        $pengajuanModel = new PengajuanPraktikPendidikanModel();

        if (!$pengajuanModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengajuan tidak ditemukan'
            ])->setStatusCode(404);
        }

        $catatan = $this->request->getPost('catatan_admin');
        if (empty($catatan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Catatan revisi harus diisi'
            ])->setStatusCode(400);
        }

        $pengajuanModel->update($id, [
            'status' => 'Revisi',
            'catatan_admin' => $catatan,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Revisi berhasil diminta'
        ]);
    }

    public function profesiList()
    {
        $model = new ProfesiPelatihanModel();
        return $this->response->setJSON($model->findAll());
    }

    public function unitKerjaList()
    {
        $model = new UnitKerjaPelatihanModel();
        return $this->response->setJSON($model->findAll());
    }

    public function staseList()
    {
        $staseModel = new StasePendidikanModel();

        $data = $staseModel
            ->select('stase_pendidikan.*, ci_pendidikan.nama_lengkap as ci_name, profesi_Pelatihan.nama_profesi')
            ->join('ci_pendidikan', 'ci_pendidikan.id = stase_pendidikan.ci_id', 'left')
            ->join('profesi_Pelatihan', 'profesi_Pelatihan.id_profesi = stase_pendidikan.profesi_id', 'left')
            ->orderBy('stase_pendidikan.nama_stase', 'ASC')
            ->findAll();

        return $this->response->setJSON($data);
    }

    public function staseStore()
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ])->setStatusCode(400);
        }

        $namaStase = trim($json['nama_stase'] ?? '');
        $profesiId = (int) ($json['profesi_id'] ?? 0);
        $ruangan = trim($json['ruangan'] ?? '');
        $ciId = (int) ($json['ci_id'] ?? 0);
        $tanggalMulai = $json['tanggal_mulai'] ?? null;
        $tanggalAkhir = $json['tanggal_akhir'] ?? null;

        if (!preg_match("/^[\\p{L}0-9][\\p{L}0-9\\s.,'()\/-]{2,149}$/u", $namaStase)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama stase wajib diisi'
            ])->setStatusCode(422);
        }

        if ($tanggalMulai && $tanggalAkhir && $tanggalAkhir < $tanggalMulai) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.'
            ])->setStatusCode(422);
        }

        $staseModel = new StasePendidikanModel();
        $id = $staseModel->insert([
            'nama_stase' => $namaStase,
            'profesi_id' => $profesiId > 0 ? $profesiId : null,
            'ruangan' => $ruangan,
            'ci_id' => $ciId > 0 ? $ciId : null,
            'tanggal_mulai' => $tanggalMulai ?: null,
            'tanggal_akhir' => $tanggalAkhir ?: null,
        ]);

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan stase'
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Stase berhasil ditambahkan',
            'id' => $id,
        ]);
    }

    public function staseUpdate($id)
    {
        $staseModel = new StasePendidikanModel();
        if (!$staseModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data stase tidak ditemukan'
            ])->setStatusCode(404);
        }

        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ])->setStatusCode(400);
        }

        $namaStase = trim($json['nama_stase'] ?? '');
        $profesiId = (int) ($json['profesi_id'] ?? 0);
        $ruangan = trim($json['ruangan'] ?? '');
        $ciId = (int) ($json['ci_id'] ?? 0);
        $tanggalMulai = $json['tanggal_mulai'] ?? null;
        $tanggalAkhir = $json['tanggal_akhir'] ?? null;

        if (!preg_match("/^[\\p{L}0-9][\\p{L}0-9\\s.,'()\/-]{2,149}$/u", $namaStase)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama stase wajib diisi'
            ])->setStatusCode(422);
        }

        if ($tanggalMulai && $tanggalAkhir && $tanggalAkhir < $tanggalMulai) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.'
            ])->setStatusCode(422);
        }

        $staseModel->update($id, [
            'nama_stase' => $namaStase,
            'profesi_id' => $profesiId > 0 ? $profesiId : null,
            'ruangan' => $ruangan,
            'ci_id' => $ciId > 0 ? $ciId : null,
            'tanggal_mulai' => $tanggalMulai ?: null,
            'tanggal_akhir' => $tanggalAkhir ?: null,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Stase berhasil diperbarui'
        ]);
    }

    public function staseDelete($id)
    {
        $staseModel = new StasePendidikanModel();
        if (!$staseModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data stase tidak ditemukan'
            ])->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        $this->lockStaseAssignments($db);
        $usedCount = $db->table('penempatan_peserta_pendidikan')->where('stase_id', $id)->countAllResults()
            + $db->table('stase_ruangan_ci_pendidikan')->where('stase_id', $id)->countAllResults()
            + $db->table('tugas_pendidikan')->where('stase_id', $id)->countAllResults();
        if ($db->fieldExists('stase_id', 'logbook_pendidikan')) {
            $usedCount += $db->table('logbook_pendidikan')->where('stase_id', $id)->countAllResults();
        }
        if ($usedCount > 0) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stase tidak dapat dihapus karena masih memiliki penempatan, CI, atau riwayat akademik.'
            ])->setStatusCode(409);
        }

        if (!$staseModel->delete($id) || !$db->transStatus()) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus stase.'])->setStatusCode(500);
        }
        $db->transCommit();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Stase berhasil dihapus'
        ]);
    }

    public function staseAssignCi($id)
    {
        $staseModel = new StasePendidikanModel();
        $stase = $staseModel->find($id);
        if (!$stase) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data stase tidak ditemukan'
            ])->setStatusCode(404);
        }

        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ])->setStatusCode(400);
        }

        $ciId = (int) ($json['ci_id'] ?? 0);
        if ($ciId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'CI harus dipilih'
            ])->setStatusCode(422);
        }

        $ciModel = new CiPendidikanModel();
        if (!$ciModel->find($ciId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'CI tidak ditemukan'
            ])->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $db->transBegin();
        $this->lockStaseAssignments($db);
        $stase = $staseModel->find($id);
        if ($error = $this->validateStaseAssignment($stase, $ciId, [])) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $error])->setStatusCode(422);
        }
        if (!$staseModel->update($id, ['ci_id' => $ciId]) || !$db->transStatus()) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menugaskan CI.'])->setStatusCode(500);
        }
        $db->transCommit();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'CI berhasil ditugaskan ke stase'
        ]);
    }

    public function staseRemoveCi($id)
    {
        $staseModel = new StasePendidikanModel();
        $stase = $staseModel->find($id);
        if (!$stase) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data stase tidak ditemukan'
            ])->setStatusCode(404);
        }

        $staseModel->update($id, ['ci_id' => null]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'CI berhasil dilepaskan dari stase'
        ]);
    }

    public function staseMahasiswaList($id)
    {
        $staseModel = new StasePendidikanModel();
        if (!$staseModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data stase tidak ditemukan'
            ])->setStatusCode(404);
        }

        $penempatanModel = new PenempatanPesertaPendidikanModel();
        $mahasiswa = $penempatanModel
            ->select('mahasiswa_pendidikan.*, penempatan_peserta_pendidikan.id as penempatan_id, penempatan_peserta_pendidikan.status_aktif, institusi_pendidikan.nama_institusi')
            ->join('mahasiswa_pendidikan', 'mahasiswa_pendidikan.id = penempatan_peserta_pendidikan.mahasiswa_id')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = mahasiswa_pendidikan.institusi_id', 'left')
            ->where('penempatan_peserta_pendidikan.stase_id', $id)
            ->findAll();

        return $this->response->setJSON($mahasiswa);
    }

    private function getMahasiswaIdsInStase($staseId)
    {
        $ids = [];

        $penempatanModel = new PenempatanPesertaPendidikanModel();
        $penempatan = $penempatanModel
            ->select('mahasiswa_id')
            ->where('stase_id', $staseId)
            ->findAll();
        $ids = array_column($penempatan, 'mahasiswa_id');

        $db = \Config\Database::connect();
        $mappings = $db->table('stase_ruangan_ci_pendidikan')
            ->select('mahasiswa_ids')
            ->where('stase_id', $staseId)
            ->where('mahasiswa_ids IS NOT NULL')
            ->get()
            ->getResultArray();

        foreach ($mappings as $m) {
            $decoded = json_decode($m['mahasiswa_ids'], true);
            if (is_array($decoded)) {
                $ids = array_unique(array_merge($ids, $decoded));
            }
        }

        return $ids;
    }

    private function getOverlappingMahasiswaIds($staseId, $filterMahasiswaIds = null)
    {
        $staseModel = new StasePendidikanModel();
        $currentStase = $staseModel->find($staseId);

        if (!$currentStase || !$currentStase['tanggal_mulai'] || !$currentStase['tanggal_akhir']) {
            return [];
        }

        $overlappingIds = [];

        $penempatanModel = new PenempatanPesertaPendidikanModel();
        $query = $penempatanModel
            ->select('penempatan_peserta_pendidikan.mahasiswa_id')
            ->join('stase_pendidikan', 'stase_pendidikan.id = penempatan_peserta_pendidikan.stase_id')
            ->where('penempatan_peserta_pendidikan.stase_id !=', $staseId)
            ->where('stase_pendidikan.tanggal_mulai <=', $currentStase['tanggal_akhir'])
            ->where('stase_pendidikan.tanggal_akhir >=', $currentStase['tanggal_mulai']);

        if ($filterMahasiswaIds !== null) {
            $query->whereIn('penempatan_peserta_pendidikan.mahasiswa_id', $filterMahasiswaIds);
        }

        $overlapping = $query->findAll();
        $overlappingIds = array_column($overlapping, 'mahasiswa_id');

        $db = \Config\Database::connect();
        $mappingQuery = $db->table('stase_ruangan_ci_pendidikan')
            ->select('stase_ruangan_ci_pendidikan.mahasiswa_ids')
            ->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id')
            ->where('stase_ruangan_ci_pendidikan.stase_id !=', $staseId)
            ->where('stase_ruangan_ci_pendidikan.mahasiswa_ids IS NOT NULL')
            ->where('stase_pendidikan.tanggal_mulai <=', $currentStase['tanggal_akhir'])
            ->where('stase_pendidikan.tanggal_akhir >=', $currentStase['tanggal_mulai'])
            ->get()
            ->getResultArray();

        foreach ($mappingQuery as $m) {
            $decoded = json_decode($m['mahasiswa_ids'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $mhsId) {
                    if ($filterMahasiswaIds === null || in_array($mhsId, $filterMahasiswaIds)) {
                        $overlappingIds[] = $mhsId;
                    }
                }
            }
        }

        return array_unique($overlappingIds);
    }

    private function lockStaseAssignments($db): void
    {
        // Semua endpoint penempatan memakai urutan lock yang sama untuk mencegah
        // dua admin melewati pemeriksaan bentrok dengan data yang sama-sama lama.
        if (in_array($db->DBDriver, ['MySQLi', 'Postgre'], true)) {
            $table = $db->protectIdentifiers($db->prefixTable('stase_pendidikan'));
            $db->query('SELECT id FROM ' . $table . ' ORDER BY id FOR UPDATE');
        }
    }

    private function validateStaseAssignment(?array $stase, $ciId, array $mahasiswaIds, $ruanganId = null): ?string
    {
        if (!$stase) {
            return 'Data stase tidak ditemukan.';
        }
        if ($ruanganId !== null) {
            $rooms = array_map('trim', explode(',', $stase['ruangan'] ?? ''));
            if (!filter_var($ruanganId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                || !in_array((string) $ruanganId, $rooms, true)
                || !$this->unitKerjaModel->find($ruanganId)) {
                return 'Ruangan tidak terdaftar pada stase ini.';
            }
        }
        if (!empty($mahasiswaIds) && empty($ciId) && $ruanganId !== null) {
            return 'Pilih CI terlebih dahulu sebelum menyimpan mahasiswa';
        }
        if (!empty($ciId) || !empty($mahasiswaIds)) {
            if (empty($stase['profesi_id']) || !$this->profesiModel->find($stase['profesi_id'])) {
                return 'Pilih profesi stase yang valid sebelum menempatkan CI atau mahasiswa.';
            }
            foreach (['tanggal_mulai', 'tanggal_akhir'] as $field) {
                $date = $stase[$field] ?? null;
                $parsed = is_string($date) ? \DateTimeImmutable::createFromFormat('!Y-m-d', $date) : false;
                if (!$parsed || $parsed->format('Y-m-d') !== $date) {
                    return 'Lengkapi periode stase yang valid sebelum menempatkan CI atau mahasiswa.';
                }
            }
            if ($stase['tanggal_akhir'] < $stase['tanggal_mulai']) {
                return 'Tanggal akhir stase tidak boleh lebih awal dari tanggal mulai.';
            }
        }
        if (!empty($ciId)) {
            if (!filter_var($ciId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                return 'CI tidak valid.';
            }
            $ci = $this->ciModel->find($ciId);
            if (!$ci || (int) $ci['id_profesi'] !== (int) $stase['profesi_id']) {
                return 'CI tidak ditemukan atau profesinya tidak sesuai stase.';
            }
            if ($ruanganId !== null && (int) $ci['id_unit_kerja'] !== (int) $ruanganId) {
                return 'CI tidak bertugas pada ruangan yang dipilih.';
            }
            if (!empty($stase['tanggal_mulai']) && !empty($stase['tanggal_akhir'])) {
                $db = \Config\Database::connect();
                $mappingCount = $db->table('stase_ruangan_ci_pendidikan m')
                    ->join('stase_pendidikan s', 's.id = m.stase_id')
                    ->where('m.ci_id', $ciId)->where('m.stase_id !=', $stase['id'])
                    ->where('s.tanggal_mulai <=', $stase['tanggal_akhir'])
                    ->where('s.tanggal_akhir >=', $stase['tanggal_mulai'])->countAllResults();
                $directCount = $db->table('stase_pendidikan')->where('ci_id', $ciId)
                    ->where('id !=', $stase['id'])
                    ->where('tanggal_mulai <=', $stase['tanggal_akhir'])
                    ->where('tanggal_akhir >=', $stase['tanggal_mulai'])->countAllResults();
                if ($mappingCount + $directCount > 0) {
                    return 'Periode CI bertabrakan dengan stase lain.';
                }
            }
        } elseif ($ciId !== null && $ciId !== '' && $ciId !== 0 && $ciId !== '0') {
            return 'CI tidak valid.';
        }
        foreach ($mahasiswaIds as $mahasiswaId) {
            if (!filter_var($mahasiswaId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
                return 'ID mahasiswa tidak valid.';
            }
            $mahasiswa = $this->mahasiswaModel->find($mahasiswaId);
            if (!$mahasiswa || (int) $mahasiswa['id_profesi'] !== (int) $stase['profesi_id']) {
                return 'Mahasiswa tidak ditemukan atau profesinya tidak sesuai stase.';
            }
            if (!in_array($mahasiswa['status'], ['Disetujui', 'Aktif', '1'], true)) {
                return 'Hanya mahasiswa yang telah disetujui dan masih aktif yang dapat ditempatkan.';
            }
        }
        if (!empty($mahasiswaIds) && !empty($this->getOverlappingMahasiswaIds($stase['id'], $mahasiswaIds))) {
            return 'Periode mahasiswa bertabrakan dengan stase lain.';
        }
        return null;
    }

    public function staseAvailableMahasiswa()
    {
        $staseId = $this->request->getGet('stase_id');

        $excludeIds = [];
        $overlappingIds = [];

        if ($staseId) {
            $excludeIds = $this->getMahasiswaIdsInStase($staseId);
            $overlappingIds = $this->getOverlappingMahasiswaIds($staseId);
        }

        $mahasiswaModel = new MahasiswaPendidikanModel();
        $builder = $mahasiswaModel
            ->select('mahasiswa_pendidikan.*, institusi_pendidikan.nama_institusi')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = mahasiswa_pendidikan.institusi_id', 'left')
            ->whereIn('mahasiswa_pendidikan.status', ['Disetujui', 'Aktif', '1']);

        if (!empty($excludeIds)) {
            $builder->whereNotIn('mahasiswa_pendidikan.id', $excludeIds);
        }

        $mahasiswa = $builder->findAll();

        $result = array_map(function ($m) use ($overlappingIds) {
            $m['has_overlap'] = in_array($m['id'], $overlappingIds);
            $m['available'] = !in_array($m['id'], $overlappingIds);
            return $m;
        }, $mahasiswa);

        return $this->response->setJSON($result);
    }

    public function staseAddMahasiswa($id)
    {
        $staseModel = new StasePendidikanModel();
        if (!$staseModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data stase tidak ditemukan'
            ])->setStatusCode(404);
        }

        $json = $this->request->getJSON(true);
        if (!$json || empty($json['mahasiswa_ids'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal satu mahasiswa'
            ])->setStatusCode(422);
        }

        $stase = $staseModel->find($id);
        $mahasiswaIds = $json['mahasiswa_ids'];
        if (!is_array($mahasiswaIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Daftar mahasiswa tidak valid.'])->setStatusCode(422);
        }
        $db = \Config\Database::connect();
        $db->transBegin();
        $this->lockStaseAssignments($db);
        $stase = $staseModel->find($id);
        if ($error = $this->validateStaseAssignment($stase, null, $mahasiswaIds)) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $error])->setStatusCode(422);
        }
        $penempatanModel = new PenempatanPesertaPendidikanModel();
        $added = 0;
        $skipped = [];

        $overlappingIds = $this->getOverlappingMahasiswaIds($id, $mahasiswaIds);

        foreach ($mahasiswaIds as $mhsId) {
            if (in_array($mhsId, $overlappingIds)) {
                $skipped[] = $mhsId;
                continue;
            }

            $exists = $penempatanModel
                ->where('mahasiswa_id', $mhsId)
                ->where('stase_id', $id)
                ->first();

            if (!$exists) {
                $penempatanModel->insert([
                    'mahasiswa_id' => $mhsId,
                    'stase_id' => $id,
                    'pengajuan_id' => null,
                    'status_aktif' => 1,
                ]);
                $added++;
            }
        }

        $msg = "$added mahasiswa berhasil ditambahkan ke stase";
        if (!empty($skipped)) {
            $msg .= ". " . count($skipped) . " mahasiswa dilewati karena periode stase bertabrakan.";
        }

        if (!$db->transStatus()) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan penempatan.'])->setStatusCode(500);
        }
        $db->transCommit();
        return $this->response->setJSON([
            'success' => true,
            'message' => $msg
        ]);
    }

    public function staseRemoveMahasiswa($id)
    {
        $penempatanModel = new PenempatanPesertaPendidikanModel();
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['penempatan_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data penempatan tidak valid'
            ])->setStatusCode(422);
        }

        $penempatan = $penempatanModel->find($json['penempatan_id']);
        if (!$penempatan || $penempatan['stase_id'] != $id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data penempatan tidak ditemukan'
            ])->setStatusCode(404);
        }

        $penempatanModel->delete($json['penempatan_id']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Mahasiswa berhasil dikeluarkan dari stase'
        ]);
    }

    public function mahasiswaUpdate($id)
    {
        $mahasiswaModel = new MahasiswaPendidikanModel();
        if (!$mahasiswaModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ])->setStatusCode(404);
        }

        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid'
            ])->setStatusCode(400);
        }

        $allowed = ['nama_lengkap', 'nim', 'jenis_kelamin', 'jenjang', 'program_studi', 'tanggal_lahir', 'semester', 'no_hp', 'email', 'status'];
        $data = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $json)) {
                $data[$field] = $json[$field];
            }
        }

        if (empty($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang diubah'
            ])->setStatusCode(422);
        }

        $mahasiswaModel->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Mahasiswa berhasil diperbarui'
        ]);
    }

    public function mahasiswaDelete($id)
    {
        $mahasiswaModel = new MahasiswaPendidikanModel();
        if (!$mahasiswaModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ])->setStatusCode(404);
        }

        $penempatanModel = new PenempatanPesertaPendidikanModel();
        $usedCount = $penempatanModel->where('mahasiswa_id', $id)->countAllResults();
        if ($usedCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mahasiswa tidak dapat dihapus karena sudah terdaftar di stase. Keluarkan dari stase terlebih dahulu.'
            ])->setStatusCode(409);
        }

        $mahasiswaModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Mahasiswa berhasil dihapus'
        ]);
    }

    public function mahasiswaUploadInvoice($id)
    {
        $mahasiswaModel = new MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->find($id);
        if (!$mahasiswa) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ])->setStatusCode(404);
        }

        if (($mahasiswa['payment_status'] ?? '') === 'Lunas') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pembayaran sudah lunas. Invoice dan nominal tidak dapat diubah.'
            ])->setStatusCode(409);
        }

        $file = $this->request->getFile('invoice_file');
        $nominal = $this->request->getPost('nominal');

        $data = [];
        if ($file) {
            if (!$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Upload file gagal: ' . $file->getErrorString() . ' (Mungkin ukuran file terlalu besar, maksimal 2MB)'
                ])->setStatusCode(422);
            }
            if (!$file->hasMoved()) {
                if ($file->getMimeType() !== 'application/pdf' || $file->getSize() > 2 * 1024 * 1024) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'File harus berupa PDF dengan ukuran maksimal 2 MB'
                    ])->setStatusCode(422);
                }
                $uploadPath = FCPATH . 'uploads/invoices';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                // Hapus invoice lama jika ada
                if (!empty($mahasiswa['invoice_file'])) {
                    $oldFile = $uploadPath . '/' . $mahasiswa['invoice_file'];
                    if (file_exists($oldFile)) {
                        @unlink($oldFile);
                    }
                }
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $data['invoice_file'] = $newName;
            }
        }

        if ($nominal !== null && $nominal !== '') {
            if (!preg_match('/^[0-9]+$/', (string) $nominal) || (int) $nominal < 0) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nominal harus berupa angka nol atau lebih.'])->setStatusCode(422);
            }
            $data['nominal'] = (int) $nominal;
        }

        if (empty($data)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada data yang diunggah'
            ])->setStatusCode(422);
        }

        $data['payment_status'] = 'Belum Bayar';

        $mahasiswaModel->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Invoice berhasil diunggah'
        ]);
    }

    public function mahasiswaBuktiBayar($id)
    {
        $mahasiswaModel = new MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->find($id);
        if (!$mahasiswa || !$mahasiswa['file_bukti_bayar']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bukti bayar tidak ditemukan'
            ])->setStatusCode(404);
        }

        $paths = [
            FCPATH . 'uploads/dokumen_mahasiswa/' . $mahasiswa['file_bukti_bayar'],
            FCPATH . 'uploads/bukti_bayar/' . $mahasiswa['file_bukti_bayar'],
        ];
        $path = null;
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }
        if (!$path) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ])->setStatusCode(404);
        }

        return $this->response->download($path, null, true)->inline();
    }

    public function mahasiswaVerifikasiPembayaran($id)
    {
        $mahasiswaModel = new MahasiswaPendidikanModel();
        $mahasiswa = $mahasiswaModel->find($id);
        if (!$mahasiswa) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ])->setStatusCode(404);
        }

        $json = $this->request->getJSON(true);
        $status = $json['status'] ?? '';

        if (!in_array($status, ['Lunas', 'Belum Bayar', 'Menunggu Verifikasi', 'Ditolak'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid'
            ])->setStatusCode(422);
        }

        $data = ['payment_status' => $status];
        if ($status === 'Ditolak') {
            $alasan = $json['alasan_penolakan'] ?? '';
            if (!is_string($alasan) || trim($alasan) === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Alasan penolakan harus diisi!'
                ])->setStatusCode(422);
            }
            $data['alasan_penolakan'] = trim($alasan);
        } elseif ($status === 'Lunas') {
            $data['alasan_penolakan'] = null;
        }

        $mahasiswaModel->update($id, $data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui'
        ]);
    }



    public function staseSaveRoomMapping($id)
    {
        $input = $this->request->getJSON();
        if (!is_object($input) || (!isset($input->ruangan_id) && !isset($input->mappings))) {
            return $this->response->setJSON(['success' => false, 'status' => 'error', 'message' => 'Invalid data'])->setStatusCode(400);
        }
        $singleRoom = isset($input->ruangan_id);
        if ($singleRoom && (!is_int($input->ruangan_id) && !is_string($input->ruangan_id))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ruangan tidak valid.'])->setStatusCode(422);
        }
        $rows = $singleRoom ? [$input->ruangan_id => $input] : ($input->mappings ?? null);
        if (!is_array($rows) && !is_object($rows)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mapping tidak valid.'])->setStatusCode(422);
        }
        $mappingModel = new \App\Models\StaseRuanganCiModel();
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->lockStaseAssignments($db);
            $stase = $this->staseModel->find($id);
            if (!$stase) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Data stase tidak ditemukan.'])->setStatusCode(404);
            }
            $validatedRows = [];
            foreach ($rows as $ruanganId => $row) {
                if (!is_object($row) || !is_array($row->mahasiswa_ids ?? [])) {
                    $db->transRollback();
                    return $this->response->setJSON(['success' => false, 'message' => 'Daftar mahasiswa tidak valid.'])->setStatusCode(422);
                }
                $ciId = $row->ci_id ?? null;
                $mahasiswaIds = $row->mahasiswa_ids ?? [];
                if ($error = $this->validateStaseAssignment($stase, $ciId, $mahasiswaIds, $ruanganId)) {
                    $db->transRollback();
                    return $this->response->setJSON(['success' => false, 'status' => 'error', 'message' => $error])->setStatusCode(422);
                }
                if (!empty($ciId)) {
                    $validatedRows[] = [
                        'stase_id' => $id,
                        'ruangan_id' => (int) $ruanganId,
                        'ci_id' => (int) $ciId,
                        'mahasiswa_ids' => empty($mahasiswaIds) ? null : json_encode(array_values(array_unique(array_map('intval', $mahasiswaIds)))),
                    ];
                }
            }
            $mappingModel->where('stase_id', $id);
            if ($singleRoom) {
                $mappingModel->where('ruangan_id', $input->ruangan_id);
            }
            if (!$mappingModel->delete()) {
                throw new \RuntimeException('Gagal mengganti mapping.');
            }
            foreach ($validatedRows as $row) {
                if (!$mappingModel->insert($row)) {
                    throw new \RuntimeException('Gagal menyimpan mapping.');
                }
            }
            if (!$db->transStatus()) {
                throw new \RuntimeException('Transaksi mapping gagal.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal menyimpan mapping stase: {message}', ['message' => $e->getMessage()]);
            return $this->response->setJSON(['success' => false, 'status' => 'error', 'message' => 'Gagal menyimpan mapping'])->setStatusCode(500);
        }
        return $this->response->setJSON(['success' => true, 'status' => 'success', 'message' => 'Mapping berhasil disimpan']);
    }


    public function ciApiList()
    {
        $staseId = $this->request->getGet('stase_id');

        $builder = $this->ciModel->builder();
        $data = $builder
            ->select('ci_pendidikan.*, profesi_Pelatihan.nama_profesi, unit_kerja_Pelatihan.nama_unit')
            ->join('profesi_Pelatihan', 'profesi_Pelatihan.id_profesi = ci_pendidikan.id_profesi', 'left')
            ->join('unit_kerja_Pelatihan', 'unit_kerja_Pelatihan.id_unit_kerja = ci_pendidikan.id_unit_kerja', 'left')
            ->get()
            ->getResultArray();

        $overlappingIds = [];
        if ($staseId) {
            $staseModel = new StasePendidikanModel();
            $currentStase = $staseModel->find($staseId);
            if ($currentStase && $currentStase['tanggal_mulai'] && $currentStase['tanggal_akhir']) {
                $db = \Config\Database::connect();

                $overlapViaMapping = $db->table('stase_ruangan_ci_pendidikan')
                    ->select('DISTINCT(stase_ruangan_ci_pendidikan.ci_id) as ci_id')
                    ->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id')
                    ->where('stase_ruangan_ci_pendidikan.stase_id !=', $staseId)
                    ->where('stase_pendidikan.tanggal_mulai <=', $currentStase['tanggal_akhir'])
                    ->where('stase_pendidikan.tanggal_akhir >=', $currentStase['tanggal_mulai'])
                    ->get()
                    ->getResultArray();

                $overlapViaStase = $db->table('stase_pendidikan')
                    ->select('DISTINCT(ci_id) as ci_id')
                    ->where('ci_id IS NOT NULL')
                    ->where('id !=', $staseId)
                    ->where('tanggal_mulai <=', $currentStase['tanggal_akhir'])
                    ->where('tanggal_akhir >=', $currentStase['tanggal_mulai'])
                    ->get()
                    ->getResultArray();

                $overlappingIds = array_unique(array_merge(
                    array_column($overlapViaMapping, 'ci_id'),
                    array_column($overlapViaStase, 'ci_id')
                ));
            }
        }

        $result = array_map(function ($item) use ($overlappingIds) {
            $hasOverlap = in_array($item['id'], $overlappingIds);
            return [
                'id' => $item['id'],
                'name' => $item['nama_lengkap'],
                'profession' => $item['nama_profesi'] ?? '',
                'nama_profesi' => $item['nama_profesi'] ?? '',
                'nama_unit_kerja' => $item['nama_unit'] ?? '',
                'contact' => $item['nomor_telepon'] ?? $item['nip'] ?? '-',
                'capacity' => 10,
                'activeStudents' => 0,
                'user_id' => $item['user_id'],
                'nip' => $item['nip'] ?? '',
                'ruangan_tugas' => $item['nama_unit'] ?? '',
                'nomor_telepon' => $item['nomor_telepon'] ?? '',
                'id_profesi' => $item['id_profesi'] ?? '',
                'id_unit_kerja' => $item['id_unit_kerja'] ?? '',
                'available' => !$hasOverlap,
                'has_overlap' => $hasOverlap,
            ];
        }, $data);

        return $this->response->setJSON($result);
    }

    public function ciApiInsert()
    {
        try {
            $json = $this->request->getJSON();
        } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
            $json = null;
        }
        $input = $json ?: (object) $this->request->getPost();

        if (empty((array) $input)) {
            return $this->response->setJSON(['message' => 'Invalid Data'])->setStatusCode(400);
        }

        $email = trim($input->email ?? '');
        $password = $input->password ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format email tidak valid'])->setStatusCode(400);
        }
        if (strlen($password) < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password minimal 6 karakter'])->setStatusCode(400);
        }

        if (isset($data['nama_lengkap']) && !preg_match("/^[\\p{L}][\\p{L}\\s.,'-]{1,149}$/u", trim((string) $data['nama_lengkap']))) return $this->response->setJSON(['success' => false, 'message' => 'Nama mahasiswa tidak valid.'])->setStatusCode(422);
        if (isset($data['nim']) && !preg_match('/^[A-Za-z0-9.\/-]{3,30}$/', trim((string) $data['nim']))) return $this->response->setJSON(['success' => false, 'message' => 'NIM tidak valid.'])->setStatusCode(422);
        if (isset($data['no_hp']) && $data['no_hp'] !== '' && !preg_match('/^[0-9]{8,20}$/D', trim((string) $data['no_hp']))) return $this->response->setJSON(['success' => false, 'message' => 'Nomor HP hanya boleh berisi angka.'])->setStatusCode(422);
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) return $this->response->setJSON(['success' => false, 'message' => 'Format email tidak valid.'])->setStatusCode(422);
        if (isset($data['semester']) && (!filter_var($data['semester'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 20]]))) return $this->response->setJSON(['success' => false, 'message' => 'Semester harus antara 1 hingga 20.'])->setStatusCode(422);
        if (isset($data['jenis_kelamin']) && !in_array($data['jenis_kelamin'], ['L', 'P'], true)) return $this->response->setJSON(['success' => false, 'message' => 'Jenis kelamin tidak valid.'])->setStatusCode(422);
        if (isset($data['tanggal_lahir'])) {
            $date = \DateTime::createFromFormat('Y-m-d', (string) $data['tanggal_lahir']);
            if (!$date || $date->format('Y-m-d') !== $data['tanggal_lahir'] || $data['tanggal_lahir'] >= date('Y-m-d')) return $this->response->setJSON(['success' => false, 'message' => 'Tanggal lahir tidak valid.'])->setStatusCode(422);
        }
        if (isset($data['status']) && !in_array($data['status'], ['Menunggu', 'Disetujui', 'Aktif', 'Ditolak', 'Lulus'], true)) return $this->response->setJSON(['success' => false, 'message' => 'Status mahasiswa tidak valid.'])->setStatusCode(422);

        $nip = trim((string) ($input->nip ?? ''));
        $nomorTelepon = trim((string) ($input->nomor_telepon ?? $input->contact ?? ''));
        $namaLengkap = trim((string) ($input->nama_lengkap ?? $input->name ?? ''));

        if (!preg_match("/^[\\p{L}][\\p{L}\\s.,'-]{1,149}$/u", $namaLengkap)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nama CI tidak valid.'])->setStatusCode(422);
        }

        if (!preg_match('/^[0-9]{18}$/D', $nip)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'NIP harus tepat 18 digit dan hanya boleh berisi angka.'
            ])->setStatusCode(422);
        }

        if ($nomorTelepon !== '' && !preg_match('/^[0-9]+$/D', $nomorTelepon)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor telepon hanya boleh berisi angka.'
            ])->setStatusCode(422);
        }

        // Check email unique
        $existingUser = $this->usersModel->where('email', $email)->first();
        if ($existingUser) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email sudah digunakan'])->setStatusCode(409);
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $userData = [
                'role_id' => 4,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'is_active' => 1,
            ];

            $userId = $this->usersModel->insert($userData);

            if (!$userId) {
                throw new \RuntimeException('Gagal membuat user');
            }

            $ciData = [
                'user_id' => $userId,
                'nama_lengkap' => $namaLengkap,
                'nip' => $nip,
                'id_profesi' => $input->id_profesi ?? null,
                'id_unit_kerja' => $input->id_unit_kerja ?? null,
                'nomor_telepon' => $nomorTelepon,
                'email' => $email,
            ];

            $ciId = $this->ciModel->insert($ciData);

            if (!$ciId) {
                throw new \RuntimeException('Gagal menyimpan data CI');
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'status' => 201,
                'message' => 'Clinical Instructor berhasil ditambahkan!',
                'id' => $ciId,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function ciApiDetail($id)
    {
        $builder = $this->ciModel->builder();
        $item = $builder
            ->select('ci_pendidikan.*, profesi_Pelatihan.nama_profesi, unit_kerja_Pelatihan.nama_unit, users_pendidikan.email')
            ->join('profesi_Pelatihan', 'profesi_Pelatihan.id_profesi = ci_pendidikan.id_profesi', 'left')
            ->join('unit_kerja_Pelatihan', 'unit_kerja_Pelatihan.id_unit_kerja = ci_pendidikan.id_unit_kerja', 'left')
            ->join('users_pendidikan', 'users_pendidikan.id = ci_pendidikan.user_id', 'left')
            ->where('ci_pendidikan.id', $id)
            ->get()
            ->getRowArray();

        if (!$item) {
            return $this->response->setJSON(['message' => 'Data tidak ditemukan'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'id' => $item['id'],
            'name' => $item['nama_lengkap'],
            'nama_lengkap' => $item['nama_lengkap'],
            'profession' => $item['nama_profesi'] ?? '',
            'nama_profesi' => $item['nama_profesi'] ?? '',
            'nama_unit_kerja' => $item['nama_unit'] ?? '',
            'contact' => $item['nomor_telepon'] ?? '-',
            'capacity' => 10,
            'activeStudents' => 0,
            'user_id' => $item['user_id'],
            'nip' => $item['nip'] ?? '',
            'ruangan_tugas' => $item['nama_unit'] ?? '',
            'nomor_telepon' => $item['nomor_telepon'] ?? '',
            'id_profesi' => $item['id_profesi'] ?? '',
            'id_unit_kerja' => $item['id_unit_kerja'] ?? '',
            'email' => $item['email'] ?? '',
        ]);
    }

    public function ciApiUpdate($id)
    {
        try {
            $json = $this->request->getJSON();
        } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
            $json = null;
        }
        $input = $json ?: (object) $this->request->getPost();

        if (empty((array) $input)) {
            return $this->response->setJSON(['message' => 'Invalid Data'])->setStatusCode(400);
        }

        $existing = $this->ciModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['message' => 'Data tidak ditemukan'])->setStatusCode(404);
        }

        if (property_exists($input, 'nip')) {
            $nip = trim((string) $input->nip);
            if (!preg_match('/^[0-9]{18}$/D', $nip)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'NIP harus tepat 18 digit dan hanya boleh berisi angka.'
                ])->setStatusCode(422);
            }
        }

        if (property_exists($input, 'nama_lengkap') || property_exists($input, 'name')) {
            $namaLengkap = trim((string) ($input->nama_lengkap ?? $input->name));
            if (!preg_match("/^[\\p{L}][\\p{L}\\s.,'-]{1,149}$/u", $namaLengkap)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nama CI tidak valid.'])->setStatusCode(422);
            }
        }
        if (property_exists($input, 'email') && trim((string) $input->email) !== '' && !filter_var($input->email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format email tidak valid.'])->setStatusCode(422);
        }

        $teleponProperty = property_exists($input, 'nomor_telepon')
            ? 'nomor_telepon'
            : (property_exists($input, 'contact') ? 'contact' : null);
        if ($teleponProperty !== null) {
            $nomorTelepon = trim((string) $input->{$teleponProperty});
            if ($nomorTelepon !== '' && !preg_match('/^[0-9]+$/D', $nomorTelepon)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Nomor telepon hanya boleh berisi angka.'
                ])->setStatusCode(422);
            }
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $updateData = [];
            if (isset($input->nama_lengkap))
                $updateData['nama_lengkap'] = $namaLengkap;
            else if (isset($input->name))
                $updateData['nama_lengkap'] = $namaLengkap;
            if (property_exists($input, 'id_profesi'))
                $updateData['id_profesi'] = $input->id_profesi ?: null;
            if (isset($nip))
                $updateData['nip'] = $nip;
            if (property_exists($input, 'id_unit_kerja'))
                $updateData['id_unit_kerja'] = $input->id_unit_kerja ?: null;
            if (isset($nomorTelepon))
                $updateData['nomor_telepon'] = $nomorTelepon;
            if (!empty($input->email))
                $updateData['email'] = $input->email;

            $this->ciModel->update($id, $updateData);

            // Update users_pendidikan if email or password changed
            $userUpdate = [];
            if (!empty($input->email))
                $userUpdate['email'] = $input->email;
            if (!empty($input->password) && strlen($input->password) >= 6) {
                $userUpdate['password'] = password_hash($input->password, PASSWORD_DEFAULT);
            }
            if (!empty($userUpdate) && !empty($existing['user_id'])) {
                $this->usersModel->update($existing['user_id'], $userUpdate);
            }

            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'status' => 200,
                'message' => 'Data CI berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    public function ciApiDelete($id)
    {
        $existing = $this->ciModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['message' => 'Data tidak ditemukan'])->setStatusCode(404);
        }
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->lockStaseAssignments($db);
            $usedCount = $db->table('stase_pendidikan')->where('ci_id', $id)->countAllResults()
                + $db->table('stase_ruangan_ci_pendidikan')->where('ci_id', $id)->countAllResults()
                + $db->table('tugas_pendidikan')->where('ci_id', $id)->countAllResults();
            if ($usedCount > 0) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'CI tidak dapat dihapus karena masih memiliki penugasan atau riwayat tugas akademik.'
                ])->setStatusCode(409);
            }
            $userId = $existing['user_id'] ?? null;
            if ($userId && (
                $db->table('ci_pendidikan')->where('user_id', $userId)->where('id !=', $id)->countAllResults()
                + $db->table('mahasiswa_pendidikan')->where('user_id', $userId)->countAllResults()
                + $db->table('institusi_pendidikan')->where('user_id', $userId)->countAllResults() > 0
            )) {
                $db->transRollback();
                return $this->response->setJSON(['success' => false, 'message' => 'Akun CI masih terhubung dengan profil lain.'])->setStatusCode(409);
            }
            if (!$this->ciModel->delete($id)
                || ($userId && !$this->usersModel->delete($userId)) || !$db->transStatus()) {
                throw new \RuntimeException('Transaksi penghapusan CI gagal.');
            }
            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal menghapus CI: {message}', ['message' => $e->getMessage()]);
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus CI.'])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'status' => 200,
            'message' => 'Data CI berhasil dihapus!',
        ]);
    }

}
