<?php 
session_start();
include 'db_connect.php';

// Check if user is logged in and has admin privileges
if(!isset($_SESSION['login_id']) || ($_SESSION['login_type'] != 1 && $_SESSION['login_type'] != 4)){
    header('location:login.php');
    exit;
}

$user_type = $_SESSION['login_type'] == 1 ? 'Admin' : 'Registrar';
$user_name = $_SESSION['login_name'] ?? 'User';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - MOIST Alumni</title>
    <?php include 'header.php'; ?>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-container {
            padding: 2rem;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .admin-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border: none;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #667eea;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .card-description {
            color: #666;
            font-size: 0.9rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="welcome-card">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <p class="lead">Welcome back, <strong><?php echo htmlspecialchars($user_name); ?></strong> (<?php echo $user_type; ?>)</p>
        <p class="text-muted">Manage your MOIST Alumni system from this central dashboard.</p>
    </div>

    <?php
    // Get some basic statistics
    $total_alumni = $conn->query("SELECT COUNT(*) as count FROM alumnus_bio WHERE status = 1")->fetch_assoc()['count'];
    $pending_alumni = $conn->query("SELECT COUNT(*) as count FROM alumnus_bio WHERE status = 0")->fetch_assoc()['count'];
    $total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
    $total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE type IN (1,4)")->fetch_assoc()['count'];
    ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_alumni; ?></div>
            <div class="stat-label">Active Alumni</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending_alumni; ?></div>
            <div class="stat-label">Pending Approval</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_courses; ?></div>
            <div class="stat-label">Courses</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_users; ?></div>
            <div class="stat-label">System Users</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-4">
            <a href="alumni.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-title">Alumni Management</div>
                <div class="card-description">View, approve, and manage alumni accounts</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="courses.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="card-title">Course Management</div>
                <div class="card-description">Add, edit, and manage academic courses</div>
            </a>
        </div>

        <?php if($_SESSION['login_type'] == 1): // Admin only ?>
        <div class="col-md-6 col-lg-4">
            <a href="users.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="card-title">User Management</div>
                <div class="card-description">Manage admin and registrar accounts</div>
            </a>
        </div>
        <?php endif; ?>

        <div class="col-md-6 col-lg-4">
            <a href="events.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="card-title">Event Management</div>
                <div class="card-description">Create and manage alumni events</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="jobs.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="card-title">Job Postings</div>
                <div class="card-description">Manage career opportunities</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="gallery.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="card-title">Gallery</div>
                <div class="card-description">Manage photo galleries</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="activity_log.php" class="admin-card">
                <div class="card-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="card-title">Activity Log</div>
                <div class="card-description">View system activity and logs</div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="logout.php" class="admin-card" style="background: rgba(220, 53, 69, 0.1);">
                <div class="card-icon" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="card-title" style="color: #dc3545;">Logout</div>
                <div class="card-description">Sign out of admin panel</div>
            </a>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add click animation
    $('.admin-card').on('click', function(e) {
        $(this).css('transform', 'scale(0.95)');
        setTimeout(() => {
            $(this).css('transform', '');
        }, 150);
    });
});
</script>

</body>
</html>
