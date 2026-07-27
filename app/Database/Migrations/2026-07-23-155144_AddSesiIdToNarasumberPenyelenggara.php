<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSesiIdToNarasumberPenyelenggara extends Migration
{
    public function up()
    {
        $this->forge->addColumn('narasumber_pelatihan', [
            'sesi_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'id_pelatihan'
            ],
        ]);
        
        $this->forge->addColumn('penyelenggara_pelatihan', [
            'sesi_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'id_pelatihan'
            ],
        ]);
        
        // Add foreign keys
        $this->db->query('ALTER TABLE narasumber_pelatihan ADD CONSTRAINT fk_narasumber_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_interaktif_pelatihan(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE penyelenggara_pelatihan ADD CONSTRAINT fk_penyelenggara_sesi FOREIGN KEY (sesi_id) REFERENCES sesi_interaktif_pelatihan(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign keys
        $this->db->query('ALTER TABLE narasumber_pelatihan DROP FOREIGN KEY fk_narasumber_sesi');
        $this->db->query('ALTER TABLE penyelenggara_pelatihan DROP FOREIGN KEY fk_penyelenggara_sesi');
        
        $this->forge->dropColumn('narasumber_pelatihan', 'sesi_id');
        $this->forge->dropColumn('penyelenggara_pelatihan', 'sesi_id');
    }
}
