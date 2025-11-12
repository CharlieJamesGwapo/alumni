<?php
session_start();
include '../admin/db_connect.php';
header('Content-Type: application/json');

// Debug log (workspace-local) to capture runtime issues when client shows generic error
$debugLog = __DIR__ . DIRECTORY_SEPARATOR . 'archive_debug.log';
function dbg($msg){ global $debugLog; file_put_contents($debugLog, date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND); }
dbg("REQUEST_START method=" . ($_SERVER['REQUEST_METHOD'] ?? 'NA'));

// Accept JSON payloads (e.g., fetch/fetch API) in addition to form-encoded posts
$rawInput = file_get_contents('php://input');
dbg("RAW_INPUT=" . substr(($rawInput ?? ''),0,2000));
if (empty($_POST['ids']) && !empty($rawInput)) {
    $json = json_decode($rawInput, true);
    if (isset($json['ids'])) $_POST['ids'] = $json['ids'];
}

// Normalize incoming ids into a clean integer array
$incomingIds = [];
if (isset($_POST['ids'])) {
    if (is_array($_POST['ids'])) $incomingIds = $_POST['ids'];
    else $incomingIds = [$_POST['ids']];
}
// cast to ints and remove invalid/zero values
$incomingIds = array_map('intval', $incomingIds);
$incomingIds = array_values(array_filter($incomingIds, function($v){ return $v > 0; }));

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Create archive_alumni table if it doesn't exist
$create_archive_table = "CREATE TABLE IF NOT EXISTS archive_alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_id INT NOT NULL,
    strand_id INT DEFAULT NULL,
    firstname VARCHAR(255) NOT NULL,
    middlename VARCHAR(255),
    lastname VARCHAR(255) NOT NULL,
    gender VARCHAR(10),
    contact_no VARCHAR(20),
    email VARCHAR(255),
    batch INT,
    course_id INT,
    connected_to VARCHAR(255),
    company_address TEXT,
    address TEXT,
    status TINYINT(1) DEFAULT 0,
    archived_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    archived_by INT,
    FOREIGN KEY (archived_by) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
)";

$conn->query($create_archive_table);

// Ensure legacy installations get the new strand_id column if missing
$colCheck = $conn->query("SHOW COLUMNS FROM archive_alumni LIKE 'strand_id'");
if ($colCheck && $colCheck->num_rows == 0) {
    // add column without breaking existing data
    $conn->query("ALTER TABLE archive_alumni ADD COLUMN strand_id INT DEFAULT NULL");
}

