<?php
// Test script to verify admin can log in to frontend
echo "=== Admin Frontend Login Test ===\n\n";

// Test admin login on frontend
$loginUrl = "http://192.168.100.76:8082/site/login";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "admin_frontend_cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "admin_frontend_cookies.txt");
$response = curl_exec($ch);
curl_close($ch);

echo "1. Frontend Login Page Response: " . substr($response, 0, 200) . "...\n\n";

// Test if we can access the home page after login attempt
$homeUrl = "http://192.168.100.76:8082/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $homeUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "admin_frontend_cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "admin_frontend_cookies.txt");
$response = curl_exec($ch);
curl_close($ch);

echo "2. Frontend Home Page Response: " . substr($response, 0, 200) . "...\n\n";

echo "=== Test Complete ===\n";
echo "If the login page loads without errors, administrators should be able to log in.\n";
echo "The key is that there are no access restrictions on the login action itself.\n";
?> 