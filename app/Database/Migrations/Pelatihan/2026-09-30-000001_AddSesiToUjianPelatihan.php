<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class AddSesiToUjianPelatihan extends Migration
{
    public function up()
    {
        $db = $this->db;

        $examSessionColumn = $db->query("SHOW COLUMNS FROM ujian_pelatihan LIKE 'sesi_id'")->getResultArray();
        if (empty($examSessionColumn)) {
            $db->query('ALTER TABLE ujian_pelatihan ADD COLUMN sesi_id INT UNSIGNED NULL AFTER pelatihan_id');
            $db->query('ALTER TABLE ujian_pelatihan ADD KEY idx_ujian_pelatihan_sesi_id (sesi_id)');
            $db->query('ALTER TABLE ujian_pelatihan ADD CONSTRAINT fk_ujian_pelatihan_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_interaktif_pelatihan(id) ON DELETE CASCADE');
        }

        // Each attempt must point to its exact exam.  The existing tipe_ujian
        // column cannot distinguish tests of the same type in different sessions.
        $attemptExamColumn = $db->query("SHOW COLUMNS FROM peserta_ujian_pelatihan LIKE 'ujian_id'")->getResultArray();
        if (empty($attemptExamColumn)) {
            $db->query('ALTER TABLE peserta_ujian_pelatihan ADD COLUMN ujian_id INT UNSIGNED NULL AFTER peserta_pelat_id');
            $db->query('ALTER TABLE peserta_ujian_pelatihan ADD KEY idx_peserta_ujian_ujian_id (ujian_id)');
            $db->query('ALTER TABLE peserta_ujian_pelatihan ADD CONSTRAINT fk_peserta_ujian_pelatihan_ujian FOREIGN KEY (ujian_id) REFERENCES ujian_pelatihan(id) ON DELETE SET NULL');
        }
    }

    public function down()
    {
        $db = $this->db;

        $attemptExamColumn = $db->query("SHOW COLUMNS FROM peserta_ujian_pelatihan LIKE 'ujian_id'")->getResultArray();
        if (!empty($attemptExamColumn)) {
            $db->query('ALTER TABLE peserta_ujian_pelatihan DROP FOREIGN KEY fk_peserta_ujian_pelatihan_ujian');
            $db->query('ALTER TABLE peserta_ujian_pelatihan DROP KEY idx_peserta_ujian_ujian_id');
            $db->query('ALTER TABLE peserta_ujian_pelatihan DROP COLUMN ujian_id');
        }

        $examSessionColumn = $db->query("SHOW COLUMNS FROM ujian_pelatihan LIKE 'sesi_id'")->getResultArray();
        if (!empty($examSessionColumn)) {
            $db->query('ALTER TABLE ujian_pelatihan DROP FOREIGN KEY fk_ujian_pelatihan_sesi');
            $db->query('ALTER TABLE ujian_pelatihan DROP KEY idx_ujian_pelatihan_sesi_id');
            $db->query('ALTER TABLE ujian_pelatihan DROP COLUMN sesi_id');
        }
    }
}
