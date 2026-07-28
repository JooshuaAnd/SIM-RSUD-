<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class MergeSesiIntoUnifiedPivotTables extends Migration
{
    public function up()
    {
        $db = $this->db;

        // Add sesi_id column to narasumber_pelatihan
        $cols = $db->query("SHOW COLUMNS FROM narasumber_pelatihan LIKE 'sesi_id'")->getResultArray();
        if (empty($cols)) {
            $db->query("ALTER TABLE narasumber_pelatihan ADD COLUMN sesi_id INT UNSIGNED NULL AFTER pelatihan_id");
            $db->query("ALTER TABLE narasumber_pelatihan ADD KEY idx_narasumber_sesi_id (sesi_id)");
        }

        // Add sesi_id column to penyelenggara_pelatihan
        $cols2 = $db->query("SHOW COLUMNS FROM penyelenggara_pelatihan LIKE 'sesi_id'")->getResultArray();
        if (empty($cols2)) {
            $db->query("ALTER TABLE penyelenggara_pelatihan ADD COLUMN sesi_id INT UNSIGNED NULL AFTER pelatihan_id");
            $db->query("ALTER TABLE penyelenggara_pelatihan ADD KEY idx_penyelenggara_sesi_id (sesi_id)");
        }

        // Migrate data from narasumber_sesi into narasumber_pelatihan (with sesi_id set)
        $db->query("INSERT INTO narasumber_pelatihan (pejabat_ttd_id, pelatihan_id, sesi_id, created_at)
            SELECT ns.pejabat_ttd_id, s.pelatihan_id, ns.sesi_id, ns.created_at
            FROM narasumber_sesi ns
            JOIN sesi_interaktif_pelatihan s ON s.id = ns.sesi_id
            WHERE NOT EXISTS (
                SELECT 1 FROM narasumber_pelatihan np
                WHERE np.pejabat_ttd_id = ns.pejabat_ttd_id
                AND np.pelatihan_id = s.pelatihan_id
                AND np.sesi_id = ns.sesi_id
            )");

        // Migrate data from penyelenggara_sesi into penyelenggara_pelatihan (with sesi_id set)
        $db->query("INSERT INTO penyelenggara_pelatihan (penyelenggara_id, pelatihan_id, sesi_id, created_at)
            SELECT ps.penyelenggara_id, s.pelatihan_id, ps.sesi_id, ps.created_at
            FROM penyelenggara_sesi ps
            JOIN sesi_interaktif_pelatihan s ON s.id = ps.sesi_id
            WHERE NOT EXISTS (
                SELECT 1 FROM penyelenggara_pelatihan pp
                WHERE pp.penyelenggara_id = ps.penyelenggara_id
                AND pp.pelatihan_id = s.pelatihan_id
                AND pp.sesi_id = ps.sesi_id
            )");

        // Drop the separate _sesi tables
        $tables = $db->query("SHOW TABLES LIKE 'narasumber_sesi'")->getResultArray();
        if (!empty($tables)) {
            $db->query("DROP TABLE narasumber_sesi");
        }
        $tables2 = $db->query("SHOW TABLES LIKE 'penyelenggara_sesi'")->getResultArray();
        if (!empty($tables2)) {
            $db->query("DROP TABLE penyelenggara_sesi");
        }
    }

    public function down()
    {
        $db = $this->db;

        // Recreate narasumber_sesi
        $tables = $db->query("SHOW TABLES LIKE 'narasumber_sesi'")->getResultArray();
        if (empty($tables)) {
            $db->query("CREATE TABLE narasumber_sesi (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                pejabat_ttd_id INT UNSIGNED NOT NULL,
                sesi_id INT UNSIGNED NOT NULL,
                created_at DATETIME DEFAULT NULL,
                PRIMARY KEY (id),
                KEY idx_ns_pejabat (pejabat_ttd_id),
                KEY idx_ns_sesi (sesi_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        }

        // Recreate penyelenggara_sesi
        $tables2 = $db->query("SHOW TABLES LIKE 'penyelenggara_sesi'")->getResultArray();
        if (empty($tables2)) {
            $db->query("CREATE TABLE penyelenggara_sesi (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                penyelenggara_id INT UNSIGNED NOT NULL,
                sesi_id INT UNSIGNED NOT NULL,
                created_at DATETIME DEFAULT NULL,
                PRIMARY KEY (id),
                KEY idx_ps_penyelenggara (penyelenggara_id),
                KEY idx_ps_sesi (sesi_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        }

        // Migrate data back from unified tables where sesi_id IS NOT NULL
        $db->query("INSERT INTO narasumber_sesi (pejabat_ttd_id, sesi_id, created_at)
            SELECT pejabat_ttd_id, sesi_id, created_at FROM narasumber_pelatihan WHERE sesi_id IS NOT NULL");

        $db->query("INSERT INTO penyelenggara_sesi (penyelenggara_id, sesi_id, created_at)
            SELECT penyelenggara_id, sesi_id, created_at FROM penyelenggara_pelatihan WHERE sesi_id IS NOT NULL");

        // Remove sesi_id column from unified tables
        $db->query("ALTER TABLE narasumber_pelatihan DROP COLUMN sesi_id, DROP KEY idx_narasumber_sesi_id");
        $db->query("ALTER TABLE penyelenggara_pelatihan DROP COLUMN sesi_id, DROP KEY idx_penyelenggara_sesi_id");

        // Remove sesi-level rows from unified tables
        $db->query("DELETE FROM narasumber_pelatihan WHERE sesi_id IS NULL");
        $db->query("DELETE FROM penyelenggara_pelatihan WHERE sesi_id IS NULL");
    }
}
