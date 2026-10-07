<?php

namespace App\Libraries\Pelatihan;

use CodeIgniter\Database\BaseConnection;

class FeedbackSessionSummary
{
    public function getStats(BaseConnection $db, int $pelatihanId): array
    {
        $kategoriSesiLabels = [
            'fasilitator' => 'Fasilitator',
            'materi' => 'Materi',
            'modul' => 'Modul',
            'narasumber' => 'Narasumber',
            'penyelenggara' => 'Penyelenggara',
        ];
        $sesiStats = [];
        $sesiList = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        foreach ($sesiList as $sesi) {
            $ratingsForSesi = $db->table('peserta_kuesioner_rating_pelatihan r')
                ->select('r.kuesioner_id, q.pertanyaan, kategori.nama_kategori as kategori, AVG(r.nilai_rating) as avg_rating, COUNT(r.id) as total_votes')
                ->join('kuesioner_master_pelatihan q', 'q.id = r.kuesioner_id')
                ->join('kategori_evaluasi_pelatihan kategori', 'kategori.id = q.kategori_id', 'left')
                ->where('r.sesi_id', $sesi['id'])
                ->where('q.pelatihan_id', $pelatihanId)
                ->groupBy('r.kuesioner_id, q.pertanyaan, kategori.nama_kategori')
                ->orderBy('q.id', 'ASC')
                ->get()->getResultArray();

            $kategoriStats = [];
            foreach ($kategoriSesiLabels as $label) {
                $kategoriStats[$label] = [
                    'pertanyaan' => [],
                    'avg_overall' => null,
                    'total_votes' => 0,
                    'weighted_total' => 0,
                ];
            }

            $sessionWeightedTotal = 0;
            $sessionTotalVotes = 0;
            foreach ($ratingsForSesi as $rating) {
                $kategoriKey = strtolower(trim($rating['kategori'] ?? ''));
                if (!isset($kategoriSesiLabels[$kategoriKey])) {
                    continue;
                }

                $kategoriLabel = $kategoriSesiLabels[$kategoriKey];
                $avgRating = (float) $rating['avg_rating'];
                $totalVotes = (int) $rating['total_votes'];
                $kategoriStats[$kategoriLabel]['pertanyaan'][] = [
                    'pertanyaan' => $rating['pertanyaan'],
                    'avg_rating' => round($avgRating, 1),
                    'total_votes' => $totalVotes,
                ];
                $kategoriStats[$kategoriLabel]['total_votes'] += $totalVotes;
                $kategoriStats[$kategoriLabel]['weighted_total'] += $avgRating * $totalVotes;
                $sessionWeightedTotal += $avgRating * $totalVotes;
                $sessionTotalVotes += $totalVotes;
            }

            foreach ($kategoriStats as &$kategoriStat) {
                if ($kategoriStat['total_votes'] > 0) {
                    $kategoriStat['avg_overall'] = round($kategoriStat['weighted_total'] / $kategoriStat['total_votes'], 1);
                }
                unset($kategoriStat['weighted_total']);
            }
            unset($kategoriStat);

            $sesiStats[] = [
                'id' => $sesi['id'],
                'nama' => $sesi['nama_sesi'],
                'kategori' => $kategoriStats,
                'avg_overall' => $sessionTotalVotes > 0 ? round($sessionWeightedTotal / $sessionTotalVotes, 1) : null,
                'total_votes' => $sessionTotalVotes,
            ];
        }

        return $sesiStats;
    }
}
