<?php
session_start();
include 'db_connect.php';

$action = $_GET['action'] ?? '';

if($action == 'login'){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if(empty($username) || empty($password)) {
        echo "2";
        exit;
    }
    
    // Sanitize input
    $username = $conn->real_escape_string($username);
    
    // Check user credentials
    $qry = $conn->query("SELECT * FROM users WHERE username = '$username' AND type IN (1, 4)");
    if($qry->num_rows > 0){
        $user = $qry->fetch_array();
        
        // Verify password
        if(md5($password) === $user['password']){
            // Set session variables
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['login_name'] = $user['name'];
            $_SESSION['login_username'] = $user['username'];
            $_SESSION['login_type'] = $user['type'];
            $_SESSION['login_establishment_id'] = $user['establishment_id'];
            
            echo "1";
            exit;
        }
    }
    
    echo "2";
    exit;
}
if($action == 'save_user'){
	extract($_POST);
	
	// Sanitize inputs
	$name = $conn->real_escape_string(trim($name));
	$username = $conn->real_escape_string(trim($username));
	$type = (int)$type;
	$id = isset($id) ? (int)$id : 0;
	
	// Validate required fields
	if(empty($name) || empty($username) || !in_array($type, [1, 4])){
		echo 0; // Invalid input
		exit;
	}
	
	// Build data string
	$data = " name = '$name' ";
	$data .= ", username = '$username' ";
	
	// Handle password
	if(!empty($password)){
		$password_hash = md5($password);
		$data .= ", password = '$password_hash' ";
	}
	
	$data .= ", type = '$type' ";
	$data .= ", establishment_id = 0 ";
	
	// Check for duplicate username
	$chk = $conn->query("SELECT * FROM users WHERE username = '$username' AND id != '$id'")->num_rows;
	if($chk > 0){
		echo 2; // Username already exists
		exit;
	}
	
	// Save or update user
	if(empty($id)){
		// Create new user
		$save = $conn->query("INSERT INTO users SET ".$data);
	} else {
		// Update existing user
		$save = $conn->query("UPDATE users SET ".$data." WHERE id = ".$id);
	}
	
	if($save){
		echo 1; // Success
	} else {
		echo 0; // Database error
	}
	exit;
}

