<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlasanPenolakanToMahasiswaPendidikan extends Migration
{
    public function up()
    {
        try {
            if (!$this->db->fieldExists('alasan_penolakan', 'mahasiswa_pendidikan')) {
                $this->forge->addColumn('mahasiswa_pendidikan', [
                    'alasan_penolakan' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'after' => 'file_bukti_bayar'
                    ]
                ]);
            }
        } catch (\Exception $e) {}
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
