<?php
/**
 * Email Open Tracking Pixel
 * Tracks when alumni open emails
 */

require_once '../admin/db_connect.php';

// Get parameters
$message_id = intval($_GET['mid'] ?? 0);
$recipient_id = intval($_GET['rid'] ?? 0);

if ($message_id && $recipient_id) {
    // Update read status
    $stmt = $conn->prepare("UPDATE message_recipients 
                           SET is_read = 1, read_at = NOW() 
                           WHERE message_id = ? AND recipient_id = ? AND is_read = 0");
    $stmt->bind_param("ii", $message_id, $recipient_id);
    $stmt->execute();
    
    // Log the open
    error_log("Email opened: Message ID $message_id, Recipient ID $recipient_id");
}

// Return 1x1 transparent pixel
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
exit();
?>
