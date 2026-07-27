<?php

namespace App\Database\Migrations\Pelatihan;

use CodeIgniter\Database\Migration;

class RemoveNarasumberPenyelenggaraFromMasterPelatihan extends Migration
{
    public function up()
    {
        // 1. Create narasumber_pelatihan table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pelatihan' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'nama_narasumber' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_pelatihan', 'master_pelatihan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('narasumber_pelatihan', true);

        // 2. Create penyelenggara_pelatihan table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pelatihan' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'nama_penyelenggara' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_pelatihan', 'master_pelatihan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penyelenggara_pelatihan', true);

        // 3. Migrate existing data
        $db = \Config\Database::connect();
        if ($db->tableExists('master_pelatihan')) {
            $query = $db->query("SELECT id, narasumber, penyelenggara FROM master_pelatihan");
            $results = $query->getResultArray();

            foreach ($results as $row) {
                if (!empty($row['narasumber']) && $row['narasumber'] !== '-') {
                    $db->table('narasumber_pelatihan')->insert([
                        'id_pelatihan' => $row['id'],
                        'nama_narasumber' => $row['narasumber'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                
                if (!empty($row['penyelenggara']) && $row['penyelenggara'] !== '-') {
                    $db->table('penyelenggara_pelatihan')->insert([
                        'id_pelatihan' => $row['id'],
                        'nama_penyelenggara' => $row['penyelenggara'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        // 4. Drop columns
        $this->forge->dropColumn('master_pelatihan', ['narasumber', 'penyelenggara']);
    }

    public function down()
    {
        // 1. Add columns back
        $fields = [
            'narasumber' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'penyelenggara' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('master_pelatihan', $fields);

        // 2. Drop tables
        $this->forge->dropTable('narasumber_pelatihan', true);
        $this->forge->dropTable('penyelenggara_pelatihan', true);
    }
}
