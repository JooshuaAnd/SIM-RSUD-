<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class RecreateNarasumberPelatihanAsPivot extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $cols = $db->query("SHOW COLUMNS FROM narasumber_pelatihan")->getResultArray();
        $colNames = array_column($cols, 'Field');
        $hasOldCols = in_array('nama_narasumber', $colNames);

        if (!$hasOldCols) {
            if (!in_array('pejabat_ttd_id', $colNames)) {
                $db->query("ALTER TABLE narasumber_pelatihan ADD COLUMN pejabat_ttd_id INT UNSIGNED AFTER id");
            }
            if (in_array('id_pelatihan', $colNames) && !in_array('pelatihan_id', $colNames)) {
                $db->query("ALTER TABLE narasumber_pelatihan CHANGE COLUMN id_pelatihan pelatihan_id INT UNSIGNED");
            }
            return;
        }

        // Step 1: Read data BEFORE dropping columns
        $rows = $db->query("SELECT np.id, np.nama_narasumber FROM narasumber_pelatihan np")->getResultArray();

        // Step 2: Create pejabat_ttd records from unique narasumber names
        foreach ($rows as $row) {
            $name = $row['nama_narasumber'];
            if (empty($name)) continue;
            $check = $db->query("SELECT id FROM pejabat_ttd_pelatihan WHERE nama_pejabat = ? AND status = 'Narasumber'", [$name])->getRowArray();
            if (!$check) {
                $db->table('pejabat_ttd_pelatihan')->insert([
                    'status' => 'Narasumber',
                    'nama_pejabat' => $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Step 3: Drop existing FK constraints
        $fks = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'narasumber_pelatihan' AND REFERENCED_TABLE_NAME IS NOT NULL")->getResultArray();
        foreach ($fks as $fk) {
            $db->query("ALTER TABLE narasumber_pelatihan DROP FOREIGN KEY `{$fk['CONSTRAINT_NAME']}`");
        }

        // Step 4: Drop old columns, add new column, rename id_pelatihan
        $db->query("ALTER TABLE narasumber_pelatihan DROP COLUMN sesi_id, DROP COLUMN nama_narasumber, DROP COLUMN updated_at, ADD COLUMN pejabat_ttd_id INT UNSIGNED AFTER id, CHANGE COLUMN id_pelatihan pelatihan_id INT UNSIGNED");

        // Step 5: Update existing rows to use FK
        foreach ($rows as $row) {
            $name = $row['nama_narasumber'];
            if (empty($name)) continue;
            $pj = $db->query("SELECT id FROM pejabat_ttd_pelatihan WHERE nama_pejabat = ? AND status = 'Narasumber'", [$name])->getRowArray();
            if ($pj) {
                $db->query("UPDATE narasumber_pelatihan SET pejabat_ttd_id = ? WHERE id = ?", [$pj['id'], $row['id']]);
            }
        }

        // Step 6: Add new foreign keys
        $db->query("ALTER TABLE narasumber_pelatihan ADD INDEX idx_np_pttd (pejabat_ttd_id), ADD INDEX idx_np_pel (pelatihan_id)");
        $db->query("ALTER TABLE narasumber_pelatihan ADD CONSTRAINT fk_narasumber_pttd FOREIGN KEY (pejabat_ttd_id) REFERENCES pejabat_ttd_pelatihan(id) ON DELETE CASCADE");
        $db->query("ALTER TABLE narasumber_pelatihan ADD CONSTRAINT fk_narasumber_pel FOREIGN KEY (pelatihan_id) REFERENCES master_pelatihan(id) ON DELETE CASCADE");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $db->query("ALTER TABLE narasumber_pelatihan DROP FOREIGN KEY fk_narasumber_pttd");
        $db->query("ALTER TABLE narasumber_pelatihan DROP FOREIGN KEY fk_narasumber_pel");
        $db->query("ALTER TABLE narasumber_pelatihan DROP COLUMN pejabat_ttd_id, CHANGE COLUMN pelatihan_id id_pelatihan INT UNSIGNED");
        $db->query("ALTER TABLE narasumber_pelatihan ADD COLUMN sesi_id INT UNSIGNED NULL, ADD COLUMN nama_narasumber VARCHAR(255), ADD COLUMN updated_at DATETIME NULL");
    }
}
