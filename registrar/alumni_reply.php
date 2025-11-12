<?php
/**
 * Alumni Reply Form
 * Allows alumni to send replies that are automatically recorded
 */

require_once '../admin/db_connect.php';

// Get parameters
$message_id = intval($_GET['mid'] ?? 0);
$recipient_id = intval($_GET['rid'] ?? 0);
$token = $_GET['token'] ?? '';

// Verify token
$expected_token = md5($message_id . $recipient_id . 'moist_rsvp_secret');
if ($token !== $expected_token) {
    die('Invalid token');
}

// Get message and recipient info
$stmt = $conn->prepare("SELECT m.subject, ab.firstname, ab.lastname, ab.email 
                       FROM messages m
                       JOIN alumnus_bio ab ON ab.id = ?
                       WHERE m.id = ?");
$stmt->bind_param("ii", $recipient_id, $message_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die('Message or recipient not found');
}

// Handle RSVP submission
if (isset($_GET['rsvp'])) {
    $rsvp_status = $_GET['rsvp'];
    if (in_array($rsvp_status, ['accept', 'decline'])) {
        $stmt = $conn->prepare("UPDATE message_recipients 
                               SET rsvp_status = ?, rsvp_at = NOW() 
                               WHERE message_id = ? AND recipient_id = ?");
        $stmt->bind_param("sii", $rsvp_status, $message_id, $recipient_id);
        if ($stmt->execute()) {
            $rsvp_success = true;
            $rsvp_message = $rsvp_status === 'accept' ? 
                "Thank you! Your attendance has been confirmed." : 
                "Thank you for letting us know you cannot attend.";
        }
    }
}

// Get current RSVP status
$stmt = $conn->prepare("SELECT rsvp_status FROM message_recipients 
                       WHERE message_id = ? AND recipient_id = ?");
$stmt->bind_param("ii", $message_id, $recipient_id);
$stmt->execute();
$result = $stmt->get_result();
$current_rsvp = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_content = trim($_POST['reply_content'] ?? '');
    
    if ($reply_content) {
        // Record reply
        $insert = $conn->prepare("INSERT INTO message_replies 
                                 (message_id, recipient_id, reply_content, replied_at) 
                                 VALUES (?, ?, ?, NOW())");
        $insert->bind_param("iis", $message_id, $recipient_id, $reply_content);
        
        if ($insert->execute()) {
            $success = true;
        } else {
            $error = "Failed to send reply. Please try again.";
        }
    } else {
        $error = "Please enter your message.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Reply - MOIST Alumni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .reply-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        .reply-header {
            background: linear-gradient(135deg, #800000 0%, #600000 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        
        .reply-body {
            padding: 30px;
        }
        
        .btn-send {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(25,118,210,0.3);
        }
        
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(25,118,210,0.4);
        }
        
        .success-message {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .success-message i {
            font-size: 60px;
            margin-bottom: 15px;
        }
        
        .rsvp-section {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
            padding: 25px;
            border-radius: 12px;
            border: 3px solid #800000;
            margin-bottom: 25px;
        }
        
        .rsvp-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        
        .rsvp-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 3px solid;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .rsvp-card.accept {
            border-color: #28a745;
        }
        
        .rsvp-card.accept:hover {
            background: #d4edda;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(40,167,69,0.3);
        }
        
        .rsvp-card.decline {
            border-color: #dc3545;
        }
        
        .rsvp-card.decline:hover {
            background: #f8d7da;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(220,53,69,0.3);
        }
        
        .rsvp-card.selected {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .rsvp-card.accept.selected {
            background: #d4edda;
            border-width: 4px;
        }
        
        .rsvp-card.decline.selected {
            background: #f8d7da;
            border-width: 4px;
        }
        
        .rsvp-card i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        
        .rsvp-card.accept i {
            color: #28a745;
        }
        
        .rsvp-card.decline i {
            color: #dc3545;
        }
        
        .rsvp-card h4 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        @media (max-width: 576px) {
            .rsvp-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="reply-container">
        <div class="reply-header">
            <h3><i class="fas fa-calendar-check me-2"></i>Event Response</h3>
            <p class="mb-0">MOIST Alumni Office</p>
        </div>
        
        <div class="reply-body">
            <?php if (isset($rsvp_success)): ?>
                <div class="success-message mb-4">
                    <i class="fas fa-check-circle text-success"></i>
                    <h4>RSVP Recorded!</h4>
                    <p class="mb-0"><?php echo $rsvp_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle text-success"></i>
                    <h4>Reply Sent Successfully!</h4>
                    <p class="mb-0">Thank you for your message. We'll get back to you soon.</p>
                    <button class="btn btn-primary mt-3" onclick="window.close()">Close Window</button>
                </div>
            <?php else: ?>
                <div class="mb-4">
                    <p><strong>From:</strong> <?php echo htmlspecialchars($data['firstname'] . ' ' . $data['lastname']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email']); ?></p>
                    <p><strong>Re:</strong> <?php echo htmlspecialchars($data['subject']); ?></p>
                </div>
                
                <!-- RSVP Section -->
                <div class="rsvp-section">
                    <h5 class="text-center mb-2" style="color: #800000; font-weight: 800;">
                        <i class="fas fa-calendar-check me-2"></i>Will you attend this event?
                    </h5>
                    <p class="text-center text-muted mb-3">
                        <small>Please confirm your attendance below</small>
                    </p>
                    
                    <div class="rsvp-cards">
                        <a href="?mid=<?php echo $message_id; ?>&rid=<?php echo $recipient_id; ?>&token=<?php echo $token; ?>&rsvp=accept" 
                           class="rsvp-card accept <?php echo ($current_rsvp && $current_rsvp['rsvp_status'] === 'accept') ? 'selected' : ''; ?>" 
                           style="text-decoration: none; color: inherit;">
                            <i class="fas fa-check-circle"></i>
                            <h4>YES, I WILL ATTEND</h4>
                            <?php if ($current_rsvp && $current_rsvp['rsvp_status'] === 'accept'): ?>
                                <small class="text-success"><i class="fas fa-check me-1"></i>Confirmed</small>
                            <?php endif; ?>
                        </a>
                        
                        <a href="?mid=<?php echo $message_id; ?>&rid=<?php echo $recipient_id; ?>&token=<?php echo $token; ?>&rsvp=decline" 
                           class="rsvp-card decline <?php echo ($current_rsvp && $current_rsvp['rsvp_status'] === 'decline') ? 'selected' : ''; ?>" 
                           style="text-decoration: none; color: inherit;">
                            <i class="fas fa-times-circle"></i>
                            <h4>NO, I CANNOT ATTEND</h4>
                            <?php if ($current_rsvp && $current_rsvp['rsvp_status'] === 'decline'): ?>
                                <small class="text-danger"><i class="fas fa-times me-1"></i>Declined</small>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <p class="text-center mb-0" style="color: #800000; font-size: 13px; font-weight: 600;">
                        <i class="fas fa-bolt me-1"></i>Your response will be recorded instantly!
                    </p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label"><strong>Your Message:</strong></label>
                        <textarea name="reply_content" class="form-control" rows="8" placeholder="Type your message here..." required autofocus></textarea>
                        <small class="text-muted">Share your thoughts, questions, or suggestions</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-send">
                            <i class="fas fa-paper-plane me-2"></i>Send Reply
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
