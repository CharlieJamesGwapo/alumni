<?php
include '../admin/db_connect.php';

// Function to check if table exists
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Check if alumni_archive table already exists
if (!tableExists($conn, 'alumni_archive')) {
    // Read the SQL file
    $sql = file_get_contents('../database/alumni_archive.sql');
    
    // Execute the SQL
    if ($conn->multi_query($sql)) {
        echo "Success: alumni_archive table has been created successfully!";
        
        // Clear out any remaining results
        while ($conn->more_results()) {
            $conn->next_result();
        }
    } else {
        echo "Error: Could not create alumni_archive table. " . $conn->error;
    }
} else {
    echo "Notice: alumni_archive table already exists.";
}

// Let's also modify the archive_alumni.php script to handle the case where the table might not exist
$checkAndCreateTable = "CREATE TABLE IF NOT EXISTS `alumni_archive` LIKE `alumnus_bio`";
if ($conn->query($checkAndCreateTable)) {
    // Add the additional columns if they don't exist
    $alterQueries = [
        "ALTER TABLE `alumni_archive` ADD COLUMN IF NOT EXISTS `archived_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `date_created`",
        "ALTER TABLE `alumni_archive` ADD COLUMN IF NOT EXISTS `archived_by` int(11) DEFAULT NULL AFTER `archived_date`",
        "ALTER TABLE `alumni_archive` ADD COLUMN IF NOT EXISTS `archive_reason` text AFTER `archived_by`"
    ];
    
    foreach ($alterQueries as $query) {
        if (!$conn->query($query)) {
            echo "\nError adding column: " . $conn->error;
        }
    }
    
    echo "\nArchive table structure has been verified and updated if necessary.";
} else {
    echo "\nError: Could not verify archive table structure. " . $conn->error;
}

$conn->close();
?>
