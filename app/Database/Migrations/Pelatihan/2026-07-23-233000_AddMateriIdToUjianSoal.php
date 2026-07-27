<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class AddMateriIdToUjianSoal extends Migration
{
    public function up()
    {
        $fields = [
            'materi_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'ujian_id'
            ]
        ];
        
        $this->forge->addColumn('ujian_soal_pelatihan', $fields);
        
        $this->forge->addForeignKey('materi_id', 'materi_pelatihan', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('ujian_soal_pelatihan', 'ujian_soal_pelatihan_materi_id_foreign');
        $this->forge->dropColumn('ujian_soal_pelatihan', 'materi_id');
    }
}
