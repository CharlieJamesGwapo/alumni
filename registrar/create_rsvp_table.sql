-- Create table for Homecoming RSVP tracking
CREATE TABLE IF NOT EXISTS `homecoming_rsvp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL UNIQUE,
  `response` enum('attending','not_attending') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_alumni` (`alumni_id`),
  UNIQUE KEY `unique_token` (`token`),
  KEY `idx_alumni_id` (`alumni_id`),
  KEY `idx_response` (`response`),
  FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
