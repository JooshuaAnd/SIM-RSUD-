<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/pelatihan/admin/monitoring/broadcast_room');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"niks":["1234"],"message":"Test"}');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-Requested-With: XMLHttpRequest']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$t = microtime(true);
$res = curl_exec($ch);
echo "Time: " . (microtime(true) - $t) . "s\n";
echo "HTTP Code: " . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
print_r($res);
