<?php
require_once 'admin/db_connect.php';

function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

function setRememberMeCookie($alumni_id) {
    global $conn;
    
    // Generate a new token
    $token = generateRememberToken();
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    // Delete any existing remember tokens for this user
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE alumni_id = ?");
    $stmt->bind_param("i", $alumni_id);
    $stmt->execute();
    
    // Insert new remember token
    $stmt = $conn->prepare("INSERT INTO remember_tokens (alumni_id, token, expiry) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $alumni_id, $token, $expiry);
    $stmt->execute();
    
    // Set the cookie with proper parameters
    $cookie_options = array(
        'expires' => strtotime('+30 days'),
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    );
    
    setcookie('remember_token', $token, $cookie_options);
    return $token;
}

function validateRememberToken() {
    global $conn;
    
    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }
    
    $token = $_COOKIE['remember_token'];
    
    // Get token from database and join with alumnus_bio table
    $stmt = $conn->prepare("SELECT rt.*, ab.* FROM remember_tokens rt 
                           JOIN alumnus_bio ab ON rt.alumni_id = ab.id 
                           WHERE rt.token = ? AND rt.expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Invalid or expired token
        clearRememberToken(null); // Clear any existing cookie
        return false;
    }
    
    $user = $result->fetch_assoc();
    
    // Set all required session variables
    $_SESSION['login'] = true;
    $_SESSION['login_id'] = $user['id'];
    $_SESSION['login_type'] = 'alumni';
    $_SESSION['bio'] = $user;
    $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['verified'] = true;
    
    // Refresh the remember token
    $new_token = setRememberMeCookie($user['id']);
    
    return true;
}

function clearRememberToken($alumni_id) {
    global $conn;
    
    if ($alumni_id !== null) {
        // Delete token from database
        $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE alumni_id = ?");
        $stmt->bind_param("i", $alumni_id);
        $stmt->execute();
    }
    
    // Clear the cookie with all parameters
    $cookie_options = array(
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    );
    
    setcookie('remember_token', '', $cookie_options);
}

function getStoredCredentials() {
    global $conn;
    
    if (!isset($_COOKIE['remember_token'])) {
        return null;
    }
    
    $token = $_COOKIE['remember_token'];
    
    // Get user details and credentials from alumnus_bio table
    $stmt = $conn->prepare("SELECT rt.*, ab.email, ab.password, ab.firstname, ab.lastname 
                           FROM remember_tokens rt 
                           JOIN alumnus_bio ab ON rt.alumni_id = ab.id 
                           WHERE rt.token = ? AND rt.expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return [
            'email' => $user['email'],
            'password' => $user['password'], // This is already hashed
            'name' => $user['firstname'] . ' ' . $user['lastname'],
            'auto_fill' => true
        ];
    }
    
    // If token exists but is invalid, clear it
    clearRememberToken(null);
    return null;
}
?>
