<?php
include 'db_connect.php';
include 'log_activity.php';

// Get filters from URL parameters
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$start_date = isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : null;
$end_date = isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;

// Get logs
$logs = get_activity_logs($user_id, $start_date, $end_date, $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="container-fluid">
        <h2>Activity Logs</h2>
        
        <!-- Filters -->
        <form class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>" placeholder="Start Date">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>" placeholder="End Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
        
        <!-- Logs Table -->
        <table class="table table-bordered table-striped" id="logs-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User ID</th>
                    <th>Action Type</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5">No logs found</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                        <td><?php echo htmlspecialchars($log['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($log['action_type']); ?></td>
                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#logs-table').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 25
            });
        });
    </script>
</body>
</html>
