<?php
/**
 * Connection Test Script
 * Test database and file connections
 */

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$results = [
    'status' => 'success',
    'tests' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

// Test 1: PHP Version
$results['tests'][] = [
    'name' => 'PHP Version',
    'status' => 'success',
    'message' => 'PHP ' . phpversion()
];

// Test 2: Database Connection
try {
    require_once '../admin/db_connect.php';
    
    if ($conn && $conn->ping()) {
        $results['tests'][] = [
            'name' => 'Database Connection',
            'status' => 'success',
            'message' => 'Connected successfully'
        ];
        
        // Test 3: Check tables exist
        $tables = ['messages', 'message_recipients', 'email_templates', 'alumnus_bio'];
        foreach ($tables as $table) {
            $check = $conn->query("SHOW TABLES LIKE '$table'");
            if ($check && $check->num_rows > 0) {
                $results['tests'][] = [
                    'name' => "Table: $table",
                    'status' => 'success',
                    'message' => 'Table exists'
                ];
            } else {
                $results['tests'][] = [
                    'name' => "Table: $table",
                    'status' => 'warning',
                    'message' => 'Table not found - run setup_messaging_db.php'
                ];
            }
        }
        
        // Test 4: Count alumni
        $alumni_count = $conn->query("SELECT COUNT(*) as count FROM alumnus_bio WHERE status = 1 AND email IS NOT NULL AND email != ''");
        if ($alumni_count) {
            $count = $alumni_count->fetch_assoc()['count'];
            $results['tests'][] = [
                'name' => 'Alumni Count',
                'status' => 'success',
                'message' => "$count validated alumni with emails"
            ];
        }
        
        // Test 5: Count templates
        $template_check = $conn->query("SHOW TABLES LIKE 'email_templates'");
        if ($template_check && $template_check->num_rows > 0) {
            $template_count = $conn->query("SELECT COUNT(*) as count FROM email_templates WHERE is_active = 1");
            if ($template_count) {
                $count = $template_count->fetch_assoc()['count'];
                $results['tests'][] = [
                    'name' => 'Email Templates',
                    'status' => 'success',
                    'message' => "$count active templates"
                ];
            }
        }
        
    } else {
        $results['tests'][] = [
            'name' => 'Database Connection',
            'status' => 'error',
            'message' => 'Failed to connect to database'
        ];
        $results['status'] = 'error';
    }
} catch (Exception $e) {
    $results['tests'][] = [
        'name' => 'Database Connection',
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    $results['status'] = 'error';
}

// Test 6: PHPMailer
if (file_exists('../PHPMailer/src/PHPMailer.php')) {
    $results['tests'][] = [
        'name' => 'PHPMailer',
        'status' => 'success',
        'message' => 'PHPMailer library found'
    ];
} else {
    $results['tests'][] = [
        'name' => 'PHPMailer',
        'status' => 'error',
        'message' => 'PHPMailer library not found'
    ];
    $results['status'] = 'error';
}

// Test 7: File Permissions
$test_file = 'test_write.tmp';
if (file_put_contents($test_file, 'test')) {
    unlink($test_file);
    $results['tests'][] = [
        'name' => 'File Permissions',
        'status' => 'success',
        'message' => 'Write permissions OK'
    ];
} else {
    $results['tests'][] = [
        'name' => 'File Permissions',
        'status' => 'warning',
        'message' => 'Cannot write to directory'
    ];
}

// Test 8: Session
session_start();
if (isset($_SESSION)) {
    $results['tests'][] = [
        'name' => 'Session Support',
        'status' => 'success',
        'message' => 'Sessions working'
    ];
} else {
    $results['tests'][] = [
        'name' => 'Session Support',
        'status' => 'error',
        'message' => 'Sessions not working'
    ];
}

// Test 9: JSON Support
if (function_exists('json_encode')) {
    $results['tests'][] = [
        'name' => 'JSON Support',
        'status' => 'success',
        'message' => 'JSON functions available'
    ];
} else {
    $results['tests'][] = [
        'name' => 'JSON Support',
        'status' => 'error',
        'message' => 'JSON functions not available'
    ];
}

// Test 10: cURL Support (for email)
if (function_exists('curl_version')) {
    $results['tests'][] = [
        'name' => 'cURL Support',
        'status' => 'success',
        'message' => 'cURL available for external connections'
    ];
} else {
    $results['tests'][] = [
        'name' => 'cURL Support',
        'status' => 'warning',
        'message' => 'cURL not available'
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>
