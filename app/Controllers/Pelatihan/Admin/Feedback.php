<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;
use App\Libraries\Pelatihan\FeedbackSessionSummary;
use CodeIgniter\Database\BaseConnection;

class Feedback extends BaseController
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
        $pesertaList = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->where('peserta_pelatihan.pelatihan_id', $id)
            ->findAll();

        $feedbacks = [];
        $totalRating = 0;
        $count = 0;
        
        $db = \Config\Database::connect();
        foreach ($pesertaList as $pl) {
            $saran = $db->table('peserta_kuesioner_saran_pelatihan')
                        ->where('peserta_pelat_id', $pl['id'])
                        ->get()->getRowArray();
                        
            if ($saran) {
                $rating = $saran['rating_umum'];
                $komentar = $saran['saran_masukan'];
                
                // Fetch post-test score
                $postTest = $db->table('peserta_ujian_pelatihan')
                               ->select('score as nilai_score')
                               ->where('peserta_pelat_id', $pl['id'])
                               ->where('tipe_ujian', 'post_test')
                               ->orderBy('created_at', 'DESC')
                               ->get()->getRowArray();
                $skorPostTest = $postTest ? $postTest['nilai_score'] : null;

                // Fetch detailed answers
                $jawaban = $db->table('peserta_kuesioner_rating_pelatihan')
                              ->select('kuesioner_master_pelatihan.pertanyaan, kategori_evaluasi_pelatihan.nama_kategori as kategori, peserta_kuesioner_rating_pelatihan.nilai_rating, sesi_interaktif_pelatihan.nama_sesi')
                              ->join('kuesioner_master_pelatihan', 'kuesioner_master_pelatihan.id = peserta_kuesioner_rating_pelatihan.kuesioner_id')
                              ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
                              ->join('sesi_interaktif_pelatihan', 'sesi_interaktif_pelatihan.id = peserta_kuesioner_rating_pelatihan.sesi_id', 'left')
                              ->where('peserta_kuesioner_rating_pelatihan.peserta_pelat_id', $pl['id'])
                              ->get()->getResultArray();

                // Group jawaban by Sesi, then Category
                $jawabanDetail = [];
                foreach ($jawaban as $j) {
                    $sesi = !empty($j['nama_sesi']) ? $j['nama_sesi'] : 'Keseluruhan';
                    $kat = $j['kategori'];
                    if (!isset($jawabanDetail[$sesi])) $jawabanDetail[$sesi] = [];
                    if (!isset($jawabanDetail[$sesi][$kat])) $jawabanDetail[$sesi][$kat] = [];
                    $jawabanDetail[$sesi][$kat][] = $j;
                }

                $feedbacks[] = [
                    'nama' => $pl['nama'],
                    'rating' => $rating,
                    'komentar' => $komentar,
                    'skor_post_test' => $skorPostTest,
                    'jawaban_detail' => $jawabanDetail
                ];
                $totalRating += $rating;
                $count++;
            }
        }

        if ($count == 0) {
            $avg = 0;
        } else {
            $avg = round($totalRating / $count, 1);
        }

        $data = [
            'title' => 'Detail Feedback: ' . $p['nama'],
            'p' => $p,
            'avg' => $avg,
            'feedbacks' => $feedbacks,
        ];

        $sesiStats = (new FeedbackSessionSummary())->getStats($db, (int) $id);

        $data = array_merge($data, $this->getAverageRatingStats($db, (int) $id));
        $data['sesiStats']         = $sesiStats;

        return view('Pelatihan/admin/feedback/detail', $data);
    }

    private function getAverageRatingStats(BaseConnection $db, int $pelatihanId): array
    {
        $materiRatings = $db->table('materi_pelatihan m')
            ->select('m.id, m.judul, q.id as kuesioner_id, q.pertanyaan, SUM(r.nilai_rating) as rating_total, COUNT(r.id) as total_votes')
            ->join('peserta_kuesioner_rating_pelatihan r', 'r.materi_id = m.id')
            ->join('kuesioner_master_pelatihan q', 'q.id = r.kuesioner_id')
            ->where('m.pelatihan_id', $pelatihanId)
            ->where('q.pelatihan_id', $pelatihanId)
            ->groupBy('m.id, m.judul, m.urutan, q.id, q.pertanyaan')
            ->orderBy('m.urutan', 'ASC')
            ->orderBy('m.id', 'ASC')
            ->orderBy('q.id', 'ASC')
            ->get()->getResultArray();

        // Group all session assignments under the same master narasumber.
        $narasumberRatings = $db->table('narasumber_pelatihan np')
            ->select('np.pejabat_ttd_id as id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang, q.id as kuesioner_id, q.pertanyaan, SUM(r.nilai_rating) as rating_total, COUNT(r.id) as total_votes')
            ->join('pejabat_ttd_pelatihan pt', 'pt.id = np.pejabat_ttd_id')
            ->join('peserta_kuesioner_rating_pelatihan r', 'r.narasumber_id = np.id')
            ->join('kuesioner_master_pelatihan q', 'q.id = r.kuesioner_id')
            ->where('np.pelatihan_id', $pelatihanId)
            ->where('q.pelatihan_id', $pelatihanId)
            ->groupBy('np.pejabat_ttd_id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang, q.id, q.pertanyaan')
            ->orderBy('pt.nama_pejabat', 'ASC')
            ->orderBy('np.pejabat_ttd_id', 'ASC')
            ->orderBy('q.id', 'ASC')
            ->get()->getResultArray();

        foreach ($narasumberRatings as &$rating) {
            $rating['nama'] = trim(($rating['gelar_depan'] ?? '') . ' ' . $rating['nama_pejabat'])
                . (!empty($rating['gelar_belakang']) ? ', ' . $rating['gelar_belakang'] : '');
        }
        unset($rating);

        // Group all session assignments under the same master penyelenggara.
        $penyelenggaraRatings = $db->table('penyelenggara_pelatihan pp')
            ->select('pp.penyelenggara_id as id, mp.nama, q.id as kuesioner_id, q.pertanyaan, SUM(r.nilai_rating) as rating_total, COUNT(r.id) as total_votes')
            ->join('master_penyelenggara mp', 'mp.id = pp.penyelenggara_id')
            ->join('peserta_kuesioner_rating_pelatihan r', 'r.penyelenggara_id = pp.id')
            ->join('kuesioner_master_pelatihan q', 'q.id = r.kuesioner_id')
            ->where('pp.pelatihan_id', $pelatihanId)
            ->where('q.pelatihan_id', $pelatihanId)
            ->groupBy('pp.penyelenggara_id, mp.nama, q.id, q.pertanyaan')
            ->orderBy('mp.nama', 'ASC')
            ->orderBy('pp.penyelenggara_id', 'ASC')
            ->orderBy('q.id', 'ASC')
            ->get()->getResultArray();

        return [
            'materiStats' => $this->buildEntityRatingStats($materiRatings, 'judul'),
            'narasumberStats' => $this->buildEntityRatingStats($narasumberRatings, 'nama'),
            'penyelenggaraStats' => $this->buildEntityRatingStats($penyelenggaraRatings, 'nama'),
        ];
    }

    private function buildEntityRatingStats(array $ratings, string $labelKey): array
    {
        $stats = [];
        foreach ($ratings as $rating) {
            $entityId = (int) $rating['id'];
            $totalVotes = (int) $rating['total_votes'];
            if ($totalVotes === 0) {
                continue;
            }

            if (!isset($stats[$entityId])) {
                $stats[$entityId] = [
                    'id' => $entityId,
                    $labelKey => $rating[$labelKey],
                    'pertanyaan' => [],
                    'rating_total' => 0,
                    'total_votes' => 0,
                ];
            }

            $ratingTotal = (float) $rating['rating_total'];
            $stats[$entityId]['pertanyaan'][] = [
                'pertanyaan' => $rating['pertanyaan'],
                'avg_rating' => round($ratingTotal / $totalVotes, 1),
                'total_votes' => $totalVotes,
            ];
            $stats[$entityId]['rating_total'] += $ratingTotal;
            $stats[$entityId]['total_votes'] += $totalVotes;
        }

        foreach ($stats as &$stat) {
            $stat['avg_overall'] = round($stat['rating_total'] / $stat['total_votes'], 1);
            unset($stat['rating_total']);
        }
        unset($stat);

        return array_values($stats);
    }
}
