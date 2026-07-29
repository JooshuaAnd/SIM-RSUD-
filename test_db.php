<?php
require 'app/Config/Database.php';
\ = new \Config\Database();
\ = mysqli_connect('viaduct.proxy.rlwy.net', 'root', 'mUjBvntKEDpCqgQeTOnrTzSjFvRmsnDB', 'railway', 58739);
\ = mysqli_query(\, 'SELECT * FROM narasumber_pelatihan WHERE pelatihan_id = 6');
while(\ = mysqli_fetch_assoc(\)) { print_r(\); }
\ = mysqli_query(\, 'SELECT * FROM narasumber_sesi');
while(\ = mysqli_fetch_assoc(\)) { print_r(\); }

