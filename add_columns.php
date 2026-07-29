<?php
require __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

$fields = $db->listFields('peserta_pelatihan');
echo "Current columns: " . implode(', ', $fields) . "\n\n";

if (!in_array('completed_steps', $fields)) {
    $db->query("ALTER TABLE `peserta_pelatihan` ADD COLUMN `completed_steps` TEXT NULL AFTER `status_akses`");
    echo "Added completed_steps\n";
} else {
    echo "completed_steps already exists\n";
}

if (!in_array('progress', $fields)) {
    $db->query("ALTER TABLE `peserta_pelatihan` ADD COLUMN `progress` DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER `completed_steps`");
    echo "Added progress\n";
} else {
    echo "progress already exists\n";
}

$fields2 = $db->listFields('peserta_pelatihan');
echo "\nFinal columns: " . implode(', ', $fields2) . "\n";
