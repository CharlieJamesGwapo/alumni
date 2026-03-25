<?php
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$userId = intval($_SESSION['login_id']);

// Get posted fields
$date_started = trim($_POST['date_started'] ?? '');
$connected_to = trim($_POST['connected_to'] ?? '');
$company_address = trim($_POST['company_address'] ?? '');
$company_email = trim($_POST['company_email'] ?? '');
$duration = trim($_POST['duration'] ?? '');

if (empty($date_started)) {
    echo json_encode(['success' => false, 'message' => 'Start date is required']);
    exit;
}

// Validate email if provided
if (!empty($company_email) && !filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Get current employment history JSON
    $stmt = $conn->prepare("SELECT employment_history FROM alumnus_bio WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Parse existing history or start fresh
    $history = [];
    if (!empty($row['employment_history'])) {
        $decoded = json_decode($row['employment_history'], true);
        if (is_array($decoded)) {
            $history = $decoded;
        }
    }

    // Add new entry
    $newEntry = [
        'date_started' => $date_started,
        'connected_to' => $connected_to,
        'company_address' => $company_address,
        'company_email' => $company_email,
        'duration' => $duration,
        'added_at' => date('Y-m-d H:i:s')
    ];
    $history[] = $newEntry;

    // Save back to database
    $jsonHistory = json_encode($history);
    $updateStmt = $conn->prepare("UPDATE alumnus_bio SET employment_history = ? WHERE id = ?");
    $updateStmt->bind_param("si", $jsonHistory, $userId);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Employment history added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save employment history']);
    }
    $updateStmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
