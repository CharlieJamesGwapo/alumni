<?php
session_start();
require_once 'auth_functions.php';

header('Content-Type: application/json');

$credentials = getStoredCredentials();

if ($credentials) {
    // Never expose passwords — only return email and name for auto-fill
    echo json_encode([
        'status' => 'success',
        'credentials' => [
            'email' => $credentials['email'],
            'name' => $credentials['name'],
            'auto_fill' => true
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No stored credentials found'
    ]);
}
