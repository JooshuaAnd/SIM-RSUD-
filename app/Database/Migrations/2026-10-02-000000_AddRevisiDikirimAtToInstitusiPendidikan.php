<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRevisiDikirimAtToInstitusiPendidikan extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('revisi_dikirim_at', 'institusi_pendidikan')) {
            $this->forge->addColumn('institusi_pendidikan', [
                'revisi_dikirim_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'catatan_revisi',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('revisi_dikirim_at', 'institusi_pendidikan')) {
            $this->forge->dropColumn('institusi_pendidikan', 'revisi_dikirim_at');
        }
    }
}
