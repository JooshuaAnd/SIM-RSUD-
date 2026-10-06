<?php

namespace App\Controllers\Pendidikan\Ci;

class LogbookController extends BaseCiController
{
    public function validateLogbook($logbookId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $json = $this->request->getJSON(true);
        $status = $json['status_validasi'] ?? 'Disetujui';
        $catatan = trim($json['catatan_ci'] ?? '');

        if (!in_array($status, ['Disetujui', 'Revisi', 'Ditolak'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status validasi tidak valid']);
        }

        // Verify logbook exists
        $logbook = $this->db->table('logbook_pendidikan')->where('id', $logbookId)->get()->getRowArray();
        if (!$logbook) {
            return $this->response->setJSON(['success' => false, 'message' => 'Logbook tidak ditemukan']);
        }

        $this->db->table('logbook_pendidikan')->where('id', $logbookId)->update([
            'status_validasi' => $status,
            'catatan_ci' => $catatan,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Logbook berhasil divalidasi']);
    }

    public function download($staseId, $ruanganId)
    {
        $this->verifyCiAccess($staseId, $ruanganId);

        // Fetch students in this room
        $mapping = $this->db->table('stase_ruangan_ci_pendidikan')
            ->where('stase_id', $staseId)
            ->where('ruangan_id', $ruanganId)
            ->where('ci_id', $this->ciId)
            ->get()->getRowArray();

        $mhsIds = json_decode((string)($mapping['mahasiswa_ids'] ?? ''), true) ?: [];
        if (empty($mhsIds)) {
            return "Tidak ada mahasiswa di ruangan ini";
        }

        // Get logbooks linked via penempatan_peserta_pendidikan
        $penempatans = $this->db->table('penempatan_peserta_pendidikan')
            ->whereIn('mahasiswa_id', $mhsIds)
            ->where('stase_id', $staseId)
            ->get()->getResultArray();

        $penempatanIds = array_column($penempatans, 'id');
        $mhsMap = [];
        foreach ($penempatans as $p) {
            $mhsMap[$p['id']] = $p['mahasiswa_id'];
        }

        $students = $this->db->table('mahasiswa_pendidikan')
            ->select('id, nama_lengkap, nim')
            ->whereIn('id', $mhsIds)
            ->get()->getResultArray();

        $studentNames = [];
        foreach ($students as $s) {
            $studentNames[$s['id']] = $s;
        }

        $logbooks = [];
        if (!empty($penempatanIds)) {
            $logbooks = $this->db->table('logbook_pendidikan')
                ->whereIn('penempatan_id', $penempatanIds)
                ->orderBy('tanggal_kegiatan', 'ASC')
                ->get()->getResultArray();
        }

        $filename = "Rekap_Logbook_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM

        fputcsv($output, ['Nama Mahasiswa', 'NIM', 'Tanggal Kegiatan', 'Judul', 'Deskripsi', 'Status Validasi', 'Catatan CI']);

        foreach ($logbooks as $l) {
            $mhsId = $mhsMap[$l['penempatan_id']] ?? null;
            $s = $studentNames[$mhsId] ?? null;

            fputcsv($output, [
                $s ? $s['nama_lengkap'] : '-',
                $s ? $s['nim'] : '-',
                $l['tanggal_kegiatan'],
                $l['judul_kegiatan'],
                $l['deskripsi_kegiatan'],
                $l['status_validasi'],
                $l['catatan_ci']
            ]);
        }

        fclose($output);
        exit;
    }
}
