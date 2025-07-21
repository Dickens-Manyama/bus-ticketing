<?php
// Test script to verify session separation between frontend and backend

echo "=== Session Separation Test ===\n\n";

// Test frontend session
echo "1. Testing Frontend Session:\n";
$frontendUrl = "http://192.168.100.76:8082/site/test";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $frontendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "frontend_cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "frontend_cookies.txt");
$response = curl_exec($ch);
curl_close($ch);

echo "Frontend Response: " . substr($response, 0, 200) . "...\n\n";

// Test backend session
echo "2. Testing Backend Session:\n";
$backendUrl = "http://192.168.100.76:8080/site/test";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $backendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "backend_cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "backend_cookies.txt");
$response = curl_exec($ch);
curl_close($ch);

echo "Backend Response: " . substr($response, 0, 200) . "...\n\n";

echo "3. Cookie Files Created:\n";
if (file_exists("frontend_cookies.txt")) {
    echo "Frontend cookies: " . file_get_contents("frontend_cookies.txt") . "\n";
}
if (file_exists("backend_cookies.txt")) {
    echo "Backend cookies: " . file_get_contents("backend_cookies.txt") . "\n";
}

echo "\n=== Test Complete ===\n";
echo "If sessions are properly separated, you should see different cookie names:\n";
echo "- Frontend: advanced-frontend\n";
echo "- Backend: advanced-backend\n";
?> 