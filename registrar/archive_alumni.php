<?php
session_start();
include '../admin/db_connect.php';
header('Content-Type: application/json');

// Accept JSON payloads (e.g., fetch/fetch API) in addition to form-encoded posts
$rawInput = file_get_contents('php://input');
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
if (!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 4) {
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
    $user_id = intval($_SESSION['login_id']);

    $report = [
        'archived' => [],
        'already' => [],
        'error' => []
    ];

    foreach ($ids as $id) {
        $idInt = intval($id);

        // Check active table (prepared statement)
        $stmt = $conn->prepare("SELECT * FROM alumnus_bio WHERE id = ?");
        $stmt->bind_param('i', $idInt);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            error_log("archive_alumni: SELECT_FAILED id=$idInt error=" . $conn->error);
        }

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();

            // Check if already archived (prepared statement)
            $checkStmt = $conn->prepare("SELECT id FROM archive_alumni WHERE alumni_id = ? LIMIT 1");
            $checkStmt->bind_param('i', $idInt);
            $checkStmt->execute();
            $check_arch = $checkStmt->get_result();
            if ($check_arch && $check_arch->num_rows > 0) {
                $report['already'][] = $idInt;
                $checkStmt->close();
                continue;
            }
            $checkStmt->close();

            // Prepare sanitized values, allow NULL where empty
            $firstname = $row['firstname'] ?? '';
            $middlename = $row['middlename'] ?? '';
            $lastname = $row['lastname'] ?? '';
            $gender = $row['gender'] ?? '';
            $contact_no = $row['contact_no'] ?? '';
            $email = $row['email'] ?? '';
            $connected_to = $row['connected_to'] ?? '';
            $company_address = $row['company_address'] ?? '';
            $address = $row['address'] ?? '';

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

                        // Event comments (user_id) - prepared statement
                        $delStmt = $conn->prepare("DELETE FROM event_comments WHERE user_id = ?");
                        $delStmt->bind_param('i', $idInt);
                        if ($delStmt->execute() === false) {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'event_comments: ' . $conn->error;
                        }
                        $delStmt->close();

                        // Forums or other comment tables (best-effort: if table exists) - prepared statement
                        $delStmt2 = $conn->prepare("DELETE FROM forum_comments WHERE user_id = ?");
                        if ($delStmt2 !== false) {
                            $delStmt2->bind_param('i', $idInt);
                            if ($delStmt2->execute() === false) {
                                // ignore if table doesn't exist; log only if real error
                                if ($conn->errno !== 1146) $dependentErrors[] = 'forum_comments: ' . $conn->error;
                            }
                            $delStmt2->close();
                        } else {
                            // prepare failed, likely table doesn't exist
                            if ($conn->errno !== 1146) $dependentErrors[] = 'forum_comments: ' . $conn->error;
                        }

                        // Users table may contain an account linked to this alumnus row; do NOT delete users rows, only null the alumnus link - prepared statement
                        $updStmt = $conn->prepare("UPDATE users SET alumnus_id = NULL WHERE alumnus_id = ?");
                        $updStmt->bind_param('i', $idInt);
                        if ($updStmt->execute() === false) {
                            $dependentErrors[] = 'users: ' . $conn->error;
                        }
                        $updStmt->close();

                        // Remove remember tokens (should cascade, but safe to delete explicitly) - prepared statement
                        $delStmt3 = $conn->prepare("DELETE FROM remember_tokens WHERE alumni_id = ?");
                        if ($delStmt3 !== false) {
                            $delStmt3->bind_param('i', $idInt);
                            if ($delStmt3->execute() === false) {
                                if ($conn->errno !== 1146) $dependentErrors[] = 'remember_tokens: ' . $conn->error;
                            }
                            $delStmt3->close();
                        } else {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'remember_tokens: ' . $conn->error;
                        }

                        // Remove password resets (should cascade) - prepared statement
                        $delStmt4 = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
                        if ($delStmt4 !== false) {
                            $delStmt4->bind_param('i', $idInt);
                            if ($delStmt4->execute() === false) {
                                if ($conn->errno !== 1146) $dependentErrors[] = 'password_resets: ' . $conn->error;
                            }
                            $delStmt4->close();
                        } else {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'password_resets: ' . $conn->error;
                        }

                        // Remove privacy agreements (should cascade) - prepared statement
                        $delStmt5 = $conn->prepare("DELETE FROM privacy_agreements WHERE user_id = ?");
                        if ($delStmt5 !== false) {
                            $delStmt5->bind_param('i', $idInt);
                            if ($delStmt5->execute() === false) {
                                if ($conn->errno !== 1146) $dependentErrors[] = 'privacy_agreements: ' . $conn->error;
                            }
                            $delStmt5->close();
                        } else {
                            if ($conn->errno !== 1146) $dependentErrors[] = 'privacy_agreements: ' . $conn->error;
                        }

                        // Attempt delete from active table - prepared statement
                        $delMainStmt = $conn->prepare("DELETE FROM alumnus_bio WHERE id = ?");
                        $delMainStmt->bind_param('i', $idInt);
                        if ($delMainStmt->execute()) {
                            // Log activity - prepared statement
                            $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, action, description, timestamp) VALUES (?, 'ARCHIVE', ?, NOW())");
                            $logDesc = 'Archived alumni with ID: ' . $idInt;
                            $logStmt->bind_param('is', $user_id, $logDesc);
                            $logStmt->execute();
                            $logStmt->close();
                            $report['archived'][] = $idInt;
                        } else {
                            // capture DB error for debugging
                            $err = $conn->error;
                            $report['error'][$idInt] = 'Failed to delete active record: ' . $err;
                            if (!empty($dependentErrors)) $report['error'][$idInt] .= ' | dependent cleanup issues: ' . implode('; ', $dependentErrors);
                        }
                        $delMainStmt->close();
                    } else {
                        $report['error'][$idInt] = 'Execute failed: ' . $insertStmt->error;
                    }
                    $insertStmt->close();
                }
            }
    } else {
            $stmt->close();
            // not in active; maybe already archived (prepared statement)
            $checkStmt2 = $conn->prepare("SELECT id FROM archive_alumni WHERE alumni_id = ? LIMIT 1");
            $checkStmt2->bind_param('i', $idInt);
            $checkStmt2->execute();
            $check_arch = $checkStmt2->get_result();
            if ($check_arch && $check_arch->num_rows > 0) {
                $report['already'][] = $idInt;
            } else {
                $report['error'][$idInt] = 'Alumni not found';
            }
            $checkStmt2->close();
        }
    }

    // Prepare response summarizing results
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
