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
    // Use absolute path for uploads directory
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    // Ensure writable
    if (!is_writable($uploadDir)) {
        @chmod($uploadDir, 0755);
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

        if ($_FILES['profileImage']['size'] > 10 * 1024 * 1024) {
            throw new Exception('Image size must be less than 10MB');
        }

        $newFilename = 'profile_' . $userId . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $newFilename;

        // Try to resize image before saving (max 800x800)
        $resized = false;
        if (function_exists('imagecreatefromjpeg')) {
            $tmpFile = $_FILES['profileImage']['tmp_name'];
            $imgInfo = @getimagesize($tmpFile);
            if ($imgInfo) {
                $mime = $imgInfo['mime'];
                $srcW = $imgInfo[0];
                $srcH = $imgInfo[1];
                $maxDim = 800;

                $src = null;
                switch ($mime) {
                    case 'image/jpeg': $src = @imagecreatefromjpeg($tmpFile); break;
                    case 'image/png': $src = @imagecreatefrompng($tmpFile); break;
                    case 'image/gif': $src = @imagecreatefromgif($tmpFile); break;
                    case 'image/webp': $src = @imagecreatefromwebp($tmpFile); break;
                }

                if ($src) {
                    // Calculate new dimensions
                    if ($srcW > $maxDim || $srcH > $maxDim) {
                        if ($srcW > $srcH) {
                            $newW = $maxDim;
                            $newH = intval($srcH * $maxDim / $srcW);
                        } else {
                            $newH = $maxDim;
                            $newW = intval($srcW * $maxDim / $srcH);
                        }
                    } else {
                        $newW = $srcW;
                        $newH = $srcH;
                    }

                    $dst = imagecreatetruecolor($newW, $newH);
                    // Preserve transparency for PNG
                    if ($mime === 'image/png') {
                        imagealphablending($dst, false);
                        imagesavealpha($dst, true);
                    }
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

                    // Save as JPEG for consistency and smaller size
                    $newFilename = 'profile_' . $userId . '_' . time() . '.jpg';
                    $uploadPath = $uploadDir . $newFilename;
                    if (imagejpeg($dst, $uploadPath, 85)) {
                        $resized = true;
                    }
                    imagedestroy($src);
                    imagedestroy($dst);
                }
            }
        }

        // Fallback: just move the file as-is
        if (!$resized) {
            if (!move_uploaded_file($_FILES['profileImage']['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to upload image. Check server permissions.');
            }
        }

        // Delete old image if exists
        $oldImgStmt = $conn->prepare("SELECT img FROM alumnus_bio WHERE id = ?");
        $oldImgStmt->bind_param("i", $userId);
        $oldImgStmt->execute();
        $oldImg = $oldImgStmt->get_result()->fetch_assoc();
        $oldImgStmt->close();
        if (!empty($oldImg['img']) && file_exists($uploadDir . $oldImg['img'])) {
            @unlink($uploadDir . $oldImg['img']);
        }

        // Update image in database
        $stmt = $conn->prepare("UPDATE alumnus_bio SET img = ? WHERE id = ?");
        $stmt->bind_param("si", $newFilename, $userId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update profile image in database');
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