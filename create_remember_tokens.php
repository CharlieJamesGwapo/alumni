<?php
require 'admin/db_connect.php';

$sql = "DROP TABLE IF EXISTS remember_tokens;
CREATE TABLE remember_tokens (
    id int(11) NOT NULL AUTO_INCREMENT,
    alumni_id int(11) NOT NULL,
    token varchar(128) NOT NULL,
    expiry datetime NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY token (token),
    KEY alumni_id (alumni_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "Remember tokens table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
