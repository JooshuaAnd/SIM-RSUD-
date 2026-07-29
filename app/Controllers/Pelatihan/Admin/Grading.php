<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class Grading extends BaseController
{
    public function index()
    {
        return redirect()->to('/pelatihan/admin/monitoring_peserta');
    }

    public function detail($id)
    {
        $masterModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $p = $masterModel->find($id);
        
        if (empty($p)) return redirect()->to('/pelatihan/admin/monitoring_peserta');

        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $pesertaList = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.nik as nip')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->where('peserta_pelatihan.pelatihan_id', $id)
            ->findAll();

        $peserta = [];
        $db = \Config\Database::connect();
        foreach ($pesertaList as $pl) {
            $ujianPre = $db->table('peserta_ujian_pelatihan')
                ->where('peserta_pelat_id', $pl['id'])
                ->where('tipe_ujian', 'pre_test')
                ->orderBy('created_at', 'DESC')
                ->get()->getRowArray();
                
            $ujianPost = $db->table('peserta_ujian_pelatihan')
                ->where('peserta_pelat_id', $pl['id'])
                ->where('tipe_ujian', 'post_test')
                ->orderBy('created_at', 'DESC')
                ->get()->getRowArray();

            $nilaiPre = $ujianPre ? $ujianPre['score'] : '-';
            $nilaiPost = $ujianPost ? $ujianPost['score'] : 0;
            
            if ($nilaiPost == 0 && !$ujianPost) {
                $nilaiPost = '-';
            }
            
            // Ambil KKM
            $ujianMaster = $db->table('ujian_pelatihan')->where('pelatihan_id', $id)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
            $kkm = $ujianMaster ? ($ujianMaster['kkm'] ?? 70) : 70;
            
            if ($ujianPost) {
                $status = ($ujianPost['score'] >= $kkm) ? 'Lulus' : 'Tidak Lulus';
            } else {
                $status = $pl['status_peserta'];
                if ($status !== 'Lulus' && $status !== 'Tidak Lulus') {
                    $status = 'Belum Lulus';
                }
            }
            
            if (strtoupper($status) == 'LULUS') {
                $status = 'LULUS';
            } elseif (strtoupper($status) == 'TIDAK LULUS') {
                $status = 'TIDAK LULUS';
            }

            $peserta[] = [
                'id' => $pl['id'],
                'peserta_pelat_id' => $pl['id'],
                'nama' => $pl['nama'],
                'nip' => $pl['nip'],
                'nilai_pre' => $nilaiPre,
                'nilai_post' => $nilaiPost,
                'status' => $status
            ];
        }

        $data = [
            'title' => 'Detail Nilai: ' . $p['nama'],
            'p' => $p,
            'peserta' => $peserta
        ];
        return view('Pelatihan/admin/grading/detail', $data);
    }

    public function log_jawaban($peserta_pelat_id)
    {
        $db = \Config\Database::connect();
        $ujian = $db->table('peserta_ujian_pelatihan')
            ->where('peserta_pelat_id', $peserta_pelat_id)
            ->orderBy('created_at', 'ASC')
            ->get()->getResultArray();
            
        $data = [];
        foreach ($ujian as $u) {
            $jawaban = $db->table('peserta_jawaban_ujian_pelatihan')
                ->select('peserta_jawaban_ujian_pelatihan.*, ujian_soal_pelatihan.pertanyaan, ujian_soal_pelatihan.jawaban_benar, ujian_soal_pelatihan.materi_id, materi_pelatihan.judul as materi_judul')
                ->join('ujian_soal_pelatihan', 'ujian_soal_pelatihan.id = peserta_jawaban_ujian_pelatihan.soal_id')
                ->join('materi_pelatihan', 'materi_pelatihan.id = ujian_soal_pelatihan.materi_id', 'left')
                ->where('peserta_ujian_id', $u['id'])
                ->get()->getResultArray();
            $data[$u['tipe_ujian']] = [
                'score' => $u['score'],
                'jawaban' => $jawaban
            ];
        }
        return $this->response->setJSON($data);
    }
}
