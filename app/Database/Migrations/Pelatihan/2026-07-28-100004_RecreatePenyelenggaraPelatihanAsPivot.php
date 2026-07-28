<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class RecreatePenyelenggaraPelatihanAsPivot extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $cols = $db->query("SHOW COLUMNS FROM penyelenggara_pelatihan")->getResultArray();
        $colNames = array_column($cols, 'Field');
        $hasOldCols = in_array('nama_penyelenggara', $colNames);

        if (!$hasOldCols) {
            if (!in_array('penyelenggara_id', $colNames)) {
                $db->query("ALTER TABLE penyelenggara_pelatihan ADD COLUMN penyelenggara_id INT UNSIGNED AFTER id");
            }
            if (in_array('id_pelatihan', $colNames) && !in_array('pelatihan_id', $colNames)) {
                $db->query("ALTER TABLE penyelenggara_pelatihan CHANGE COLUMN id_pelatihan pelatihan_id INT UNSIGNED");
            }
            // Add indexes/constraints if missing
            $idxCheck = $db->query("SHOW INDEX FROM penyelenggara_pelatihan WHERE Key_name = 'fk_penyelenggara_mp'")->getRowArray();
            if (!$idxCheck) {
                $db->query("ALTER TABLE penyelenggara_pelatihan ADD INDEX idx_pp_mp (penyelenggara_id), ADD INDEX idx_pp_pel (pelatihan_id)");
                $db->query("ALTER TABLE penyelenggara_pelatihan ADD CONSTRAINT fk_penyelenggara_mp FOREIGN KEY (penyelenggara_id) REFERENCES master_penyelenggara(id) ON DELETE CASCADE");
                $db->query("ALTER TABLE penyelenggara_pelatihan ADD CONSTRAINT fk_penyelenggara_pel FOREIGN KEY (pelatihan_id) REFERENCES master_pelatihan(id) ON DELETE CASCADE");
            }
            return;
        }

        // Step 1: Read data BEFORE dropping columns
        $rows = $db->query("SELECT pp.id, pp.nama_penyelenggara FROM penyelenggara_pelatihan pp")->getResultArray();

        // Step 2: Create master_penyelenggara records from unique names
        foreach ($rows as $row) {
            $name = $row['nama_penyelenggara'];
            if (empty($name)) continue;
            $check = $db->query("SELECT id FROM master_penyelenggara WHERE nama = ?", [$name])->getRowArray();
            if (!$check) {
                $db->table('master_penyelenggara')->insert([
                    'nama' => $name,
                    'status' => 'Aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Step 3: Drop existing FK constraints
        $fks = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penyelenggara_pelatihan' AND REFERENCED_TABLE_NAME IS NOT NULL")->getResultArray();
        foreach ($fks as $fk) {
            $db->query("ALTER TABLE penyelenggara_pelatihan DROP FOREIGN KEY `{$fk['CONSTRAINT_NAME']}`");
        }

        // Step 4: Drop old columns, add new column, rename id_pelatihan
        $db->query("ALTER TABLE penyelenggara_pelatihan DROP COLUMN sesi_id, DROP COLUMN nama_penyelenggara, DROP COLUMN updated_at, ADD COLUMN penyelenggara_id INT UNSIGNED AFTER id, CHANGE COLUMN id_pelatihan pelatihan_id INT UNSIGNED");

        // Step 5: Update existing rows to use FK
        foreach ($rows as $row) {
            $name = $row['nama_penyelenggara'];
            if (empty($name)) continue;
            $mp = $db->query("SELECT id FROM master_penyelenggara WHERE nama = ?", [$name])->getRowArray();
            if ($mp) {
                $db->query("UPDATE penyelenggara_pelatihan SET penyelenggara_id = ? WHERE id = ?", [$mp['id'], $row['id']]);
            }
        }

        // Step 6: Add new foreign keys
        $db->query("ALTER TABLE penyelenggara_pelatihan ADD INDEX idx_pp_mp (penyelenggara_id), ADD INDEX idx_pp_pel (pelatihan_id)");
        $db->query("ALTER TABLE penyelenggara_pelatihan ADD CONSTRAINT fk_penyelenggara_mp FOREIGN KEY (penyelenggara_id) REFERENCES master_penyelenggara(id) ON DELETE CASCADE");
        $db->query("ALTER TABLE penyelenggara_pelatihan ADD CONSTRAINT fk_penyelenggara_pel FOREIGN KEY (pelatihan_id) REFERENCES master_pelatihan(id) ON DELETE CASCADE");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $db->query("ALTER TABLE penyelenggara_pelatihan DROP FOREIGN KEY fk_penyelenggara_mp");
        $db->query("ALTER TABLE penyelenggara_pelatihan DROP FOREIGN KEY fk_penyelenggara_pel");
        $db->query("ALTER TABLE penyelenggara_pelatihan DROP COLUMN penyelenggara_id, CHANGE COLUMN pelatihan_id id_pelatihan INT UNSIGNED");
        $db->query("ALTER TABLE penyelenggara_pelatihan ADD COLUMN sesi_id INT UNSIGNED NULL, ADD COLUMN nama_penyelenggara VARCHAR(255), ADD COLUMN updated_at DATETIME NULL");
    }
}
