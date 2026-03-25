<?php
include 'db_connect.php';
include 'log_activity.php';

// Real-time statistics from database
function safe_count($conn, $sql) {
    $r = $conn->query($sql);
    return $r ? ($r->fetch_assoc()['count'] ?? 0) : 0;
}

$alumni_count = safe_count($conn, "SELECT COUNT(*) as count FROM alumnus_bio WHERE status = 1");
$pending_alumni = safe_count($conn, "SELECT COUNT(*) as count FROM alumnus_bio WHERE status = 0");
$total_alumni = safe_count($conn, "SELECT COUNT(*) as count FROM alumnus_bio");
$jobs_count = safe_count($conn, "SELECT COUNT(*) as count FROM careers");
$events_count = safe_count($conn, "SELECT COUNT(*) as count FROM events WHERE DATE(schedule) >= CURDATE()");
$total_events = safe_count($conn, "SELECT COUNT(*) as count FROM events");
$past_events = $total_events - $events_count;
$users_count = safe_count($conn, "SELECT COUNT(*) as count FROM users WHERE type IN (1, 4)");
$courses_count = safe_count($conn, "SELECT COUNT(*) as count FROM courses");
$forum_count = safe_count($conn, "SELECT COUNT(*) as count FROM forum_topics");
$total_users_all = safe_count($conn, "SELECT COUNT(*) as count FROM users");

// Recent activities
$recent_activities = $conn->query("SELECT * FROM activity_log ORDER BY timestamp DESC LIMIT 8");

// Recent alumni registrations
$recent_alumni = $conn->query("SELECT id, firstname, lastname, course, batch, status, date_created FROM alumnus_bio ORDER BY id DESC LIMIT 5");

// Upcoming events
$upcoming_events = $conn->query("SELECT id, title, schedule FROM events WHERE DATE(schedule) >= CURDATE() ORDER BY schedule ASC LIMIT 5");
?>

