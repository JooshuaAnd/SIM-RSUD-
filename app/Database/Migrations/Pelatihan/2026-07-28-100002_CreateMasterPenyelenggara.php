<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class CreateMasterPenyelenggara extends Migration
{
    public function up()
    {
        $forge = \Config\Database::forge();

        $forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR(255)'],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'fokus_bidang' => ['type' => 'VARCHAR(255)', 'null' => true],
            'kontak' => ['type' => 'VARCHAR(100)', 'null' => true],
            'email' => ['type' => 'VARCHAR(255)', 'null' => true],
            'logo' => ['type' => 'VARCHAR(255)', 'null' => true],
            'status' => ['type' => "ENUM('Aktif','Nonaktif')", 'default' => 'Aktif'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('master_penyelenggara');
    }

    public function down()
    {
        $forge = \Config\Database::forge();
        $forge->dropTable('master_penyelenggara');
    }
}