if($action == 'delete_user'){
	$id = (int)($_POST['id'] ?? 0);
	
	// Validate input
	if($id <= 0) {
		echo 0;
		exit;
	}
	
	// Check if user exists and is admin/registrar type
	$check = $conn->query("SELECT type FROM users WHERE id = $id");
	if($check->num_rows == 0) {
		echo 0; // User not found
		exit;
	}
	
	$user_data = $check->fetch_assoc();
	if(!in_array($user_data['type'], [1, 4])) {
		echo 0; // Not admin or registrar user
		exit;
	}
	
	// Delete the user
	$delete = $conn->query("DELETE FROM users WHERE id = $id AND type IN (1, 4)");
	
	if($delete && $conn->affected_rows > 0) {
		echo 1; // Success
	} else {
		echo 0; // Failed to delete
	}
	exit;
}
if($action == 'signup'){
    $data = $_POST;
    
    // Check if alumni ID exists and not already registered
    $check = $conn->query("SELECT * FROM alumnus_bio WHERE alumni_id = '{$data['alumni_id']}'")->num_rows;
    if($check > 0){
        echo "Alumni ID already registered";
        exit;
    }
    
    // Get verified data from alumni_ids table
    $verify = $conn->query("SELECT * FROM alumni_ids WHERE alumni_id = '{$data['alumni_id']}'");
    if($verify->num_rows == 0){
        echo "Invalid Alumni ID";
        exit;
    }
    
    // Get the verified data
    $verified_data = $verify->fetch_assoc();
    
    // Create clean data array with only valid columns
    $insert_data = [
        'alumni_id' => $data['alumni_id'],
        'lastname' => $verified_data['lastname'],
        'firstname' => $verified_data['firstname'],
        'middlename' => $verified_data['middlename'],
        'suffixname' => $verified_data['suffixname'],
        'birthdate' => $verified_data['birthdate'],
        'gender' => $verified_data['gender'],
        'batch' => $verified_data['batch'],
    // Ensure course_id is not null (use 0 for SHS or missing course)
    'course_id' => isset($verified_data['course_id']) && $verified_data['course_id'] !== '' ? $verified_data['course_id'] : 0,
    // Include strand_id from the verified alumni_ids record (for SHS students)
    'strand_id' => isset($verified_data['strand_id']) ? $verified_data['strand_id'] : null,
    // Persist major if provided by the signup form or present in verified record
    'major_id' => isset($data['major_id']) && $data['major_id'] !== '' ? $data['major_id'] : (isset($verified_data['major_id']) ? $verified_data['major_id'] : null),
        'email' => $data['email'],
        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        'address' => $data['address'],
    // Employment status dropdown (employed, not employed, student, self-employed)
    'employment_status' => isset($data['employment_status']) ? $data['employment_status'] : '',
    // Full employment history (JSON encoded) from signup form
    'employment_history' => isset($data['employment_history']) ? $data['employment_history'] : '',
        'contact_no' => $data['contact_no'] ?? '',
        'company_address' => $data['company_address'] ?? '',
        'company_email' => $data['company_email'] ?? '',
    // Type of industry / employer
    'connected_to' => $data['connected_to'] ?? '',
        'status' => 0, // Set initial status as unvalidated
        'date_created' => date('Y-m-d H:i:s')
    ];
    
    // Handle image upload
    if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
        $fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
        if(move_uploaded_file($_FILES['img']['tmp_name'], '../assets/uploads/'.$fname)){
            $insert_data['img'] = $fname;
        }
    }

    // Removed camera/captured image (profileCapture) handling per request.
    // Retain regular file upload handling above (if user uploaded an image via input[name="img"]).
    
    // Build query using only valid columns
    $fields = array_keys($insert_data);
    // First check if email already exists
    $email = $conn->real_escape_string($insert_data['email']);
    $check_email = $conn->query("SELECT id FROM alumnus_bio WHERE email = '$email'");
    
    if($check_email->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'This email address is already registered. Please use a different email or contact support if you need assistance.',
            'type' => 'email_exists'
        ]);
        exit;
    }

    $values = array_map(function($v) use ($conn) {
        if (is_null($v)) {
            return "NULL"; // keep NULL unquoted for SQL
        }
        return "'" . $conn->real_escape_string($v) . "'";
    }, array_values($insert_data));
    
    $query = "INSERT INTO alumnus_bio (`" . implode('`, `', $fields) . "`) 
              VALUES (" . implode(', ', $values) . ")";
    
    if($conn->query($query)){
        // After successful insert, send confirmation email to the registered address (non-blocking)
        $email_to = $insert_data['email'] ?? '';
        $email_sent = false;
        if(!empty($email_to) && filter_var($email_to, FILTER_VALIDATE_EMAIL)){
            try{
                require_once __DIR__ . '/../PHPMailer/src/Exception.php';
                require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
                require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'capstonee2@gmail.com';
                $mail->Password = 'ewus hfar ghit fdkt';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Portal');
                $mail->addAddress($email_to);
                $mail->isHTML(true);
                $mail->Subject = 'MOIST Alumni Portal - Registration received';
                $fname = $insert_data['firstname'] ?? '';
                $alumni_id_sent = $insert_data['alumni_id'] ?? '';
                $mail->Body = "<p>Hi " . htmlspecialchars($fname) . ",</p>
                    <p>Your account has been created successfully on the MOIST Alumni Portal.</p>
                    <p><strong>Alumni ID:</strong> " . htmlspecialchars($alumni_id_sent) . "</p>
                    <p>Please wait for validation from the MOIST Registrar. We will notify you when your account is approved.</p>
                    <p>If you did not register, please contact support.</p>
                    <p>Thank you,<br/>MOIST Alumni Portal</p>";

                $mail->send();
                $email_sent = true;
            } catch (Exception $e) {
                // Log but do not fail the registration
                error_log('Signup confirmation email failed: ' . $e->getMessage());
                $email_sent = false;
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Account created successfully! Please wait for account verification.',
            'type' => 'success',
            'email_sent' => $email_sent
        ]);
    } else {
        if($conn->errno == 1062) { // Duplicate entry error
            echo json_encode([
                'status' => 'error',
                'message' => 'This email address is already registered. Please use a different email or contact support if you need assistance.',
                'type' => 'email_exists'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred while creating your account. Please try again later.',
                'type' => 'system_error'
            ]);
        }
    }
}
if($action == 'update_account'){
	$save = $crud->update_account();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if(isset($action) && $action == "save_course"){
    // Save or update course with 'about' and duplicate check
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $course = isset($_POST['course']) ? trim($_POST['course']) : '';
    $about = isset($_POST['about']) ? trim($_POST['about']) : '';

    if($course == ''){
        echo 0; exit;
    }

    // Duplicate check (case-insensitive); exclude current id when updating
    if($id > 0){
        $chk = $conn->prepare("SELECT id FROM courses WHERE LOWER(course)=LOWER(?) AND id<>?");
        $chk->bind_param("si", $course, $id);
    } else {
        $chk = $conn->prepare("SELECT id FROM courses WHERE LOWER(course)=LOWER(?)");
        $chk->bind_param("s", $course);
    }
    $chk->execute();
    $res = $chk->get_result();
    if($res && $res->num_rows > 0){
        echo 3; // duplicate
        exit;
    }

    if($id == 0){
        $stmt = $conn->prepare("INSERT INTO courses (course, about) VALUES (?, ?)");
        $stmt->bind_param("ss", $course, $about);
        if($stmt->execute()){
            // Log course creation
            if(isset($_SESSION['login_id'])) {
                $new_id = $conn->insert_id;
                log_activity($_SESSION['login_id'], 'CREATE_COURSE', "Created course: $course", $new_id, 'course');
            }
            echo 1; // inserted
        } else {
            echo 0; // error
        }
        exit;
    } else {
        $stmt = $conn->prepare("UPDATE courses SET course = ?, about = ? WHERE id = ?");
        $stmt->bind_param("ssi", $course, $about, $id);
        if($stmt->execute()){
            // Log course update
            if(isset($_SESSION['login_id'])) {
                log_activity($_SESSION['login_id'], 'UPDATE_COURSE', "Updated course: $course", $id, 'course');
            }
            echo 2; // updated
        } else {
            echo 0; // error
        }
        exit;
    }
}

if(isset($action) && $action == "delete_course"){
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if($id <= 0){
        echo 0; exit;
    }
    
    // Get course name before deletion for logging
    $course_query = $conn->prepare("SELECT course FROM courses WHERE id = ?");
    $course_query->bind_param("i", $id);
    $course_query->execute();
    $course_result = $course_query->get_result();
    $course_name = '';
    if($course_result->num_rows > 0) {
        $course_data = $course_result->fetch_assoc();
        $course_name = $course_data['course'];
    }
    $course_query->close();
    
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        // Log the course deletion
        if(isset($_SESSION['login_id'])) {
            log_activity($_SESSION['login_id'], 'DELETE_COURSE', "Deleted course: $course_name", $id, 'course');
        }
        echo 1; // deleted
    } else {
        echo 0; // error
    }
    exit;
}
if($action == "update_alumni_acc"){
	$save = $crud->update_alumni_acc();
	if($save)
		echo $save;
}
if($action == "save_gallery"){
	$save = $crud->save_gallery();
	if($save)
		echo $save;
}
if($action == "delete_gallery"){
	$save = $crud->delete_gallery();
	if($save)
		echo $save;
}

