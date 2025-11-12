-- Create password reset tables if they don't exist
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `contact_index` (`contact`),
  KEY `user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alter the alumni table to ensure it has the necessary columns
ALTER TABLE `alumnus_bio` 
ADD COLUMN IF NOT EXISTS `email` varchar(200) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `contact` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `password` varchar(255) DEFAULT NULL;
