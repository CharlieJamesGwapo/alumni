<?php
session_start();
include 'db_connect.php';
include 'log_activity.php';
include_once 'admin_class.php';
include_once 'rate_limit.php';
require_once __DIR__ . '/../includes/security.php';
$crud = new Action();

$action = $_GET['action'] ?? '';

// CSRF validation for all POST actions except login and signup (pre-auth)
$csrf_exempt = ['login', 'signup', 'save_register'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($action, $csrf_exempt)) {
    if (!isset($_SESSION['login_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        exit;
    }
    $submitted_token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!csrf_validate($submitted_token)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid security token. Please refresh the page.']);
        exit;
    }
}

if($action == 'login'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    if(empty($username) || empty($password)) {
        echo "2";
        exit;
    }

    // Rate limit check
    $rl = check_rate_limit($conn, $ip, 'admin', 5, 15);
    if($rl['blocked']) {
        echo "rate_limited";
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND type IN (1, 4)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $qry = $stmt->get_result();
    if($qry->num_rows > 0){
        $user = $qry->fetch_array();

        // Check account lockout
        if(is_account_locked($conn, 'users', $user['id'])) {
            $stmt->close();
            record_login_attempt($conn, $ip, $username, 'admin', false);
            echo "locked";
            exit;
        }

        $password_valid = false;
        if(password_verify($password, $user['password'])){
            $password_valid = true;
        } elseif(md5($password) === $user['password']){
            $password_valid = true;
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_hash, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();
        }

        if($password_valid){
            session_regenerate_id(true);
            $_SESSION['login_id'] = $user['id'];
            $_SESSION['login_name'] = $user['name'];
            $_SESSION['login_username'] = $user['username'];
            $_SESSION['login_type'] = $user['type'];

            reset_failed_attempts($conn, 'users', $user['id']);
            record_login_attempt($conn, $ip, $username, 'admin', true);

            echo "1";
            $stmt->close();
            exit;
        } else {
            increment_failed_attempts($conn, 'users', $user['id']);
        }
    }
    $stmt->close();

    record_login_attempt($conn, $ip, $username, 'admin', false);
    echo "2";
    exit;
}
if($action == 'save_user'){
	$name = trim($_POST['name'] ?? '');
	$username = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';
	$type = (int)($_POST['type'] ?? 0);
	$id = (int)($_POST['id'] ?? 0);

	// Validate required fields
	if(empty($name) || empty($username) || !in_array($type, [1, 4])){
		echo 0; // Invalid input
		exit;
	}

	// Check for duplicate username using prepared statement
	$chk_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
	$chk_stmt->bind_param("si", $username, $id);
	$chk_stmt->execute();
	$chk_result = $chk_stmt->get_result();
	if($chk_result->num_rows > 0){
		$chk_stmt->close();
		echo 2; // Username already exists
		exit;
	}
	$chk_stmt->close();

	// Save or update user
	if(empty($id)){
		// Create new user
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$stmt = $conn->prepare("INSERT INTO users (name, username, password, type, auto_generated_pass, alumnus_id) VALUES (?, ?, ?, ?, '', 0)");
		$stmt->bind_param("sssi", $name, $username, $password_hash, $type);
	} else {
		// Update existing user
		if(!empty($password)){
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			$stmt = $conn->prepare("UPDATE users SET name = ?, username = ?, password = ?, type = ? WHERE id = ?");
			$stmt->bind_param("sssii", $name, $username, $password_hash, $type, $id);
		} else {
			$stmt = $conn->prepare("UPDATE users SET name = ?, username = ?, type = ? WHERE id = ?");
			$stmt->bind_param("ssii", $name, $username, $type, $id);
		}
	}

	$save = $stmt->execute();
	$stmt->close();

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
	$check_stmt = $conn->prepare("SELECT type FROM users WHERE id = ?");
	$check_stmt->bind_param("i", $id);
	$check_stmt->execute();
	$check_result = $check_stmt->get_result();
	if($check_result->num_rows == 0) {
		$check_stmt->close();
		echo 0; // User not found
		exit;
	}

	$user_data = $check_result->fetch_assoc();
	$check_stmt->close();
	if(!in_array($user_data['type'], [1, 4])) {
		echo 0; // Not admin or registrar user
		exit;
	}

	// Delete the user
	$delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND type IN (1, 4)");
	$delete_stmt->bind_param("i", $id);
	$delete_stmt->execute();
	$affected = $conn->affected_rows;
	$delete_stmt->close();

	if($affected > 0) {
		echo 1; // Success
	} else {
		echo 0; // Failed to delete
	}
	exit;
}
if($action == 'signup'){
    $data = $_POST;

    // Check if alumni ID exists and not already registered using prepared statement
    $check_stmt = $conn->prepare("SELECT id FROM alumnus_bio WHERE alumni_id = ?");
    $alumni_id_input = $data['alumni_id'] ?? '';
    $check_stmt->bind_param("s", $alumni_id_input);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if($check_result->num_rows > 0){
        $check_stmt->close();
        echo "Alumni ID already registered";
        exit;
    }
    $check_stmt->close();

    // Get verified data from alumni_ids table using prepared statement
    $verify_stmt = $conn->prepare("SELECT * FROM alumni_ids WHERE alumni_id = ?");
    $verify_stmt->bind_param("s", $alumni_id_input);
    $verify_stmt->execute();
    $verify = $verify_stmt->get_result();
    if($verify->num_rows == 0){
        $verify_stmt->close();
        echo "Invalid Alumni ID";
        exit;
    }

    // Get the verified data
    $verified_data = $verify->fetch_assoc();
    $verify_stmt->close();

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

    // Handle image upload with validation
    if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])){
        $check = validate_image_upload($_FILES['img']);
        if($check['valid']){
            $fname = safe_filename('signup', $check['ext']);
            ensure_upload_dir('../assets/uploads/');
            if(move_uploaded_file($_FILES['img']['tmp_name'], '../assets/uploads/'.$fname)){
                $insert_data['img'] = $fname;
            }
        }
    }

    // Removed camera/captured image (profileCapture) handling per request.
    // Retain regular file upload handling above (if user uploaded an image via input[name="img"]).

    // First check if email already exists using prepared statement
    $check_email_stmt = $conn->prepare("SELECT id FROM alumnus_bio WHERE email = ?");
    $email_val = $insert_data['email'];
    $check_email_stmt->bind_param("s", $email_val);
    $check_email_stmt->execute();
    $check_email = $check_email_stmt->get_result();

    if($check_email->num_rows > 0) {
        $check_email_stmt->close();
        echo json_encode([
            'status' => 'error',
            'message' => 'This email address is already registered. Please use a different email or contact support if you need assistance.',
            'type' => 'email_exists'
        ]);
        exit;
    }
    $check_email_stmt->close();

    // Build prepared INSERT using parameterized query
    $fields = array_keys($insert_data);
    $placeholders = array_map(function($v) {
        return is_null($v) ? "NULL" : "?";
    }, array_values($insert_data));

    // Separate non-null values for binding
    $bind_values = [];
    $bind_types = "";
    $placeholder_parts = [];
    foreach($insert_data as $key => $v) {
        if(is_null($v)) {
            $placeholder_parts[] = "NULL";
        } else {
            $placeholder_parts[] = "?";
            $bind_values[] = $v;
            $bind_types .= "s";
        }
    }

    $query = "INSERT INTO alumnus_bio (`" . implode('`, `', $fields) . "`)
              VALUES (" . implode(', ', $placeholder_parts) . ")";

    $insert_stmt = $conn->prepare($query);
    if($insert_stmt && !empty($bind_values)) {
        $insert_stmt->bind_param($bind_types, ...$bind_values);
    }

    if($insert_stmt && $insert_stmt->execute()){
        $insert_stmt->close();
        // After successful insert, send confirmation email to the registered address (non-blocking)
        $email_to = $insert_data['email'] ?? '';
        $email_sent = false;
        if(!empty($email_to) && filter_var($email_to, FILTER_VALIDATE_EMAIL)){
            try{
                require_once __DIR__ . '/../PHPMailer/src/Exception.php';
                require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
                require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                require_once __DIR__ . '/email_config.php';
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_ENCRYPTION;
                $mail->Port = SMTP_PORT;

                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
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
        if($insert_stmt) $insert_stmt->close();
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
	$company = trim($_POST['company'] ?? '');
	$title = trim($_POST['title'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$description = trim($_POST['description'] ?? '');
	$salary = trim($_POST['salary'] ?? '');
	$job_type = trim($_POST['job_type'] ?? 'Full-time');
	$id = (int)($_POST['id'] ?? 0);
	$user_id = $_SESSION['login_id'] ?? 1;
	$remove_image = ($_POST['remove_image'] ?? '0') === '1';

	if(empty($company) || empty($title)){
		echo 0;
		exit;
	}

	// Handle image upload
	$image_filename = null;
	$upload_dir = __DIR__ . '/../uploads/jobs/';
	if(!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);

	if(isset($_FILES['job_image']) && $_FILES['job_image']['error'] === UPLOAD_ERR_OK){
		$file = $_FILES['job_image'];
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$allowed = ['jpg','jpeg','png','gif','webp'];

		if(!in_array($ext, $allowed)){
			echo 0; exit;
		}
		if($file['size'] > 5 * 1024 * 1024){
			echo 0; exit;
		}

		$image_filename = 'job_' . $user_id . '_' . time() . '_' . uniqid() . '.' . $ext;
		if(!move_uploaded_file($file['tmp_name'], $upload_dir . $image_filename)){
			$image_filename = null;
		}

		// Delete old image on update
		if($id > 0 && $image_filename){
			$old = $conn->prepare("SELECT image FROM careers WHERE id = ?");
			$old->bind_param("i", $id);
			$old->execute();
			$old_row = $old->get_result()->fetch_assoc();
			if($old_row && !empty($old_row['image']) && file_exists($upload_dir . $old_row['image'])){
				@unlink($upload_dir . $old_row['image']);
			}
			$old->close();
		}
	}

	// Remove image if requested
	if($remove_image && $id > 0){
		$old = $conn->prepare("SELECT image FROM careers WHERE id = ?");
		$old->bind_param("i", $id);
		$old->execute();
		$old_row = $old->get_result()->fetch_assoc();
		if($old_row && !empty($old_row['image']) && file_exists($upload_dir . $old_row['image'])){
			@unlink($upload_dir . $old_row['image']);
		}
		$old->close();
		$image_filename = ''; // Set to empty to clear in DB
	}

	if(empty($id)){
		$stmt = $conn->prepare("INSERT INTO careers (company, job_title, location, description, salary, job_type, image, user_id, date_created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
		$stmt->bind_param("sssssssi", $company, $title, $location, $description, $salary, $job_type, $image_filename, $user_id);
	} else {
		if($image_filename !== null){
			$stmt = $conn->prepare("UPDATE careers SET company = ?, job_title = ?, location = ?, description = ?, salary = ?, job_type = ?, image = ? WHERE id = ?");
			$stmt->bind_param("sssssssi", $company, $title, $location, $description, $salary, $job_type, $image_filename, $id);
		} else {
			$stmt = $conn->prepare("UPDATE careers SET company = ?, job_title = ?, location = ?, description = ?, salary = ?, job_type = ? WHERE id = ?");
			$stmt->bind_param("ssssssi", $company, $title, $location, $description, $salary, $job_type, $id);
		}
	}

	$save = $stmt->execute();
	$stmt->close();

	if($save) {
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
if($action == "save_register"){
	$event_id = intval($_POST['event_id'] ?? 0);
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$contact = trim($_POST['contact'] ?? '');
	$id = intval($_POST['id'] ?? 0);
	if($event_id <= 0 || empty($name)){ echo 0; exit; }
	if(empty($id)){
		$stmt = $conn->prepare("INSERT INTO audience (event_id, name, email, contact) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("isss", $event_id, $name, $email, $contact);
	} else {
		$stmt = $conn->prepare("UPDATE audience SET event_id = ?, name = ?, email = ?, contact = ? WHERE id = ?");
		$stmt->bind_param("isssi", $event_id, $name, $email, $contact, $id);
	}
	echo $stmt->execute() ? 1 : 0;
	$stmt->close();
}
if($action == "delete_register"){
	$id = intval($_POST['id'] ?? 0);
	if($id <= 0){ echo 0; exit; }
	$stmt = $conn->prepare("DELETE FROM audience WHERE id = ?");
	$stmt->bind_param("i", $id);
	echo $stmt->execute() ? 1 : 0;
	$stmt->close();
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
	$id = trim($_POST['id'] ?? '');
	$title = trim($_POST['title'] ?? '');
	$content = $_POST['content'] ?? '';
	$schedule = trim($_POST['schedule'] ?? '');

	if(empty($title) || empty($schedule)) {
		echo '0';
		exit;
	}

	// Handle banner file upload
	$banner_filename = '';
	$upload_dir = __DIR__ . '/../uploads/';
	if(!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);

	if(isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK){
		$file = $_FILES['banner'];
		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$allowed = ['jpg','jpeg','png','gif','webp'];
		if(in_array($ext, $allowed) && $file['size'] <= 10 * 1024 * 1024){
			$banner_filename = time() . '_' . uniqid() . '.' . $ext;
			if(!move_uploaded_file($file['tmp_name'], $upload_dir . $banner_filename)){
				$banner_filename = '';
			}
		}
	}

	$login_id = $_SESSION['login_id'] ?? 0;

	if($id == '') {
		$stmt = $conn->prepare("INSERT INTO events (title, content, schedule, banner, user_id, approved, date_created) VALUES (?, ?, ?, ?, ?, 1, NOW())");
		$stmt->bind_param("ssssi", $title, $content, $schedule, $banner_filename, $login_id);
		if($stmt->execute()) {
			$new_id = $conn->insert_id;
			$stmt->close();
			if(function_exists('log_activity')) {
				@log_activity($login_id, 'CREATE_EVENT', "Event created: $title", $new_id, 'event');
			}
			echo '1';
		} else {
			$stmt->close();
			echo '0';
		}
	} else {
		$id = (int)$id;
		if(!empty($banner_filename)) {
			// Delete old banner
			$old = $conn->prepare("SELECT banner FROM events WHERE id = ?");
			$old->bind_param("i", $id);
			$old->execute();
			$old_row = $old->get_result()->fetch_assoc();
			if($old_row && !empty($old_row['banner']) && file_exists($upload_dir . $old_row['banner'])){
				@unlink($upload_dir . $old_row['banner']);
			}
			$old->close();

			$stmt = $conn->prepare("UPDATE events SET title = ?, content = ?, schedule = ?, banner = ? WHERE id = ?");
			$stmt->bind_param("ssssi", $title, $content, $schedule, $banner_filename, $id);
		} else {
			$stmt = $conn->prepare("UPDATE events SET title = ?, content = ?, schedule = ? WHERE id = ?");
			$stmt->bind_param("sssi", $title, $content, $schedule, $id);
		}
		if($stmt->execute()) {
			$stmt->close();
			if(function_exists('log_activity')) {
				@log_activity($login_id, 'UPDATE_EVENT', "Event updated: $title", $id, 'event');
			}
			echo '2';
		} else {
			$stmt->close();
			echo '0';
		}
	}
	exit;
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

// Database Import Function
if($action == "import_database"){
	// Clear any output buffers and start fresh FIRST
	while(ob_get_level()) {
		ob_end_clean();
	}
	ob_start();
	
	// Suppress error output to prevent JSON corruption
	error_reporting(0);
	ini_set('display_errors', 0);
	
	// Set headers BEFORE any output
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	
	// Register shutdown function to catch fatal errors
	register_shutdown_function(function() {
		$error = error_get_last();
		if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
			while(ob_get_level()) {
				ob_end_clean();
			}
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode([
				'success' => false,
				'message' => 'Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . $error['line']
			]);
			exit;
		}
	});
	
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
		
		$response = [
			'success' => true,
			'message' => $message,
			'executed_statements' => $executed_count,
			'errors' => $error_count,
			'database_created' => $database_created,
			'backup_created' => $backup_created
		];
		
		echo json_encode($response);
		exit;
		
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
		exit;
		
	} catch(Throwable $fatal_error) {
		// Catch any fatal errors (PHP 7+)
		if(ob_get_level()) {
			ob_end_clean();
		}
		echo json_encode([
			'success' => false,
			'message' => 'Fatal error: ' . $fatal_error->getMessage()
		]);
		exit;
	}
	exit;
}
if($action == "validate_alumni"){
    $id = (int)($_POST['id'] ?? 0);
    if($id <= 0) {
        echo 0;
        exit;
    }
    // Check if already validated
    $check = $conn->prepare("SELECT status FROM alumnus_bio WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $row = $check->get_result()->fetch_assoc();
    $check->close();
    if($row && intval($row['status']) == 1) {
        echo 2; // Already validated
        exit;
    }
    $stmt = $conn->prepare("UPDATE alumnus_bio SET status = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $update = $stmt->execute();
    $stmt->close();
    if($update){
        if(isset($_SESSION['login_id'])) {
            log_alumni_action($_SESSION['login_id'], 'validate', $id, "Validated alumni account ID: $id");
        }

        // Send verification notification email to alumni
        $alumni_stmt = $conn->prepare("SELECT firstname, lastname, email, alumni_id FROM alumnus_bio WHERE id = ?");
        $alumni_stmt->bind_param("i", $id);
        $alumni_stmt->execute();
        $alumni_data = $alumni_stmt->get_result()->fetch_assoc();
        $alumni_stmt->close();

        $email_sent = false;
        if($alumni_data && !empty($alumni_data['email']) && filter_var($alumni_data['email'], FILTER_VALIDATE_EMAIL)){
            try {
                require_once __DIR__ . '/../PHPMailer/src/Exception.php';
                require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
                require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
                require_once __DIR__ . '/email_config.php';

                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_ENCRYPTION;
                $mail->Port = SMTP_PORT;

                $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                $mail->addAddress($alumni_data['email']);
                $mail->isHTML(true);
                $mail->Subject = 'Your MOIST Alumni Account Has Been Verified!';

                $fname = htmlspecialchars($alumni_data['firstname']);
                $lname = htmlspecialchars($alumni_data['lastname']);
                $aid = htmlspecialchars($alumni_data['alumni_id'] ?? '');

                $mail->Body = '
                <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
                    <div style="background:#800000;color:#fff;padding:24px;text-align:center;">
                        <h1 style="margin:0;font-size:22px;">Account Verified!</h1>
                    </div>
                    <div style="padding:24px;">
                        <p>Dear <strong>'.$fname.' '.$lname.'</strong>,</p>
                        <p>Great news! Your MOIST Alumni Portal account has been <strong style="color:#059669;">verified</strong> by the registrar.</p>
                        '.(!empty($aid) ? '<p><strong>Alumni ID:</strong> '.$aid.'</p>' : '').'
                        <p>You can now log in and enjoy full access to:</p>
                        <ul style="color:#333;line-height:1.8;">
                            <li>Official Alumni ID Card generation</li>
                            <li>Alumni events and reunions</li>
                            <li>Job postings and career services</li>
                            <li>Community forums and networking</li>
                            <li>Alumni newsletter and updates</li>
                        </ul>
                        <div style="text-align:center;margin:24px 0;">
                            <a href="http://localhost/alumni/login.php" style="background:#800000;color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">Login Now</a>
                        </div>
                        <p style="color:#888;font-size:13px;">If you did not register for this account, please disregard this email.</p>
                    </div>
                    <div style="background:#f5f5f5;padding:16px;text-align:center;font-size:12px;color:#999;">
                        MOIST Alumni Portal &copy; '.date('Y').'
                    </div>
                </div>';

                $mail->send();
                $email_sent = true;
            } catch (\Exception $e) {
                error_log('Validation email failed for alumni ID '.$id.': '.$e->getMessage());
            }
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

// Handle backup and import logging
if($action == 'log_backup_action') {
    include 'log_activity.php';
    
    $backup_action = $_POST['action'] ?? '';
    $status = $_POST['status'] ?? '';
    $filename = $_POST['filename'] ?? '';
    $error = $_POST['error'] ?? '';
    $filesize = $_POST['filesize'] ?? 0;
    
    $details = [
        'filename' => $filename,
        'error' => $error,
        'filesize' => $filesize
    ];
    
    $result = log_backup_action($backup_action, $status, $details);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
}

// Get dashboard statistics for real-time updates
if($action == 'get_dashboard_stats') {
    header('Content-Type: application/json');
    
    try {
        $alumni_count = $conn->query("SELECT COUNT(*) as count FROM alumnus_bio WHERE status = 1")->fetch_assoc()['count'];
        $jobs_count = $conn->query("SELECT COUNT(*) as count FROM careers")->fetch_assoc()['count'];
        $events_count = $conn->query("SELECT COUNT(*) as count FROM events WHERE DATE(schedule) >= CURDATE()")->fetch_assoc()['count'];
        $users_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE type IN (1, 4)")->fetch_assoc()['count'];
        $courses_count = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
        
        echo json_encode([
            'success' => true,
            'alumni_count' => $alumni_count,
            'jobs_count' => $jobs_count,
            'events_count' => $events_count,
            'users_count' => $users_count,
            'courses_count' => $courses_count,
            'current_time' => date('M d, Y h:i A')
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Handle event management logging
if($action == 'log_event_action') {
    include 'log_activity.php';
    
    $event_action = $_POST['action'] ?? '';
    $event_id = $_POST['eventId'] ?? null;
    $title = $_POST['title'] ?? '';
    $status = $_POST['status'] ?? '';
    $error = $_POST['error'] ?? '';
    
    // Build details string
    $details = "Event $event_action: $title - Status: $status";
    if($error) {
        $details .= " - Error: $error";
    }
    
    $result = log_event_action($_SESSION['login_id'] ?? 0, $event_action, $event_id, $details);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => $result]);
    exit;
}

// Handle course management
if($action == 'save_course') {
    // Clear any output buffers
    while(ob_get_level()) {
        ob_end_clean();
    }
    ob_start();

    try {
        $id = $_POST['id'] ?? '';
        $course = $_POST['course'] ?? '';
        $about = $_POST['about'] ?? '';

        if(empty($course)) {
            ob_end_clean();
            echo '0';
            exit;
        }

        if($id == '') {
            // Check for duplicate using prepared statement
            $check_stmt = $conn->prepare("SELECT id FROM courses WHERE course = ?");
            $check_stmt->bind_param("s", $course);
            $check_stmt->execute();
            $check = $check_stmt->get_result();
            if($check && $check->num_rows > 0) {
                $check_stmt->close();
                ob_end_clean();
                echo '3'; // Duplicate
                exit;
            }
            $check_stmt->close();

            // Insert new course using prepared statement
            $stmt = $conn->prepare("INSERT INTO courses (course, about) VALUES (?, ?)");
            $stmt->bind_param("ss", $course, $about);
            if($stmt->execute()) {
                $new_id = $conn->insert_id;
                $stmt->close();
                // Log course creation
                if(isset($_SESSION['login_id']) && function_exists('log_course_action')) {
                    try {
                        log_course_action($_SESSION['login_id'], 'create', $new_id, "Course created: $course");
                    } catch(Exception $e) {
                        // Ignore logging errors
                    }
                }
                ob_end_clean();
                echo '1'; // Success
                exit;
            } else {
                $stmt->close();
                ob_end_clean();
                echo '0'; // Error
                exit;
            }
        } else {
            $id = (int)$id;
            // Update existing course using prepared statement
            $stmt = $conn->prepare("UPDATE courses SET course = ?, about = ? WHERE id = ?");
            $stmt->bind_param("ssi", $course, $about, $id);
            if($stmt->execute()) {
                $stmt->close();
                // Log course update
                if(isset($_SESSION['login_id']) && function_exists('log_course_action')) {
                    try {
                        log_course_action($_SESSION['login_id'], 'update', $id, "Course updated: $course");
                    } catch(Exception $e) {
                        // Ignore logging errors
                    }
                }
                ob_end_clean();
                echo '2'; // Updated
                exit;
            } else {
                $stmt->close();
                ob_end_clean();
                echo '0'; // Error
                exit;
            }
        }
    } catch(Exception $e) {
        ob_end_clean();
        echo '0'; // Error
        exit;
    }
}

if($action == 'delete_course') {
    // Clear any output buffers
    while(ob_get_level()) {
        ob_end_clean();
    }
    ob_start();

    try {
        $id = intval($_POST['id'] ?? 0);
        if($id <= 0) {
            ob_end_clean();
            echo '0';
            exit;
        }

        // Get course name for logging using prepared statement
        $name_stmt = $conn->prepare("SELECT course FROM courses WHERE id = ?");
        $name_stmt->bind_param("i", $id);
        $name_stmt->execute();
        $course_row = $name_stmt->get_result()->fetch_assoc();
        $course_name = $course_row['course'] ?? 'Unknown';
        $name_stmt->close();

        // Delete course using prepared statement
        $del_stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        $delete = $del_stmt->execute();
        $del_stmt->close();
        if($delete) {
            // Log course deletion
            if(isset($_SESSION['login_id']) && function_exists('log_course_action')) {
                try {
                    log_course_action($_SESSION['login_id'], 'delete', $id, "Course deleted: $course_name");
                } catch(Exception $e) {
                    // Ignore logging errors
                }
            }
            ob_end_clean();
            echo '1'; // Success
            exit;
        } else {
            ob_end_clean();
            echo '0'; // Error
            exit;
        }
    } catch(Exception $e) {
        ob_end_clean();
        echo '0'; // Error
        exit;
    }
}

?>