if($action == "save_career"){
	extract($_POST);
	
	// Sanitize inputs
	$company = $conn->real_escape_string(trim($company ?? ''));
	$title = $conn->real_escape_string(trim($title ?? ''));
	$location = $conn->real_escape_string(trim($location ?? ''));
	$description = $conn->real_escape_string(trim($description ?? ''));
	$id = isset($id) ? (int)$id : 0;
	$user_id = $_SESSION['login_id'] ?? 1; // Default to admin user
	
	// Validate required fields
	if(empty($company) || empty($title)){
		echo 0; // Invalid input
		exit;
	}
	
	// Create careers table if it doesn't exist
	$conn->query("CREATE TABLE IF NOT EXISTS careers (
		id INT(30) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		company VARCHAR(250) NOT NULL,
		location TEXT NOT NULL,
		job_title VARCHAR(250) NOT NULL,
		description TEXT NOT NULL,
		user_id INT(30) NOT NULL,
		date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
	)");
	
	if(empty($id)){
		// Create new job
		$save = $conn->query("INSERT INTO careers SET 
			company = '$company',
			job_title = '$title',
			location = '$location',
			description = '$description',
			user_id = '$user_id',
			date_created = NOW()
		");
	} else {
		// Update existing job
		$save = $conn->query("UPDATE careers SET 
			company = '$company',
			job_title = '$title',
			location = '$location',
			description = '$description'
			WHERE id = '$id'
		");
	}
	
	if($save) {
		// Log career action
		if(isset($_SESSION['login_id'])) {
			$action_type = empty($id) ? 'CREATE_JOB' : 'UPDATE_JOB';
			log_activity($_SESSION['login_id'], $action_type, "Job: $title at $company", null, 'job');
		}
		echo 1;
	} else {
		echo 0;
	}
}
if($action == "delete_career"){
	$job_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	
	if($job_id <= 0) {
		echo 0;
		exit;
	}
	
	// Get job details before deletion for logging
	$job_details = 'Unknown Job';
	$job_query = $conn->prepare("SELECT job_title, company FROM careers WHERE id = ?");
	$job_query->bind_param("i", $job_id);
	$job_query->execute();
	$job_result = $job_query->get_result();
	if($job_result->num_rows > 0) {
		$job_data = $job_result->fetch_assoc();
		$job_details = $job_data['job_title'] . ' at ' . $job_data['company'];
	}
	$job_query->close();
	
	// Delete the job
	$delete_query = $conn->prepare("DELETE FROM careers WHERE id = ?");
	$delete_query->bind_param("i", $job_id);
	$save = $delete_query->execute();
	$delete_query->close();
	
	if($save) {
		// Log job deletion
		if(isset($_SESSION['login_id'])) {
			log_activity($_SESSION['login_id'], 'DELETE_JOB', "Deleted job: $job_details", $job_id, 'job');
		}
		echo 1;
	} else {
		echo 0;
	}
}
if($action == "save_forum"){
	$save = $crud->save_forum();
	if($save)
		echo $save;
}
if($action == "delete_forum"){
	$save = $crud->delete_forum();
	if($save)
		echo $save;
}

if($action == "save_comment"){
	$save = $crud->save_comment();
	if($save)
		echo $save;
}
if($action == "delete_comment"){
	$save = $crud->delete_comment();
	if($save)
		echo $save;

}

if($action == "save_event"){
	$save = $crud->save_event();
	if($save) {
		// Log event action
		if(isset($_SESSION['login_id'])) {
			$action_type = ($save == 1) ? 'CREATE_EVENT' : 'UPDATE_EVENT';
			$event_title = $_POST['title'] ?? 'Unknown Event';
			log_activity($_SESSION['login_id'], $action_type, "Event: $event_title", null, 'event');
		}
		echo $save;
	}
}
if($action == "delete_event"){
	// Get event title before deletion for logging
	$event_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$event_title = 'Unknown Event';
	if($event_id > 0) {
		$event_query = $conn->prepare("SELECT title FROM events WHERE id = ?");
		$event_query->bind_param("i", $event_id);
		$event_query->execute();
		$event_result = $event_query->get_result();
		if($event_result->num_rows > 0) {
			$event_data = $event_result->fetch_assoc();
			$event_title = $event_data['title'];
		}
		$event_query->close();
	}
	
	$save = $crud->delete_event();
	if($save) {
		// Log event deletion
		if(isset($_SESSION['login_id'])) {
			log_activity($_SESSION['login_id'], 'DELETE_EVENT', "Deleted event: $event_title", $event_id, 'event');
		}
		echo $save;
	}
}	
if($action == "approve_event"){
    $save = $crud->approve_event();
    if($save) echo $save;
}
if($action == "participate"){
	$save = $crud->participate();
	if($save)
		echo $save;
}
if($action == "get_venue_report"){
	$get = $crud->get_venue_report();
	if($get)
		echo $get;
}
if($action == "save_art_fs"){
	$save = $crud->save_art_fs();
	if($save)
		echo $save;
}
if($action == "delete_art_fs"){
	$save = $crud->delete_art_fs();
	if($save)
		echo $save;
}
if($action == "get_pdetails"){
	$get = $crud->get_pdetails();
	if($get)
		echo $get;
}

// Database Backup Functions
if($action == "create_backup"){
	header('Content-Type: application/json');
	
	// Check if database exists
	if(isset($GLOBALS['db_missing']) && $GLOBALS['db_missing']) {
		echo json_encode([
			'success' => false,
			'message' => 'Cannot create backup: Database does not exist. Please restore from a backup file first.'
		]);
		exit;
	}
	
	$database = 'alumni_db';
	$filename = 'alumni_backup_' . date('Y-m-d_H-i-s') . '.sql';
	$filepath = __DIR__ . '/backups/' . $filename;
	
	// Create backups directory if it doesn't exist
	if (!is_dir(__DIR__ . '/backups/')) {
		mkdir(__DIR__ . '/backups/', 0755, true);
	}
	
	try {
		// Get all tables
		$tables = [];
		$result = $conn->query("SHOW TABLES");
		while($row = $result->fetch_row()) {
			$tables[] = $row[0];
		}
		
		$sql_dump = "-- Alumni Database Backup\n";
		$sql_dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
		$sql_dump .= "-- Database: $database\n\n";
		$sql_dump .= "-- Create database if not exists\n";
		$sql_dump .= "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
		$sql_dump .= "USE `$database`;\n\n";
		$sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n";
		$sql_dump .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";
		
		foreach($tables as $table) {
			// Get table structure
			$result = $conn->query("SHOW CREATE TABLE `$table`");
			$row = $result->fetch_row();
			
			$sql_dump .= "-- Table structure for `$table`\n";
			$sql_dump .= "DROP TABLE IF EXISTS `$table`;\n";
			$sql_dump .= $row[1] . ";\n\n";
			
			// Get table data
			$result = $conn->query("SELECT * FROM `$table`");
			if($result->num_rows > 0) {
				$sql_dump .= "-- Data for table `$table`\n";
				while($row = $result->fetch_assoc()) {
					$sql_dump .= "INSERT INTO `$table` VALUES (";
					$values = [];
					foreach($row as $value) {
						if($value === null) {
							$values[] = 'NULL';
						} else {
							$values[] = "'" . $conn->real_escape_string($value) . "'";
						}
					}
					$sql_dump .= implode(', ', $values) . ");\n";
				}
				$sql_dump .= "\n";
			}
		}
		
		$sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
		
		// Write to file
		if(file_put_contents($filepath, $sql_dump)) {
			header('Content-Type: application/json');
			echo json_encode([
				'success' => true,
				'filename' => $filename,
				'message' => 'Backup created successfully'
			]);
		} else {
			throw new Exception('Failed to write backup file');
		}
		
	} catch(Exception $e) {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => false,
			'message' => $e->getMessage()
		]);
	}
	exit;
}

