<?php
session_start();
include 'admin/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$userId = $_SESSION['login_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    // Handle image upload
    $imageUpdated = false;
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['profileImage']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            throw new Exception('Invalid image format. Allowed: JPG, PNG, GIF, WEBP');
        }

        // Check file size (max 5MB)
        if ($_FILES['profileImage']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Image size must be less than 5MB');
        }

        $newFilename = 'profile_' . $userId . '_' . time() . '.' . $ext;
        $uploadPath = 'uploads/' . $newFilename;

        if (!move_uploaded_file($_FILES['profileImage']['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload image');
        }

        // Delete old image if exists
        $oldImg = $conn->query("SELECT img FROM alumnus_bio WHERE id = $userId")->fetch_assoc();
        if (!empty($oldImg['img']) && file_exists('uploads/' . $oldImg['img'])) {
            @unlink('uploads/' . $oldImg['img']);
        }

        // Update image in database
        $stmt = $conn->prepare("UPDATE alumnus_bio SET img = ? WHERE id = ?");
        $stmt->bind_param("si", $newFilename, $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update profile image');
        }
        $imageUpdated = true;
    }

    // Validate required fields
    if (empty($_POST['firstname']) || empty($_POST['lastname']) || empty($_POST['email'])) {
        throw new Exception('First name, last name, and email are required');
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Update all profile fields
    $stmt = $conn->prepare("UPDATE alumnus_bio SET 
        firstname = ?,
        lastname = ?,
        middlename = ?,
        email = ?,
        contact_no = ?,
        gender = ?,
        birthdate = ?,
        address = ?,
        employment_status = ?,
        connected_to = ?,
        company_address = ?,
        company_email = ?
        WHERE id = ?");

    $stmt->bind_param("ssssssssssssi", 
        $_POST['firstname'],
        $_POST['lastname'],
        $_POST['middlename'],
        $_POST['email'],
        $_POST['contact_no'],
        $_POST['gender'],
        $_POST['birthdate'],
        $_POST['address'],
        $_POST['employment_status'],
        $_POST['connected_to'],
        $_POST['company_address'],
        $_POST['company_email'],
        $userId
    );

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully!';
        $response['imageUpdated'] = $imageUpdated;
        
        // Get updated user data
        $userStmt = $conn->prepare("SELECT firstname, lastname, img FROM alumnus_bio WHERE id = ?");
        $userStmt->bind_param("i", $userId);
        $userStmt->execute();
        $userData = $userStmt->get_result()->fetch_assoc();
        $response['userData'] = $userData;
    } else {
        throw new Exception('Failed to update profile information: ' . $stmt->error);
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;