// Ensure activity_log exists (create lightweight version if missing)
$checkActivity = $conn->query("SHOW TABLES LIKE 'activity_log'");
if ($checkActivity->num_rows == 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(50),
        description TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($incomingIds)) {
    $ids = $incomingIds;
    $user_id = $_SESSION['user_id'];

    $report = [
        'archived' => [],
        'already' => [],
        'error' => []
    ];

    foreach ($ids as $id) {
        $idInt = intval($id);

        // Check active table
        $select_query = "SELECT * FROM alumnus_bio WHERE id = '$idInt'";
        $result = $conn->query($select_query);
        if ($result === false) {
            dbg("SELECT_FAILED id=$idInt query={$select_query} error=" . $conn->error);
        }

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Check if already archived
            $check_arch = $conn->query("SELECT id FROM archive_alumni WHERE alumni_id = '$idInt' LIMIT 1");
            if ($check_arch && $check_arch->num_rows > 0) {
                $report['already'][] = $idInt;
                continue;
            }

            // Prepare sanitized values, allow NULL where empty
            $firstname = $conn->real_escape_string($row['firstname'] ?? '');
            $middlename = $conn->real_escape_string($row['middlename'] ?? '');
            $lastname = $conn->real_escape_string($row['lastname'] ?? '');
            $gender = $conn->real_escape_string($row['gender'] ?? '');
            $contact_no = $conn->real_escape_string($row['contact_no'] ?? '');
            $email = $conn->real_escape_string($row['email'] ?? '');
            $batch = is_numeric($row['batch']) ? intval($row['batch']) : 'NULL';
            $course_id = is_numeric($row['course_id']) ? intval($row['course_id']) : 'NULL';
            $connected_to = $conn->real_escape_string($row['connected_to'] ?? '');
            $company_address = $conn->real_escape_string($row['company_address'] ?? '');
            $address = $conn->real_escape_string($row['address'] ?? '');
            $status = is_numeric($row['status']) ? intval($row['status']) : 0;

            // Use prepared statement to avoid SQL issues with quotes/nulls and ensure stability
            // Include strand_id so SHS/strand information is preserved. Use NULLIF(?,0) for batch, course_id and strand_id so that 0 is inserted as NULL (SHS rows may have course_id = 0)
            $insertStmt = $conn->prepare("INSERT INTO archive_alumni (alumni_id, firstname, middlename, lastname, gender, contact_no, email, batch, course_id, strand_id, connected_to, company_address, address, status, archived_by) VALUES (?, ?, ?, ?, ?, ?, ?, NULLIF(?,0), NULLIF(?,0), NULLIF(?,0), ?, ?, ?, ?, ?)");
            if ($insertStmt === false) {
                $report['error'][$idInt] = 'Prepare failed: ' . $conn->error;
            } else {
                $alumni_id = $idInt;
                // bind as integers; use 0 to indicate NULL (NULLIF in SQL will convert 0 -> NULL)
                $batch_val = is_numeric($row['batch']) ? intval($row['batch']) : 0;
                $course_id_val = is_numeric($row['course_id']) ? intval($row['course_id']) : 0;
                $strand_id_val = is_numeric($row['strand_id']) ? intval($row['strand_id']) : 0;
                $status_val = is_numeric($row['status']) ? intval($row['status']) : 0;
                $archived_by = $user_id;

                // types: i (alumni_id) + 6s (strings) + i (batch) + i (course_id) + i (strand_id) + 3s (strings) + i (status) + i (archived_by)
                // order matches the SQL placeholders above
                $types = 'issssssiiisssii';

                if (!$insertStmt->bind_param($types, $alumni_id, $firstname, $middlename, $lastname, $gender, $contact_no, $email, $batch_val, $course_id_val, $strand_id_val, $connected_to, $company_address, $address, $status_val, $archived_by)) {
                    $report['error'][$idInt] = 'Bind failed: ' . $insertStmt->error;
                    $insertStmt->close();
                } else {
                    if ($insertStmt->execute()) {
                        // Before deleting the alumnus record, remove or null dependent rows that may not have ON DELETE CASCADE
                        // This avoids foreign key constraint failures when deleting.
                        $dependentErrors = [];
                        // Known tables referencing alumnus_bio: remember_tokens (has cascade), password_resets (cascade), privacy_agreements (cascade)
                        // Other tables that use alumnus_bio.id as user_id or user reference: event_comments (user_id), forums/comments, users.alumnus_id

                        // Event comments (user_id)
                        if ($conn->query("DELETE FROM event_comments WHERE user_id = '$idInt'") === false) {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'event_comments: ' . $conn->error;
                        }

                        // Forums or other comment tables (best-effort: if table exists)
                        if ($conn->query("DELETE FROM forum_comments WHERE user_id = '$idInt'") === false) {
                            // ignore if table doesn't exist; log only if real error
                            if ($conn->errno !== 1146) $dependentErrors[] = 'forum_comments: ' . $conn->error;
                        }

                        // Users table may contain an account linked to this alumnus row; do NOT delete users rows, only null the alumnus link
                        if ($conn->query("UPDATE users SET alumnus_id = NULL WHERE alumnus_id = '$idInt'") === false) {
                            $dependentErrors[] = 'users: ' . $conn->error;
                        }

                        // Remove remember tokens (should cascade, but safe to delete explicitly)
                        if ($conn->query("DELETE FROM remember_tokens WHERE alumni_id = '$idInt'") === false) {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'remember_tokens: ' . $conn->error;
                        }

                        // Remove password resets and privacy agreements (should cascade)
                        if ($conn->query("DELETE FROM password_resets WHERE user_id = '$idInt'") === false) {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'password_resets: ' . $conn->error;
                        }
                        if ($conn->query("DELETE FROM privacy_agreements WHERE user_id = '$idInt'") === false) {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'privacy_agreements: ' . $conn->error;
                        }

                        // Attempt delete from active table
                        if ($conn->query("DELETE FROM alumnus_bio WHERE id = '$idInt'")) {
                            // log
                            $conn->query("INSERT INTO activity_log (user_id, action, description, timestamp) VALUES ('$user_id', 'ARCHIVE', 'Archived alumni with ID: $idInt', NOW())");
                            $report['archived'][] = $idInt;
                        } else {
                            // capture DB error for debugging
                            $err = $conn->error;
                            dbg("DELETE_FAILED id=$idInt error={$err}");
                            $report['error'][$idInt] = 'Failed to delete active record: ' . $err;
                            if (!empty($dependentErrors)) $report['error'][$idInt] .= ' | dependent cleanup issues: ' . implode('; ', $dependentErrors);
                        }
                    } else {
                        $report['error'][$idInt] = 'Execute failed: ' . $insertStmt->error;
                        dbg("INSERT_ARCHIVE_FAILED id=$idInt error=" . $insertStmt->error);
                    }
                    $insertStmt->close();
                }
            }
    } else {
            // not in active; maybe already archived
            $check_arch = $conn->query("SELECT id FROM archive_alumni WHERE alumni_id = '$idInt' LIMIT 1");
            if ($check_arch && $check_arch->num_rows > 0) {
                $report['already'][] = $idInt;
            } else {
                $report['error'][$idInt] = 'Alumni not found';
        dbg("ALUMNI_NOT_FOUND id=$idInt");
            }
        }
    }

    // Prepare response summarizing results
    // Provide clearer top-level status
    // Log final report for debugging
    dbg('REPORT=' . substr(json_encode($report),0,2000));
    if (!empty($report['error']) && !empty($report['archived'])) {
        echo json_encode(['status' => 'partial', 'message' => 'Some items archived, some failed', 'report' => $report]);
    } elseif (!empty($report['error'])) {
        echo json_encode(['status' => 'error', 'message' => 'Some items failed', 'report' => $report]);
    } elseif (!empty($report['archived'])) {
        echo json_encode(['status' => 'success', 'message' => 'Archived processed', 'report' => $report]);
    } elseif (!empty($report['already'])) {
        echo json_encode(['status' => 'already', 'message' => 'Items already archived', 'report' => $report]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No valid alumni to archive', 'report' => $report]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
