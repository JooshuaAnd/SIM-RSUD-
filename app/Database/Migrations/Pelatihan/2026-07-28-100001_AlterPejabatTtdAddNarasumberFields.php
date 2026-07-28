<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class AlterPejabatTtdAddNarasumberFields extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        $rows = $db->query("SHOW COLUMNS FROM pejabat_ttd_pelatihan")->getResultArray();
        $existing = array_column($rows, 'Field');
        $columnsToAdd = [];

        $allColumns = [
            'status'       => ['type' => "ENUM('Pejabat','Narasumber')", 'default' => 'Pejabat', 'after' => 'id'],
            'gelar_depan'  => ['type' => 'VARCHAR(50)', 'null' => true, 'after' => 'status'],
            'gelar_belakang' => ['type' => 'VARCHAR(50)', 'null' => true, 'after' => 'gelar_depan'],
            'pendidikan'   => ['type' => 'VARCHAR(100)', 'null' => true, 'after' => 'gelar_belakang'],
            'riwayat'      => ['type' => 'TEXT', 'null' => true, 'after' => 'pendidikan'],
            'keahlian'     => ['type' => 'VARCHAR(255)', 'null' => true, 'after' => 'riwayat'],
            'foto'         => ['type' => 'VARCHAR(255)', 'null' => true, 'after' => 'keahlian'],
            'kontak'       => ['type' => 'VARCHAR(100)', 'null' => true, 'after' => 'foto'],
            'email'        => ['type' => 'VARCHAR(255)', 'null' => true, 'after' => 'kontak'],
        ];

        foreach ($allColumns as $col => $def) {
            if (!in_array($col, $existing)) {
                $columnsToAdd[$col] = $def;
            }
        }

        if (!empty($columnsToAdd)) {
            $forge->addColumn('pejabat_ttd_pelatihan', $columnsToAdd);
        }
    }

    public function down()
    {
        $forge = \Config\Database::forge();
        $forge->dropColumn('pejabat_ttd_pelatihan', [
            'status', 'gelar_depan', 'gelar_belakang', 'pendidikan',
            'riwayat', 'keahlian', 'foto', 'kontak', 'email'
        ]);
    }
}
