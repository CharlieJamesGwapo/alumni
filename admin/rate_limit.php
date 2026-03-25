<?php
/**
 * Rate Limiting & Account Lockout Helper
 * Include this in login handlers to prevent brute force attacks.
 */

/**
 * Check if an IP is rate limited
 * @param mysqli $conn
 * @param string $ip
 * @param string $type 'admin', 'registrar', or 'alumni'
 * @param int $maxAttempts Max failed attempts in window
 * @param int $windowMinutes Time window in minutes
 * @return array ['blocked' => bool, 'remaining' => int, 'retry_after' => int seconds]
 */
function check_rate_limit($conn, $ip, $type = 'alumni', $maxAttempts = 5, $windowMinutes = 15) {
    // Check if table exists
    $check = $conn->query("SHOW TABLES LIKE 'login_attempts'");
    if (!$check || $check->num_rows == 0) {
        return ['blocked' => false, 'remaining' => $maxAttempts, 'retry_after' => 0];
    }

    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM login_attempts WHERE ip_address = ? AND attempt_type = ? AND success = 0 AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
    $stmt->bind_param("ssi", $ip, $type, $windowMinutes);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $attempts = (int)$row['cnt'];
    $remaining = max(0, $maxAttempts - $attempts);
    $blocked = $attempts >= $maxAttempts;

    $retry_after = 0;
    if ($blocked) {
        $stmt2 = $conn->prepare("SELECT attempted_at FROM login_attempts WHERE ip_address = ? AND attempt_type = ? AND success = 0 ORDER BY attempted_at ASC LIMIT 1");
        $stmt2->bind_param("ss", $ip, $type);
        $stmt2->execute();
        $first = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();
        if ($first) {
            $unlock_time = strtotime($first['attempted_at']) + ($windowMinutes * 60);
            $retry_after = max(0, $unlock_time - time());
        }
    }

    return ['blocked' => $blocked, 'remaining' => $remaining, 'retry_after' => $retry_after];
}

/**
 * Record a login attempt
 */
function record_login_attempt($conn, $ip, $username, $type = 'alumni', $success = false) {
    $check = $conn->query("SHOW TABLES LIKE 'login_attempts'");
    if (!$check || $check->num_rows == 0) return;

    $s = $success ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, username, attempt_type, success) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $ip, $username, $type, $s);
    $stmt->execute();
    $stmt->close();

    // If successful, clear previous failures for this IP+type
    if ($success) {
        $stmt2 = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND attempt_type = ? AND success = 0");
        $stmt2->bind_param("ss", $ip, $type);
        $stmt2->execute();
        $stmt2->close();
    }
}

/**
 * Check if a user account is locked
 */
function is_account_locked($conn, $table, $id) {
    $stmt = $conn->prepare("SELECT locked_until FROM `$table` WHERE id = ?");
    if (!$stmt) return false;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['locked_until']) && strtotime($row['locked_until']) > time()) {
        return true;
    }
    return false;
}

/**
 * Increment failed attempts and lock if threshold reached
 */
function increment_failed_attempts($conn, $table, $id, $maxFails = 5, $lockMinutes = 30) {
    $conn->query("UPDATE `$table` SET failed_attempts = COALESCE(failed_attempts, 0) + 1 WHERE id = $id");

    $r = $conn->query("SELECT failed_attempts FROM `$table` WHERE id = $id");
    if ($r && $row = $r->fetch_assoc()) {
        if ((int)$row['failed_attempts'] >= $maxFails) {
            $lock_until = date('Y-m-d H:i:s', strtotime("+$lockMinutes minutes"));
            $conn->query("UPDATE `$table` SET locked_until = '$lock_until' WHERE id = $id");
        }
    }
}

/**
 * Reset failed attempts on successful login
 */
function reset_failed_attempts($conn, $table, $id) {
    $conn->query("UPDATE `$table` SET failed_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = $id");
}

/**
 * Clean old login attempts (run periodically)
 */
function cleanup_old_attempts($conn, $daysOld = 7) {
    $conn->query("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL $daysOld DAY)");
}
