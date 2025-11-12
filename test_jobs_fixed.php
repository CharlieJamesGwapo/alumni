<?php
// Test the jobs system after fixing the duplicate function error
include 'admin/db_connect.php';

echo "<h1>Jobs System Test - After Fix</h1>";

try {
    // Test database connection
    if ($conn) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit();
    }

    // Test the fetch_jobs function directly
    $_POST['ajax_action'] = 'fetch_jobs';
    $_POST['page'] = 1;

    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Include the home.php file to test the function
    include 'home.php';

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>Stack trace: " . $e->getTraceAsString() . "</p>";
}

$conn->close();
?>
