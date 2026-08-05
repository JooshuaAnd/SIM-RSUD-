<?php

namespace App\Controllers\Pelatihan\AdminPengabdian;

use App\Controllers\BaseController;

class Certificate extends BaseController
{
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
        $sertifikatModel = new \App\Models\Pelatihan\SertifikatPelatihanModel();
        
        $list = $sertifikatModel->groupStart()
                                    ->where('ranah', 'Pengabdian')
                                    ->orWhere('jenis_dokumen', 'Pengabdian')
                                ->groupEnd()
                                ->orderBy("CASE WHEN verifikasi = 'pending' THEN 0 ELSE 1 END", 'ASC', false)
                                ->orderBy('created_at', 'DESC')
                                ->paginate(10, 'sertifikat');
        $pendingCount = $sertifikatModel->groupStart()
                ->where('ranah', 'Pengabdian')
                ->orWhere('jenis_dokumen', 'Pengabdian')
            ->groupEnd()
            ->where('verifikasi', 'pending')
            ->countAllResults();

        $data = [
            'title' => 'Kelola Sertifikat Pengabdian',
            'sertifikat' => $list,
            'pager' => $sertifikatModel->pager,
            'pending_pengabdian' => $pendingCount
        ];
        return view('Pelatihan/admin_pengabdian/sertifikat/index', $data);
    }

    public function updateskp(string $id)
    {
        $skp = $this->request->getPost('skp');
        $sertifikatModel = new \App\Models\Pelatihan\SertifikatPelatihanModel();
        
        $sertifikat = $sertifikatModel->find($id);
        if ($sertifikat) {
            $sertifikatModel->update($id, [
                'skp' => $skp
            ]);
        }

        return redirect()->to('/pelatihan/admin_pengabdian/sertifikat')->with('success', 'Nilai JPL berhasil disimpan.');
    }

    public function approve(string $id)
    {
        $sertifikatModel = new \App\Models\Pelatihan\SertifikatPelatihanModel();
        
        $sertifikat = $sertifikatModel->find($id);
        if ($sertifikat) {
            $sertifikatModel->update($id, [
                'verifikasi' => 'approved',
                'tgl_verifikasi' => date('Y-m-d H:i:s')
            ]);
            
            $userModel = new \App\Models\Pelatihan\UserPelatihanModel();
            $userModel->recalculateJpl($sertifikat['user_id']);
            $this->createNotification(
                $sertifikat['user_id'], 
                'Pengabdian Disetujui', 
                'Kegiatan pengabdian Anda dengan judul "' . $sertifikat['judul'] . '" telah disetujui oleh admin.', 
                'success'
            );
        }

        return redirect()->to('/pelatihan/admin_pengabdian/sertifikat')->with('success', 'Kegiatan Pengabdian Masyarakat telah disetujui.');
    }

    public function reject(string $id)
    {
        $alasan = $this->request->getPost('alasan_penolakan');
        $sertifikatModel = new \App\Models\Pelatihan\SertifikatPelatihanModel();
        
        $sertifikat = $sertifikatModel->find($id);
        if ($sertifikat) {
            $sertifikatModel->update($id, [
                'verifikasi' => 'rejected',
                'alasan_penolakan' => $alasan,
                'tgl_verifikasi' => date('Y-m-d H:i:s')
            ]);
            $this->createNotification(
                $sertifikat['user_id'],
                'Pengabdian Ditolak',
                'Kegiatan pengabdian Anda dengan judul "' . $sertifikat['judul'] . '" telah ditolak. Alasan: ' . ($alasan ?: 'Tidak ada alasan yang diberikan.'),
                'danger'
            );
        }

        return redirect()->to('/pelatihan/admin_pengabdian/sertifikat')->with('success', 'Kegiatan Pengabdian Masyarakat telah ditolak.');
    }
}
