<?php
$db = mysqli_connect('localhost', 'root', '', 'sim_diklat');
if (!$db) die(mysqli_connect_error());

$query1 = "ALTER TABLE ujian_soal_pelatihan ADD COLUMN materi_id INT UNSIGNED NULL AFTER ujian_id";
$res1 = mysqli_query($db, $query1);
if ($res1) {
    echo "Column materi_id added.\n";
} else {
    echo "Error 1: " . mysqli_error($db) . "\n";
}

$query2 = "ALTER TABLE ujian_soal_pelatihan ADD CONSTRAINT ujian_soal_pelatihan_materi_id_foreign FOREIGN KEY (materi_id) REFERENCES materi_pelatihan(id) ON DELETE CASCADE ON UPDATE CASCADE";
$res2 = mysqli_query($db, $query2);
if ($res2) {
    echo "Foreign key added.\n";
} else {
    echo "Error 2: " . mysqli_error($db) . "\n";
}
