<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class AddProgressToPesertaPelatihan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('peserta_pelatihan', [
            'completed_steps' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'status_akses',
            ],
            'progress' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
                'after' => 'completed_steps',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('peserta_pelatihan', ['completed_steps', 'progress']);
    }
}
