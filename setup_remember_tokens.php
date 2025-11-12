<?php
require_once 'admin/db_connect.php';

// Drop the table if it exists to ensure we have the correct structure
$sql = "DROP TABLE IF EXISTS remember_tokens;";
$conn->query($sql);

// Create the remember_tokens table
$sql = "CREATE TABLE remember_tokens (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    alumni_id INT(11) NOT NULL,
    token VARCHAR(128) NOT NULL,
    expiry DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY token (token),
    KEY alumni_id (alumni_id),
    FOREIGN KEY (alumni_id) REFERENCES alumnus_bio(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql)) {
    echo "Remember tokens table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