<style>
    .dashboard-header { margin-bottom: 1.5rem; }
    .dashboard-header h4 { color: #1e293b; font-weight: 700; font-size: 1.5rem; margin-bottom: 0.25rem; }
    .dashboard-header p { color: #94a3b8; font-size: 0.85rem; margin: 0; }

    .stat-card {
        position: relative; overflow: hidden; border-radius: 12px;
        border: none; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
    .stat-card .card-body { padding: 1.25rem 1.5rem; position: relative; z-index: 1; }
    .stat-card .stat-icon-bg {
        position: absolute; right: -5px; bottom: -10px;
        font-size: 4.5rem; opacity: 0.15; color: white; z-index: 0;
    }
    .stat-card .stat-icon-circle {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: white; background: rgba(255,255,255,0.25) !important; flex-shrink: 0;
    }
    .stat-card .stat-number { font-size: 2rem; font-weight: 700; color: white; line-height: 1; }
    .stat-card .stat-label { font-size: 0.82rem; color: rgba(255,255,255,0.85); margin-top: 0.35rem; font-weight: 500; }
    .stat-card .stat-sub { font-size: 0.72rem; color: rgba(255,255,255,0.6); margin-top: 0.2rem; }

    .dash-card {
        background: #ffffff; border: 1px solid #e2e8f0;
        border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .dash-card .card-body { padding: 1.25rem 1.5rem; }
    .dash-card .card-title-custom {
        color: #1e293b; font-size: 0.95rem; font-weight: 600;
        margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;
    }
    .dash-card .card-title-custom i { color: #64748b; font-size: 0.85rem; }

    .activity-item {
        padding: 0.75rem 1rem; border-left: 3px solid #4f46e5;
        margin-bottom: 0.4rem; background: #f8fafc;
        border-radius: 0 8px 8px 0; transition: background 0.2s;
    }
    .activity-item:hover { background: #f1f5f9; }
    .activity-item:nth-child(even) { border-left-color: #dc2626; }
    .activity-item:nth-child(3n) { border-left-color: #059669; }
    .activity-item:nth-child(4n) { border-left-color: #d97706; }
    .activity-time { font-size: 0.73rem; color: #94a3b8; }
    .activity-action { font-weight: 600; color: #1e293b; font-size: 0.85rem; margin-top: 1px; }
    .activity-item small { color: #64748b !important; font-size: 0.76rem; }

    .quick-link {
        display: flex; align-items: center; gap: 12px;
        padding: 0.75rem 1rem; border-radius: 8px; background: #f8fafc;
        border: 1px solid #e2e8f0; text-decoration: none; color: #1e293b;
        transition: all 0.15s ease; font-size: 0.88rem; font-weight: 500;
    }
    .quick-link:hover { background: #eff6ff; border-color: #4f46e5; color: #4f46e5; text-decoration: none; }
    .quick-link i { width: 20px; text-align: center; color: #64748b; }
    .quick-link:hover i { color: #4f46e5; }

    .upcoming-event-item {
        display: flex; align-items: center; gap: 12px;
        padding: 0.65rem 0; border-bottom: 1px solid #f1f5f9;
    }
    .upcoming-event-item:last-child { border-bottom: none; }
    .upcoming-event-date {
        background: #4f46e5; color: white; border-radius: 8px;
        padding: 6px 10px; text-align: center; min-width: 50px; flex-shrink: 0;
    }
    .upcoming-event-date .day { display: block; font-size: 1.1rem; font-weight: 700; line-height: 1; }
    .upcoming-event-date .month { display: block; font-size: 0.65rem; text-transform: uppercase; opacity: 0.8; }

    .alumni-row { padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
    .alumni-row:last-child { border-bottom: none; }

    @media screen and (max-width: 768px) {
        .stat-card .stat-number { font-size: 1.6rem; }
        .stat-card .stat-icon-circle { width: 38px; height: 38px; font-size: 0.95rem; }
        .dashboard-header h4 { font-size: 1.25rem; }
    }
    @media screen and (max-width: 576px) {
        .stat-card .stat-number { font-size: 1.4rem; }
        .stat-card .stat-label { font-size: 0.75rem; }
        .stat-card .card-body { padding: 1rem; }
    }
</style>

<div class="container-fluid">
    <div class="dashboard-header">
        <h4>Welcome back, <?php echo htmlspecialchars($_SESSION['login_name']); ?></h4>
        <p><i class="fa-regular fa-clock"></i> Dashboard updated: <span id="update-time"><?php echo date('M d, Y h:i A'); ?></span></p>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-circle"><i class="fa-solid fa-users"></i></div>
                    </div>
                    <div class="stat-number"><?php echo $alumni_count; ?></div>
                    <div class="stat-label">Verified Alumni</div>
                    <?php if($pending_alumni > 0): ?>
                    <div class="stat-sub"><?php echo $pending_alumni; ?> pending verification</div>
                    <?php endif; ?>
                    <div class="stat-icon-bg"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #dc2626, #e11d48);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-circle"><i class="fa-solid fa-briefcase"></i></div>
                    </div>
                    <div class="stat-number"><?php echo $jobs_count; ?></div>
                    <div class="stat-label">Job Postings</div>
                    <div class="stat-icon-bg"><i class="fa-solid fa-briefcase"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #0891b2, #0284c7);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-circle"><i class="fa-solid fa-calendar-days"></i></div>
                    </div>
                    <div class="stat-number"><?php echo $events_count; ?></div>
                    <div class="stat-label">Upcoming Events</div>
                    <div class="stat-sub"><?php echo $total_events; ?> total events</div>
                    <div class="stat-icon-bg"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card" style="background: linear-gradient(135deg, #059669, #0d9488);">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-circle"><i class="fa-solid fa-graduation-cap"></i></div>
                    </div>
                    <div class="stat-number"><?php echo $courses_count; ?></div>
                    <div class="stat-label">Courses</div>
                    <div class="stat-sub"><?php echo $forum_count; ?> forum topics</div>
                    <div class="stat-icon-bg"><i class="fa-solid fa-graduation-cap"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-3">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recent Activity -->
            <div class="dash-card mb-3">
                <div class="card-body">
                    <div class="card-title-custom">
                        <i class="fa-solid fa-clock-rotate-left"></i> Recent Activity
                    </div>
                    <?php if($recent_activities && $recent_activities->num_rows > 0): ?>
                        <?php while($activity = $recent_activities->fetch_assoc()): ?>
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="activity-action"><?php echo htmlspecialchars($activity['action_type']); ?></div>
                                    <small>By: <?php echo htmlspecialchars($activity['user_name']); ?> &mdash; <?php echo htmlspecialchars($activity['details']); ?></small>
                                </div>
                                <div class="activity-time text-nowrap ms-2">
                                    <?php echo date('M d, h:i A', strtotime($activity['timestamp'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <a href="index.php?page=activity_log" class="btn btn-sm btn-outline-primary mt-2">View all activity</a>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fa-regular fa-folder-open" style="font-size:2rem;display:block;margin-bottom:0.5rem;opacity:0.4;"></i>
                            No recent activity
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Alumni -->
            <div class="dash-card">
                <div class="card-body">
                    <div class="card-title-custom">
                        <i class="fa-solid fa-user-plus"></i> Recent Alumni Registrations
                    </div>
                    <?php if($recent_alumni && $recent_alumni->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Batch</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while($alum = $recent_alumni->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-medium"><?php echo htmlspecialchars(($alum['firstname'] ?? '').' '.($alum['lastname'] ?? '')); ?></td>
                                    <td><?php echo htmlspecialchars($alum['course'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($alum['batch'] ?? ''); ?></td>
                                    <td>
                                        <?php if($alum['status'] == 1): ?>
                                        <span class="badge" style="background:#059669;color:#fff;">Verified</span>
                                        <?php else: ?>
                                        <span class="badge" style="background:#d97706;color:#fff;">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="index.php?page=alumni" class="btn btn-sm btn-outline-primary mt-2">View all alumni</a>
                    <?php else: ?>
                    <p class="text-muted text-center py-3">No alumni registrations yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- System Overview -->
            <div class="dash-card mb-3">
                <div class="card-body">
                    <div class="card-title-custom">
                        <i class="fa-solid fa-chart-pie"></i> System Overview
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f5f9 !important;">
                        <span class="text-muted" style="font-size:0.85rem;">Admin Users</span>
                        <strong><?php echo $users_count; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f5f9 !important;">
                        <span class="text-muted" style="font-size:0.85rem;">Total Accounts</span>
                        <strong><?php echo $total_users_all; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="border-color:#f1f5f9 !important;">
                        <span class="text-muted" style="font-size:0.85rem;">Past Events</span>
                        <strong><?php echo $past_events; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted" style="font-size:0.85rem;">System Status</span>
                        <strong style="color:#059669;"><i class="fa-solid fa-circle-check"></i> Active</strong>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="dash-card mb-3">
                <div class="card-body">
                    <div class="card-title-custom">
                        <i class="fa-solid fa-calendar-check"></i> Upcoming Events
                    </div>
                    <?php if($upcoming_events && $upcoming_events->num_rows > 0): ?>
                        <?php while($evt = $upcoming_events->fetch_assoc()): ?>
                        <div class="upcoming-event-item">
                            <div class="upcoming-event-date">
                                <span class="day"><?php echo date('d', strtotime($evt['schedule'])); ?></span>
                                <span class="month"><?php echo date('M', strtotime($evt['schedule'])); ?></span>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:0.88rem; color:#1e293b;"><?php echo htmlspecialchars($evt['title']); ?></div>
                                <div style="font-size:0.75rem; color:#94a3b8;"><?php echo date('h:i A', strtotime($evt['schedule'])); ?></div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3" style="font-size:0.88rem;">No upcoming events</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dash-card">
                <div class="card-body">
                    <div class="card-title-custom">
                        <i class="fa-solid fa-bolt"></i> Quick Actions
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a href="index.php?page=manage_event" class="quick-link">
                            <i class="fa-solid fa-calendar-plus"></i> Create Event
                        </a>
                        <a href="index.php?page=alumni" class="quick-link">
                            <i class="fa-solid fa-users"></i> Manage Alumni
                        </a>
                        <a href="index.php?page=courses" class="quick-link">
                            <i class="fa-solid fa-graduation-cap"></i> Manage Courses
                        </a>
                        <a href="index.php?page=backup" class="quick-link">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Backup Database
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh dashboard stats every 30 seconds
    setInterval(function() {
        $.ajax({
            url: 'ajax.php?action=get_dashboard_stats',
            method: 'GET',
            success: function(resp) {
                try {
                    var data = JSON.parse(resp);
                    if(data.success) {
                        $('.stat-card').eq(0).find('.stat-number').text(data.alumni_count);
                        $('.stat-card').eq(1).find('.stat-number').text(data.jobs_count);
                        $('.stat-card').eq(2).find('.stat-number').text(data.events_count);
                        $('.stat-card').eq(3).find('.stat-number').text(data.courses_count);
                        $('#update-time').text(data.current_time);
                    }
                } catch(e) {}
            }
        });
    }, 30000);
</script>
