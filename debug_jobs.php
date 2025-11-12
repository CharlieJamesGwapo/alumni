<?php
include 'admin/db_connect.php';

echo "<h2>Database Debug - Jobs Table</h2>";

// Check if careers table exists
$result = $conn->query("SHOW TABLES LIKE 'careers'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>❌ careers table does not exist!</p>";
} else {
    echo "<p style='color: green;'>✅ careers table exists</p>";
}

// Check careers table structure
echo "<h3>Table Structure:</h3>";
$result = $conn->query("DESCRIBE careers");
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td></tr>";
    }
    echo "</table>";
}

// Check data in careers table
echo "<h3>Jobs Data:</h3>";
$result = $conn->query("SELECT * FROM careers ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Company</th><th>Job Title</th><th>Location</th><th>Description</th><th>User ID</th><th>Date Created</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['company']}</td>";
        echo "<td>{$row['job_title']}</td>";
        echo "<td>{$row['location']}</td>";
        echo "<td>" . substr($row['description'], 0, 50) . "...</td>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['date_created']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No jobs found in careers table</p>";
}

// Test the exact query used in home.php
echo "<h3>Testing Home.php Query:</h3>";
$stmt = $conn->prepare("SELECT c.*, COALESCE(u.name, 'Admin') as posted_by FROM careers c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.id DESC LIMIT 10 OFFSET 0");
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo "<p style='color: green;'>✅ Query successful, found {$res->num_rows} jobs</p>";
    echo "<table border='1'><tr><th>ID</th><th>Company</th><th>Job Title</th><th>Posted By</th></tr>";
    while ($row = $res->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['company']}</td><td>{$row['job_title']}</td><td>{$row['posted_by']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Query returned no results</p>";
}

$conn->close();
?>
