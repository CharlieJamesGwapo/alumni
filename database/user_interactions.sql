-- User interactions tables for likes and comments functionality

-- Table for user likes
CREATE TABLE IF NOT EXISTS `user_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User who liked',
  `target_user_id` int(11) NOT NULL COMMENT 'User being liked',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`user_id`, `target_user_id`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for user comments
CREATE TABLE IF NOT EXISTS `user_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User who commented',
  `target_user_id` int(11) NOT NULL COMMENT 'User being commented on',
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraints if alumnus_bio table exists
-- ALTER TABLE `user_likes` ADD CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;
-- ALTER TABLE `user_likes` ADD CONSTRAINT `fk_likes_target` FOREIGN KEY (`target_user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;
-- ALTER TABLE `user_comments` ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;
-- ALTER TABLE `user_comments` ADD CONSTRAINT `fk_comments_target` FOREIGN KEY (`target_user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;
