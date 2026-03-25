<?php
/**
 * Application Configuration
 * Update these values for your production environment.
 */

// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'alumni_db');
define('DB_PORT', 3306);
define('DB_SOCKET', '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');

// Uploads
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_DIR_PERMISSIONS', 0755);

// Security
define('OTP_MAX_ATTEMPTS', 5);
define('OTP_LOCKOUT_MINUTES', 15);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 30);
define('REMEMBER_TOKEN_DAYS', 30);
define('CSRF_TOKEN_LENGTH', 32);
