<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class AddAdminAksesPesertaToUsersPelatihan extends Migration
{
    public function up()
    {
        $column = $this->db->query("SHOW COLUMNS FROM users_pelatihan LIKE 'admin_akses_peserta'")->getResultArray();
        if (empty($column)) {
            $this->forge->addColumn('users_pelatihan', [
                'admin_akses_peserta' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => true,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'role',
                ],
            ]);
        }
    }

    public function down()
    {
        $column = $this->db->query("SHOW COLUMNS FROM users_pelatihan LIKE 'admin_akses_peserta'")->getResultArray();
        if (!empty($column)) {
            $this->forge->dropColumn('users_pelatihan', 'admin_akses_peserta');
        }
    }
}
