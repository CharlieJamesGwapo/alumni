<?php
/**
 * Update Messages Table
 * Adds is_archived column if it doesn't exist
 */

require_once '../admin/db_connect.php';

echo "<h2>Updating Messages Table...</h2>";

// Add is_archived column
$sql = "ALTER TABLE messages ADD COLUMN IF NOT EXISTS is_archived TINYINT(1) DEFAULT 0";
if ($conn->query($sql)) {
    echo "✓ Added is_archived column<br>";
} else {
    echo "Note: is_archived column may already exist<br>";
}

echo "<br><strong>Update complete!</strong><br>";
echo "<a href='view_messages.php'>Go to Messages</a>";

$conn->close();
?>
