<?php
include 'db_connect.php';
require_once __DIR__ . '/../includes/security.php';

if (isset($_POST['firstname'])) {
    try {
        $conn->begin_transaction();

        // Validate required fields
        $required_fields = ['firstname', 'lastname', 'email', 'password', 'gender', 'batch', 'course_id'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }

        // Validate email
        $email = sanitize_email($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check email existence
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            throw new Exception("Email already exists");
        }
        $stmt->close();

        // Sanitize inputs
        $firstname = sanitize_string($_POST['firstname']);
        $lastname = sanitize_string($_POST['lastname']);
        $middlename = sanitize_string($_POST['middlename'] ?? '');
        $suffixname = sanitize_string($_POST['suffixname'] ?? '');
        $password = $_POST['password'];
        $gender = sanitize_string($_POST['gender']);
        $batch = sanitize_string($_POST['batch']);
        $course_id = sanitize_int($_POST['course_id']);
        $birthdate = sanitize_string($_POST['birthdate'] ?? '');
        $address = sanitize_string($_POST['address'] ?? '');
        $connected_to = sanitize_string($_POST['connected_to'] ?? '');
        $company_name = sanitize_string($_POST['company_name'] ?? '');
        $company_address = sanitize_string($_POST['company_address'] ?? '');
        $contact_no = sanitize_string($_POST['contact_no'] ?? '');
        $company_email = sanitize_email($_POST['company_email'] ?? '');

        // Handle image upload with validation
        $avatar = '';
        if (isset($_FILES['img']) && !empty($_FILES['img']['tmp_name'])) {
            $check = validate_image_upload($_FILES['img']);
            if ($check['valid']) {
                $avatar = safe_filename('avatar', $check['ext']);
                ensure_upload_dir('../assets/uploads/');
                move_uploaded_file($_FILES['img']['tmp_name'], '../assets/uploads/' . $avatar);
            }
        }

        // Insert user with bcrypt hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $fullname = $firstname . ' ' . $lastname;

        $stmt = $conn->prepare("INSERT INTO users (name, username, password, type) VALUES (?, ?, ?, 3)");
        $stmt->bind_param("sss", $fullname, $email, $hashed_password);
        if (!$stmt->execute()) {
            throw new Exception("Error creating user account");
        }
        $user_id = $conn->insert_id;
        $stmt->close();

        // Insert alumnus bio
        $stmt = $conn->prepare("INSERT INTO alumnus_bio (firstname, lastname, middlename, suffixname, gender, batch, course_id, birthdate, address, email, connected_to, company_name, company_address, contact_no, company_email, avatar, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssssssisssssssss",
            $firstname, $lastname, $middlename, $suffixname,
            $gender, $batch, $course_id, $birthdate, $address,
            $email, $connected_to, $company_name, $company_address,
            $contact_no, $company_email, $avatar
        );
        if (!$stmt->execute()) {
            throw new Exception("Error creating alumni profile");
        }
        $alumnus_id = $conn->insert_id;
        $stmt->close();

        // Link user to alumni bio
        $stmt = $conn->prepare("UPDATE users SET alumnus_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $alumnus_id, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Error linking profiles");
        }
        $stmt->close();

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Account created successfully']);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Signup error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
