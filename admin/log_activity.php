<?php
/**
 * Enhanced Activity Logging System
 * Tracks actions by Admin, Registrar, and Alumni users
 */

/**
 * Log user activity with comprehensive details
 * @param int $user_id User ID performing the action
 * @param string $action_type Type of action performed
 * @param string $details Additional details about the action
 * @param int $target_id Optional ID of target record affected
 * @param string $target_type Optional type of target (alumni, event, etc.)
 * @return bool Success status
 */
function log_activity($user_id, $action_type, $details = '', $target_id = null, $target_type = null) {
    global $conn;
    
    // Determine user type and get user info
    $user_info = get_user_info($user_id);
    if (!$user_info) {
        error_log("Activity Log: User ID $user_id not found");
        return false;
    }
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $timestamp = date('Y-m-d H:i:s');
    
    // Prepare the comprehensive query
    $sql = "INSERT INTO activity_log (user_id, user_name, user_type, action_type, details, target_id, target_type, ip_address, user_agent, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Activity Log SQL Prepare Error: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("issssissss", 
        $user_id, 
        $user_info['name'], 
        $user_info['type'], 
        $action_type, 
        $details, 
        $target_id, 
        $target_type, 
        $ip_address, 
        $user_agent, 
        $timestamp
    );
    
    try {
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } catch (Exception $e) {
        error_log("Activity Log Error: " . $e->getMessage());
        $stmt->close();
        return false;
    }
}

/**
 * Get user information from appropriate table
 * @param int $user_id User ID
 * @return array|false User info or false if not found
 */
function get_user_info($user_id) {
    global $conn;
    
    // Check admin/registrar users table first
    $admin_query = $conn->prepare("SELECT name, type FROM users WHERE id = ?");
    $admin_query->bind_param("i", $user_id);
    $admin_query->execute();
    $admin_result = $admin_query->get_result();
    
    if ($admin_result->num_rows > 0) {
        $user = $admin_result->fetch_assoc();
        $admin_query->close();
        return [
            'name' => $user['name'],
            'type' => $user['type'] == 1 ? 'admin' : 'registrar'
        ];
    }
    $admin_query->close();
    
    // Check alumni table
    $alumni_query = $conn->prepare("SELECT CONCAT(firstname, ' ', lastname) as name FROM alumnus_bio WHERE id = ?");
    $alumni_query->bind_param("i", $user_id);
    $alumni_query->execute();
    $alumni_result = $alumni_query->get_result();
    
    if ($alumni_result->num_rows > 0) {
        $user = $alumni_result->fetch_assoc();
        $alumni_query->close();
        return [
            'name' => $user['name'],
            'type' => 'alumni'
        ];
    }
    $alumni_query->close();
    
    return false;
}

/**
 * Quick logging functions for common actions
 */
function log_login($user_id, $user_type = 'admin') {
    return log_activity($user_id, 'LOGIN', "User logged into the system");
}

function log_logout($user_id) {
    return log_activity($user_id, 'LOGOUT', "User logged out of the system");
}

function log_alumni_action($user_id, $action, $alumni_id, $details = '') {
    $action_map = [
        'validate' => 'VALIDATE_ALUMNI',
        'archive' => 'ARCHIVE_ALUMNI',
        'restore' => 'RESTORE_ALUMNI',
        'delete' => 'DELETE_ALUMNI',
        'edit' => 'EDIT_ALUMNI'
    ];
    
    $action_type = $action_map[$action] ?? strtoupper($action);
    $full_details = $details ?: "$action alumni record";
    
    return log_activity($user_id, $action_type, $full_details, $alumni_id, 'alumni');
}

function log_event_action($user_id, $action, $event_id, $details = '') {
    $action_type = 'EVENT_' . strtoupper($action);
    $full_details = $details ?: "$action event";
    
    return log_activity($user_id, $action_type, $full_details, $event_id, 'event');
}

function log_forum_action($user_id, $action, $forum_id, $details = '') {
    $action_type = 'FORUM_' . strtoupper($action);
    $full_details = $details ?: "$action forum post";
    
    return log_activity($user_id, $action_type, $full_details, $forum_id, 'forum');
}

function log_course_action($user_id, $action, $course_id, $details = '') {
    $action_map = [
        'create' => 'CREATE_COURSE',
        'update' => 'UPDATE_COURSE', 
        'delete' => 'DELETE_COURSE',
        'edit' => 'EDIT_COURSE'
    ];
    
    $action_type = $action_map[$action] ?? strtoupper($action);
    $full_details = $details ?: "$action course";
    
    return log_activity($user_id, $action_type, $full_details, $course_id, 'course');
}

/**
 * Get activity logs with enhanced filtering
 * @param int $user_id Filter by user ID
 * @param string $start_date Start date filter
 * @param string $end_date End date filter
 * @param string $user_type Filter by user type (admin, registrar, alumni)
 * @param string $action_type Filter by action type
 * @param int $limit Number of records to return
 * @return array Array of log records
 */
function get_activity_logs($user_id = null, $start_date = null, $end_date = null, $user_type = null, $action_type = null, $limit = 100) {
    global $conn;
    
    $where_clauses = array();
    $params = array();
    $types = "";
    
    if ($user_id) {
        $where_clauses[] = "user_id = ?";
        $params[] = $user_id;
        $types .= "i";
    }
    
    if ($user_type) {
        $where_clauses[] = "user_type = ?";
        $params[] = $user_type;
        $types .= "s";
    }
    
    if ($action_type) {
        $where_clauses[] = "action_type LIKE ?";
        $params[] = "%$action_type%";
        $types .= "s";
    }
    
    if ($start_date) {
        $where_clauses[] = "DATE(timestamp) >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    
    if ($end_date) {
        $where_clauses[] = "DATE(timestamp) <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
    
    $sql = "SELECT * FROM activity_log";
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $sql .= " ORDER BY timestamp DESC LIMIT ?";
    $params[] = $limit;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Get Activity Logs SQL Error: " . $conn->error);
        return [];
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $logs = array();
    
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    
    $stmt->close();
    return $logs;
}

/**
 * Get activity statistics
 * @return array Statistics about activity logs
 */
function get_activity_stats() {
    global $conn;
    
    $stats = [];
    
    // Total logs
    $total_query = $conn->query("SELECT COUNT(*) as total FROM activity_log");
    $stats['total_logs'] = $total_query->fetch_assoc()['total'];
    
    // Logs by user type
    $type_query = $conn->query("SELECT user_type, COUNT(*) as count FROM activity_log GROUP BY user_type");
    while ($row = $type_query->fetch_assoc()) {
        $stats['by_type'][$row['user_type']] = $row['count'];
    }
    
    // Recent activity (last 24 hours)
    $recent_query = $conn->query("SELECT COUNT(*) as recent FROM activity_log WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $stats['recent_24h'] = $recent_query->fetch_assoc()['recent'];
    
    // Top actions
    $action_query = $conn->query("SELECT action_type, COUNT(*) as count FROM activity_log GROUP BY action_type ORDER BY count DESC LIMIT 5");
    $stats['top_actions'] = [];
    while ($row = $action_query->fetch_assoc()) {
        $stats['top_actions'][] = $row;
    }
    
    return $stats;
}
?>