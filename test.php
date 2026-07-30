<?php
require "public/index.php";
$db = \Config\Database::connect();
$narasumberList = $db->table("narasumber_pelatihan")
    ->select("narasumber_pelatihan.*, pejabat_ttd_pelatihan.nama_pejabat")
    ->join("pejabat_ttd_pelatihan", "pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id", "left")
    ->where("narasumber_pelatihan.pelatihan_id", 6)
    ->get()->getResultArray();
echo "NarasumberList:\n";
print_r($narasumberList);
$penyelenggaraList = $db->table("penyelenggara_pelatihan")
    ->select("penyelenggara_pelatihan.*, master_penyelenggara.nama")
    ->join("master_penyelenggara", "master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id", "left")
    ->where("penyelenggara_pelatihan.pelatihan_id", 6)
    ->get()->getResultArray();
echo "\nPenyelenggaraList:\n";
print_r($penyelenggaraList);

