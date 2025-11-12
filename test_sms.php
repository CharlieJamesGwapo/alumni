<?php

require_once 'admin/db_connect.php';

// Function to log SMS attempts
function log_sms($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/logs/sms_test.log');
}

// Test phone number
$test_phone = '09677314559'; // Replace with a test number

// Test message
$message = urlencode("MOIST Alumni SMS Test: " . date('Y-m-d H:i:s'));

// TextBlaster URL
$textblaster_base = 'http://192.168.8.34:8080/send';
$url = "{$textblaster_base}?phoneNumber={$test_phone}&message={$message}";

log_sms("Attempting SMS: URL = {$url}");

$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET'
));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

log_sms("Response: " . ($err ?: $response) . " HTTP Code: {$httpcode}");

echo "Test complete. Check logs/sms_test.log for results.";