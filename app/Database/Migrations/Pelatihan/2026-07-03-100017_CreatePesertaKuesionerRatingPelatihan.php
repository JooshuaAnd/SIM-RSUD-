<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class CreatePesertaKuesionerRatingPelatihan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'peserta_pelat_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'kuesioner_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'sesi_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'materi_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'narasumber_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'penyelenggara_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'nilai_rating' => [
                'type'    => 'INT',
                'default' => '5',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('peserta_kuesioner_rating_pelatihan');
    }

    public function down()
    {
        $this->forge->dropTable('peserta_kuesioner_rating_pelatihan',true);
    }
}
