-- Add ID release tracking to alumnus_bio table
ALTER TABLE alumnus_bio ADD COLUMN id_release_count INT DEFAULT 0 COMMENT 'Number of times ID has been printed/released';

-- Create index for better performance
CREATE INDEX idx_id_release_count ON alumnus_bio(id_release_count);

-- Optional: Create a separate table for detailed release tracking (if needed for audit trail)
CREATE TABLE IF NOT EXISTS id_release_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_id INT NOT NULL,
    release_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    released_by VARCHAR(100),
    release_method ENUM('print_button', 'ctrl_p', 'manual') DEFAULT 'print_button',
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (alumni_id) REFERENCES alumnus_bio(id) ON DELETE CASCADE,
    INDEX idx_alumni_release (alumni_id, release_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
