<?php
session_start();
require_once '../admin/db_connect.php';

// Get token and response from URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$response = isset($_GET['response']) ? trim($_GET['response']) : '';

if (empty($token)) {
    die('Invalid RSVP link');
}

// Validate token and get alumni info
$stmt = $conn->prepare("
    SELECT hr.*, ab.firstname, ab.lastname, ab.email, c.course 
    FROM homecoming_rsvp hr 
    JOIN alumnus_bio ab ON hr.alumni_id = ab.id 
    LEFT JOIN courses c ON ab.course_id = c.id 
    WHERE hr.token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Invalid or expired RSVP link');
}

$rsvpData = $result->fetch_assoc();
$stmt->close();

// Process response if provided
$message = '';
$messageType = '';
if (!empty($response) && in_array($response, ['accept', 'decline'])) {
    $responseValue = ($response === 'accept') ? 'attending' : 'not_attending';
    
    // Update RSVP response
    $stmt = $conn->prepare("UPDATE homecoming_rsvp SET response = ?, responded_at = NOW() WHERE token = ?");
    $stmt->bind_param("ss", $responseValue, $token);
    
    if ($stmt->execute()) {
        if ($response === 'accept') {
            $message = "Thank you for confirming your attendance! We're excited to see you at the Homecoming 2026.";
            $messageType = 'success';
        } else {
            $message = "Thank you for letting us know. We'll miss you at this year's homecoming, but we hope to see you at future events!";
            $messageType = 'info';
        }
    } else {
        $message = "There was an error processing your response. Please try again.";
        $messageType = 'error';
    }
    $stmt->close();
    
    // Refresh data to show updated status
    $stmt = $conn->prepare("
        SELECT hr.*, ab.firstname, ab.lastname, ab.email, c.course 
        FROM homecoming_rsvp hr 
        JOIN alumnus_bio ab ON hr.alumni_id = ab.id 
        LEFT JOIN courses c ON ab.course_id = c.id 
        WHERE hr.token = ?
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $rsvpData = $result->fetch_assoc();
    $stmt->close();
}

$fullName = $rsvpData['firstname'] . ' ' . $rsvpData['lastname'];
$course = $rsvpData['course'] ?? 'Unknown Course';
$currentResponse = $rsvpData['response'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP - MOIST Alumni Homecoming 2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #800000;
            --primary-dark: #600000;
            --accent: #ffd700;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: var(--accent);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .main-content {
            max-width: 800px;
            margin: -50px auto 0;
            position: relative;
            z-index: 10;
            padding: 0 1rem;
        }
        
        .rsvp-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 2rem;
            text-align: center;
            border-bottom: 3px solid var(--primary);
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .event-info {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 5px solid #ffc107;
        }
        
        .rsvp-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .btn-rsvp {
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            min-width: 180px;
            text-align: center;
        }
        
        .btn-accept {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-accept:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            color: white;
        }
        
        .btn-decline {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-decline:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-attending {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .status-not-attending {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .rsvp-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-rsvp {
                width: 100%;
                max-width: 300px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-graduation-cap me-3"></i>Alumni Homecoming 2026</h1>
            <p class="lead mb-0">Misamis Oriental Institute of Science and Technology</p>
        </div>
    </div>

    <div class="main-content">
        <div class="rsvp-card">
            <div class="card-header">
                <h2 class="text-primary mb-3">
                    <i class="fas fa-envelope-open-text me-2"></i>
                    RSVP Confirmation
                </h2>
                <h4 class="text-muted">Welcome, <?php echo htmlspecialchars($fullName); ?>!</h4>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($course); ?></p>
            </div>
            
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : ($messageType === 'info' ? 'info' : 'danger'); ?> mb-4">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'info' ? 'info-circle' : 'exclamation-triangle'); ?> me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="event-info">
                    <h5 class="text-warning mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Event Details
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Date:</strong> February 14-15, 2026
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Time:</strong> 6:00 PM - 11:00 PM
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Venue:</strong> MOIST Grand Auditorium
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Theme:</strong> "Reconnect, Reminisce, Rejoice"
                        </div>
                    </div>
                </div>

                <?php if (!empty($currentResponse)): ?>
                    <div class="text-center mb-4">
                        <h5>Your Current Response:</h5>
                        <?php if ($currentResponse === 'attending'): ?>
                            <span class="status-badge status-attending">
                                <i class="fas fa-check-circle me-2"></i>
                                Attending - We're excited to see you!
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-not-attending">
                                <i class="fas fa-times-circle me-2"></i>
                                Not Attending - We'll miss you!
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-muted">Want to change your response?</p>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <h5 class="text-primary">Will you be joining us for this special celebration?</h5>
                        <p class="text-muted">Please let us know so we can prepare accordingly.</p>
                    </div>
                <?php endif; ?>

                <div class="rsvp-buttons">
                    <a href="?token=<?php echo urlencode($token); ?>&response=accept" class="btn-rsvp btn-accept">
                        <i class="fas fa-check me-2"></i>
                        Yes, I'll Attend!
                    </a>
                    <a href="?token=<?php echo urlencode($token); ?>&response=decline" class="btn-rsvp btn-decline">
                        <i class="fas fa-times me-2"></i>
                        Can't Make It
                    </a>
                </div>

                <div class="text-center mt-4">
                    <div class="alert alert-light">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Need Help?
                        </h6>
                        <p class="mb-1"><strong>MOIST Alumni Office</strong></p>
                        <p class="mb-1">📧 alumni@moist.edu.ph | 📱 (088) 123-4567</p>
                        <p class="mb-0 small text-muted">
                            You can change your response anytime by clicking this link again.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
