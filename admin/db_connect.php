<?php 
// Suppress errors for initial connection attempt
@$conn = new mysqli('localhost', 'root', '', 'alumni_db');

// Check if connection failed
if ($conn->connect_error) {
    // Check if this is a database-related operation that should work without database
    $allow_without_db = false;
    
    // Allow backup page to load even if database doesn't exist
    if(isset($_GET['page']) && $_GET['page'] == 'backup') {
        $allow_without_db = true;
    }
    
    // Allow system settings page to load even if database doesn't exist
    if(isset($_GET['page']) && $_GET['page'] == 'site_settings') {
        $allow_without_db = true;
    }
    
    // Allow import action to work without database
    if(isset($_GET['action']) && $_GET['action'] == 'import_database') {
        $allow_without_db = true;
    }
    
    if($allow_without_db) {
        // Create a temporary connection without database
        $conn = new mysqli('localhost', 'root', '');
        if($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Set a flag to indicate database doesn't exist
        $GLOBALS['db_missing'] = true;
    } else {
        // For other operations, show error
        die("Connection failed: " . $conn->connect_error);
    }
} else {
    // Enable error reporting only if connection succeeded
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
    // Database exists
    $GLOBALS['db_missing'] = false;
}
?>
