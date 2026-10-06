<?php

namespace App\Controllers\Pendidikan\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\MahasiswaPendidikanModel;
use Dompdf\Dompdf;

class NilaiController extends BaseController
{
    public function download()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Silakan login terlebih dahulu.']);
        }

        // Identity comes exclusively from the logged-in student, not request parameters.
        $mahasiswa = model(MahasiswaPendidikanModel::class)
            ->select('mahasiswa_pendidikan.*, institusi_pendidikan.nama_institusi')
            ->join('institusi_pendidikan', 'institusi_pendidikan.id = mahasiswa_pendidikan.institusi_id', 'left')
            ->where('mahasiswa_pendidikan.user_id', $userId)
            ->first();
        if (!$mahasiswa) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data mahasiswa tidak ditemukan.']);
        }
        if (($mahasiswa['payment_status'] ?? '') !== 'Lunas') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Akses rekap nilai terkunci. Harap selesaikan administrasi pembayaran stase.']);
        }

        $html = view('Pendidikan/mahasiswa/rekap_nilai_pdf', [
            'mahasiswa' => $mahasiswa,
            'staseList' => $this->getNilaiStase((int) $mahasiswa['id']),
        ]);
        $pdf = new Dompdf();
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $canvas = $pdf->getCanvas();
        $font = $pdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $canvas->page_text(435, 809, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', $font, 8, [0.4, 0.4, 0.4]);

        $nim = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) ($mahasiswa['nim'] ?? $mahasiswa['id']));
        return $this->response->download('Rekap_Nilai_' . $nim . '.pdf', $pdf->output(), true);
    }

    protected function getNilaiStase(int $mahasiswaId): array
    {
        $db = \Config\Database::connect();
        $staseList = $db->table('stase_ruangan_ci_pendidikan')
            ->select('stase_ruangan_ci_pendidikan.stase_id, stase_ruangan_ci_pendidikan.ruangan_id, stase_pendidikan.nama_stase, ci_pendidikan.nama_lengkap as ci_name, unit_kerja_pelatihan.nama_unit as nama_ruangan')
            ->join('stase_pendidikan', 'stase_pendidikan.id = stase_ruangan_ci_pendidikan.stase_id')
            ->join('ci_pendidikan', 'ci_pendidikan.id = stase_ruangan_ci_pendidikan.ci_id', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = stase_ruangan_ci_pendidikan.ruangan_id', 'left')
            ->where("JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '\"" . $mahasiswaId . "\"') OR JSON_CONTAINS(stase_ruangan_ci_pendidikan.mahasiswa_ids, '" . $mahasiswaId . "')")
            ->orderBy('stase_pendidikan.tanggal_mulai', 'ASC')
            ->orderBy('stase_ruangan_ci_pendidikan.id', 'ASC')
            ->get()->getResultArray();

        foreach ($staseList as &$stase) {
            $stase['tasks'] = $db->table('tugas_pendidikan')
                ->select('tugas_pendidikan.nama_tugas, pengumpulan_tugas_pendidikan.nilai, pengumpulan_tugas_pendidikan.status')
                ->join('pengumpulan_tugas_pendidikan', 'pengumpulan_tugas_pendidikan.tugas_id = tugas_pendidikan.id AND pengumpulan_tugas_pendidikan.mahasiswa_id = ' . $mahasiswaId, 'left')
                ->where('tugas_pendidikan.stase_id', $stase['stase_id'])
                ->where('tugas_pendidikan.ruangan_id', $stase['ruangan_id'])
                ->orderBy('tugas_pendidikan.id', 'ASC')
                ->get()->getResultArray();
        }
        unset($stase);

        return $staseList;
    }
}
