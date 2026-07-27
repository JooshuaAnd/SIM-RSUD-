<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlasanPenolakanToMahasiswa extends Migration
{
    public function up()
    {
        try {
            $this->forge->addColumn('mahasiswa_pendidikan', [
                'alasan_penolakan' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
            ]);
        } catch (\Exception $e) {
            // Ignore if column exists
        }
    }

    public function down()
    {
        try {
            if ($this->db->fieldExists('alasan_penolakan', 'mahasiswa_pendidikan')) {
                $this->forge->dropColumn('mahasiswa_pendidikan', 'alasan_penolakan');
            }
        } catch (\Exception $e) {}
    }
}
