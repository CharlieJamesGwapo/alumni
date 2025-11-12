<?php
session_start();
include '../admin/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];
    $success = true;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Get archived alumni data (prepared)
        $stmt = $conn->prepare("SELECT * FROM archive_alumni WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Original alumni id (the primary key in alumnus_bio before archive)
            $original_id = is_numeric($row['alumni_id']) ? intval($row['alumni_id']) : 0;

            // Map archived fields to alumnus_bio columns (match actual schema)
            $firstname = $row['firstname'] ?? '';
            $middlename = $row['middlename'] ?? '';
            $lastname = $row['lastname'] ?? '';
            $suffixname = $row['suffixname'] ?? '';
            $gender = !empty($row['gender']) ? $row['gender'] : 'Male';
            $birthdate = (!empty($row['birthdate']) && $row['birthdate'] !== 'NULL') ? $row['birthdate'] : null;
            $address = $row['address'] ?? '';
            $batch = (!empty($row['batch']) && $row['batch'] !== 'NULL') ? $row['batch'] : date('Y');
            $course_id = is_numeric($row['course_id']) ? intval($row['course_id']) : 0;
            $email = $row['email'] ?? '';
            $connected_to = $row['connected_to'] ?? '';
            $company_name = $row['company_name'] ?? '';
            $company_address = $row['company_address'] ?? '';
            // Preserve strand for SHS records when restoring
            $strand_id = isset($row['strand_id']) && is_numeric($row['strand_id']) ? intval($row['strand_id']) : null;
            $contact_no = $row['contact_no'] ?? '';
            $company_email = $row['company_email'] ?? '';
            $avatar = $row['avatar'] ?? 'default_avatar.jpg';
            $status_val = is_numeric($row['status']) ? intval($row['status']) : 1;

            // Determine which columns actually exist in alumnus_bio to avoid unknown-column errors
            $existingCols = [];
            $colDetails = [];
            $colRes = $conn->query("SHOW COLUMNS FROM alumnus_bio");
            if ($colRes) {
                while ($c = $colRes->fetch_assoc()) {
                    $existingCols[] = $c['Field'];
                    $colDetails[$c['Field']] = $c; // keep Type, Null, Default, Extra
                }
            }

            // Prepare mapping of candidate columns => values
            $candidate = [
                'id' => $original_id,
                'firstname' => $firstname,
                'middlename' => $middlename,
                'lastname' => $lastname,
                'suffixname' => $suffixname,
                'gender' => $gender,
                'birthdate' => $birthdate,
                'address' => $address,
                'batch' => $batch,
                'course_id' => $course_id,
                'strand_id' => $strand_id,
                'email' => $email,
                'connected_to' => $connected_to,
                'company_name' => $company_name,
                'company_address' => $company_address,
                'contact_no' => $contact_no,
                'company_email' => $company_email,
                'avatar' => $avatar,
                'status' => $status_val
            ];

            // Build lists for existing fields only
            $fields = [];
            foreach ($candidate as $col => $val) {
                if (in_array($col, $existingCols)) {
                    $fields[$col] = $val;
                }
            }

            // Ensure required NOT NULL columns have safe defaults to avoid DB errors
            foreach ($colDetails as $col => $info) {
                if ($col === 'id') continue; // id handled separately
                $isNotNullNoDefault = (isset($info['Null']) && strtoupper($info['Null']) === 'NO') && (!isset($info['Default']) || $info['Default'] === NULL);
                if ($isNotNullNoDefault) {
                    if (!array_key_exists($col, $fields) || $fields[$col] === null || $fields[$col] === '') {
                        // provide column-specific sensible defaults
                        if ($col === 'batch') {
                            $fields[$col] = date('Y');
                        } elseif (stripos($info['Type'], 'int') !== false || preg_match('/(_id$|^id$|^status$|^course_id$|batch)/i', $col)) {
                            $fields[$col] = 0;
                        } elseif (stripos($info['Type'], 'date') !== false) {
                            $fields[$col] = date('Y-m-d');
                        } else {
                            $fields[$col] = '';
                        }
                    }
                }
            }

            // Cast integer-like fields to int for proper binding
            foreach ($fields as $col => $val) {
                if (preg_match('/(^id$|_id$|^status$|^course_id$|batch)/i', $col)) {
                    $fields[$col] = intval($val);
                }
            }

            // Helper to bind params dynamically
            $bindDynamic = function($stmt, $params) {
                if (empty($params)) return true;
                $types = '';
                $refs = [];
                foreach ($params as $p) {
                    // choose type: integer for ints, string otherwise
                    if (is_int($p)) $types .= 'i'; else $types .= 's';
                    $refs[] = $p;
                }
                // bind_param requires references
                $bind_names[] = $types;
                foreach ($refs as $key => $value) $bind_names[] = & $refs[$key];
                return call_user_func_array([$stmt, 'bind_param'], $bind_names);
            };

            // Check if original id exists
            $exists = $conn->prepare("SELECT id FROM alumnus_bio WHERE id = ? LIMIT 1");
            if ($exists) {
                $exists->bind_param('i', $original_id);
                $exists->execute();
                $exRes = $exists->get_result();
                $exists->close();
            } else {
                $exRes = null;
            }

            if ($exRes && $exRes->num_rows > 0) {
                // build UPDATE dynamically (exclude id)
                $updateCols = [];
                $updateParams = [];
                foreach ($fields as $col => $val) {
                    if ($col === 'id') continue;
                    $updateCols[] = "$col = ?";
                    $updateParams[] = $val;
                }
                $updateParams[] = $original_id; // WHERE id = ?
                if (!empty($updateCols)) {
                    $sql = "UPDATE alumnus_bio SET " . implode(', ', $updateCols) . " WHERE id = ?";
                    $up = $conn->prepare($sql);
                    if ($up === false) throw new Exception('Prepare failed (dynamic update): ' . $conn->error);
                    if (!$bindDynamic($up, $updateParams)) throw new Exception('Bind failed (dynamic update)');
                    if (!$up->execute()) throw new Exception('Execute failed (dynamic update): ' . $up->error);
                    $up->close();
                }
            } else {
                // build INSERT dynamically
                $insertCols = array_keys($fields);
                $placeholders = implode(', ', array_fill(0, count($insertCols), '?'));
                $sql = "INSERT INTO alumnus_bio (" . implode(', ', $insertCols) . ") VALUES (" . $placeholders . ")";
                $ins = $conn->prepare($sql);
                if ($ins === false) throw new Exception('Prepare failed (dynamic insert): ' . $conn->error);
                $insertParams = array_values($fields);
                if (!$bindDynamic($ins, $insertParams)) throw new Exception('Bind failed (dynamic insert)');
                if (!$ins->execute()) throw new Exception('Execute failed (dynamic insert): ' . $ins->error);
                $ins->close();
            }

            // Re-link users record if there is a user account that matches this email/username
            if (!empty($email)) {
                $u = $conn->prepare("UPDATE users SET alumnus_id = ? WHERE username = ? LIMIT 1");
                if ($u) {
                    $u->bind_param('is', $original_id, $email);
                    $u->execute();
                    $u->close();
                }
            }

            // Delete the archive entry
            $delSt = $conn->prepare("DELETE FROM archive_alumni WHERE id = ?");
            $delSt->bind_param('i', $id);
            if (!$delSt->execute()) {
                throw new Exception("Error removing alumni from archive: " . $delSt->error);
            }
        } else {
            throw new Exception('Archived record not found');
        }
        
        // Ensure activity_log exists
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

        // Log the restore action (do not fail restore if logging fails)
        $user_id = $_SESSION['user_id'];
        $log_query = "INSERT INTO activity_log (user_id, action, description, timestamp) 
                     VALUES ('$user_id', 'RESTORE', 'Restored alumni with ID: $id from archives', NOW())";
        @$conn->query($log_query);
        
        // Commit transaction
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Alumni restored successfully']);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
