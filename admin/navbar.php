
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Sidebar - solid dark */
        nav#sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background: #1e293b;
            border-right: 1px solid #334155;
            z-index: 1045;
            transition: all 0.3s ease-in-out;
            overflow-y: auto;
            overflow-x: hidden;
        }

        nav#sidebar::-webkit-scrollbar { width: 4px; }
        nav#sidebar::-webkit-scrollbar-track { background: transparent; }
        nav#sidebar::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }

        /* Sidebar Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid #334155;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 10px;
            background: white;
            padding: 3px;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            font-size: 0.82rem;
            color: #f1f5f9;
            font-weight: 600;
            line-height: 1.3;
            min-width: 0;
            word-break: break-word;
        }

        /* Section Labels */
        .sidebar-section {
            padding: 1rem 1.25rem 0.4rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
        }

        /* Navigation List */
        .sidebar-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 0 0.75rem;
        }

        .sidebar-list a {
            text-decoration: none;
            color: #94a3b8;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.87rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.15s ease;
        }

        .sidebar-list a:hover {
            background: #334155;
            color: #f1f5f9;
            text-decoration: none;
        }

        .sidebar-list a.active {
            background: #4f46e5;
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-list a.active .icon-field {
            color: #ffffff;
            opacity: 1;
        }

        .icon-field {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            flex-shrink: 0;
            opacity: 0.7;
        }

        .sidebar-list a:hover .icon-field { opacity: 1; }

        /* Main content offset */
        main#view-panel {
            margin-left: 250px;
            padding: 80px 24px 24px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Mobile toggle */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1060;
            background: #1e293b;
            border: 1px solid #334155;
            color: #f1f5f9;
            border-radius: 8px;
            width: 40px;
            height: 40px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .sidebar-toggle:hover { background: #334155; }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1039;
        }

        @media (max-width: 991px) {
            nav#sidebar {
                transform: translateX(-100%);
                z-index: 1050;
                width: 270px;
            }
            nav#sidebar.show { transform: translateX(0); }
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            main#view-panel {
                margin-left: 0;
                padding: 70px 12px 20px;
            }
            .sidebar-overlay.show { display: block; }
        }

        @media (max-width: 576px) {
            nav#sidebar { width: 260px; }
            .sidebar-list a { font-size: 0.84rem; padding: 9px 12px; }
            .brand-logo { width: 36px; height: 36px; }
            main#view-panel { padding: 60px 10px 16px; }
        }
    </style>

<!-- Mobile sidebar toggle -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="fa fa-bars"></i>
</button>

<!-- Sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <img src="assets/img/logo.png" alt="logo" class="brand-logo">
        <span class="brand-text"><?php echo htmlspecialchars($_SESSION['system']['name']); ?></span>
    </div>

    <div class="sidebar-section">Main</div>
    <div class="sidebar-list">
        <a href="index.php?page=home" class="nav-item nav-home">
            <span class="icon-field"><i class="fa-solid fa-house"></i></span> Dashboard
        </a>
        <a href="index.php?page=courses" class="nav-item nav-courses">
            <span class="icon-field"><i class="fa-solid fa-graduation-cap"></i></span> Courses
        </a>
        <a href="index.php?page=alumni" class="nav-item nav-alumni">
            <span class="icon-field"><i class="fa-solid fa-users"></i></span> Alumni
        </a>
        <a href="index.php?page=jobs" class="nav-item nav-jobs">
            <span class="icon-field"><i class="fa-solid fa-briefcase"></i></span> Jobs
        </a>
        <a href="index.php?page=events" class="nav-item nav-events">
            <span class="icon-field"><i class="fa-solid fa-calendar-days"></i></span> Events
        </a>
    </div>

    <div class="sidebar-section">System</div>
    <div class="sidebar-list">
        <a href="index.php?page=backup" class="nav-item nav-backup">
            <span class="icon-field"><i class="fa-solid fa-cloud-arrow-up"></i></span> Backups
        </a>
        <a href="index.php?page=activity_log" class="nav-item nav-activity_log">
            <span class="icon-field"><i class="fa-solid fa-clock-rotate-left"></i></span> Activity Log
        </a>
        <?php if($_SESSION['login_type'] == 1): ?>
        <a href="index.php?page=users" class="nav-item nav-users">
            <span class="icon-field"><i class="fa-solid fa-user-gear"></i></span> Users
        </a>
        <a href="index.php?page=site_settings" class="nav-item nav-site_settings">
            <span class="icon-field"><i class="fa-solid fa-sliders"></i></span> Settings
        </a>
        <?php endif; ?>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let currentPage = "<?php echo isset($_GET['page']) ? htmlspecialchars($_GET['page'], ENT_QUOTES) : '' ?>";
        if (currentPage) {
            document.querySelector(".nav-" + currentPage)?.classList.add("active");
        }

        var sidebar = document.getElementById('sidebar');
        var toggle = document.getElementById('sidebarToggle');
        var overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (toggle) toggle.addEventListener('click', function() {
            sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
        });
        if (overlay) overlay.addEventListener('click', closeSidebar);

        document.querySelectorAll('#sidebar .nav-item').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) closeSidebar();
            });
        });
    });
</script>