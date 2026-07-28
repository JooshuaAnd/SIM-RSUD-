<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class CreateNarasumberSesi extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $tables = $db->query("SHOW TABLES LIKE 'narasumber_sesi'")->getResultArray();
        if (!empty($tables)) {
            return;
        }

        $db->query("CREATE TABLE narasumber_sesi (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            pejabat_ttd_id INT UNSIGNED NOT NULL,
            sesi_id INT UNSIGNED NOT NULL,
            created_at DATETIME NULL,
            INDEX idx_ns_pttd (pejabat_ttd_id),
            INDEX idx_ns_sesi (sesi_id),
            CONSTRAINT fk_ns_pttd FOREIGN KEY (pejabat_ttd_id) REFERENCES pejabat_ttd_pelatihan(id) ON DELETE CASCADE,
            CONSTRAINT fk_ns_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_interaktif_pelatihan(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("DROP TABLE IF EXISTS narasumber_sesi");
    }
}
