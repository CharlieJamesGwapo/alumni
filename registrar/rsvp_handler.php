<?php
/**
 * RSVP Handler for Email Responses
 * Handles Accept/Decline responses from alumni
 */

date_default_timezone_set('Asia/Manila');
require_once '../admin/db_connect.php';

// Get parameters from URL
$message_id = isset($_GET['mid']) ? intval($_GET['mid']) : 0;
$recipient_id = isset($_GET['rid']) ? intval($_GET['rid']) : 0;
$response = isset($_GET['response']) ? $_GET['response'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validate token
$expected_token = md5($message_id . $recipient_id . 'moist_rsvp_secret');

if ($token !== $expected_token) {
    die('Invalid token');
}

// Validate response
if (!in_array($response, ['accept', 'decline', 'maybe'])) {
    die('Invalid response');
}

// Update the response in database
$stmt = $conn->prepare("UPDATE message_recipients SET 
                        rsvp_status = ?, 
                        rsvp_at = NOW(),
                        is_read = 1,
                        read_at = NOW()
                        WHERE message_id = ? AND recipient_id = ?");
$stmt->bind_param("sii", $response, $message_id, $recipient_id);
$success = $stmt->execute();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP Response - MOIST Alumni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #800000 0%, #600000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .response-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 500px;
            padding: 40px;
            text-align: center;
        }
        .icon-success {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .icon-error {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .response-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        .response-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-portal {
            background: #800000;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-portal:hover {
            background: #a00000;
            color: white;
            transform: translateY(-2px);
        }
        .response-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin: 15px 0;
        }
        .badge-accept {
            background: #d4edda;
            color: #155724;
        }
        .badge-decline {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-maybe {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="response-card">
        <?php if ($success): ?>
            <i class="fas fa-check-circle icon-success"></i>
            <h1 class="response-title">Response Recorded!</h1>
            
            <?php if ($response === 'accept'): ?>
                <div class="response-badge badge-accept">
                    <i class="fas fa-check me-2"></i>Accepted
                </div>
                <p class="response-message">
                    Thank you for accepting the invitation! We're excited to have you join us. 
                    You will receive more details closer to the event date.
                </p>
            <?php elseif ($response === 'decline'): ?>
                <div class="response-badge badge-decline">
                    <i class="fas fa-times me-2"></i>Declined
                </div>
                <p class="response-message">
                    Thank you for your response. We're sorry you can't make it this time. 
                    We hope to see you at future events!
                </p>
            <?php else: ?>
                <div class="response-badge badge-maybe">
                    <i class="fas fa-question me-2"></i>Maybe
                </div>
                <p class="response-message">
                    Thank you for your response. We understand you're unsure at this time. 
                    Feel free to update your response later if your plans change.
                </p>
            <?php endif; ?>
            
            <div class="mt-4">
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Your response has been recorded and the MOIST Alumni Office has been notified.
                </p>
                <a href="../home.php" class="btn-portal">
                    <i class="fas fa-home me-2"></i>Go to Alumni Portal
                </a>
            </div>
            
        <?php else: ?>
            <i class="fas fa-exclamation-circle icon-error"></i>
            <h1 class="response-title">Oops! Something Went Wrong</h1>
            <p class="response-message">
                We couldn't record your response. This might be because:
                <br>• The link has expired
                <br>• You've already responded
                <br>• There was a technical issue
            </p>
            <a href="../home.php" class="btn-portal">
                <i class="fas fa-home me-2"></i>Go to Alumni Portal
            </a>
        <?php endif; ?>
        
        <div class="mt-4 pt-4 border-top">
            <small class="text-muted">
                <strong>MOIST Alumni Office</strong><br>
                Misamis Oriental Institute of Science and Technology
            </small>
        </div>
    </div>
</body>
</html>
