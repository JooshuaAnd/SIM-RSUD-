<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddProgressColumns2 extends BaseCommand
{
    protected $group = 'App';
    protected $name = 'app:add-progress-columns';
    protected $description = 'Add completed_steps and progress columns to peserta_pelatihan';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $fields = $db->listFields('peserta_pelatihan');

        if (!in_array('completed_steps', $fields)) {
            $db->query("ALTER TABLE `peserta_pelatihan` ADD COLUMN `completed_steps` TEXT NULL AFTER `status_akses`");
            CLI::write('Added completed_steps', 'green');
        } else {
            CLI::write('completed_steps already exists', 'cyan');
        }

        if (!in_array('progress', $fields)) {
            $db->query("ALTER TABLE `peserta_pelatihan` ADD COLUMN `progress` DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER `completed_steps`");
            CLI::write('Added progress', 'green');
        } else {
            CLI::write('progress already exists', 'cyan');
        }

        $fields2 = $db->listFields('peserta_pelatihan');
        CLI::write('Final columns: ' . implode(', ', $fields2), 'green');
    }
}
