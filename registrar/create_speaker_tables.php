<?php
/**
 * Database Setup for Guest Speaker Invitation System
 * Run this file once to create the necessary database tables
 */

session_start();
require_once '../admin/db_connect.php';

// Only allow Registrar (type = 4) to run this
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    die('Unauthorized access');
}

$success_messages = [];
$error_messages = [];

// 1. Create speaker_rsvp table for tracking RSVP responses
$speaker_rsvp_sql = "CREATE TABLE IF NOT EXISTS `speaker_rsvp` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `alumni_id` INT(11) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `response` ENUM('pending', 'accept', 'decline') DEFAULT 'pending',
    `event_date` VARCHAR(100) DEFAULT NULL,
    `event_time` VARCHAR(100) DEFAULT NULL,
    `event_venue` VARCHAR(255) DEFAULT NULL,
    `event_topic` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_token` (`token`),
    UNIQUE KEY `unique_alumni` (`alumni_id`),
    KEY `idx_response` (`response`),
    KEY `idx_alumni` (`alumni_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($speaker_rsvp_sql)) {
    $success_messages[] = "✓ Table 'speaker_rsvp' created successfully";
} else {
    $error_messages[] = "✗ Error creating 'speaker_rsvp' table: " . $conn->error;
}

// 2. Create speaker_invitations table for logging all invitations sent
$speaker_invitations_sql = "CREATE TABLE IF NOT EXISTS `speaker_invitations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `alumni_id` INT(11) NOT NULL,
    `event_date` VARCHAR(100) DEFAULT NULL,
    `event_time` VARCHAR(100) DEFAULT NULL,
    `event_venue` VARCHAR(255) DEFAULT NULL,
    `event_topic` VARCHAR(255) DEFAULT NULL,
    `subject` VARCHAR(255) DEFAULT NULL,
    `content` TEXT DEFAULT NULL,
    `invited_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `invited_by` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_alumni` (`alumni_id`),
    KEY `idx_invited_at` (`invited_at`),
    KEY `idx_invited_by` (`invited_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($speaker_invitations_sql)) {
    $success_messages[] = "✓ Table 'speaker_invitations' created successfully";
} else {
    $error_messages[] = "✗ Error creating 'speaker_invitations' table: " . $conn->error;
}

// 3. Create speaker_events table for managing speaker events
$speaker_events_sql = "CREATE TABLE IF NOT EXISTS `speaker_events` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `event_name` VARCHAR(255) NOT NULL,
    `event_date` VARCHAR(100) NOT NULL,
    `event_time` VARCHAR(100) NOT NULL,
    `event_venue` VARCHAR(255) NOT NULL,
    `event_topic` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `status` ENUM('planning', 'invitations_sent', 'confirmed', 'completed', 'cancelled') DEFAULT 'planning',
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($speaker_events_sql)) {
    $success_messages[] = "✓ Table 'speaker_events' created successfully";
} else {
    $error_messages[] = "✗ Error creating 'speaker_events' table: " . $conn->error;
}

// 4. Add indexes for better performance
$indexes = [
    "ALTER TABLE `speaker_rsvp` ADD INDEX IF NOT EXISTS `idx_created_at` (`created_at`)",
    "ALTER TABLE `speaker_invitations` ADD INDEX IF NOT EXISTS `idx_event_date` (`event_date`)"
];

foreach ($indexes as $index_sql) {
    if ($conn->query($index_sql)) {
        $success_messages[] = "✓ Index added successfully";
    } else {
        // Ignore errors for indexes that already exist
        if (strpos($conn->error, 'Duplicate key name') === false) {
            $error_messages[] = "✗ Error adding index: " . $conn->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Guest Speaker System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .setup-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header i {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 20px;
        }
        .message-box {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .message-box i {
            margin-right: 10px;
            font-size: 20px;
        }
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="header">
            <i class="fas fa-database"></i>
            <h2>Guest Speaker System - Database Setup</h2>
            <p class="text-muted">Database tables creation and configuration</p>
        </div>

        <?php if (!empty($success_messages)): ?>
            <h5 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Success Messages</h5>
            <?php foreach ($success_messages as $message): ?>
                <div class="message-box success-box">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($error_messages)): ?>
            <h5 class="text-danger mb-3"><i class="fas fa-exclamation-circle me-2"></i>Error Messages</h5>
            <?php foreach ($error_messages as $message): ?>
                <div class="message-box error-box">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="summary">
            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Database Tables Created</h5>
            <ul class="list-unstyled mb-0">
                <li class="mb-2">
                    <i class="fas fa-table text-primary me-2"></i>
                    <strong>speaker_rsvp</strong> - Tracks RSVP responses from invited speakers
                </li>
                <li class="mb-2">
                    <i class="fas fa-table text-primary me-2"></i>
                    <strong>speaker_invitations</strong> - Logs all speaker invitations sent
                </li>
                <li class="mb-2">
                    <i class="fas fa-table text-primary me-2"></i>
                    <strong>speaker_events</strong> - Manages speaker events and their details
                </li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="alumni.php" class="btn btn-custom">
                <i class="fas fa-arrow-left me-2"></i>Back to Alumni Management
            </a>
        </div>

        <div class="alert alert-info mt-4">
            <i class="fas fa-lightbulb me-2"></i>
            <strong>Note:</strong> This setup only needs to be run once. The tables are now ready for the Guest Speaker Invitation System.
        </div>
    </div>
</body>
</html>
