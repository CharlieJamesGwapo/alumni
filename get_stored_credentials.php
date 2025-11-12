<?php
require_once 'auth_functions.php';

function getStoredCredentials() {
    global $conn;
    
    if (!isset($_COOKIE['remember_token'])) {
        return null;
    }
    
    $token = $_COOKIE['remember_token'];
    
    $stmt = $conn->prepare("SELECT rt.credentials, ab.firstname, ab.lastname 
                           FROM remember_tokens rt 
                           JOIN alumnus_bio ab ON rt.alumni_id = ab.id 
                           WHERE rt.token = ? AND rt.expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored = json_decode($row['credentials'], true);
        
        return [
            'email' => $stored['email'],
            'password' => $stored['password'],
            'name' => $row['firstname'] . ' ' . $row['lastname']
        ];
    }
    
    clearRememberToken(null);
    return null;
}

header('Content-Type: application/json');

$credentials = getStoredCredentials();

if ($credentials) {
    echo json_encode([
        'status' => 'success',
        'credentials' => $credentials
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No stored credentials found'
    ]);
}
?>
