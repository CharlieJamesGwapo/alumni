<?php
include 'db_connect.php';
include 'log_activity.php';
if(!isset($_SESSION['login_id']))
    header('location:login.php');

// Get filters from URL parameters
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : null;
$action_type = isset($_GET['action_type']) ? $_GET['action_type'] : null;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;

// Get logs with enhanced filtering
$logs = get_activity_logs($user_id, $start_date, $end_date, $user_type, $action_type, $limit);

// Get statistics
$stats = get_activity_stats();
?>

<div class="container-fluid">
    <!-- Activity Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Logs</h5>
                    <h3><?php echo number_format($stats['total_logs']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Recent (24h)</h5>
                    <h3><?php echo number_format($stats['recent_24h']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Admin Actions</h5>
                    <h3><?php echo number_format($stats['by_type']['admin'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Registrar Actions</h5>
                    <h3><?php echo number_format($stats['by_type']['registrar'] ?? 0); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Activity Logs</h4>
            <div class="text-muted">
                <i class="fas fa-user"></i> <?php echo $_SESSION['login_name']; ?> | 
                <i class="fas fa-clock"></i> <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
        <div class="card-body">

            <!-- Enhanced Filters -->
            <form class="mb-4">
                <div class="row">
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User Type</label>
                        <select name="user_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="admin" <?php echo $user_type == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="registrar" <?php echo $user_type == 'registrar' ? 'selected' : ''; ?>>Registrar</option>
                            <option value="alumni" <?php echo $user_type == 'alumni' ? 'selected' : ''; ?>>Alumni</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Action</label>
                        <select name="action_type" class="form-control form-control-sm">
                            <option value="">All Actions</option>
                            <option value="LOGIN" <?php echo $action_type == 'LOGIN' ? 'selected' : ''; ?>>Login</option>
                            <option value="ALUMNI" <?php echo $action_type == 'ALUMNI' ? 'selected' : ''; ?>>Alumni Actions</option>
                            <option value="EVENT" <?php echo $action_type == 'EVENT' ? 'selected' : ''; ?>>Event Actions</option>
                            <option value="FORUM" <?php echo $action_type == 'FORUM' ? 'selected' : ''; ?>>Forum Actions</option>
                            <option value="COURSE" <?php echo $action_type == 'COURSE' ? 'selected' : ''; ?>>Course Actions</option>
                            <option value="JOB" <?php echo $action_type == 'JOB' ? 'selected' : ''; ?>>Job Actions</option>
                            <option value="USER" <?php echo $action_type == 'USER' ? 'selected' : ''; ?>>User Management</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Limit</label>
                        <select name="limit" class="form-control form-control-sm">
                            <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                            <option value="200" <?php echo $limit == 200 ? 'selected' : ''; ?>>200</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="dropdown d-grid">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="activity_export.php?format=csv<?php echo http_build_query(array_filter(['user_type'=>$user_type,'action_type'=>$action_type,'start_date'=>$start_date,'end_date'=>$end_date,'limit'=>$limit]), '&'); ?>">CSV</a></li>
                                <li><a class="dropdown-item" href="activity_export.php?format=json<?php echo http_build_query(array_filter(['user_type'=>$user_type,'action_type'=>$action_type,'start_date'=>$start_date,'end_date'=>$end_date,'limit'=>$limit]), '&'); ?>">JSON</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Enhanced Logs Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="logs-table">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 15%">Timestamp</th>
                            <th style="width: 15%">User</th>
                            <th style="width: 10%">Type</th>
                            <th style="width: 15%">Action</th>
                            <th style="width: 25%">Details</th>
                            <th style="width: 10%">IP</th>
                            <th style="width: 5%">Target</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (empty($logs)): 
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> No activity logs found matching your criteria.
                            </td>
                        </tr>
                        <?php 
                        else:
                            $i = 1;
                            foreach ($logs as $log): 
                                // User type badge colors
                                $type_colors = [
                                    'admin' => 'danger',
                                    'registrar' => 'warning',
                                    'alumni' => 'info'
                                ];
                                $badge_color = $type_colors[$log['user_type']] ?? 'secondary';
                                
                                // Action type colors
                                $action_colors = [
                                    'LOGIN' => 'success',
                                    'LOGOUT' => 'secondary',
                                    'VALIDATE_ALUMNI' => 'primary',
                                    'ARCHIVE_ALUMNI' => 'warning',
                                    'DELETE_ALUMNI' => 'danger',
                                    'CREATE_COURSE' => 'info',
                                    'UPDATE_COURSE' => 'warning',
                                    'DELETE_COURSE' => 'danger',
                                    'CREATE_EVENT' => 'info',
                                    'UPDATE_EVENT' => 'warning',
                                    'DELETE_EVENT' => 'danger',
                                    'CREATE_JOB' => 'info',
                                    'UPDATE_JOB' => 'warning',
                                    'DELETE_JOB' => 'danger',
                                    'CREATE_USER' => 'info',
                                    'UPDATE_USER' => 'warning',
                                    'DELETE_USER' => 'danger'
                                ];
                                $action_color = 'secondary';
                                foreach ($action_colors as $pattern => $color) {
                                    if (strpos($log['action_type'], $pattern) !== false) {
                                        $action_color = $color;
                                        break;
                                    }
                                }
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date("M d, Y", strtotime($log['timestamp'])); ?><br>
                                    <?php echo date("H:i:s", strtotime($log['timestamp'])); ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-<?php echo $badge_color; ?> me-2">
                                        <?php echo strtoupper($log['user_type']); ?>
                                    </span>
                                    <small><?php echo htmlspecialchars($log['user_name']); ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $badge_color; ?>">
                                    <?php echo strtoupper($log['user_type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $action_color; ?>">
                                    <?php echo htmlspecialchars($log['action_type']); ?>
                                </span>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($log['details']); ?></small>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo htmlspecialchars($log['ip_address']); ?></small>
                            </td>
                            <td>
                                <?php if ($log['target_id']): ?>
                                    <small class="text-info">
                                        <?php echo $log['target_type']; ?>:<?php echo $log['target_id']; ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">-</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.table th {
    font-weight: 600;
    font-size: 0.875rem;
}
.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}
.badge {
    font-size: 0.75rem;
}
</style>

<script>
$(document).ready(function() {
    $('#logs-table').DataTable({
        "order": [[ 1, "desc" ]],
        "pageLength": 25,
        "responsive": true,
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        "language": {
            "search": "Search logs:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ logs",
            "paginate": {
                "previous": "Previous",
                "next": "Next"
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [0, 6, 7] },
            { "className": "text-center", "targets": [0, 3, 7] }
        ]
    });
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        if (!$('#logs-table').DataTable().search()) {
            location.reload();
        }
    }, 30000);
});
</script>