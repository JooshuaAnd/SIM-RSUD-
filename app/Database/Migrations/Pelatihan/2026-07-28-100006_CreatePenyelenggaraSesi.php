<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class CreatePenyelenggaraSesi extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $tables = $db->query("SHOW TABLES LIKE 'penyelenggara_sesi'")->getResultArray();
        if (!empty($tables)) {
            return;
        }

        $db->query("CREATE TABLE penyelenggara_sesi (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            penyelenggara_id INT UNSIGNED NOT NULL,
            sesi_id INT UNSIGNED NOT NULL,
            created_at DATETIME NULL,
            INDEX idx_ps_mp (penyelenggara_id),
            INDEX idx_ps_sesi (sesi_id),
            CONSTRAINT fk_ps_mp FOREIGN KEY (penyelenggara_id) REFERENCES master_penyelenggara(id) ON DELETE CASCADE,
            CONSTRAINT fk_ps_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_interaktif_pelatihan(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("DROP TABLE IF EXISTS penyelenggara_sesi");
    }
}
