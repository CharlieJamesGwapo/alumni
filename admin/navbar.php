
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Sidebar Styling */
        nav#sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease-in-out;
        }

        /* Sidebar Branding */
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px; /* Adds spacing between logo and text */
    padding: 1rem;
    text-align: left;
    flex-direction: row; /* Ensures horizontal layout */
    flex-wrap: nowrap; /* Prevents the text from dropping below unless necessary */
}

.brand-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
    border-radius: 50%;
    background: white;
    padding: 2px;
    flex-shrink: 0; /* Prevents the logo from resizing */
}

.sidebar-brand large {
    font-size: 12px;
    color: white;
    flex-grow: 1; /* Allows text to take up available space */
    min-width: 0; /* Ensures text does not force wrapping */
    word-break: break-word; /* Breaks long words if necessary */
}



        /* Navigation List */
        .sidebar-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar-list a {
            text-decoration: none;
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease-in-out;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-list a:hover,
        .sidebar-list .active {
            background: rgba(255, 255, 255, 0.2);
            color: #ffcc00;
        }

        /* Sidebar Icons */
        .icon-field {
            font-size: 18px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            nav#sidebar {
                width: 220px;
            }

            .brand-logo {
                width: 50px;
                height: 50px;
            }

            .sidebar-list a {
                font-size: 13px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <img src="assets/img/logo.png" alt="logo" class="brand-logo">
        <large><?php echo $_SESSION['system']['name'] ?></large>
    </div>
    <div class="sidebar-list">
        <a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-home"></i></span> Home</a>
        <a href="index.php?page=courses" class="nav-item nav-courses"><span class='icon-field'><i class="fa fa-list"></i></span> Course List</a>
        <a href="index.php?page=alumni" class="nav-item nav-alumni"><span class='icon-field'><i class="fa fa-users"></i></span> Alumni List</a>
        <a href="index.php?page=jobs" class="nav-item nav-jobs"><span class='icon-field'><i class="fa fa-briefcase"></i></span> Jobs</a>
        <a href="index.php?page=events" class="nav-item nav-events"><span class='icon-field'><i class="fa fa-calendar-day"></i></span> Events</a>
        <a href="index.php?page=backup" class="nav-item nav-backup"><span class='icon-field'><i class="fa fa-database"></i></span> Backups</a>
        <a href="index.php?page=activity_log" class="nav-item nav-activity_log"><span class='icon-field'><i class="fa fa-history"></i></span> Activity Log</a>
        
        <?php if($_SESSION['login_type'] == 1): ?>
        <a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users"></i></span> Users</a>
        <a href="index.php?page=site_settings" class="nav-item nav-site_settings"><span class='icon-field'><i class="fa fa-cogs"></i></span> System Settings</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    // Highlight active menu item
    document.addEventListener("DOMContentLoaded", function() {
        let currentPage = "<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>";
        if (currentPage) {
            document.querySelector(".nav-" + currentPage)?.classList.add("active");
        }
    });
</script>