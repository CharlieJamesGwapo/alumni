<?php
/**
 * Update Database - Add RSVP Columns
 * Run this ONCE to add RSVP tracking columns
 */

require_once '../admin/db_connect.php';

echo "<h2>Adding RSVP Columns to Database...</h2>";

try {
    // Check if columns already exist
    $check = $conn->query("SHOW COLUMNS FROM message_recipients LIKE 'rsvp_status'");
    
    if ($check->num_rows > 0) {
        echo "<p style='color: orange;'>⚠️ RSVP columns already exist. No changes needed.</p>";
    } else {
        // Add rsvp_status column
        $sql1 = "ALTER TABLE message_recipients 
                 ADD COLUMN rsvp_status ENUM('pending', 'accept', 'decline', 'maybe') DEFAULT 'pending' AFTER read_at";
        
        if ($conn->query($sql1)) {
            echo "<p style='color: green;'>✓ Added rsvp_status column</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding rsvp_status: " . $conn->error . "</p>";
        }
        
        // Add rsvp_at column
        $sql2 = "ALTER TABLE message_recipients 
                 ADD COLUMN rsvp_at DATETIME NULL AFTER rsvp_status";
        
        if ($conn->query($sql2)) {
            echo "<p style='color: green;'>✓ Added rsvp_at column</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding rsvp_at: " . $conn->error . "</p>";
        }
        
        // Add index for rsvp_status
        $sql3 = "ALTER TABLE message_recipients 
                 ADD INDEX idx_rsvp_status (rsvp_status)";
        
        if ($conn->query($sql3)) {
            echo "<p style='color: green;'>✓ Added index for rsvp_status</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding index: " . $conn->error . "</p>";
        }
        
        echo "<br><h3 style='color: green;'>✅ Database updated successfully!</h3>";
        echo "<p>RSVP tracking is now enabled.</p>";
    }
    
    echo "<br><a href='alumni.php' style='padding: 10px 20px; background: #800000; color: white; text-decoration: none; border-radius: 5px;'>Go to Alumni Management</a>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
