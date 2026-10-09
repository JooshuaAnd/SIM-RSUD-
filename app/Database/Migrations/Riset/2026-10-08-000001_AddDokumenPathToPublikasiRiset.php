<?php
namespace App\Database\Migrations\Riset;

use CodeIgniter\Database\Migration;

class AddDokumenPathToPublikasiRiset extends Migration
{
    public function up()
    {
        // Berkas arsip yang diunggah admin disimpan langsung di publikasi, karena
        // dokumen_riset memiliki foreign key ke pengajuan_riset (bukan publikasi_riset).
        $this->forge->addColumn('publikasi_riset', [
            'dokumen_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'abstrak',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('publikasi_riset', 'dokumen_path');
    }
}