if($action == "download_backup"){
	$filename = $_GET['file'] ?? '';
	$filepath = __DIR__ . '/backups/' . basename($filename);
	
	if(file_exists($filepath)) {
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Length: ' . filesize($filepath));
		readfile($filepath);
	} else {
		echo "File not found";
	}
	exit;
}

if($action == "get_recent_backups"){
	$backups = [];
	$backup_dir = __DIR__ . '/backups/';
	
	if(is_dir($backup_dir)) {
		$files = glob($backup_dir . '*.sql');
		rsort($files); // Sort by newest first
		
		foreach(array_slice($files, 0, 5) as $file) { // Get last 5 backups
			$backups[] = [
				'name' => basename($file),
				'date' => date('M j, Y H:i', filemtime($file)),
				'size' => formatBytes(filesize($file))
			];
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode([
		'success' => true,
		'backups' => $backups
	]);
	exit;
}

function formatBytes($size, $precision = 2) {
	$units = ['B', 'KB', 'MB', 'GB'];
	for($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
		$size /= 1024;
	}
	return round($size, $precision) . ' ' . $units[$i];
}

// Database Import Function
if($action == "import_database"){
	// Register shutdown function to catch fatal errors
	register_shutdown_function(function() {
		$error = error_get_last();
		if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
			while(ob_get_level()) {
				ob_end_clean();
			}
			header('Content-Type: application/json');
			echo json_encode([
				'success' => false,
				'message' => 'Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']
			]);
			exit;
		}
	});
	
	// Clear any output buffers and start fresh
	while(ob_get_level()) {
		ob_end_clean();
	}
	ob_start();
	
	// Suppress error output to prevent JSON corruption
	error_reporting(0);
	ini_set('display_errors', 0);
	
	// Set headers
	header('Content-Type: application/json');
	header('Cache-Control: no-cache, must-revalidate');
	
	// Wrap everything in try-catch to ensure we always return JSON
	try {
		// Check if file was uploaded
		if(!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
			throw new Exception('No file uploaded or upload error occurred');
		}
		
		$file = $_FILES['sql_file'];
		
		// Validate file type
		$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if($file_extension !== 'sql') {
			throw new Exception('Invalid file type. Only SQL files are allowed.');
		}
		
		// Validate file size (50MB limit)
		$max_size = 50 * 1024 * 1024; // 50MB
		if($file['size'] > $max_size) {
			throw new Exception('File size exceeds 50MB limit.');
		}
		
		// Read the SQL file content
		$sql_content = file_get_contents($file['tmp_name']);
		if($sql_content === false) {
			throw new Exception('Failed to read the uploaded file.');
		}
		
		// Validate SQL content
		if(empty(trim($sql_content))) {
			throw new Exception('The uploaded file is empty.');
		}
		
		$database = 'alumni_db';
		$backup_created = false;
		$backup_filename = '';
		
		// Try to create a backup before importing (only if database exists)
		try {
			// Create backups directory if it doesn't exist
			if (!is_dir(__DIR__ . '/backups/')) {
				mkdir(__DIR__ . '/backups/', 0755, true);
			}
			
			// Check if connection exists and database has tables
			if(isset($conn) && $conn && !$conn->connect_error) {
				$result = @$conn->query("SHOW TABLES");
				if($result && $result->num_rows > 0) {
					$backup_filename = 'pre_import_backup_' . date('Y-m-d_H-i-s') . '.sql';
					$backup_filepath = __DIR__ . '/backups/' . $backup_filename;
					
					$tables = [];
					while($row = $result->fetch_row()) {
						$tables[] = $row[0];
					}
					
					$backup_sql = "-- Pre-Import Backup\n";
					$backup_sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
					$backup_sql .= "-- Database: $database\n\n";
					$backup_sql .= "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
					$backup_sql .= "USE `$database`;\n\n";
					$backup_sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
					$backup_sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";
					
					foreach($tables as $table) {
						// Get table structure
						$result = $conn->query("SHOW CREATE TABLE `$table`");
						$row = $result->fetch_row();
						
						$backup_sql .= "-- Table structure for `$table`\n";
						$backup_sql .= "DROP TABLE IF EXISTS `$table`;\n";
						$backup_sql .= $row[1] . ";\n\n";
						
						// Get table data
						$result = $conn->query("SELECT * FROM `$table`");
						if($result->num_rows > 0) {
							$backup_sql .= "-- Data for table `$table`\n";
							while($row = $result->fetch_assoc()) {
								$backup_sql .= "INSERT INTO `$table` VALUES (";
								$values = [];
								foreach($row as $value) {
									if($value === null) {
										$values[] = 'NULL';
									} else {
										$values[] = "'" . $conn->real_escape_string($value) . "'";
									}
								}
								$backup_sql .= implode(', ', $values) . ");\n";
							}
							$backup_sql .= "\n";
						}
					}
					
					$backup_sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
					
					// Save backup
					if(file_put_contents($backup_filepath, $backup_sql)) {
						$backup_created = true;
					}
				}
			}
		} catch(Exception $backup_error) {
			// Continue with import even if backup fails (database might not exist)
		}
		
		// Close existing connection if it exists
		if(isset($conn) && $conn) {
			@$conn->close();
		}
		
		// Create new connection without selecting a database
		$import_conn = new mysqli('localhost', 'root', '');
		
		if ($import_conn->connect_error) {
			throw new Exception("Connection failed: " . $import_conn->connect_error);
		}
		
		// Set connection options
		$import_conn->set_charset("utf8mb4");
		$import_conn->query("SET FOREIGN_KEY_CHECKS=0");
		$import_conn->query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
		
		// Split SQL content into individual statements
		$statements = [];
		$current_statement = '';
		$lines = explode("\n", $sql_content);
		
		foreach($lines as $line) {
			$line = trim($line);
			
			// Skip comments and empty lines
			if(empty($line) || substr($line, 0, 2) === '--' || substr($line, 0, 1) === '#') {
				continue;
			}
			
			$current_statement .= $line . " ";
			
			// Check if statement ends with semicolon
			if(substr(rtrim($line), -1) === ';') {
				$statements[] = trim($current_statement);
				$current_statement = '';
			}
		}
		
		// Add any remaining statement
		if(!empty(trim($current_statement))) {
			$statements[] = trim($current_statement);
		}
		
		// Execute statements
		$executed_count = 0;
		$error_count = 0;
		$errors = [];
		$database_created = false;
		
		foreach($statements as $statement) {
			if(empty(trim($statement))) continue;
			
			// Check if this is a CREATE DATABASE statement
			if(stripos($statement, 'CREATE DATABASE') !== false) {
				$database_created = true;
			}
			
			$result = $import_conn->query($statement);
			if($result === false) {
				$error_count++;
				$error_msg = $import_conn->error;
				$errors[] = "Error: " . $error_msg . " | Statement: " . substr($statement, 0, 100) . "...";
				
				// Stop on critical errors for CREATE/DROP statements
				if(stripos($statement, 'CREATE TABLE') !== false || stripos($statement, 'DROP TABLE') !== false) {
					// If it's a "database doesn't exist" error, try to create it
					if(stripos($error_msg, "doesn't exist") !== false || stripos($error_msg, "Unknown database") !== false) {
						$import_conn->query("CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
						$import_conn->select_db($database);
						// Retry the statement
						$result = $import_conn->query($statement);
						if($result !== false) {
							$executed_count++;
							$error_count--;
							array_pop($errors);
						}
					} else {
						throw new Exception('Critical error during import: ' . $error_msg);
					}
				}
			} else {
				$executed_count++;
			}
		}
		
		// Re-enable foreign key checks
		$import_conn->query("SET FOREIGN_KEY_CHECKS=1");
		
		// Close import connection
		$import_conn->close();
		
		// Reconnect to the database for normal operations
		$conn = new mysqli('localhost', 'root', '', 'alumni_db');
		
		// Log the import action
		if(isset($_SESSION['login_id'])) {
			try {
				log_activity($_SESSION['login_id'], 'IMPORT_DATABASE', "Imported database from file: " . $file['name'], null, 'database');
			} catch(Exception $log_error) {
				// Ignore logging errors
			}
		}
		
		$message = "Database imported successfully! ";
		if($database_created) {
			$message .= "Database '$database' was created. ";
		}
		$message .= "Executed $executed_count statements.";
		if($error_count > 0) {
			$message .= " $error_count non-critical errors occurred.";
		}
		if($backup_created) {
			$message .= " Previous data backed up as: $backup_filename";
		}
		
		// Clean output buffer and send JSON
		if(ob_get_level()) {
			ob_end_clean();
		}
		echo json_encode([
			'success' => true,
			'message' => $message,
			'executed_statements' => $executed_count,
			'errors' => $error_count,
			'database_created' => $database_created,
			'backup_created' => $backup_created
		]);
		flush();
		
	} catch(Exception $e) {
		// Try to re-enable foreign key checks
		try {
			if(isset($import_conn) && $import_conn) {
				$import_conn->query("SET FOREIGN_KEY_CHECKS=1");
				$import_conn->close();
			}
		} catch(Exception $cleanup_error) {
			// Ignore cleanup errors
		}
		
		// Reconnect for normal operations
		try {
			$conn = new mysqli('localhost', 'root', '', 'alumni_db');
		} catch(Exception $reconnect_error) {
			// Database might not exist
		}
		
		// Clean output buffer and send JSON
		if(ob_get_level()) {
			ob_end_clean();
		}
		echo json_encode([
			'success' => false,
			'message' => $e->getMessage()
		]);
		flush();
	} catch(Throwable $fatal_error) {
		// Catch any fatal errors (PHP 7+)
		if(ob_get_level()) {
			ob_end_clean();
		}
		echo json_encode([
			'success' => false,
			'message' => 'Fatal error: ' . $fatal_error->getMessage()
		]);
		flush();
	}
	exit;
}
if($action == "validate_alumni"){
    $id = $_POST['id'];
    $update = $conn->query("UPDATE alumnus_bio SET status = 1 WHERE id = ".$id);
    if($update){
        // Log alumni validation
        if(isset($_SESSION['login_id'])) {
            log_alumni_action($_SESSION['login_id'], 'validate', $id, "Validated alumni account ID: $id");
        }
        echo 1;
    } else {
        echo 0;
    }
}
if(isset($action) && $action == "delete_alumni"){
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if($id <= 0){
        echo 0; exit;
    }
    // Optionally: you can soft-delete instead of hard-delete by updating a 'deleted' flag
    $stmt = $conn->prepare("DELETE FROM alumnus_bio WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        // Log alumni deletion
        if(isset($_SESSION['login_id'])) {
            log_alumni_action($_SESSION['login_id'], 'delete', $id, "Permanently deleted alumni record ID: $id");
        }
        echo 1; // deleted
    } else {
        echo 0; // error
    }
    exit;
}

// Replace existing action-handler block with the following robust handlers:
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'archive_alumni') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo 0; exit; }

        // Ensure archive table exists
        if (!$conn->query("CREATE TABLE IF NOT EXISTS `alumnus_bio_archive` LIKE `alumnus_bio`")) {
            error_log("Archive table create failed: " . $conn->error);
            echo 0; exit;
        }

        $conn->begin_transaction();
        // Try to copy the row into archive (ignore if already present)
        $ins = $conn->prepare("INSERT IGNORE INTO `alumnus_bio_archive` SELECT * FROM `alumnus_bio` WHERE `id` = ?");
        if (!$ins) { error_log("Prepare insert failed: ".$conn->error); $conn->rollback(); echo 0; exit; }
        $ins->bind_param('i', $id);
        if (!$ins->execute()) {
            error_log("Insert to archive failed: ".$ins->error);
            $ins->close();
            $conn->rollback();
            echo 0; exit;
        }
        $ins_rows = $ins->affected_rows;
        $ins->close();

        // Delete from source regardless (we want move semantics)
        $del = $conn->prepare("DELETE FROM `alumnus_bio` WHERE `id` = ?");
        if (!$del) { error_log("Prepare delete failed: ".$conn->error); $conn->rollback(); echo 0; exit; }
        $del->bind_param('i', $id);
        if (!$del->execute()) {
            error_log("Delete from source failed: ".$del->error);
            $del->close();
            $conn->rollback();
            echo 0; exit;
        }
        $del_rows = $del->affected_rows;
        $del->close();

        // Success if we deleted the original OR we inserted into archive
        if ($del_rows > 0 || $ins_rows > 0) {
            if (!$conn->commit()) { error_log("Commit failed (archive): ".$conn->error); $conn->rollback(); echo 0; exit; }
            
            // Log archive action
            if(isset($_SESSION['login_id'])) {
                log_alumni_action($_SESSION['login_id'], 'archive', $id, "Archived alumni record ID: $id");
            }
            
            echo 1;
            exit;
        } else {
            $conn->rollback();
            echo 0;
            exit;
        }
    }

    if ($action === 'restore_alumni') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo 0; exit; }

        // Ensure source table exists (rare)
        if (!$conn->query("CREATE TABLE IF NOT EXISTS `alumnus_bio` LIKE `alumnus_bio_archive`")) {
            error_log("Source table check/create failed: " . $conn->error);
            echo 0; exit;
        }

        $conn->begin_transaction();
        $ins = $conn->prepare("INSERT IGNORE INTO `alumnus_bio` SELECT * FROM `alumnus_bio_archive` WHERE `id` = ?");
        if (!$ins) { error_log("Prepare restore insert failed: ".$conn->error); $conn->rollback(); echo 0; exit; }
        $ins->bind_param('i', $id);
        if (!$ins->execute()) {
            error_log("Restore insert failed: ".$ins->error);
            $ins->close();
            $conn->rollback();
            echo 0; exit;
        }
        $ins_rows = $ins->affected_rows;
        $ins->close();

        $del = $conn->prepare("DELETE FROM `alumnus_bio_archive` WHERE `id` = ?");
        if (!$del) { error_log("Prepare archive-delete failed: ".$conn->error); $conn->rollback(); echo 0; exit; }
        $del->bind_param('i', $id);
        if (!$del->execute()) {
            error_log("Archive delete failed: ".$del->error);
            $del->close();
            $conn->rollback();
            echo 0; exit;
        }
        $del_rows = $del->affected_rows;
        $del->close();

        if ($del_rows > 0 || $ins_rows > 0) {
            if (!$conn->commit()) { error_log("Commit failed (restore): ".$conn->error); $conn->rollback(); echo 0; exit; }
            
            // Log restore action
            if(isset($_SESSION['login_id'])) {
                log_alumni_action($_SESSION['login_id'], 'restore', $id, "Restored alumni record ID: $id from archives");
            }
            
            echo 1;
            exit;
        } else {
            $conn->rollback();
            echo 0;
            exit;
        }
    }

    if ($action === 'perma_delete_archive') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) { echo 0; exit; }
        $del = $conn->prepare("DELETE FROM `alumnus_bio_archive` WHERE `id` = ?");
        if (!$del) { error_log("Prepare perma-delete failed: " . $conn->error); echo 0; exit; }
        $del->bind_param('i', $id);
        if ($del->execute() && $del->affected_rows > 0) {
            // Log permanent deletion
            if(isset($_SESSION['login_id'])) {
                log_alumni_action($_SESSION['login_id'], 'delete', $id, "Permanently deleted archived alumni record ID: $id");
            }
            echo 1;
        } else {
            error_log("Perma-delete failed: " . $del->error);
            echo 0;
        }
        $del->close();
        exit;
    }
}

?>
