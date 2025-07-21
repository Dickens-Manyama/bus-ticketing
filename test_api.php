<?php
$url = "http://192.168.100.76:8082/booking/api-verify-ticket?id=8";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "X-Requested-With: XMLHttpRequest"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";
?> 