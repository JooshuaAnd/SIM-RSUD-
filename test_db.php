<?php
$db = mysqli_connect('localhost', 'root', '', 'sim_diklat');
if (!$db) die(mysqli_connect_error());
$res = mysqli_query($db, "SELECT * FROM ujian_soal_pelatihan LIMIT 1");
if (!$res) {
    echo "Error: " . mysqli_error($db);
} else {
    $fields = mysqli_fetch_fields($res);
    foreach ($fields as $f) {
        echo $f->name . "\n";
    }
}
