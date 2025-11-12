<?php
/**
 * Activity Log Cleanup and Archiving System
 * Manages old log entries to maintain database performance
 */

include 'db_connect.php';
include 'log_activity.php';

if(!isset($_SESSION['login_id'])) {
    header('location:login.php');
    exit;
}

// Handle cleanup actions
if ($_POST['action'] ?? '' === 'cleanup') {
    $days_old = intval($_POST['days_old'] ?? 90);
    $action_performed = false;
    
    if ($days_old >= 30) { // Safety check - don't delete logs newer than 30 days
        $cutoff_date = date('Y-m-d', strtotime("-$days_old days"));
        
        // Archive old logs to a separate table first
        $archive_query = "INSERT INTO activity_log_archive 
                         SELECT * FROM activity_log 
                         WHERE DATE(timestamp) < ?";
        
        $stmt = $conn->prepare($archive_query);
        $stmt->bind_param('s', $cutoff_date);
        
        if ($stmt->execute()) {
            $archived_count = $stmt->affected_rows;
            
            // Now delete from main table
            $delete_query = "DELETE FROM activity_log WHERE DATE(timestamp) < ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param('s', $cutoff_date);
            
            if ($delete_stmt->execute()) {
                $deleted_count = $delete_stmt->affected_rows;
                
                // Log the cleanup action
                log_activity($_SESSION['login_id'], 'CLEANUP_LOGS', 
                    "Archived $archived_count and deleted $deleted_count log entries older than $days_old days");
                
                $success_message = "Successfully cleaned up $deleted_count log entries (archived $archived_count)";
                $action_performed = true;
            }
        }
    }
}

// Get current statistics
$stats_query = "SELECT 
    COUNT(*) as total_logs,
    MIN(timestamp) as oldest_log,
    MAX(timestamp) as newest_log,
    COUNT(CASE WHEN timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30_days,
    COUNT(CASE WHEN timestamp >= DATE_SUB(NOW(), INTERVAL 90 DAY) THEN 1 END) as last_90_days,
    COUNT(CASE WHEN timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY) THEN 1 END) as older_90_days
FROM activity_log";

$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Activity Log Cleanup & Maintenance</h4>
        </div>
        <div class="card-body">
            
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <!-- Current Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5>Total Logs</h5>
                            <h3><?php echo number_format($stats['total_logs']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>Last 30 Days</h5>
                            <h3><?php echo number_format($stats['last_30_days']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>Last 90 Days</h5>
                            <h3><?php echo number_format($stats['last_90_days']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h5>Older than 90 Days</h5>
                            <h3><?php echo number_format($stats['older_90_days']); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Log Date Range -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Oldest Log Entry</h6>
                            <p class="card-text">
                                <?php echo $stats['oldest_log'] ? date('F j, Y g:i A', strtotime($stats['oldest_log'])) : 'No logs found'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Newest Log Entry</h6>
                            <p class="card-text">
                                <?php echo $stats['newest_log'] ? date('F j, Y g:i A', strtotime($stats['newest_log'])) : 'No logs found'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cleanup Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cleanup Old Logs</h5>
                </div>
                <div class="card-body">
                    <form method="POST" onsubmit="return confirm('Are you sure you want to cleanup old log entries? This action cannot be undone.');">
                        <input type="hidden" name="action" value="cleanup">
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Delete logs older than:</label>
                                <select name="days_old" class="form-control" required>
                                    <option value="">Select timeframe</option>
                                    <option value="90">90 days (3 months)</option>
                                    <option value="180">180 days (6 months)</option>
                                    <option value="365">365 days (1 year)</option>
                                    <option value="730">730 days (2 years)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Cleanup Logs
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info mb-0">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        Logs will be archived before deletion for safety.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recommendations -->
            <div class="mt-4">
                <h6>Maintenance Recommendations:</h6>
                <ul class="list-group">
                    <?php if ($stats['older_90_days'] > 1000): ?>
                    <li class="list-group-item list-group-item-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        You have <?php echo number_format($stats['older_90_days']); ?> logs older than 90 days. Consider cleanup to improve performance.
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($stats['total_logs'] > 10000): ?>
                    <li class="list-group-item list-group-item-info">
                        <i class="fas fa-database"></i>
                        Large log database detected (<?php echo number_format($stats['total_logs']); ?> entries). Regular cleanup recommended.
                    </li>
                    <?php endif; ?>
                    
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i>
                        Regular cleanup every 3-6 months helps maintain optimal database performance.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
