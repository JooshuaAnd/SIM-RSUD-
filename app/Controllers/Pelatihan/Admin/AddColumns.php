<?php

namespace App\Controllers\Pelatihan\Admin;

use App\Controllers\BaseController;

class AddColumns extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $fields = $db->listFields('peserta_pelatihan');
        $result = [];

        if (!in_array('completed_steps', $fields)) {
            $db->query("ALTER TABLE `peserta_pelatihan` ADD COLUMN `completed_steps` TEXT NULL AFTER `status_akses`");
            $result[] = 'Added completed_steps';
        } else {
            $result[] = 'completed_steps already exists';
        }

        if (!in_array('progress', $fields)) {
            $db->query("ALTER TABLE `peserta_pelatihan` ADD COLUMN `progress` DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER `completed_steps`");
            $result[] = 'Added progress';
        } else {
            $result[] = 'progress already exists';
        }

        return $this->response->setJSON($result);
    }
}
