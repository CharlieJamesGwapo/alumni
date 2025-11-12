-- Update activity_log table structure for comprehensive logging
-- Run this SQL to update your database

-- Drop existing table if it exists with old structure
DROP TABLE IF EXISTS `activity_log`;

-- Create updated activity_log table
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','registrar','alumni') DEFAULT 'alumni',
  `action_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_user_type` (`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some sample data to show the structure
INSERT INTO `activity_log` (`user_id`, `user_name`, `user_type`, `action_type`, `details`, `ip_address`, `timestamp`) VALUES
(1, 'Admin User', 'admin', 'LOGIN', 'Admin logged into the system', '127.0.0.1', NOW()),
(1, 'Admin User', 'admin', 'VALIDATE_ALUMNI', 'Validated alumni account ID: 123', '127.0.0.1', NOW()),
(2, 'Registrar User', 'registrar', 'LOGIN', 'Registrar logged into the system', '127.0.0.1', NOW()),
(2, 'Registrar User', 'registrar', 'ARCHIVE_ALUMNI', 'Archived alumni record ID: 456', '127.0.0.1', NOW());
