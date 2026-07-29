<?php

namespace App\Controllers\Pelatihan\Peserta;

use App\Controllers\BaseController;
use App\Models\Pelatihan\SertifikatPelatihanModel;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\MasterKategoriSkpPelatihanModel;

class Certificate extends BaseController
{
    protected $certModel;
    protected $userModel;

    public function __construct()
    {
        $this->certModel = new SertifikatPelatihanModel();
        $this->userModel = new UserPelatihanModel();
    }

    public function index()
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);
        
        // Recalculate just in case
        $this->userModel->recalculateJpl($userId);
        
        $myCerts = $this->certModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Calculate Target
        $targetJpl = $user['target_jpl'] ?? 20;
        if (empty($targetJpl) && $user['id_profesi']) {
            $db = \Config\Database::connect();
            $prof = $db->table('profesi_pelatihan')->where('id_profesi', $user['id_profesi'])->get()->getRowArray();
            $targetJpl = $prof['target_jpl'] ?? 20;
        }

        $data = [
            'title' => 'Sertifikat Saya',
            'sertifikat' => $myCerts,
            'user' => $user,
            'target_jpl' => $targetJpl,
            'capaian_jpl' => $user['capaian_jpl'] ?? 0
<?php

namespace App\Controllers\Pelatihan\Peserta;

use App\Controllers\BaseController;
use App\Models\Pelatihan\SertifikatPelatihanModel;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\MasterKategoriSkpPelatihanModel;

class Certificate extends BaseController
{
    protected $certModel;
    protected $userModel;

    public function __construct()
    {
        $this->certModel = new SertifikatPelatihanModel();
        $this->userModel = new UserPelatihanModel();
    }

    public function index()
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/login');
        }

        $user = $this->userModel->find($userId);
        
        // Recalculate just in case
        $this->userModel->recalculateJpl($userId);
        
        $myCerts = $this->certModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Calculate Target
        $targetJpl = $user['target_jpl'] ?? 20;
        if (empty($targetJpl) && $user['id_profesi']) {
            $db = \Config\Database::connect();
            $prof = $db->table('profesi_pelatihan')->where('id_profesi', $user['id_profesi'])->get()->getRowArray();
            $targetJpl = $prof['target_jpl'] ?? 20;
        }

        $data = [
            'title' => 'Sertifikat Saya',
            'sertifikat' => $myCerts,
            'user' => $user,
            'target_jpl' => $targetJpl,
            'capaian_jpl' => $user['capaian_jpl'] ?? 0
        ];
        return view('pelatihan/peserta/sertifikat/index', $data);
    }

    public function upload()
    {
            $updateData['file_path'] = 'uploads/pelatihan/sertifikat/' . $newName;
        }

        $fileSt = $this->request->getFile('dokumen_st');
        if ($fileSt && $fileSt->isValid() && !$fileSt->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/surat_tugas')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/surat_tugas', 0777, true);
            }
            $newNameSt = "SuratTugas_{$judulSafe}_{$namaUser}_" . date('Ymd_His') . "." . $fileSt->getExtension();
            $fileSt->move(ROOTPATH . 'public/uploads/pelatihan/surat_tugas', $newNameSt);
            $updateData['surat_tugas_path'] = 'uploads/pelatihan/surat_tugas/' . $newNameSt;
        }

        $this->certModel->update($id, $updateData);

        return redirect()->to(site_url('pelatihan/peserta/sertifikat'))->with('success', 'Sertifikat kegiatan berhasil diperbarui.');
    }

    public function download($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        // Verify the certificate belongs to the user and is published by RSUD
        $cert = $this->certModel->where('id', $id)->where('user_id', $userId)->first();
        if (!$cert || $cert['jenis_dokumen'] !== 'rsud') {
            return redirect()->to('pelatihan/peserta/sertifikat')->with('error', 'Sertifikat tidak valid atau bukan terbitan sistem.');
        }

        $db = \Config\Database::connect();
        $pelatihanId = $cert['pelatihan_id'];

        // Get template
        $template = $db->table('sertif_terbit_pelatihan')
            ->select('sertif_terbit_pelatihan.*, p1.nama_pejabat as nama_1, p1.jabatan as jab_1, p1.an_pejabat as an_1, p1.nip_pejabat as nip_1, p1.ttd_image as ttd_1, p2.nama_pejabat as nama_2, p2.jabatan as jab_2, p2.an_pejabat as an_2, p2.nip_pejabat as nip_2, p2.ttd_image as ttd_2')
            ->join('pejabat_ttd_pelatihan p1', 'p1.id = sertif_terbit_pelatihan.pejabat_id_1', 'left')
            ->join('pejabat_ttd_pelatihan p2', 'p2.id = sertif_terbit_pelatihan.pejabat_id_2', 'left')
            ->where('sertif_terbit_pelatihan.pelatihan_id', $pelatihanId)
            ->orderBy("FIELD(sertif_terbit_pelatihan.status, 'diterbitkan', 'draft')", 'ASC', false)
            ->orderBy('sertif_terbit_pelatihan.id', 'DESC')
            ->get()->getRowArray();

        if (!$template) {
            return redirect()->to('pelatihan/peserta/sertifikat')->with('error', 'Template sertifikat tidak ditemukan.');
        }

        $pelatihan = $db->table('master_pelatihan')->where('id', $pelatihanId)->get()->getRowArray();
        $user = $this->userModel->find($userId);

        $data = [
            'title' => 'Pratinjau Sertifikat',
            'pelatihan' => $pelatihan,
            'users' => [$user],
            'template' => $template,
            'no_sertifikat' => $cert['no_sertifikat'] ?? ($template['no_sertifikat'] ?? 'KT.03.02/F/0001/SER/' . date('Y'))
        ];
        
        return view('pelatihan/admin/sertifikat/template/preview', $data);
    }
}
