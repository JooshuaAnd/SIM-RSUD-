<?php

namespace App\Controllers\Pendidikan\Ci;

class TaskController extends BaseCiController
{
    public function create()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $json = $this->request->getJSON(true);
        $staseId = $json['stase_id'] ?? null;
        $ruanganId = $json['ruangan_id'] ?? null;
        $namaTugas = trim($json['nama_tugas'] ?? '');
        $deskripsi = trim($json['deskripsi'] ?? '');
        $deadline = $json['deadline'] ?? null;

        if (!$staseId || !$ruanganId || empty($namaTugas) || empty($deadline)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }
        $deadlineDate = \DateTime::createFromFormat('Y-m-d\\TH:i', str_replace(' ', 'T', $deadline));
        if (mb_strlen($namaTugas) > 150 || !$deadlineDate) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nama tugas atau deadline tidak valid']);
        }

        // Verify access
        $this->verifyCiAccess($staseId, $ruanganId);

        try {
            $this->db->table('tugas_pendidikan')->insert([
                'stase_id' => $staseId,
                'ruangan_id' => $ruanganId,
                'ci_id' => $this->ciId,
                'nama_tugas' => $namaTugas,
                'deskripsi' => $deskripsi,
                'deadline' => str_replace('T', ' ', $deadline), // Fix datetime format
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', 'CREATE TASK ERROR: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Tugas berhasil dibuat']);
    }

    public function grade($submissionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $json = $this->request->getJSON(true);
        $nilai = isset($json['nilai']) ? (int)$json['nilai'] : null;
        $catatan = trim($json['catatan_ci'] ?? '');

        if ($nilai === null || $nilai < 0 || $nilai > 100) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nilai harus diisi antara 0 hingga 100']);
        }

        // Verify submission exists
        $submission = $this->db->table('pengumpulan_tugas_pendidikan')->where('id', $submissionId)->get()->getRowArray();
        if (!$submission) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tugas tidak ditemukan']);
        }

        // Update
        $this->db->table('pengumpulan_tugas_pendidikan')->where('id', $submissionId)->update([
            'nilai' => $nilai,
            'catatan_ci' => $catatan,
            'status' => 'Selesai',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Nilai berhasil disimpan']);
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

        $students = $this->db->table('mahasiswa_pendidikan')
            ->select('id, nama_lengkap, nim')
            ->whereIn('id', $mhsIds)
            ->get()->getResultArray();

        // Get all tasks for this room
        $tasks = $this->db->table('tugas_pendidikan')
            ->where('stase_id', $staseId)
            ->where('ruangan_id', $ruanganId)
            ->where('ci_id', $this->ciId)
            ->get()->getResultArray();

        $filename = "Rekap_Tugas_Nilai_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM

        // Header row
        $headers = ['NIM', 'Nama Mahasiswa'];
        foreach ($tasks as $t) {
            $headers[] = $t['nama_tugas'] . ' (Nilai)';
        }
        fputcsv($output, $headers);

        foreach ($students as $mhs) {
            $row = [$mhs['nim'], $mhs['nama_lengkap']];
            foreach ($tasks as $t) {
                // Find submission
                $sub = $this->db->table('pengumpulan_tugas_pendidikan')
                    ->where('tugas_id', $t['id'])
                    ->where('mahasiswa_id', $mhs['id'])
                    ->get()->getRowArray();
                
                $row[] = $sub && $sub['nilai'] !== null ? $sub['nilai'] : 'Belum dinilai';
            }
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
