<?php
/**
 * Guest Speaker RSVP Response Handler
 * Handles accept/decline responses from invited speakers
 */

date_default_timezone_set('Asia/Manila');
session_start();
require_once '../admin/db_connect.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$response = isset($_GET['response']) ? trim($_GET['response']) : '';

if (empty($token) || !in_array($response, ['accept', 'decline'])) {
    header('Location: ../index.php');
    exit();
}

// Validate token and get RSVP record
$stmt = $conn->prepare("SELECT sr.*, ab.firstname, ab.lastname, ab.email 
                        FROM speaker_rsvp sr 
                        JOIN alumnus_bio ab ON sr.alumni_id = ab.id 
                        WHERE sr.token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "Invalid or expired invitation link.";
} else {
    $rsvp = $result->fetch_assoc();
    
    // Update RSVP response
    $update_stmt = $conn->prepare("UPDATE speaker_rsvp SET response = ?, updated_at = NOW() WHERE token = ?");
    $update_stmt->bind_param("ss", $response, $token);
    
    if ($update_stmt->execute()) {
        $success = true;
        $alumni_name = $rsvp['firstname'] . ' ' . $rsvp['lastname'];
    } else {
        $error = "Failed to update your response. Please try again.";
    }
    $update_stmt->close();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP Response - Guest Speaker Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .rsvp-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .rsvp-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .rsvp-header i {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .rsvp-body {
            padding: 40px;
        }
        .success-message {
            text-align: center;
            padding: 30px;
        }
        .success-message i {
            font-size: 100px;
            margin-bottom: 20px;
        }
        .accept-icon {
            color: #10b981;
        }
        .decline-icon {
            color: #ef4444;
        }
        .event-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .detail-item i {
            width: 30px;
            color: #3b82f6;
        }
        .btn-home {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: white;
        }
        .error-message {
            background: #fee2e2;
            border: 2px solid #ef4444;
            border-radius: 10px;
            padding: 20px;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="rsvp-container">
        <div class="rsvp-header">
            <i class="fas fa-microphone-alt"></i>
            <h2>Guest Speaker Invitation</h2>
            <p>MOIST Alumni Office</p>
        </div>
        
        <div class="rsvp-body">
            <?php if (isset($error)): ?>
                <div class="error-message text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>Oops!</h4>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php elseif (isset($success)): ?>
                <div class="success-message">
                    <?php if ($response === 'accept'): ?>
                        <i class="fas fa-check-circle accept-icon"></i>
                        <h3 class="text-success mb-3">Thank You for Accepting!</h3>
                        <p class="lead">Dear <?php echo htmlspecialchars($alumni_name); ?>,</p>
                        <p>We're thrilled that you've accepted our invitation to be a guest speaker! Your expertise and insights will be invaluable to our students.</p>
                        
                        <?php if (!empty($rsvp['event_date'])): ?>
                        <div class="event-details">
                            <h5 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Event Details</h5>
                            <div class="detail-item">
                                <i class="fas fa-calendar-day"></i>
                                <span><strong>Date:</strong> <?php echo htmlspecialchars($rsvp['event_date']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span><strong>Time:</strong> <?php echo htmlspecialchars($rsvp['event_time']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><strong>Venue:</strong> <?php echo htmlspecialchars($rsvp['event_venue']); ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-lightbulb"></i>
                                <span><strong>Topic:</strong> <?php echo htmlspecialchars($rsvp['event_topic']); ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Our team will contact you soon with more details and arrangements.
                        </div>
                    <?php else: ?>
                        <i class="fas fa-times-circle decline-icon"></i>
                        <h3 class="text-danger mb-3">We Understand</h3>
                        <p class="lead">Dear <?php echo htmlspecialchars($alumni_name); ?>,</p>
                        <p>Thank you for your response. We understand that you're unable to accept this invitation at this time.</p>
                        <p>We hope to have the opportunity to work with you in future events.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="btn-home">
                    <i class="fas fa-home me-2"></i>Return to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>
