<?php
/**
 * Check Templates in Database
 * Quick diagnostic tool
 */

require_once '../admin/db_connect.php';

echo "<h2>Template Database Check</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background: #800000; color: white; }
    tr:nth-child(even) { background: #f2f2f2; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 10px 0; }
</style>";

try {
    // Check if table exists
    $check = $conn->query("SHOW TABLES LIKE 'email_templates'");
    
    if ($check->num_rows === 0) {
        echo "<p class='error'>❌ Table 'email_templates' does not exist!</p>";
        echo "<p class='info'>Run: <a href='setup_messaging_db.php'>setup_messaging_db.php</a></p>";
        exit();
    }
    
    echo "<p class='success'>✓ Table 'email_templates' exists</p>";
    
    // Get all templates
    $result = $conn->query("SELECT * FROM email_templates ORDER BY id");
    $count = $result->num_rows;
    
    echo "<h3>Found $count templates:</h3>";
    
    if ($count === 0) {
        echo "<p class='error'>❌ No templates found in database!</p>";
        echo "<p class='info'>Run: <a href='setup_messaging_db.php'>setup_messaging_db.php</a> to create default templates</p>";
    } else {
        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Active</th>
                <th>Body Preview</th>
              </tr>";
        
        while ($row = $result->fetch_assoc()) {
            $bodyPreview = substr($row['template_body'], 0, 100) . '...';
            $active = $row['is_active'] ? '✓ Yes' : '✗ No';
            $activeClass = $row['is_active'] ? 'success' : 'error';
            
            echo "<tr>";
            echo "<td><strong>{$row['id']}</strong></td>";
            echo "<td>{$row['template_name']}</td>";
            echo "<td>{$row['template_subject']}</td>";
            echo "<td>{$row['category']}</td>";
            echo "<td class='$activeClass'>$active</td>";
            echo "<td><small>$bodyPreview</small></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Test JSON output
        echo "<h3>JSON Test (what JavaScript receives):</h3>";
        $result2 = $conn->query("SELECT * FROM email_templates WHERE is_active = 1 ORDER BY category, template_name");
        $templates = [];
        while ($row = $result2->fetch_assoc()) {
            $templates[] = $row;
        }
        
        echo "<div class='info'>";
        echo "<pre>" . json_encode(['status' => 'success', 'templates' => $templates], JSON_PRETTY_PRINT) . "</pre>";
        echo "</div>";
        
        echo "<h3>Quick Actions:</h3>";
        echo "<p>
                <a href='setup_messaging_db.php' style='padding: 10px 20px; background: #800000; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px;'>
                    Reset Templates
                </a>
                <a href='alumni.php' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px;'>
                    Go to Alumni Page
                </a>
              </p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
