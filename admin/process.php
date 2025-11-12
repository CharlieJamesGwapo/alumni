<?php
include 'db_connect.php';

if(isset($_POST['firstname'])) { 
    try {
        // Begin transaction
        mysqli_begin_transaction($conn);

        // Validate required fields
        $required_fields = ['firstname', 'lastname', 'email', 'password', 'gender', 'batch', 'course_id'];
        foreach($required_fields as $field) {
            if(empty($_POST[$field])) {
                throw new Exception("Please fill in all required fields");
            }
        }

        // Validate email format
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check email existence
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $email_check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$email'");
        if(mysqli_num_rows($email_check) > 0) {
            throw new Exception("Email already exists");
        }

        // Sanitize inputs
        $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
        $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
        $middlename = mysqli_real_escape_string($conn, $_POST['middlename'] ?? '');
        $suffixname = mysqli_real_escape_string($conn, $_POST['suffixname'] ?? ''); 
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $batch = mysqli_real_escape_string($conn, $_POST['batch']);
        $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
        $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate'] ?? '');
        $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
        $connected_to = mysqli_real_escape_string($conn, $_POST['connected_to'] ?? '');
        $company_name = mysqli_real_escape_string($conn, $_POST['company_name'] ?? '');
        $company_address = mysqli_real_escape_string($conn, $_POST['company_address'] ?? '');
        $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no'] ?? '');
        $company_email = mysqli_real_escape_string($conn, $_POST['company_email'] ?? '');

        // Handle image upload
        $avatar = '';
        if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '') {
            $img_name = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
            $upload_path = '../assets/uploads/' . $img_name;
            
            // Create directory if it doesn't exist
            if(!is_dir('../assets/uploads/')) {
                mkdir('../assets/uploads/', 0777, true);
            }
            
            if(move_uploaded_file($_FILES['img']['tmp_name'], $upload_path)) {
                $avatar = $img_name;
            }
        }

        // Insert user
        $hashed_password = md5($password); // Hash password
        $fullname = $firstname . ' ' . $lastname;
        
        $sql = "INSERT INTO users (name, username, password, type) VALUES (?, ?, ?, 3)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $hashed_password);
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating user account");
        }
        
        $user_id = mysqli_insert_id($conn);

        // Insert alumnus bio
        $bio_sql = "INSERT INTO alumnus_bio (
            firstname, lastname, middlename, suffixname,
            gender, batch, course_id, birthdate, address,
            email, connected_to, company_name, company_address,
            contact_no, company_email, avatar, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        
        $stmt = mysqli_prepare($conn, $bio_sql);
        mysqli_stmt_bind_param($stmt, "ssssssissssssssss",
            $firstname, $lastname, $middlename, $suffixname,
            $gender, $batch, $course_id, $birthdate, $address,
            $email, $connected_to, $company_name, $company_address,
            $contact_no, $company_email, $avatar
        );
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error creating alumni profile");
        }
        
        $alumnus_id = mysqli_insert_id($conn);

        // Update user with alumnus_id
        $update_sql = "UPDATE users SET alumnus_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "ii", $alumnus_id, $user_id);
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error linking profiles");
        }

        mysqli_commit($conn);
        echo json_encode(['status' => 'success', 'message' => 'Account created successfully']);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Signup error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        if(isset($stmt)) {
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>