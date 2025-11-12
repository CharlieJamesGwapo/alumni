<?php
session_start();
include '../admin/db_connect.php';
include '../admin/log_activity.php';

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    header("Location: login.php");
    exit();
}

$raw_program = isset($_GET['program']) ? trim(strtolower($_GET['program'])) : 'all';
// Normalize common user-facing values into canonical values: 'shs', 'college', 'all'
$program_map = [
    'shs' => 'shs',
    'senior high (shs)' => 'shs',
    'senior high' => 'shs',
    'senior_high' => 'shs',
    'senior high (senior high)' => 'shs',
    'college' => 'college',
    'all' => 'all',
    '' => 'all'
];
$selected_program = 'all';
if (isset($program_map[$raw_program])) {
    $selected_program = $program_map[$raw_program];
} else {
    // try substring matches for lenient inputs
    if (strpos($raw_program, 'shs') !== false || strpos($raw_program, 'senior') !== false) {
        $selected_program = 'shs';
    } elseif (strpos($raw_program, 'college') !== false) {
        $selected_program = 'college';
    }
}

// Build server-side WHERE clause to ensure all aggregations and the table are filtered
$where_clause = '';
if ($selected_program === 'shs') {
    $where_clause = " WHERE ab.strand_id IS NOT NULL AND ab.strand_id > 0";
} elseif ($selected_program === 'college') {
    $where_clause = " WHERE (ab.strand_id IS NULL OR ab.strand_id = 0) AND ab.course_id IS NOT NULL AND ab.course_id > 0";
}

// Get total counts for the current filter (cards/statistics should reflect selection)
$count_query = "SELECT 
    SUM(CASE WHEN ab.strand_id IS NOT NULL AND ab.strand_id > 0 THEN 1 ELSE 0 END) as shs_count,
    SUM(CASE WHEN (ab.strand_id IS NULL OR ab.strand_id = 0) AND ab.course_id IS NOT NULL AND ab.course_id > 0 THEN 1 ELSE 0 END) as college_count,
    COUNT(*) as total_count
FROM alumnus_bio ab 
LEFT JOIN alumni_ids ai ON ab.id = ai.alumni_id"
    . $where_clause;

$count_result = $conn->query($count_query);
$counts = $count_result->fetch_assoc();

// Base query with detailed strand information
// Base query with detailed strand information (apply server-side where clause)
$base_query = "SELECT ab.*, ai.program_type, ab.strand_id, s.name as strand_name, 
               CASE 
                   WHEN ab.strand_id IS NOT NULL AND ab.strand_id > 0 THEN 'SHS'
                   ELSE 'College' 
               END as education_level
               FROM alumnus_bio ab 
               LEFT JOIN alumni_ids ai ON ab.id = ai.alumni_id
               LEFT JOIN strands s ON ab.strand_id = s.id"
               . $where_clause;

$result = $conn->query($base_query);
$data_result = $conn->query($base_query);

$genderData = [];
$batchData = [];
$courseData = [];
$strandData = [];

// Build course id => name map so charts show friendly course names
$coursesMap = [];
$courses_res = $conn->query("SELECT id, course FROM courses");
while ($c = $courses_res->fetch_assoc()) {
    $coursesMap[$c['id']] = $c['course'];
}

// Count validation status
$validatedCount = 0;
$notValidatedCount = 0;

while ($row = $data_result->fetch_assoc()) {
    // Determine SHS by presence of strand_id in alumnus_bio (authoritative)
    $isSHS = !empty($row['strand_id']) && $row['strand_id'] > 0;
    $isCollege = (!empty($row['course_id']) && $row['course_id'] > 0) && (empty($row['strand_id']) || $row['strand_id'] == 0);
    
    // Only process rows that match the selected program filter
    if (($selected_program === 'shs' && $isSHS) || 
        ($selected_program === 'college' && $isCollege) || 
        $selected_program === 'all') {
        
        // Count validation status
        if ($row['status'] == 1) {
            $validatedCount++;
        } else {
            $notValidatedCount++;
        }
        
        // Count genders
        $gender = $row['gender'] ?: "Unknown";
        if (!isset($genderData[$gender])) $genderData[$gender] = 0;
        $genderData[$gender]++;

        // Count batches
        $batch = $row['batch'] ?: "Unknown";
        if (!isset($batchData[$batch])) $batchData[$batch] = 0;
        $batchData[$batch]++;

        // For Senior High, count strands instead of courses (use ab.strand_id / strand_name)
        if ($isSHS) {
            if (!empty($row['strand_id']) && $row['strand_id'] > 0) {
                $strandName = $row['strand_name'] ?: ('Strand ' . $row['strand_id']);
                if (!isset($strandData[$strandName])) $strandData[$strandName] = 0;
                $strandData[$strandName]++;
            }
        } elseif ($isCollege) {
            // For college, count courses from alumnus_bio.course_id
            if (!empty($row['course_id']) && $row['course_id'] > 0) {
                $courseName = $coursesMap[$row['course_id']] ?? 'Unknown';
                if (!isset($courseData[$courseName])) $courseData[$courseName] = 0;
                $courseData[$courseName]++;
            }
        }
    }
}

// Encode for JS
$genderDataJSON = json_encode($genderData);
$batchDataJSON  = json_encode($batchData);
$courseDataJSON = json_encode($courseData);

// --- Strand aggregation (for Senior High) ---
// Build strands map and color codes
$strandsMap = [];
$strandColors = [
    'STEM' => '#2ecc71',
    'HUMSS' => '#e74c3c',
    'ABM' => '#3498db',
    'GAS' => '#f1c40f',
    'TVL' => '#9b59b6'
];

$strands_res = $conn->query("SELECT id, name FROM strands");
if ($strands_res) {
    while ($s = $strands_res->fetch_assoc()) {
        $strandsMap[$s['id']] = $s['name'];
    }
}

$strandData = [];
// Aggregate counts from alumnus_bio using strand_id (authoritative source)
// Only compute strand counts when viewing SHS or All (not needed for College-only view)
if ($selected_program !== 'college') {
    $strand_where = ($selected_program === 'shs') ? " WHERE strand_id IS NOT NULL AND strand_id > 0" : " WHERE strand_id IS NOT NULL AND strand_id > 0";
    $strand_count_q = "SELECT strand_id, COUNT(*) as cnt FROM alumnus_bio" . $strand_where . " GROUP BY strand_id";
    $strand_count_res = $conn->query($strand_count_q);
    if ($strand_count_res) {
        while ($r = $strand_count_res->fetch_assoc()) {
            $sid = $r['strand_id'];
            $name = $strandsMap[$sid] ?? ('Strand ' . $sid);
            $strandData[$name] = intval($r['cnt']);
        }
    }
}

$strandDataJSON = json_encode($strandData);

// expose selected program for frontend
$selected_program_json = json_encode($selected_program);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOIST ONLINE ALUMNI TRACKING</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Tailwind CDN for utility classes (optional) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #800000;
            --primary-dark: #600000;
            --primary-light: #ffebeb;
            --accent: #ffd700;
            --text-light: #ffffff;
            --text-dark: #333333;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
            --danger: #dc3545;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.12);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.15);
            --shadow-xl: 0 20px 50px rgba(0,0,0,0.2);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.15s ease;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            
            /* SHS Specific Colors */
            --stem: #2ecc71;
            --humss: #e74c3c;
            --abm: #3498db;
            --gas: #f1c40f;
            --tvl: #9b59b6;
        }

        /* Modern Body & Layout */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }
        
        .container-fluid {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* SHS Specific Styles */
        .bg-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        .custom-select {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 2px solid rgba(128,0,0,0.1);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .custom-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(128,0,0,0.1);
        }

        .shs-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: var(--shadow-lg);
        }

        .strand-legend {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .strand-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-sm);
        }

        .strand-badge.stem { background-color: var(--stem); }
        .strand-badge.humss { background-color: var(--humss); }
        .strand-badge.abm { background-color: var(--abm); }
        .strand-badge.gas { background-color: var(--gas); }
        .strand-badge.tvl { background-color: var(--tvl); }

        .badge.bg-maroon {
            background-color: var(--primary) !important;
            color: white;
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
            border-radius: 20px;
        }

        /* Enhanced Navbar Styling */
        .custom-nav {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            padding: 1rem 0;
            box-shadow: var(--shadow-md);
            position: relative; /* allow absolute positioning of logout button */
        }

        .logo-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .logo-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .main-logo {
            height: 70px;
            width: auto;
            object-fit: contain;
            vertical-align: middle;
            transition: var(--transition);
        }

        /* Logout button styling to make it clearly visible on the maroon header */
        .logout-btn {
            border-radius: 999px;
            padding: 6px 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        /* Always-visible top-right logout button */
        .logout-top {
            position: absolute;
            right: 18px;
            top: 12px;
            z-index: 2050; /* above navbar content */
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--accent);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: 1.5px;
            line-height: 1;
            transition: var(--transition);
        }

        .brand-subtitle {
            font-size: 1.4rem;
            font-weight: 500;
            color: var(--text-light);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            letter-spacing: 0.5px;
        }

        /* DataTable Styling */
        .dt-buttons {
            margin-bottom: 15px;
        }
        .dt-button {
            background: var(--primary) !important;
            color: var(--text-light) !important;
            border: none !important;
            padding: 8px 16px !important;
            margin-right: 8px !important;
            border-radius: 8px !important;
            box-shadow: var(--shadow-sm) !important;
            transition: var(--transition) !important;
        }
        .dt-button:hover {
            background-color: var(--primary-dark) !important;
        }
        @media screen and (max-width: 768px) {
            .dt-buttons {
                text-align: center;
                margin-bottom: 10px;
            }
            .dt-button {
                margin-bottom: 5px;
                width: 100%;
            }
        }
        
        /* Additional styles for responsive modal */
        .modal-body p {
            margin-bottom: 0.8rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.8rem;
        }
        
        .modal-body strong {
            min-width: 140px;
            display: inline-block;
        }
        
        @media screen and (max-width: 768px) {
            .modal-body .row > div {
                margin-bottom: 15px;
            }
            .modal-dialog {
                margin: 0.5rem;
                max-width: 98%;
            }
            .modal-body p {
                font-size: 0.9rem;
            }
        }

        /* Improved styling */
        .navbar {
            background: linear-gradient(135deg, #800000 0%, #600000 100%) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card {
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            border: none;
            background: white;
            overflow: hidden;
            position: relative;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .clickable-row:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        }

        .badge.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
        }
        
        /* Modern Button Styles */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: var(--transition);
            border: none;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #218838 100%);
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info) 0%, #138496 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #e0a800 100%);
        }

        /* Modal Improvements */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #800000 0%, #600000 100%) !important;
            padding: 1.5rem;
        }

        .text-maroon {
            color: #800000;
            font-weight: 600;
        }

        .modal-body p {
            transition: all 0.3s ease;
            padding: 12px;
            border-radius: 8px;
        }

        .modal-body p:hover {
            background-color: #f8f9fa;
        }

        /* Stats Cards */
        .stats-card {
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .stats-card h4 {
            color: #800000;
            margin-bottom: 10px;
        }
        #currentDateTime {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary);
            background: #fff;
            padding: 8px 12px;
            border-radius: 10px;
            box-shadow: var(--shadow-sm);
            display: inline-block;
            letter-spacing: 0.2px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .notification-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            border-left: 4px solid #800000;
        }

        .notification-time {
            font-size: 0.8em;
            color: #666;
        }

        /* Action buttons: text-colored, no filled boxes, stable width */
        .btn-action {
            padding: 8px 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.12s ease, background-color 0.12s ease;
            border: none;
            font-weight: 600;
            background: transparent !important; /* remove colored box */
            box-shadow: none !important;
            min-width: 160px; /* prevent width shift when counts appended */
            justify-content: center;
        }
        .btn-action i { font-size: 1.1rem; }
        .btn-validate { color: #28a745 !important; }
        .btn-validate i { color: #28a745 !important; }
        .btn-archive { color: #ffc107 !important; }
        .btn-archive i { color: #ffc107 !important; }
        .btn-action:hover { text-decoration: underline; color: var(--primary); }

        /* Table styling */
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .table thead th {
            background: var(--primary);
            color: white;
            font-weight: 600;
            border: none;
        }
        .table tbody tr {
            transition: background-color 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: rgba(128,0,0,0.05);
        }

        /* Chart cards */
        .chart-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 20px;
            height: 250px;
        }
        .chart-card h5 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        /* Status badges: white box, colored text (green/yellow) */
        .badge.bg-success {
            background: #fff !important;
            color: #28a745 !important; /* green text */
            border: 1px solid rgba(128,0,0,0.08);
        }

        .badge.bg-warning {
            background: #fff !important;
            color: #ffc107 !important; /* yellow text */
            border: 1px solid rgba(128,0,0,0.08);
        }

        /* Action buttons in table: white boxes with maroon icons/text */
        table .btn-sm {
            background: #fff !important;
            color: var(--primary) !important;
            border: 1px solid rgba(128,0,0,0.08) !important;
            box-shadow: none !important;
        }
        table .btn-sm i { color: var(--primary) !important; }

        /* Checkbox styling */
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.25em;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Real-time Indicator */
        .realtime-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            color: white;
            font-size: 0.9rem;
            animation: fadeIn 0.5s ease;
        }
        
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        /* Loading Skeleton */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
            height: 20px;
            margin: 10px 0;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Enhanced Table */
        .table thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }
        
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            padding: 1rem;
            border: none;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        /* Search Bar Enhancement */
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(128,0,0,0.15);
        }
        
        /* Stat Card Numbers */
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            animation: countUp 1s ease-out;
        }
        
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .btn-action {
                width: 100%;
                justify-content: center;
                margin-bottom: 10px;
            }
            .chart-card {
                height: 200px;
            }
            .dt-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            .dt-button {
                flex: 1;
                margin: 0 !important;
                text-align: center;
            }
            /* stack gender legend under chart on mobile to prevent overlap */
            .gender-block .gender-inner { flex-direction: column; align-items: flex-start; }
            .gender-block .gender-inner > div:first-child { max-width:100%; }
            .gender-block .gender-inner > div:last-child { margin-left:0; margin-top:8px; }
        }
        /* Compact, responsive modal details */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 8px 18px;
            align-items: start;
        }
        .detail-item strong {
            display: block;
            font-size: 12px;
            color: #6b2b2b;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 14px;
            color: #222;
        }
        .truncate {
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }
        .expand-link {
            display:inline-block;
            margin-left:8px;
            font-size:12px;
            color:#0d6efd;
            cursor:pointer;
        }
        @media (max-width: 576px) {
            .details-grid { grid-template-columns: 1fr; }
            #modalImg { width:96px;height:96px; }
        }

        /* Enhanced Homecoming Modal Styles */
        .text-maroon {
            color: var(--primary) !important;
        }
        
        .nav-pills .nav-link {
            border-radius: 25px;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .nav-pills .nav-link:not(.active) {
            color: var(--primary);
            border: 1px solid rgba(128, 0, 0, 0.3);
        }
        
        .nav-pills .nav-link:not(.active):hover {
            background-color: rgba(128, 0, 0, 0.1);
        }
        
        .course-checkbox:checked + label {
            background-color: rgba(128, 0, 0, 0.1);
            border-radius: 5px;
            padding: 5px;
        }
        
        #letterPreview {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        
        @keyframes progress-bar-stripes {
            0% { background-position: 1rem 0; }
            100% { background-position: 0 0; }
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark custom-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3" href="#">
                <div class="logo-wrapper">
                    <img src="../assets/img/logo.png" alt="MOIST Logo" class="main-logo">
                </div>
                <div class="brand-text">
                    <span class="brand-title">MOIST</span>
                    <span class="brand-subtitle">Alumni Management</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3 d-none d-lg-block">
                        <!-- small date area mirrored from main datetime display -->
                        <div id="currentDateTimeSmall" class="text-white small"></div>
                    </li>
                </ul>
            </div>
            <!-- Always visible logout button -->
            <div class="logout-top">
                <a id="logoutBtn" class="btn btn-light text-dark fw-bold logout-btn" href="../logout.php" data-href="../logout.php" aria-label="Logout">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Program Type Selector and Stats Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div></div>
            <h6 class="text-muted" id="currentDateTime"></h6>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card clickable-stat" data-type="total">
                    <h4>Total Alumni</h4>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="stats-number" id="totalAlumni"><?php echo intval($counts['total_count'] ?? 0); ?></div>
                        <div class="d-flex gap-2 align-items-center">
                            <button id="openBirthdayCalendar" class="btn btn-sm btn-outline-secondary" title="View birthdays calendar">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                            <button id="sendBirthdayBtn" class="btn btn-sm btn-outline-primary ms-2" title="Send birthday greetings to today's celebrants">
                                <i class="fas fa-birthday-cake"></i>
                                <span class="ms-1">Send Birthday Greetings</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card clickable-stat" data-type="notvalidated">
                    <h4>Not Validated</h4>
                    <div class="stats-number" id="notValidatedCount"><?php echo intval($notValidatedCount); ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card clickable-stat" data-type="validated">
                    <h4>Validated</h4>
                    <div class="stats-number" id="validatedCount"><?php echo intval($validatedCount); ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card clickable-stat" data-type="courses">
                    <h4><?php echo ($selected_program === 'shs') ? 'Strands' : 'Courses'; ?></h4>
                    <div class="stats-number" id="courseCount"><?php
                        if ($selected_program === 'shs') {
                            echo count($strandData);
                        } elseif ($selected_program === 'college') {
                            echo count($courseData);
                        } else {
                            // all: show courses + strands unique count approximation
                            echo (count($courseData) + count($strandData));
                        }
                    ?></div>
                </div>
            </div>
        </div>

        <style>
        /* Tidy DataTable search input: compact, fixed size, non-expanding on focus */
        #alumniTable_filter .form-control {
            width: 180px;
            max-width: 34vw;
            min-width: 140px;
            border-radius: 10px;
            padding: 6px 10px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: none;
            transition: none;
            font-size: 0.95rem;
        }
        #alumniTable_filter label { width: 100%; display:flex; justify-content:flex-end; }

        /* Small responsive badges for top course area */
        #topCourseBadge { font-size: 0.95rem; }
        #topCourseControls { display:flex; gap:10px; align-items:center; margin-left:8px }
        #topNSelect { border-radius: 8px; padding:4px 8px; border:1px solid rgba(0,0,0,0.08); }

          /* Professional Chart Enhancements */
          .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            animation: fadeIn 0.6s ease-out;
          }
          
          .chart-box {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(128,0,0,0.08);
            position: relative;
            overflow: hidden;
          }
          
          .chart-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
          }
          
          .chart-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(128,0,0,0.15);
          }
          
          /* Responsive improvements */
          @media (max-width: 768px) {
            .chart-grid {
              grid-template-columns: 1fr;
              gap: 16px;
            }
            .chart-box {
              padding: 16px;
            }
          }
          
          @media (min-width: 1200px) {
            .chart-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }
          
          /* Animations */
          @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
          }
          
          @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
          }
          
          /* Gender block enhancements */
          .gender-block {
            animation: fadeIn 0.6s ease-out 0.1s both;
          }
          
          .gender-inner {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(128,0,0,0.08);
            position: relative;
          }
          
          .gender-inner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 16px 16px 0 0;
          }
          
          .gender-inner:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(128,0,0,0.15);
          }
          
          /* Chart canvas styling */
          canvas {
            transition: transform 0.3s ease;
          }
          
          canvas:hover {
            transform: scale(1.02);
          }
          
          #genderCounts {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
          }
          
          #genderLegend {
            margin-top: 12px;
            color: #333;
            font-size: 0.95rem;
          }
          
          /* Batch and Course chart containers */
          .col-md-4.mb-4:nth-child(2) {
            animation: fadeIn 0.6s ease-out 0.2s both;
          }
          
          .col-md-4.mb-4:nth-child(3) {
            animation: fadeIn 0.6s ease-out 0.3s both;
          }
          
          /* Print optimization */
          @media print {
            .chart-box, .gender-inner {
              break-inside: avoid;
              page-break-inside: avoid;
              box-shadow: none;
              border: 1px solid #ddd;
            }
          }
        </style>

        <div class="table-container mt-4">
          <div class="row">
            <div class="col-md-4 mb-4 gender-block">
                <div class="d-flex align-items-start gap-3 gender-inner">
                    <div style="flex:0 0 220px;min-width:180px;display:flex;align-items:center;justify-content:center;">
                        <canvas id="genderChart" style="max-width:220px;max-height:220px;width:100%;height:100%"></canvas>
                    </div>
                    <div style="min-width:140px;max-width:240px;">
                        <div id="genderCounts" style="font-weight:800;color:var(--primary);font-size:1.05rem;margin-bottom:6px"></div>
                        <div id="genderLegend" style="margin-top:8px;color:#333;font-size:0.95rem;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 chart-box"><canvas id="batchChart"></canvas></div>
            <div class="col-md-4 mb-4 chart-box" style="position:relative"><canvas id="courseChart"></canvas></div>
            <!-- Strand chart (for Senior High) - hidden by default, will be shown when SHS selected -->
            <div class="col-md-4 mb-4 chart-box" id="strandChartWrapper" style="display:none;">
                <canvas id="strandChart"></canvas>
            </div>
          </div>
        </div>

        <script>
          // Parse PHP JSON data
          const genderData = <?php echo $genderDataJSON; ?>;
          const batchData  = <?php echo $batchDataJSON; ?>;
          const courseData = <?php echo $courseDataJSON; ?>;
          // Optional: server-side strand aggregate (if available). If not present this will be an empty object.
          const strandData = <?php echo isset($strandDataJSON) ? $strandDataJSON : json_encode((object)[]); ?>;

                    // Professional Chart Configuration
                    const MAROON = '#800000';
                    const MAROON_DARK = '#600000';
                    
                    // Global Chart.js defaults for professional appearance
                    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
                    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.85)';
                    Chart.defaults.plugins.tooltip.padding = 12;
                    Chart.defaults.plugins.tooltip.cornerRadius = 8;
                    Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: 'bold' };
                    Chart.defaults.plugins.tooltip.bodyFont = { size: 13 };

                    // Gender Chart (Donut) - Enhanced with animations
                    (function renderGender(){
                        const normalized = {};
                        Object.keys(genderData || {}).forEach(k => {
                            const key = String(k || '').trim();
                            if (!key) return;
                            const lower = key.toLowerCase();
                            if (lower === 'unknown') return;
                            let label = key;
                            if (/^m(ale)?$/i.test(key)) label = 'Male';
                            else if (/^f(emale)?$/i.test(key)) label = 'Female';
                            normalized[label] = (normalized[label] || 0) + (genderData[k] || 0);
                        });

                        const order = [];
                        if (normalized['Male']) order.push('Male');
                        if (normalized['Female']) order.push('Female');
                        Object.keys(normalized).forEach(l => { if (l !== 'Male' && l !== 'Female') order.push(l); });

                        const labels = order;
                        const values = labels.map(l => normalized[l] || 0);
                        const colors = labels.map(l => {
                            if (/male/i.test(l)) return MAROON;
                            if (/female/i.test(l)) return MAROON_DARK;
                            return '#6c757d';
                        });

                        const ctx = document.getElementById('genderChart').getContext('2d');
                        if (ctx.canvas && ctx.canvas._chartInstance) try { ctx.canvas._chartInstance.destroy(); } catch(e){}
                        
                        const chart = new Chart(ctx, {
                            type: 'doughnut',
                            data: { 
                                labels: labels, 
                                datasets: [{ 
                                    data: values, 
                                    backgroundColor: colors, 
                                    borderColor: '#fff', 
                                    borderWidth: 3,
                                    hoverOffset: 15,
                                    hoverBorderWidth: 4
                                }] 
                            },
                            options: { 
                                responsive: true, 
                                maintainAspectRatio: false, 
                                cutout: '65%',
                                animation: {
                                    animateRotate: true,
                                    animateScale: true,
                                    duration: 1000,
                                    easing: 'easeInOutQuart'
                                },
                                plugins: { 
                                    legend: { display: false }, 
                                    tooltip: { 
                                        enabled: true,
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.parsed || 0;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return `${label}: ${value} (${percentage}%)`;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        ctx.canvas._chartInstance = chart;

                        // Enhanced legend with percentages
                        const parts = [];
                        let total = 0;
                        labels.forEach((lbl, i) => { 
                            total += values[i] || 0; 
                            const c = colors[i] || '#333'; 
                            const percentage = total > 0 ? ((values[i] / total) * 100).toFixed(1) : 0;
                            parts.push(`<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;transition:all 0.3s ease;cursor:pointer" onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='translateX(0)'"><div style="display:flex;align-items:center;gap:8px"><span style="width:14px;height:14px;background:${c};display:inline-block;border-radius:3px;box-shadow:0 2px 4px rgba(0,0,0,0.2)"></span><span style="font-weight:600">${lbl}</span></div><strong style="color:${c}">${values[i] || 0} (${percentage}%)</strong></div>`); 
                        });
                        document.getElementById('genderCounts').innerText = `Total: ${total}`;
                        document.getElementById('genderLegend').innerHTML = parts.join('') || '<div class="text-muted">No recorded genders</div>';
                    })();

                    // Batch Chart (Bar) - Enhanced with gradient and animations
                    const batchLabels = Object.keys(batchData);
                    const batchValues = Object.values(batchData);
                    const batchCtx = document.getElementById('batchChart').getContext('2d');
                    
                    // Create gradient
                    const batchGradient = batchCtx.createLinearGradient(0, 0, 0, 400);
                    batchGradient.addColorStop(0, MAROON);
                    batchGradient.addColorStop(1, MAROON_DARK);
                    
                    const batchChart = new Chart(batchCtx, {
                        type: 'bar',
                        data: {
                            labels: batchLabels,
                            datasets: [{
                                label: 'Alumni Count',
                                data: batchValues,
                                backgroundColor: batchGradient,
                                borderColor: MAROON_DARK,
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                                hoverBackgroundColor: MAROON_DARK,
                                hoverBorderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            },
                            plugins: { 
                                legend: { display: false }, 
                                tooltip: { 
                                    enabled: true,
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed.y;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return `Alumni: ${value} (${percentage}%)`;
                                        }
                                    }
                                }
                            },
                            scales: { 
                                y: { 
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: { size: 11 },
                                        color: '#666'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: { size: 11, weight: '600' },
                                        color: MAROON
                                    }
                                }
                            }
                        }
                    });

                    // Course Chart (Horizontal Bar) - small, sorted, highlight top course
                    // prepare data sorted desc to make it easy to spot top course
                    // Filter out Unknown course label (only show recorded courses)
                    const courseEntries = Object.entries(courseData)
                        .filter(([k,v]) => k && String(k).toLowerCase() !== 'unknown')
                        .map(([k,v])=>({course:k,count:v}));
                    courseEntries.sort((a,b)=>b.count - a.count);
                    const courseLabels = courseEntries.map(e=>e.course);
                    const courseValues = courseEntries.map(e=>e.count);

                    // persistent color map for courses so Top5/Top10 keeps same colors
                    window._courseColorMap = window._courseColorMap || {};
                    function courseColor(label, idx){
                                        if (window._courseColorMap[label]) return window._courseColorMap[label];
                                        
                                        // Custom color assignment for specific courses
                                        let color;
                                        const labelLower = label.toLowerCase();
                                        
                                        if (labelLower.includes('bsed') || labelLower.includes('beed') || labelLower.includes('bachelor of secondary education') || labelLower.includes('bachelor of elementary education')) {
                                            color = MAROON; // Maroon for BSED and BEED
                                        } else if (labelLower.includes('information technology') || labelLower.includes('bs it') || labelLower.includes('bsit')) {
                                            color = '#28a745'; // Green for BS Information Technology
                                        } else if (labelLower.includes('criminology') || labelLower.includes('bs criminology') || labelLower.includes('bscrim')) {
                                            color = '#007bff'; // Blue for Criminology
                                        } else if (labelLower.includes('midwifery') || labelLower.includes('bs midwifery') || labelLower.includes('bsm')) {
                                            color = '#6f42c1'; // Violet for Midwifery
                                        } else if (labelLower.includes('bsba') || labelLower.includes('business administration') || labelLower.includes('bachelor of science in business administration')) {
                                            color = '#ffc107'; // Yellow for BSBA
                                        } else {
                                            // Default colors for other courses
                                            if (idx === 0) color = MAROON_DARK;
                                            else if (idx === 1) color = MAROON;
                                            else if (idx === 2) color = '#b44d4d';
                                            else {
                                                // hash label to number for deterministic hue
                                                let h = 0;
                                                for (let i = 0; i < label.length; i++) h = (h << 5) - h + label.charCodeAt(i);
                                                const hue = Math.abs(h) % 360;
                                                color = `hsl(${hue},62%,46%)`;
                                            }
                                        }
                                        
                                        window._courseColorMap[label] = color;
                                        return color;
                    }

                    function buildCourseChart(labels, values){
                        const colors = labels.map((l,i)=>courseColor(l,i));
                        const ctx = document.getElementById('courseChart').getContext('2d');
                        if (window.courseChartInstance) window.courseChartInstance.destroy();
                        document.getElementById('courseChart').parentElement.style.height = Math.max(260, labels.length * 36) + 'px';
                        window.courseChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: { labels: labels, datasets:[{ label:'Alumni by Course', data: values, backgroundColor: colors, borderColor:'#fff', borderWidth:1 }] },
                            options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ enabled:true } }, scales:{ x:{ beginAtZero:true } } }
                        });

                        // clicking on items filters table
                        document.getElementById('courseChart').onclick = function(evt){
                            const points = window.courseChartInstance.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                            if (points.length) {
                                const firstPoint = points[0];
                                const label = window.courseChartInstance.data.labels[firstPoint.index];
                                table.column(3).search('^' + label + '$', true, false).draw();
                            }
                        };
                    }

                    // Strand chart (SHS) - similar to course chart but uses strands
                    function strandColor(label, idx){
                        // reuse courseColor deterministic colors
                        return courseColor(label, idx);
                    }

                    function buildStrandChart(labels, values){
                        const colors = labels.map((l,i)=>strandColor(l,i));
                        const ctx = document.getElementById('strandChart').getContext('2d');
                        if (window.strandChartInstance) window.strandChartInstance.destroy();
                        document.getElementById('strandChart').parentElement.style.height = Math.max(260, labels.length * 36) + 'px';
                        window.strandChartInstance = new Chart(ctx, {
                            type: 'bar',
                            data: { labels: labels, datasets:[{ label:'Alumni by Strand', data: values, backgroundColor: colors, borderColor:'#fff', borderWidth:1 }] },
                            options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ enabled:true } }, scales:{ x:{ beginAtZero:true } } }
                        });

                        // clicking on items filters table
                        document.getElementById('strandChart').onclick = function(evt){
                            const points = window.strandChartInstance.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                            if (points.length) {
                                const firstPoint = points[0];
                                const label = window.strandChartInstance.data.labels[firstPoint.index];
                                // try to filter by data-strand attribute if there is a column; otherwise no-op
                                // We attempt to match exact text in Course column as fallback
                                table.column(3).search('^' + label + '$', true, false).draw();
                            }
                        };
                    }

                    // top controls use updateCourseChart which will call buildCourseChart
                    function updateCourseChart(topN) {
                        const entries = courseEntries.slice();
                        const subset = (topN === 'all' || !isFinite(topN)) ? entries : entries.slice(0, topN);
                        const labels = subset.map(e=>e.course);
                        const values = subset.map(e=>e.count);
                        buildCourseChart(labels, values);
                        // update top badge
                        const topCourse = labels.length ? `${labels[0]} (${values[0]})` : '—';
                        const topBadge = document.getElementById('topCourseBadge');
                        if (topBadge) topBadge.innerHTML = `Top Course: <span style="color:var(--primary-dark)">${topCourse}</span>`;
                    }

                    // build initial top area and course chart (Course is default)
                    createTopCourseArea();
                    updateCourseChart(5);

                    // Helper: compute strand counts from visible table rows (fallback)
                    function computeStrandCountsFromRows(){
                        const counts = {};
                        if (typeof table === 'undefined') return counts;
                        const nodes = table.rows({ search: 'applied' }).nodes();
                        $(nodes).each(function(){
                            const s = $(this).data('strand') || '';
                            if (!s) return; // no strand info on this row
                            counts[s] = (counts[s] || 0) + 1;
                        });
                        return counts;
                    }

                    // Update strand chart using server-provided or row-derived data
                    function updateStrandChart(topN){
                        // prefer server-provided strandData if it has keys
                        let entries = [];
                        if (strandData && Object.keys(strandData).length) {
                            entries = Object.entries(strandData).map(([k,v])=>({strand:k,count:v}));
                        } else {
                            const counts = computeStrandCountsFromRows();
                            entries = Object.entries(counts).map(([k,v])=>({strand:k,count:v}));
                        }
                        entries.sort((a,b)=>b.count - a.count);
                        const subset = (topN === 'all' || !isFinite(topN)) ? entries : entries.slice(0, topN);
                        const labels = subset.map(e=>e.strand);
                        const values = subset.map(e=>e.count);
                        buildStrandChart(labels, values);
                        const top = labels.length ? `${labels[0]} (${values[0]})` : '—';
                        const topBadge = document.getElementById('topCourseBadge');
                        if (topBadge) topBadge.innerHTML = `Top Strand: <span style="color:var(--primary-dark)">${top}</span>`;
                    }

                    // Render charts depending on selected program
                    function renderChartsForProgram(prog){
                        const p = String(prog || '').toLowerCase();
                        if (p === 'senior high' || p === 'shs'){
                            // show strand chart, hide course chart
                            document.getElementById('strandChartWrapper').style.display = '';
                            document.getElementById('courseChart').parentElement.style.display = 'none';
                            // pick current topN from control
                            const select = document.getElementById('topNSelect');
                            const val = select ? (select.value === 'all' ? Infinity : parseInt(select.value,10)) : 5;
                            updateStrandChart(val);
                        } else {
                            // show course chart, hide strand chart
                            document.getElementById('strandChartWrapper').style.display = 'none';
                            document.getElementById('courseChart').parentElement.style.display = '';
                            // ensure course chart updates to current topN
                            const select = document.getElementById('topNSelect');
                            const val = select ? (select.value === 'all' ? Infinity : parseInt(select.value,10)) : 5;
                            updateCourseChart(val);
                        }
                    }

                                // Add a small visual indicator of top course and controls above charts
                                function createTopCourseArea() {
                                    const container = document.querySelector('.table-container');
                                    let wrapper = document.getElementById('topCourseWrapper');
                                    if (!wrapper) {
                                        wrapper = document.createElement('div');
                                        wrapper.id = 'topCourseWrapper';
                                        wrapper.style.display = 'flex';
                                        wrapper.style.alignItems = 'center';
                                        wrapper.style.justifyContent = 'space-between';
                                        wrapper.style.margin = '8px 0';
                                        container.parentNode.insertBefore(wrapper, container);
                                    }

                                    let topBadge = document.getElementById('topCourseBadge');
                                    if (!topBadge) {
                                        topBadge = document.createElement('div');
                                        topBadge.id = 'topCourseBadge';
                                        topBadge.style.cssText = 'font-weight:700;color:var(--primary);';
                                        wrapper.appendChild(topBadge);
                                    }

                                    let controls = document.getElementById('topCourseControls');
                                    if (!controls) {
                                        controls = document.createElement('div');
                                        controls.id = 'topCourseControls';
                                        // top N selector
                                        const select = document.createElement('select');
                                        select.id = 'topNSelect';
                                        select.innerHTML = '<option value="5">Top 5</option><option value="10">Top 10</option><option value="all">All</option>';
                                        controls.appendChild(select);
                                        wrapper.appendChild(controls);

                                        select.addEventListener('change', function(){
                                            const val = select.value === 'all' ? Infinity : parseInt(select.value,10);
                                            updateCourseChart(val);
                                        });
                                    }

                                    updateTopBadge();
                                }

                                function updateTopBadge() {
                                    const topCourse = courseLabels.length ? `${courseLabels[0]} (${courseValues[0]})` : '—';
                                    const topBadge = document.getElementById('topCourseBadge');
                                    if (topBadge) topBadge.innerHTML = `Top Course: <span style="color:var(--primary-dark)">${topCourse}</span>`;
                                }

                                // Reset filter when clicking outside charts
                                document.addEventListener('click', function(e){
                                    const insideCourse = e.target.closest('#courseChart');
                                    if (!insideCourse && !e.target.closest('.dt-button')) {
                                        // clear course filter
                                        if (typeof table !== 'undefined') table.column(3).search('').draw();
                                    }
                                });

                                // NOTE: duplicate block removed - using the persistent-color implementation above
        </script>

        <!-- Table Section -->
        <div class="table-container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">Alumni List</h4>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label" for="selectAll">Select All</label>
                    </div>
                    <!-- Program filter: College / Senior High (SHS) -->
                    <div class="ms-2">
                        <select id="programFilter" class="form-select form-select-sm" style="min-width:150px">
                            <option value="all">All Programs</option>
                            <option value="college">College</option>
                            <option value="shs">Senior High (SHS)</option>
                        </select>
                    </div>
                    <button class="btn btn-outline-primary ms-2" id="homecomingInviteBtn" title="Invite alumni to Homecoming 2026">
                        <i class="fas fa-envelope-open-text"></i>
                        Invite All (Homecoming 2026)
                    </button>
                    <button class="btn btn-outline-success ms-2" id="viewRSVPBtn" title="View Homecoming RSVP responses">
                        <i class="fas fa-users"></i>
                        View RSVP Responses
                    </button>
                    <button class="btn btn-outline-info ms-2" id="composeMessageBtn" title="Compose and send messages to alumni">
                        <i class="fas fa-envelope"></i>
                        Compose Message
                    </button>
                    <button class="btn btn-outline-primary ms-2" id="viewMessagesBtn" title="View sent messages and responses" onclick="window.location.href='view_messages.php'">
                        <i class="fas fa-inbox"></i>
                        View Messages
                    </button>
                    <a href="test_email_config.php" class="btn btn-outline-warning ms-2" title="Test email configuration" target="_blank">
                        <i class="fas fa-vial"></i>
                        Test Email
                    </a>
                    <button class="btn-action btn-validate" id="validateSelected">
                        <i class="fas fa-check-circle"></i>
                        Validate Selected
                    </button>
                    <button class="btn-action btn-archive" id="archiveSelected">
                        <i class="fas fa-archive"></i>
                        Archive Selected
                    </button>
                    <a href="archive_view.php" class="btn-action btn-view-archive" title="View Archives" style="color:var(--primary);">
                        <i class="fas fa-archive"></i>
                        VIEW ARCHIVES
                    </a>
                    <button class="btn btn-outline-info ms-2" id="allAlumniBtn" title="View All Alumni">
                        <i class="fas fa-users"></i>
                        All Alumni
                    </button>
                </div>
            </div>

            <table id="alumniTable" class="table table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="headerCheckbox"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th id="courseHeader">Course</th>
                        <th>Batch</th>
                        <th>Email</th>
                        <th>Registration Status</th>
                        <th>Employment Status</th>
                        <th>ID Release</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $row_number = 1;
                    while ($row = $result->fetch_assoc()): 
                        $fullname = $row['firstname'] . ' ' . ($row['middlename'] ? $row['middlename'] . ' ' : '') . $row['lastname'];
                    ?>
                    <?php
                        // safe course name lookup (avoid chaining fetch_assoc()['course'] on possible null)
                        $course_id = intval($row['course_id']);
                        $course_name = '';
                        if ($course_id) {
                            $course_query = $conn->query("SELECT course FROM courses WHERE id = $course_id LIMIT 1");
                            if ($course_query) {
                                $course_row = $course_query->fetch_assoc();
                                if ($course_row && isset($course_row['course'])) {
                                    $course_name = $course_row['course'];
                                }
                            }
                        }

                        // determine program type for this alumni (prefer row field, else lookup in alumni_ids)
                        $row_program_type = '';
                        if (!empty($row['program_type'])) {
                            $row_program_type = $row['program_type'];
                        } else {
                            $alumni_identifier = $conn->real_escape_string($row['alumni_id'] ?? '');
                            if ($alumni_identifier !== '') {
                                $pid_res = $conn->query("SELECT program_type FROM alumni_ids WHERE alumni_id = '" . $alumni_identifier . "' LIMIT 1");
                                if ($pid_res && $pid_res->num_rows) {
                                    $tmp = $pid_res->fetch_assoc();
                                    $row_program_type = $tmp['program_type'] ?? '';
                                }
                            }
                        }
                        $program_attr = htmlspecialchars($row_program_type ?: 'Unknown', ENT_QUOTES);

                        // Determine authoritative strand name for this alumni (prefer alumnus_bio->strand_name)
                        $strand_name = '';
                        if (!empty($row['strand_name'])) {
                            $strand_name = $row['strand_name'];
                        } elseif (!empty($row['strand'])) {
                            $strand_name = $row['strand'];
                        } else {
                            // fallback to alumni_ids mapping if available
                            $alumni_identifier = $conn->real_escape_string($row['alumni_id'] ?? '');
                            if ($alumni_identifier !== '') {
                                $sid_res = $conn->query("SELECT strand_id FROM alumni_ids WHERE alumni_id = '" . $alumni_identifier . "' LIMIT 1");
                                if ($sid_res && $sid_res->num_rows) {
                                    $tmp2 = $sid_res->fetch_assoc();
                                    $sid = $tmp2['strand_id'] ?? null;
                                    if ($sid) {
                                        $strand_name = $strandsMap[$sid] ?? ('Strand ' . $sid);
                                    }
                                }
                            }
                        }
                        $strand_attr = htmlspecialchars($strand_name ?: '', ENT_QUOTES);

                        // Decide what to display in the Course/Strand column based on server-selected program
                        // Display rules:
                        // - shs: show strand only (prefer real data, otherwise empty)
                        // - college: show course only (prefer real data, otherwise empty)
                        // - all: show "Course / Strand" when both are present, otherwise show whichever exists
                        $is_shs_record = !empty($row['strand_id']) && $row['strand_id'] > 0;
                        $is_college_record = (!empty($row['course_id']) && $row['course_id'] > 0) && (empty($row['strand_id']) || $row['strand_id'] == 0);
                        
                        if ($selected_program === 'shs') {
                            $displayCourseOrStrand = $is_shs_record ? htmlspecialchars($strand_name ?: '', ENT_QUOTES) : '';
                        } elseif ($selected_program === 'college') {
                            $displayCourseOrStrand = $is_college_record ? htmlspecialchars($course_name ?: '', ENT_QUOTES) : '';
                        } else { // all
                            $c = $course_name ?: '';
                            $s = $strand_name ?: '';
                            if ($c !== '' && $s !== '') {
                                $displayCourseOrStrand = htmlspecialchars($c . ' / ' . $s, ENT_QUOTES);
                            } elseif ($c !== '') {
                                $displayCourseOrStrand = htmlspecialchars($c, ENT_QUOTES);
                            } elseif ($s !== '') {
                                $displayCourseOrStrand = htmlspecialchars($s, ENT_QUOTES);
                            } else {
                                $displayCourseOrStrand = '';
                            }
                        }
                    ?>
                    <tr class="clickable-row" data-program="<?php echo $program_attr; ?>" data-strand="<?php echo $strand_attr; ?>" data-gender="<?php echo htmlspecialchars($row['gender'] ?: 'Unknown', ENT_QUOTES); ?>">
                        <td><input type="checkbox" class="form-check-input row-checkbox" data-id="<?php echo $row['id']; ?>"></td>
                        <td><?php echo $row_number++; ?></td>
                        <td><?php echo ucwords($fullname); ?></td>
                        <td data-original-course="<?php echo htmlspecialchars($displayCourseOrStrand, ENT_QUOTES); ?>"><?php echo $displayCourseOrStrand; ?></td>
                        <td><?php echo $row['batch'] ?? ''; ?></td>
                        <td><?php echo $row['email'] ?? ''; ?></td>
                        <td>
                            <?php if($row['status'] == 1): ?>
                                <span class="badge bg-success">Validated</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Not Validated</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $employment_status = trim($row['employment_status'] ?? '');
                            $current_company = trim($row['current_company'] ?? '');
                            $current_position = trim($row['current_position'] ?? '');
                            
                            if (!empty($employment_status)) {
                                // Use employment_status field if available
                                switch(strtolower($employment_status)) {
                                    case 'employed':
                                        echo '<span class="badge bg-success">Employed</span>';
                                        break;
                                    case 'unemployed':
                                        echo '<span class="badge bg-danger">Unemployed</span>';
                                        break;
                                    case 'self-employed':
                                    case 'self employed':
                                        echo '<span class="badge bg-info">Self-Employed</span>';
                                        break;
                                    case 'student':
                                        echo '<span class="badge bg-primary">Student</span>';
                                        break;
                                    case 'retired':
                                        echo '<span class="badge bg-secondary">Retired</span>';
                                        break;
                                    default:
                                        echo '<span class="badge bg-light text-dark">' . htmlspecialchars(ucfirst($employment_status), ENT_QUOTES) . '</span>';
                                }
                            } elseif (!empty($current_company) || !empty($current_position)) {
                                // Infer employment status from company/position data
                                echo '<span class="badge bg-success">Employed</span>';
                                if (!empty($current_company)) {
                                    echo '<br><small class="text-muted">' . htmlspecialchars($current_company, ENT_QUOTES) . '</small>';
                                }
                            } else {
                                echo '<span class="badge bg-secondary">Not Specified</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            $release_count = intval($row['id_release_count'] ?? 0);
                            if($release_count > 0): 
                            ?>
                                <span class="badge bg-info"><?php echo $release_count; ?> time<?php echo $release_count > 1 ? 's' : ''; ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not Released</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success validate-btn" data-id="<?php echo $row['id']; ?>">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-warning archive-btn" data-id="<?php echo $row['id']; ?>">
                                <i class="fas fa-archive"></i>
                            </button>
                            <a href="print_id.php?id=<?php echo $row['id']; ?>" target="_blank" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-id-card"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alumni Details Modal -->
    <div class="modal fade" id="alumniModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Alumni Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex" style="gap:18px;align-items:flex-start;">
                        <div class="text-center" style="min-width:130px;">
                            <img id="modalImg" src="" alt="Profile" style="width:120px;height:120px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" />
                            <div style="margin-top:8px;font-weight:700;color:#333;" id="modalNameShort"></div>
                            <div id="statusLabelSmall" style="margin-top:6px;font-size:13px"></div>
                        </div>
                        <div style="flex:1;">
                            <div class="details-grid">
                                <div class="detail-item"><strong>Full Name</strong><div class="detail-value" id="modalName"></div></div>
                                <div class="detail-item"><strong>Alumni ID</strong><div class="detail-value" id="modalAlumniId"></div></div>
                                <div class="detail-item"><strong>Gender</strong><div class="detail-value" id="modalGender"></div></div>
                                <div class="detail-item"><strong>Birthdate</strong><div class="detail-value" id="modalBirthdate"></div></div>
                                <div class="detail-item"><strong>Course / Strand</strong><div class="detail-value" id="modalCourse"></div></div>
                                <div class="detail-item"><strong>Batch</strong><div class="detail-value" id="modalBatch"></div></div>
                                <div class="detail-item"><strong>Email</strong><div class="detail-value" id="modalEmail"></div></div>
                                <div class="detail-item"><strong>Contact</strong><div class="detail-value" id="modalContact"></div></div>
                                <div class="detail-item"><strong>Address</strong><div class="detail-value truncate" id="modalAddress"></div><a id="addressExpand" class="expand-link" style="display:none">Show more</a></div>
                                <div class="detail-item"><strong>Company</strong><div class="detail-value" id="modalCompany"></div></div>
                                <div class="detail-item"><strong>Company Address</strong><div class="detail-value truncate" id="modalCompanyAddress"></div><a id="companyAddressExpand" class="expand-link" style="display:none">Show more</a></div>
                                <div class="detail-item"><strong>Type of Industry</strong><div class="detail-value" id="modalConnected"></div></div>
                            </div>
                            
                            <!-- Employment History Section -->
                            <div class="mt-4">
                                <h6 class="text-maroon mb-3"><i class="fas fa-briefcase me-2"></i>Employment History</h6>
                                <div id="employmentHistorySection">
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle me-2"></i>Loading employment information...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Enhanced Homecoming 2026 Invite Modal -->
    <div class="modal fade" id="homecomingInviteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        Homecoming 2026 - Invite Alumni
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Step Navigation -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-pills nav-fill" id="inviteSteps">
                                <li class="nav-item">
                                    <a class="nav-link active" id="step1-tab" data-bs-toggle="pill" href="#step1">
                                        <i class="fas fa-graduation-cap me-1"></i>1. Select Courses
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step2-tab" data-bs-toggle="pill" href="#step2">
                                        <i class="fas fa-users me-1"></i>2. Set Limits
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step3-tab" data-bs-toggle="pill" href="#step3">
                                        <i class="fas fa-edit me-1"></i>3. Compose Letter
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step4-tab" data-bs-toggle="pill" href="#step4">
                                        <i class="fas fa-paper-plane me-1"></i>4. Send Invites
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="inviteTabContent">
                        <!-- Step 1: Course Selection -->
                        <div class="tab-pane fade show active" id="step1">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-maroon mb-3">
                                        <i class="fas fa-list me-2"></i>Available Courses
                                    </h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="selectAllCourses">
                                        <label class="form-check-label fw-bold" for="selectAllCourses">
                                            Select All Courses
                                        </label>
                                    </div>
                                    <div id="coursesList" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading courses...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-maroon mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Selection Summary
                                    </h6>
                                    <div class="alert alert-info">
                                        <div id="selectionSummary">
                                            <p class="mb-1"><strong>Selected Courses:</strong> <span id="selectedCourseCount">0</span></p>
                                            <p class="mb-1"><strong>Total Alumni:</strong> <span id="totalSelectedAlumni">0</span></p>
                                            <p class="mb-0"><strong>With Email:</strong> <span id="alumniWithEmail">0</span></p>
                                        </div>
                                    </div>
                                    <div id="selectedCoursesList" class="mt-3">
                                        <!-- Selected courses will be listed here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Set Limits -->
                        <div class="tab-pane fade" id="step2">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="text-maroon mb-3">
                                        <i class="fas fa-sliders-h me-2"></i>Alumni Limits per Course
                                    </h6>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Minimum alumni per course:</label>
                                            <input type="number" id="minAlumniLimit" class="form-control" value="5" min="1" max="50">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Maximum alumni per course:</label>
                                            <input type="number" id="maxAlumniLimit" class="form-control" value="10" min="1" max="50">
                                        </div>
                                    </div>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Note:</strong> If a course has fewer than the minimum, all alumni from that course will be selected. 
                                        If it has more than the maximum, alumni will be randomly selected.
                                    </div>
                                    <div id="limitsPreview" class="mt-3">
                                        <!-- Limits preview will be shown here -->
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-calculator me-2"></i>Invitation Summary
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="invitationSummary">
                                                <p class="mb-2"><strong>Total Invitations:</strong> <span id="totalInvitations">0</span></p>
                                                <p class="mb-2"><strong>Selected Courses:</strong> <span id="summarySelectedCourses">0</span></p>
                                                <p class="mb-0"><strong>Avg per Course:</strong> <span id="avgPerCourse">0</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Compose Letter -->
                        <div class="tab-pane fade" id="step3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-maroon mb-3">
                                        <i class="fas fa-edit me-2"></i>Compose Invitation Letter
                                    </h6>
                                    <div class="mb-3">
                                        <label class="form-label">Subject Line:</label>
                                        <input type="text" id="inviteSubjectLine" class="form-control" 
                                               value="🎓 You're Invited! MOIST Alumni Homecoming 2026">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Invitation Message:</label>
                                        <small class="text-muted d-block mb-2">
                                            Available placeholders: {{name}}, {{firstname}}, {{lastname}}, {{course}}
                                        </small>
                                        <textarea id="inviteLetterContent" class="form-control" rows="12" style="font-family: 'Times New Roman', serif;">Dear {{name}},

Greetings from the MOIST Alumni Office!

We are thrilled to invite you to our much-anticipated Alumni Homecoming 2026! This special event is designed to bring together our beloved graduates from {{course}} and all other programs for a memorable reunion.

🗓️ Event Details:
Date: February 14-15, 2026
Time: 6:00 PM - 11:00 PM
Venue: MOIST Grand Auditorium & Grounds
Theme: "Reconnect, Reminisce, Rejoice"

✨ What to Expect:
• Welcome reception and networking
• Alumni recognition ceremony  
• Cultural presentations and entertainment
• Delicious dinner and refreshments
• Photo opportunities and memory lane
• Special surprises and giveaways

This is a wonderful opportunity to reconnect with your batchmates, meet fellow alumni, catch up with your favorite professors, and see how our beloved institution has grown.

Please confirm your attendance by replying to this email or visiting our alumni portal at [portal link]. We encourage you to bring your family members as well!

For any questions or special arrangements, please don't hesitate to contact us.

We look forward to celebrating with you!

Warm regards,

MOIST Alumni Office
Homecoming 2026 Organizing Committee
Misamis Oriental Institute of Science and Technology</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-maroon mb-3">
                                        <i class="fas fa-eye me-2"></i>Live Preview
                                    </h6>
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0" id="previewSubject">Subject Preview</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                            <div id="letterPreview" style="font-family: 'Times New Roman', serif; line-height: 1.6;">
                                                <!-- Preview content will be generated here -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="sendTestPreview">
                                            <i class="fas fa-paper-plane me-1"></i>Send Test Email
                                        </button>
                                        <input type="email" id="testEmailAddress" class="form-control mt-2" 
                                               placeholder="Enter test email address">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Send Invites -->
                        <div class="tab-pane fade" id="step4">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="text-maroon mb-3">
                                        <i class="fas fa-paper-plane me-2"></i>Ready to Send Invitations
                                    </h6>
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle me-2"></i>Final Review</h6>
                                        <div id="finalReview">
                                            <!-- Final review content will be populated here -->
                                        </div>
                                    </div>
                                    <div id="sendingProgress" style="display: none;">
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div id="sendProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: 0%">0%</div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span id="sendProgressText">Preparing to send...</span>
                                            <span id="sendProgressCount">0 / 0 sent</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-rocket me-2"></i>Launch Invitations
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <button type="button" class="btn btn-success btn-lg" id="launchInvitations">
                                                <i class="fas fa-paper-plane me-2"></i>
                                                Send All Invitations
                                            </button>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    This will send personalized invitations to all selected alumni.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary" id="prevStepBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextStepBtn">
                        Next <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Details Modal -->
    <div class="modal fade" id="statsDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="statsDetailsTitle">Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="statsDetailsBody">
                    <!-- Content will be injected by JS -->
                </div>
            </div>
        </div>

                <!-- Archive Modal -->
                <div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
                        <div class="modal-content">
                            <div class="modal-header" style="background:#800000;color:#fff">
                                <h5 class="modal-title">Archived Alumni</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <strong>Total Archived:</strong> <span id="archiveTotal">0</span>
                                    </div>
                                    <div>
                                        <input type="text" id="archiveSearch" class="form-control form-control-sm" placeholder="Search archived..." style="min-width:220px;">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="archiveTable">
                                        <thead style="background:#800000;color:#fff">
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Course</th>
                                                <th>Batch</th>
                                                <th>Email</th>
                                                <th>Archived Date</th>
                                                <th>Archived By</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-success" id="restoreSelectedArchived"><i class="fas fa-undo"></i> Restore Selected</button>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

    <!-- Birthday Calendar Modal -->
    <div class="modal fade" id="birthdayModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background:#800000;color:#fff">
                    <h5 class="modal-title"><i class="fas fa-calendar-alt me-2"></i>Alumni Birthday Calendar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="birthdayMonth" class="form-label">Select Month:</label>
                            <select id="birthdayMonth" class="form-select">
                                <option value="0">All Months</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button id="sendSelectedDateBtn" class="btn btn-primary">
                                <i class="fas fa-envelope me-1"></i>Send Greetings to Selected Date
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="birthdayList">
                            <thead style="background:#800000;color:#fff">
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Birthday Greetings Modal with Message Editor -->
    <div class="modal fade" id="birthdayGreetingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg, #800000 0%, #600000 100%);color:#fff">
                    <h5 class="modal-title"><i class="fas fa-birthday-cake me-2"></i>Send Birthday Greetings</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column: Recipients List -->
                        <div class="col-md-5">
                            <h6 class="text-maroon mb-3"><i class="fas fa-users me-2"></i>Today's Celebrants</h6>
                            <div id="todaysBirthdays">
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Message Editor & Preview -->
                        <div class="col-md-7">
                            <h6 class="text-maroon mb-3"><i class="fas fa-edit me-2"></i>Customize Message</h6>
                            
                            <!-- Message Editor -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Birthday Message:</label>
                                <div class="alert alert-info py-2 px-3 mb-2" style="font-size:0.85rem">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Use placeholders: <code>{{firstname}}</code> <code>{{lastname}}</code> <code>{{name}}</code> <code>{{birthdate}}</code>
                                </div>
                                <textarea id="birthdayMessageEditor" class="form-control" rows="6" 
                                    placeholder="Enter your custom birthday message here..."></textarea>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="resetMessageBtn">
                                        <i class="fas fa-undo me-1"></i>Reset to Default
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="previewMessageBtn">
                                        <i class="fas fa-eye me-1"></i>Preview
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="testBirthdayEmailBtn">
                                        <i class="fas fa-paper-plane me-1"></i>Send Test Email
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Live Preview -->
                            <div id="messagePreviewSection" style="display:none">
                                <label class="form-label fw-bold">Live Preview:</label>
                                <div id="messagePreview" class="border rounded p-3" style="background:#f8f9fa;max-height:300px;overflow-y:auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="sendAllBirthdaysBtn" disabled>
                        <i class="fas fa-paper-plane me-1"></i>Send All Greetings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- RSVP Responses Modal -->
    <div class="modal fade" id="rsvpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-users me-2"></i>
                        Homecoming 2026 - RSVP Responses
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- RSVP Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center border-success">
                                <div class="card-body">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h4 class="text-success" id="attendingCount">0</h4>
                                    <p class="card-text">Attending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                    <h4 class="text-danger" id="notAttendingCount">0</h4>
                                    <p class="card-text">Not Attending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-warning">
                                <div class="card-body">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h4 class="text-warning" id="pendingCount">0</h4>
                                    <p class="card-text">No Response</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-primary">
                                <div class="card-body">
                                    <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                    <h4 class="text-primary" id="totalInvited">0</h4>
                                    <p class="card-text">Total Invited</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <ul class="nav nav-pills mb-3" id="rsvpTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="all-tab" data-bs-toggle="pill" href="#all-responses">
                                <i class="fas fa-list me-1"></i>All Responses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="attending-tab" data-bs-toggle="pill" href="#attending-responses">
                                <i class="fas fa-check-circle me-1"></i>Attending
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="not-attending-tab" data-bs-toggle="pill" href="#not-attending-responses">
                                <i class="fas fa-times-circle me-1"></i>Not Attending
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pending-tab" data-bs-toggle="pill" href="#pending-responses">
                                <i class="fas fa-clock me-1"></i>No Response
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="rsvpTabContent">
                        <div class="tab-pane fade show active" id="all-responses">
                            <div class="table-responsive">
                                <table class="table table-hover" id="rsvpTable">
                                    <thead style="background: #800000; color: white;">
                                        <tr>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Email</th>
                                            <th>Response</th>
                                            <th>Response Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="attending-responses">
                            <div class="table-responsive">
                                <table class="table table-hover" id="attendingTable">
                                    <thead style="background: #28a745; color: white;">
                                        <tr>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Email</th>
                                            <th>Confirmed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="not-attending-responses">
                            <div class="table-responsive">
                                <table class="table table-hover" id="notAttendingTable">
                                    <thead style="background: #dc3545; color: white;">
                                        <tr>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Email</th>
                                            <th>Declined Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pending-responses">
                            <div class="table-responsive">
                                <table class="table table-hover" id="pendingTable">
                                    <thead style="background: #ffc107; color: #212529;">
                                        <tr>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Email</th>
                                            <th>Invited Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="exportRSVPBtn">
                        <i class="fas fa-download me-1"></i>Export to Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Speaker RSVP Responses Modal -->
    <div class="modal fade" id="speakerRSVPModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-microphone-alt me-2"></i>
                        Guest Speaker Invitation - RSVP Responses
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- RSVP Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center border-success">
                                <div class="card-body">
                                    <h3 class="text-success mb-0" id="speakerAcceptCount">0</h3>
                                    <p class="text-muted mb-0">Accepted</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <h3 class="text-danger mb-0" id="speakerDeclineCount">0</h3>
                                    <p class="text-muted mb-0">Declined</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-warning">
                                <div class="card-body">
                                    <h3 class="text-warning mb-0" id="speakerPendingCount">0</h3>
                                    <p class="text-muted mb-0">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-primary">
                                <div class="card-body">
                                    <h3 class="text-primary mb-0" id="speakerTotalCount">0</h3>
                                    <p class="text-muted mb-0">Total Invited</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <ul class="nav nav-pills mb-3" id="speakerRsvpTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="speaker-all-tab" data-bs-toggle="pill" href="#speaker-all-responses">
                                <i class="fas fa-list me-1"></i>All Responses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="speaker-accept-tab" data-bs-toggle="pill" href="#speaker-accept-responses">
                                <i class="fas fa-check-circle me-1"></i>Accepted
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="speaker-decline-tab" data-bs-toggle="pill" href="#speaker-decline-responses">
                                <i class="fas fa-times-circle me-1"></i>Declined
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="speaker-pending-tab" data-bs-toggle="pill" href="#speaker-pending-responses">
                                <i class="fas fa-clock me-1"></i>Pending
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="speakerRsvpTabContent">
                        <div class="tab-pane fade show active" id="speaker-all-responses">
                            <div class="table-responsive">
                                <table class="table table-hover" id="speakerRsvpTable">
                                    <thead style="background: #1e3a8a; color: white;">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Course</th>
                                            <th>Batch</th>
                                            <th>Event Date</th>
                                            <th>Event Topic</th>
                                            <th>Response</th>
                                            <th>Responded At</th>
                                        </tr>
                                    </thead>
                                    <tbody id="speakerRsvpTableBody">
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="exportSpeakerRSVPBtn">
                        <i class="fas fa-download me-1"></i>Export to Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles for Guest Speaker Modal -->
    <style>
        #speakerInviteModal .nav-pills .nav-link {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        #speakerInviteModal .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        #speakerInviteModal .nav-pills .nav-link:not(.active):hover {
            background-color: #e0f2fe;
            color: #1e3a8a;
        }
        .alumni-item {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .alumni-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .alumni-item.border-primary {
            border-left-width: 4px !important;
        }
        #alumniSearchResults::-webkit-scrollbar {
            width: 8px;
        }
        #alumniSearchResults::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        #alumniSearchResults::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 10px;
        }
        #alumniSearchResults::-webkit-scrollbar-thumb:hover {
            background: #1e3a8a;
        }
        .cursor-pointer {
            cursor: pointer;
        }
        @media (max-width: 768px) {
            #speakerInviteModal .modal-dialog {
                margin: 0.5rem;
            }
            #speakerInviteModal .nav-pills .nav-link {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
        }
    </style>

    <!-- Guest Speaker Invitation Modal -->
    <div class="modal fade" id="speakerInviteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-microphone-alt me-2"></i>
                        Guest Speaker Invitation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Step Navigation -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-pills nav-fill" id="speakerSteps">
                                <li class="nav-item">
                                    <a class="nav-link active" id="speaker-step1-tab" data-bs-toggle="pill" href="#speaker-step1">
                                        <i class="fas fa-user-check me-1"></i>1. Select Alumni
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="speaker-step2-tab" data-bs-toggle="pill" href="#speaker-step2">
                                        <i class="fas fa-calendar-alt me-1"></i>2. Event Details
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="speaker-step3-tab" data-bs-toggle="pill" href="#speaker-step3">
                                        <i class="fas fa-edit me-1"></i>3. Compose Invitation
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="speaker-step4-tab" data-bs-toggle="pill" href="#speaker-step4">
                                        <i class="fas fa-paper-plane me-1"></i>4. Send Invitation
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="speakerTabContent">
                        <!-- Step 1: Select Alumni -->
                        <div class="tab-pane fade show active" id="speaker-step1">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-search me-2"></i>Search and Select Alumni
                                    </h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-search me-1"></i>Search Alumni:
                                            </label>
                                            <input type="text" id="speakerSearchInput" class="form-control" 
                                                   placeholder="Type name, email, or course...">
                                            <small class="text-muted">Real-time search - results update as you type</small>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-filter me-1"></i>Filter by Course:
                                            </label>
                                            <select id="speakerCourseFilter" class="form-select">
                                                <option value="0">All Courses</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary w-100" id="searchAlumniBtn">
                                                <i class="fas fa-search me-1"></i>Search
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Selected Alumni:</strong> <span id="selectedSpeakerCount">0</span>
                                    </div>
                                    
                                    <div id="alumniSearchResults" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        <p class="text-muted text-center">Use the search above to find alumni</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Event Details -->
                        <div class="tab-pane fade" id="speaker-step2">
                            <div class="row g-3">
                                <div class="col-12 col-lg-8">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Event Information
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-calendar-day me-1"></i>Event Date: <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="speakerEventDate" class="form-control" 
                                                   placeholder="e.g., March 15, 2026" required>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-clock me-1"></i>Event Time: <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="speakerEventTime" class="form-control" 
                                                   placeholder="e.g., 2:00 PM - 4:00 PM" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-map-marker-alt me-1"></i>Event Venue: <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="speakerEventVenue" class="form-control" 
                                                   placeholder="e.g., MOIST Auditorium" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-lightbulb me-1"></i>Topic/Theme: <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="speakerEventTopic" class="form-control" 
                                                   placeholder="e.g., Career Success and Professional Development" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-lightbulb me-2"></i>Helpful Tips
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="small mb-0 ps-3">
                                                <li class="mb-2">📅 Be specific with dates and times</li>
                                                <li class="mb-2">📍 Include complete venue information</li>
                                                <li class="mb-2">💡 Clearly state the topic or theme</li>
                                                <li class="mb-2">🎯 Consider the speaker's expertise</li>
                                                <li>✨ Make it professional yet inviting</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Compose Invitation -->
                        <div class="tab-pane fade" id="speaker-step3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-edit me-2"></i>Compose Invitation Letter
                                    </h6>
                                    <div class="mb-3">
                                        <label class="form-label">Subject Line:</label>
                                        <input type="text" id="speakerSubjectLine" class="form-control" 
                                               value="🎤 Invitation to be Our Guest Speaker">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Invitation Message:</label>
                                        <small class="text-muted d-block mb-2">
                                            Available placeholders: {{name}}, {{firstname}}, {{lastname}}, {{course}}, {{event_date}}, {{event_time}}, {{event_venue}}, {{event_topic}}
                                        </small>
                                        <textarea id="speakerLetterContent" class="form-control" rows="14" style="font-family: 'Times New Roman', serif;">Dear {{name}},

Greetings from the MOIST Alumni Office!

We are honored to extend this invitation to you to be a guest speaker at our upcoming event. Your achievements and expertise in your field make you an ideal candidate to inspire and guide our current students.

🎤 Event Details:
Date: {{event_date}}
Time: {{event_time}}
Venue: {{event_venue}}
Topic: {{event_topic}}

As a distinguished alumnus from {{course}}, your insights and experiences would be invaluable to our students who are preparing to enter the professional world. Your success story serves as an inspiration to many.

✨ What We Offer:
• Certificate of Appreciation
• Honorarium for your time and expertise
• Opportunity to give back to your alma mater
• Networking with faculty and students
• Transportation assistance (if needed)
• Refreshments and meals provided

We believe that your participation would greatly enrich this event and provide our students with real-world perspectives that complement their academic learning.

Please let us know at your earliest convenience if you would be able to accept this invitation. We are flexible and willing to work around your schedule.

Thank you for considering this invitation. We look forward to welcoming you back to MOIST!

Warm regards,

MOIST Alumni Office
Guest Speaker Program
Misamis Oriental Institute of Science and Technology</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-eye me-2"></i>Live Preview
                                    </h6>
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0" id="speakerPreviewSubject">Subject Preview</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                                            <div id="speakerLetterPreview" style="font-family: 'Times New Roman', serif; line-height: 1.6;">
                                                <!-- Preview content will be generated here -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="sendSpeakerTestPreview">
                                            <i class="fas fa-paper-plane me-1"></i>Send Test Email
                                        </button>
                                        <input type="email" id="speakerTestEmailAddress" class="form-control mt-2" 
                                               placeholder="Enter test email address">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Send Invitation -->
                        <div class="tab-pane fade" id="speaker-step4">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-paper-plane me-2"></i>Ready to Send Invitation
                                    </h6>
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle me-2"></i>Final Review</h6>
                                        <div id="speakerFinalReview">
                                            <!-- Final review content will be populated here -->
                                        </div>
                                    </div>
                                    <div id="speakerSendingProgress" style="display: none;">
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div id="speakerSendProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                                                 role="progressbar" style="width: 0%">0%</div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span id="speakerSendProgressText">Preparing to send...</span>
                                            <span id="speakerSendProgressCount">0 / 0 sent</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-rocket me-2"></i>Send Invitations
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <button type="button" class="btn btn-info btn-lg text-white" id="launchSpeakerInvitations">
                                                <i class="fas fa-paper-plane me-2"></i>
                                                Send Invitations
                                            </button>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    This will send personalized speaker invitations to all selected alumni.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary" id="speakerPrevStepBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="speakerNextStepBtn">
                        Next <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gmail-like Compose Message Modal -->
    <div class="modal fade" id="composeMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope me-2"></i>
                        Compose Message
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Step Navigation -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-pills nav-fill" id="messageSteps">
                                <li class="nav-item">
                                    <a class="nav-link active" id="message-step1-tab" data-bs-toggle="pill" href="#message-step1">
                                        <i class="fas fa-users me-1"></i>1. Select Recipients
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="message-step2-tab" data-bs-toggle="pill" href="#message-step2">
                                        <i class="fas fa-edit me-1"></i>2. Compose Message
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="message-step3-tab" data-bs-toggle="pill" href="#message-step3">
                                        <i class="fas fa-paper-plane me-1"></i>3. Send Message
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="messageTabContent">
                        <!-- Step 1: Select Recipients -->
                        <div class="tab-pane fade show active" id="message-step1">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-search me-2"></i>Search and Select Alumni
                                    </h6>
                                    <div class="row g-3 mb-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-search me-1"></i>Search Alumni:
                                            </label>
                                            <input type="text" id="messageSearchInput" class="form-control" 
                                                   placeholder="Type name, email, or course...">
                                            <small class="text-muted">Real-time search - results update as you type</small>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-filter me-1"></i>Filter by Course:
                                            </label>
                                            <select id="messageCourseFilter" class="form-select">
                                                <option value="0">All Courses</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary w-100" id="searchMessageAlumniBtn">
                                                <i class="fas fa-search me-1"></i>Search
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Selected Recipients:</strong> <span id="selectedMessageCount">0</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="loadAllMessageAlumni">
                                            <i class="fas fa-users me-1"></i>Load All Alumni
                                        </button>
                                    </div>
                                    
                                    <div id="alumniMessageResults" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                        <p class="text-muted text-center">Use the search above or click "Load All Alumni"</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Compose Message -->
                        <div class="tab-pane fade" id="message-step2">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-edit me-2"></i>Compose Your Message
                                    </h6>
                                    
                                    <!-- Template Selector -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-file-alt me-1"></i>Select Message Template: <span class="text-danger">*</span>
                                        </label>
                                        <select id="templateSelector" class="form-select" required>
                                            <option value="">-- Select a Template --</option>
                                            <option value="custom">✏️ Custom Message (Type Your Own)</option>
                                        </select>
                                        <small class="text-muted">Choose a template or select "Custom Message" to write your own</small>
                                    </div>
                                    
                                    <!-- Subject -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-heading me-1"></i>Subject: <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="messageSubject" class="form-control" 
                                               placeholder="Enter message subject">
                                    </div>
                                    
                                    <!-- Message Body -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-align-left me-1"></i>Message: <span class="text-danger">*</span>
                                        </label>
                                        <small class="text-muted d-block mb-2">
                                            Available placeholders: {{name}}, {{firstname}}, {{lastname}}, {{email}}, {{course}}, {{batch}}, {{event_date}}, {{event_time}}, {{event_end_time}}
                                        </small>
                                        <textarea id="messageBody" class="form-control" rows="8" 
                                                  placeholder="Enter your message here..."></textarea>
                                    </div>
                                    
                                    <!-- Event Details (Optional) -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-calendar-alt me-2"></i>Event Details (Optional)
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label">Event Date:</label>
                                                    <input type="date" id="eventDate" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Start Time:</label>
                                                    <input type="time" id="eventStartTime" class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">End Time:</label>
                                                    <input type="time" id="eventEndTime" class="form-control">
                                                </div>
                                            </div>
                                            <small class="text-muted">Use placeholders in your message: {{event_date}}, {{event_time}}, {{event_end_time}}</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Options -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="sendEmailNotification" checked>
                                            <label class="form-check-label" for="sendEmailNotification">
                                                <i class="fas fa-envelope me-1"></i>Send email notification to recipients
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-outline-success btn-sm" id="saveAsTemplate">
                                            <i class="fas fa-save me-1"></i>Save as Template
                                        </button>
                                        <a href="test_connection.html" target="_blank" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-stethoscope me-1"></i>Test Connection
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-eye me-2"></i>Live Preview
                                    </h6>
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0" id="messagePreviewSubject">Subject Preview</h6>
                                        </div>
                                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                            <div id="messagePreview" style="line-height: 1.6; white-space: pre-wrap;">
                                                Your message preview will appear here...
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Test Email -->
                                    <div class="mt-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-vial me-1"></i>Send Test Email:
                                        </label>
                                        <div class="input-group">
                                            <input type="email" id="messageTestEmail" class="form-control" 
                                                   placeholder="Enter test email address">
                                            <button type="button" class="btn btn-outline-primary" id="sendMessageTest">
                                                <i class="fas fa-paper-plane me-1"></i>Send Test
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Send Message -->
                        <div class="tab-pane fade" id="message-step3">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-paper-plane me-2"></i>Ready to Send Message
                                    </h6>
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle me-2"></i>Final Review</h6>
                                        <div id="messageFinalReview">
                                            <!-- Final review content will be populated here -->
                                        </div>
                                    </div>
                                    <div id="messageSendingProgress" style="display: none;">
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div id="messageSendProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                                 role="progressbar" style="width: 0%">0%</div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span id="messageSendProgressText">Preparing to send...</span>
                                            <span id="messageSendProgressCount">0 / 0 sent</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header text-white" style="background: linear-gradient(135deg, #800000 0%, #600000 100%);">
                                            <h6 class="mb-0">
                                                <i class="fas fa-rocket me-2"></i>Send Messages
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <button type="button" class="btn btn-lg text-white" id="launchMessages" 
                                                    style="background: linear-gradient(135deg, #800000 0%, #600000 100%);">
                                                <i class="fas fa-paper-plane me-2"></i>
                                                Send Messages
                                            </button>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    This will send personalized messages to all selected alumni.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary" id="messagePrevStepBtn" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i>Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="messageNextStepBtn">
                        Next <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

    <script>
        // Real-time ID Print Tracking - Listen for messages from print_id.php
        window.addEventListener('message', function(event) {
            // Security: Verify origin if needed
            if (event.data && event.data.type === 'id_printed') {
                const { alumniId, count, method } = event.data;
                
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'ID Card Printed!',
                    html: `
                        <div class="text-start">
                            <p><strong>Alumni ID:</strong> ${alumniId}</p>
                            <p><strong>Print Count:</strong> ${count}</p>
                            <p><strong>Method:</strong> ${method === 'print_button' ? 'Print Button' : 'Ctrl+P'}</p>
                        </div>
                    `,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                
                // Refresh the DataTable to show updated print count
                if (typeof table !== 'undefined' && table.ajax) {
                    table.ajax.reload(null, false); // false = stay on current page
                }
                
                console.log(`Real-time update: ID ${alumniId} printed (Count: ${count})`);
            }
        });
        
        // On load: check for today's birthdays and notify registrar
        $(function(){
            // lightweight ping to endpoint to check count without sending mail
            $.post('send_birthday_greetings.php', { action: 'count' })
                .done(function(resp){
                    try {
                        if (resp && resp.total && resp.total > 0) {
                            // Notify registrar with SweetAlert
                            Swal.fire({
                                title: 'Birthdays Today',
                                html: `There are <strong>${resp.total}</strong> alumni celebrating today.`,
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Send Greetings',
                                cancelButtonText: 'Dismiss'
                            }).then(function(result){
                                if (result.isConfirmed) {
                                    // trigger send
                                    doSendBirthdays();
                                }
                            });
                        }
                    } catch(e){}
                }).fail(function(){ /* ignore */ });

            // button click: show birthday greetings modal with template preview
            $('#sendBirthdayBtn').on('click', function(){
                showBirthdayGreetingsModal();
            });

            function doSendBirthdays(){
                const btn = $('#sendAllBirthdaysBtn');
                const messageText = $('#birthdayMessageEditor').val().trim();
                const customMessage = messageText ? textToHtml(messageText) : '';
                
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
                
                Swal.fire({
                    title: 'Sending Birthday Greetings...',
                    html: 'Please wait while we send the emails',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.post('send_birthday_greetings.php', {
                    action: 'send_all',
                    custom_message: customMessage
                })
                    .done(function(r){
                        if (r && r.status === 'ok') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Greetings Sent!',
                                html: `
                                    <div class="text-start">
                                        <p><strong>✅ Successfully sent:</strong> ${r.sent}</p>
                                        <p><strong>❌ Failed:</strong> ${r.failed}</p>
                                        <p><strong>📊 Total:</strong> ${r.total}</p>
                                    </div>
                                `,
                                confirmButtonColor: '#28a745'
                            });
                        } else {
                            Swal.fire('Error', (r && r.message) ? r.message : 'Failed to send', 'error');
                        }
                    })
                    .fail(function(){ 
                        Swal.fire('Error','Request failed. Please try again.','error'); 
                    })
                    .always(function(){ 
                        btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Send All Greetings'); 
                    });
            }

            // Birthday calendar handlers
            $('#openBirthdayCalendar').on('click', function(){
                const modalEl = document.getElementById('birthdayModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                loadBirthdayList();
            });

            $('#birthdayMonth').on('change', function(){ loadBirthdayList(); });

            let selectedDateMD = '';

            function loadBirthdayList(){
                const month = parseInt($('#birthdayMonth').val() || 0);
                $.post('send_birthday_greetings.php', { action: 'list', month: month })
                    .done(function(r){
                        if (!r || r.status !== 'ok') { $('#birthdayList tbody').html('<tr><td colspan="4">No data</td></tr>'); return; }
                        renderBirthdayTable(r.data || []);
                    }).fail(function(){ $('#birthdayList tbody').html('<tr><td colspan="4">Failed to load</td></tr>'); });
            }

            function renderBirthdayTable(data){
                const grouped = {};
                data.forEach(function(item){
                    const md = item.md;
                    if (!grouped[md]) grouped[md] = [];
                    grouped[md].push(item);
                });
                const tbody = $('#birthdayList tbody');
                tbody.empty();
                const keys = Object.keys(grouped).sort();
                
                if (keys.length === 0) { 
                    tbody.html('<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-calendar-times me-2"></i>No birthdays found for selected month</td></tr>'); 
                    return; 
                }
                
                keys.forEach(function(md){
                    const rows = grouped[md];
                    const displayDate = formatMD(md);
                    
                    // Create expandable row for each date
                    const tr = $('<tr>').addClass('date-row').attr('data-md', md);
                    const tdDate = $('<td>').html(`
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-day me-2 text-primary"></i>
                            <strong>${displayDate}</strong>
                            <span class="badge bg-primary ms-2">${rows.length}</span>
                        </div>
                    `);
                    
                    // Show all names in a list
                    const namesList = rows.map(r => `
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-user-circle me-2 text-secondary" style="font-size:0.9rem"></i>
                            <span>${r.firstname} ${r.lastname}</span>
                        </div>
                    `).join('');
                    const tdName = $('<td>').html(namesList);
                    
                    // Show all emails in a list
                    const emailsList = rows.map(r => `
                        <div class="mb-1">
                            <i class="fas fa-envelope me-2 text-secondary" style="font-size:0.8rem"></i>
                            <small class="text-muted">${r.email}</small>
                        </div>
                    `).join('');
                    const tdEmail = $('<td>').html(emailsList);
                    
                    const tdAction = $('<td>');
                    const selectBtn = $('<button>')
                        .addClass('btn btn-sm btn-primary')
                        .html('<i class="fas fa-check me-1"></i>Select')
                        .on('click', function(e){
                            e.stopPropagation();
                            $('.date-row').removeClass('table-success');
                            tr.addClass('table-success');
                            selectedDateMD = md;
                            Swal.fire({
                                icon: 'success',
                                title: 'Date Selected',
                                text: `${displayDate} - ${rows.length} alumni`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        });
                    tdAction.append(selectBtn);
                    
                    tr.append(tdDate, tdName, tdEmail, tdAction);
                    tbody.append(tr);
                });
                
                // Add summary at the bottom
                const totalAlumni = data.length;
                const totalDates = keys.length;
                tbody.append(`
                    <tr class="table-info">
                        <td colspan="4" class="text-center fw-bold">
                            <i class="fas fa-info-circle me-2"></i>
                            Total: ${totalAlumni} alumni across ${totalDates} dates
                        </td>
                    </tr>
                `);
            }

            function formatMD(md){
                try {
                    const parts = md.split('-');
                    const mm = parseInt(parts[0],10);
                    const dd = parseInt(parts[1],10);
                    const d = new Date(2000, mm-1, dd);
                    return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric' });
                } catch(e){ return md; }
            }

            $('#sendSelectedDateBtn').on('click', function(){
                if (!selectedDateMD) { Swal.fire('Select date','Please select a date row first','info'); return; }
                Swal.fire({
                    title: 'Confirm send',
                    text: `Send greetings to all alumni with birthday on ${formatMD(selectedDateMD)}?`,
                    showCancelButton: true,
                }).then(function(res){
                    if (!res.isConfirmed) return;
                    const btn = $('#sendSelectedDateBtn');
                    btn.prop('disabled', true).text('Sending...');
                    $.post('send_birthday_greetings.php', { action: 'send_date', date: selectedDateMD })
                        .done(function(r){
                            if (r && r.status === 'ok') Swal.fire('Done', `Sent: ${r.sent} Failed: ${r.failed}`, 'success');
                            else Swal.fire('Error', (r && r.message) ? r.message : 'Send failed', 'error');
                        }).fail(function(){ Swal.fire('Error','Request failed','error'); })
                        .always(function(){ btn.prop('disabled', false).text('Send Greetings for Selected Date'); });
                });
            });

            // Birthday message editor variables
            let defaultBirthdayMessage = '';
            let currentBirthdayMessage = '';
            
            // Function to show birthday greetings modal
            function showBirthdayGreetingsModal() {
                const modal = new bootstrap.Modal(document.getElementById('birthdayGreetingsModal'));
                modal.show();
                
                // Load default message template
                loadDefaultBirthdayMessage();
                
                // Load today's birthday celebrants
                loadTodaysBirthdays();
            }
            
            // Load default birthday message template
            function loadDefaultBirthdayMessage() {
                $.post('send_birthday_greetings.php', { action: 'get_template' })
                    .done(function(resp) {
                        if (resp && resp.status === 'ok') {
                            // Extract plain text message from HTML
                            const tempDiv = $('<div>').html(resp.template);
                            const messageContent = tempDiv.find('div[style*="padding:40px"]').html() || '';
                            
                            // Set default message (plain text version)
                            defaultBirthdayMessage = `<p>Dear {{name}},</p><p>Warmest wishes from the MOIST Alumni Office on your special day ({{birthdate}}). May your day be filled with joy, good health, and wonderful memories.</p><p>We appreciate being part of your alumni journey. We hope to see you at our next alumni event!</p>`;
                            currentBirthdayMessage = defaultBirthdayMessage;
                            $('#birthdayMessageEditor').val(stripHtmlTags(defaultBirthdayMessage));
                        }
                    });
            }
            
            // Strip HTML tags for textarea display
            function stripHtmlTags(html) {
                const temp = $('<div>').html(html);
                return temp.text().trim();
            }
            
            // Convert plain text to HTML paragraphs
            function textToHtml(text) {
                return text.split('\n\n').map(p => `<p>${p.trim()}</p>`).join('');
            }

            // Function to load today's birthday celebrants
            function loadTodaysBirthdays() {
                $('#todaysBirthdays').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#sendAllBirthdaysBtn').prop('disabled', true);
                
                $.post('send_birthday_greetings.php', { action: 'count' })
                    .done(function(resp) {
                        if (resp && resp.total > 0) {
                            // Get the list of today's celebrants
                            $.post('send_birthday_greetings.php', { action: 'list', month: new Date().getMonth() + 1 })
                                .done(function(listResp) {
                                    if (listResp && listResp.status === 'ok') {
                                        const today = new Date();
                                        const todayMD = String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                                        const todaysCelebrants = listResp.data.filter(person => person.md === todayMD);
                                        
                                        if (todaysCelebrants.length > 0) {
                                            let html = `<div class="alert alert-success">
                                                <i class="fas fa-birthday-cake me-2"></i>
                                                <strong>${todaysCelebrants.length}</strong> alumni celebrating today!
                                            </div>`;
                                            html += '<div class="list-group" style="max-height:400px;overflow-y:auto">';
                                            todaysCelebrants.forEach(person => {
                                                html += `<div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1"><i class="fas fa-user-circle me-2 text-primary"></i>${person.firstname} ${person.lastname}</h6>
                                                            <small class="text-muted"><i class="fas fa-envelope me-1"></i>${person.email}</small>
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill" style="font-size:1.2rem">🎂</span>
                                                    </div>
                                                </div>`;
                                            });
                                            html += '</div>';
                                            $('#todaysBirthdays').html(html);
                                            $('#sendAllBirthdaysBtn').prop('disabled', false);
                                        } else {
                                            $('#todaysBirthdays').html('<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>No alumni celebrating today.</div>');
                                        }
                                    }
                                });
                        } else {
                            $('#todaysBirthdays').html('<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>No alumni celebrating today.</div>');
                        }
                    })
                    .fail(function() {
                        $('#todaysBirthdays').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load birthday data.</div>');
                    });
            }
            
            // Reset message to default
            $('#resetMessageBtn').on('click', function() {
                $('#birthdayMessageEditor').val(stripHtmlTags(defaultBirthdayMessage));
                $('#messagePreviewSection').hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Reset Complete',
                    text: 'Message has been reset to default',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
            
            // Preview message
            $('#previewMessageBtn').on('click', function() {
                const messageText = $('#birthdayMessageEditor').val().trim();
                if (!messageText) {
                    Swal.fire('Empty Message', 'Please enter a message to preview', 'warning');
                    return;
                }
                
                // Convert to HTML and replace placeholders with sample data
                const messageHtml = textToHtml(messageText);
                const previewHtml = messageHtml
                    .replace(/\{\{firstname\}\}/g, 'John')
                    .replace(/\{\{lastname\}\}/g, 'Doe')
                    .replace(/\{\{name\}\}/g, 'John Doe')
                    .replace(/\{\{birthdate\}\}/g, new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric' }));
                
                // Show preview with email styling
                const fullPreview = `
                    <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1)">
                        <div style="background:linear-gradient(135deg, #800000 0%, #600000 100%);padding:30px;text-align:center">
                            <div style="font-size:48px;margin-bottom:10px">🎂</div>
                            <h2 style="color:#fbbf24;margin:0;font-size:28px">Happy Birthday!</h2>
                        </div>
                        <div style="padding:30px;color:#333">
                            ${previewHtml}
                            <div style="margin-top:20px;padding:15px;background:linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);border-radius:8px">
                                <p style="margin:0;font-size:14px;color:#78350f">🎉 <strong>Wishing you a wonderful year ahead!</strong></p>
                            </div>
                        </div>
                        <div style="background:#f8f9fa;padding:20px;text-align:center;border-top:3px solid #800000">
                            <p style="margin:0;color:#666;font-size:13px"><strong>MOIST Alumni Office</strong></p>
                        </div>
                    </div>
                `;
                
                $('#messagePreview').html(fullPreview);
                $('#messagePreviewSection').slideDown();
            });
            
            // Send test email
            $('#testBirthdayEmailBtn').on('click', function() {
                Swal.fire({
                    title: 'Send Test Email',
                    input: 'email',
                    inputLabel: 'Enter test email address',
                    inputPlaceholder: 'your-email@example.com',
                    showCancelButton: true,
                    confirmButtonText: 'Send Test',
                    confirmButtonColor: '#28a745',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please enter an email address';
                        }
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            return 'Please enter a valid email address';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const testEmail = result.value;
                        const messageText = $('#birthdayMessageEditor').val().trim();
                        const customMessage = messageText ? textToHtml(messageText) : '';
                        
                        Swal.fire({
                            title: 'Sending...',
                            text: 'Please wait while we send the test email',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $.post('send_birthday_greetings.php', {
                            action: 'test',
                            test_email: testEmail,
                            custom_message: customMessage
                        })
                        .done(function(resp) {
                            if (resp && resp.status === 'ok') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Test Email Sent!',
                                    text: `Check your inbox at ${testEmail}`,
                                    confirmButtonColor: '#28a745'
                                });
                            } else {
                                Swal.fire('Error', resp.message || 'Failed to send test email', 'error');
                            }
                        })
                        .fail(function() {
                            Swal.fire('Error', 'Request failed. Please try again.', 'error');
                        });
                    }
                });
            });

            // Send all birthdays button
            $('#sendAllBirthdaysBtn').on('click', function() {
                Swal.fire({
                    title: 'Confirm Send',
                    text: 'This will send birthday greetings to all alumni celebrating today. Continue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, send all',
                    confirmButtonColor: '#28a745'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        doSendBirthdays();
                    }
                });
            });

            // Send test email button
            $('#sendTestEmailBtn').on('click', function() {
                Swal.fire({
                    title: 'Send Test Email',
                    input: 'email',
                    inputLabel: 'Enter test email address',
                    inputPlaceholder: 'test@example.com',
                    showCancelButton: true,
                    confirmButtonText: 'Send Test',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please enter an email address';
                        }
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            return 'Please enter a valid email address';
                        }
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        const testEmail = result.value;
                        const btn = $('#sendTestEmailBtn');
                        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                        
                        $.post('send_birthday_greetings.php', { action: 'test', test_email: testEmail })
                            .done(function(r) {
                                if (r && r.status === 'ok') {
                                    Swal.fire('Test Sent!', 'Test email sent successfully', 'success');
                                } else {
                                    Swal.fire('Error', r.message || 'Failed to send test email', 'error');
                                }
                            })
                            .fail(function() {
                                Swal.fire('Error', 'Request failed', 'error');
                            })
                            .always(function() {
                                btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Send Test Email');
                            });
                    }
                });
            });
        });
        // Chart configuration and data - colors defined from PHP
        const strandColors = <?php echo json_encode($strandColors); ?>;

        // Function to create SHS-specific charts
        function createSHSCharts() {
            const ctx = document.getElementById('strandDistribution').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(strandData),
                    datasets: [{
                        data: Object.values(strandData),
                        backgroundColor: Object.keys(strandData).map(strand => strandColors[strand] || '#gray'),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: { size: 12 },
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        title: {
                            display: true,
                            text: 'Strand Distribution',
                            font: { size: 16, weight: 'bold' }
                        }
                    },
                    cutout: '60%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        }

        // Function to update strand legends
        function updateStrandLegends() {
            const legendContainer = document.querySelector('.strand-legend');
            if (!legendContainer) return;

            Object.entries(strandData).forEach(([strand, count]) => {
                const badge = document.createElement('div');
                badge.className = `strand-badge ${strand.toLowerCase()}`;
                badge.innerHTML = `
                    <i class="fas fa-user-graduate"></i>
                    ${strand}: ${count}
                `;
                legendContainer.appendChild(badge);
            });
        }

        // Function to change program type and reload page
        function changeProgramType(type) {
            window.location.href = 'alumni.php?program=' + type;
        }

        // Initialize DataTable with program type filter
        $(document).ready(function() {
            // canonical current program from server
            let currentProgram = <?php echo $selected_program_json; ?>;
            // fallback if JS sees undefined
            if (!currentProgram) currentProgram = '<?php echo $selected_program; ?>';
            let programTitle = currentProgram === 'shs' ? 'Senior High School' : 
                             currentProgram === 'college' ? 'College' : 'All Programs';

            // Initialize charts to match the server-selected program
            try {
                renderChartsForProgram(currentProgram);
                if (currentProgram === 'shs') updateStrandLegends();
                // Ensure course/strand column displays correctly on load
                try { swapCourseAndStrandDisplay(currentProgram); } catch(e) {}
                // set header label and stats card title
                const courseLabel = (currentProgram === 'shs') ? 'Strand' : (currentProgram === 'college' ? 'Course' : 'Course / Strand');
                $('#courseHeader').text(courseLabel);
                const statsCourseTitle = (currentProgram === 'shs') ? 'SHS Graduated' : (currentProgram === 'college' ? 'Courses Graduated' : 'Courses & Strands Graduated');
                $('.stats-card[data-type="courses"] h4').text(statsCourseTitle);
            } catch(e) { /* ignore if charts not ready */ }
            // Initialize DataTable with enhanced features
            const table = $('#alumniTable').DataTable({
                responsive: true,
                columnDefs: [
                    { targets: 0, orderable: false, searchable: false }, // checkbox
                    { targets: 7, orderable: false, searchable: false }, // actions
                    { targets: 3, searchable: true } // course column (exact search used via regex when filtering)
                ],
                dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                order: [[6, "asc"], [2, "asc"]], // Sort by Registration Status first (Not Validated first), then by name
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-primary',
                        text: '<i class="fas fa-copy"></i> Copy'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-primary',
                        text: '<i class="fas fa-file-csv"></i> CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-primary',
                        text: '<i class="fas fa-file-excel"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary',
                        text: '<i class="fas fa-file-pdf"></i> PDF'
                    },
                                        {
                                                text: '<i class="fas fa-print"></i> Print',
                                                className: 'btn btn-primary',
                                                action: function () {
                                                        // Build a clean printable page with maroon header
                                                        const rows = $('#alumniTable').DataTable().rows({ search: 'applied' }).nodes();
                                                        const headerHtml = `
                                                                <div style="padding:14px;border-bottom:4px solid #800000;margin-bottom:20px;display:flex;align-items:center;gap:15px;">
                                                                    <img src="../assets/img/logo.png" style="height:60px;"/>
                                                                    <div>
                                                                        <h2 style="margin:0;color:#800000;font-weight:700">Misamis Oriental Institute of Science and Technology</h2>
                                                                        <div style="color:#600000;font-weight:600">Sta. Cruz, Cogon, Balingasag, Misamis Oriental</div>
                                                                        <div style="margin-top:6px;color:#333;font-size:13px">Alumni List</div>
                                                                    </div>
                                                                </div>`;
                                                        const tableHtml = `<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;width:100%;font-family:Arial, sans-serif;font-size:12px;">
                                                                <thead><tr>
                                                                    <th>#</th>
                                                                    <th>Name</th>
                                                                    <th>Course / Strand</th>
                                                                    <th>Batch</th>
                                                                    <th>Email</th>
                                                                    <th>Status</th>
                                                                </tr></thead>
                                                                <tbody>` +
                                                                $(rows).map(function(){
                                                                        const tds = $(this).find('td');
                                                                        return `<tr><td>${tds.eq(1).text()}</td><td>${tds.eq(2).text()}</td><td>${tds.eq(3).text()}</td><td>${tds.eq(4).text()}</td><td>${tds.eq(5).text()}</td><td>${tds.eq(6).text()}</td></tr>`;
                                                                }).get().join('') +
                                                                `</tbody></table>`;

                                                        // Preload logo and convert to data URL to ensure printing across browsers
                                                        const imgSrc = '../assets/img/logo.png';
                                                        const img = new Image();
                                                        img.crossOrigin = 'anonymous';
                                                        img.onload = function(){
                                                            try{
                                                                const cvs = document.createElement('canvas');
                                                                const maxW = 1200;
                                                                const scale = Math.min(1, maxW / img.width);
                                                                cvs.width = img.width * scale;
                                                                cvs.height = img.height * scale;
                                                                const cctx = cvs.getContext('2d');
                                                                cctx.drawImage(img, 0, 0, cvs.width, cvs.height);
                                                                const dataUrl = cvs.toDataURL('image/png');
                                                                openPrintWindow(dataUrl);
                                                                return;
                                                            } catch(e) {
                                                                openPrintWindow(imgSrc);
                                                            }
                                                        };
                                                        img.onerror = function(){ openPrintWindow(imgSrc); };
                                                        img.src = imgSrc;

                                                        function openPrintWindow(logoSrc){
                                                            const printWindow = window.open('', '_blank');
                                                            printWindow.document.write('<html><head><title>Print Alumni</title>');
                                                            printWindow.document.write(`<style>
                                                                @page { margin: 18mm; }
                                                                body{font-family: Arial, Helvetica, sans-serif; padding: 18px; color: #222;}
                                                                .print-header{padding:14px 0 18px;border-bottom:4px solid #800000;margin-bottom:18px;display:flex;align-items:center;gap:18px}
                                                                .print-header img{height:64px}
                                                                .print-title{font-size:20px;color:#800000;margin:0;font-weight:700}
                                                                .print-sub{color:#600000;font-size:13px;margin-top:2px}
                                                                table{width:100%;border-collapse:collapse;font-size:12px}
                                                                table th, table td{border:1px solid #ddd;padding:8px;text-align:left}
                                                                table thead th{background:#f8f8f8;color:#111;font-weight:700}
                                                                .watermark{position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);opacity:0.06;z-index:0;pointer-events:none}
                                                                .watermark img{max-width:520px;width:60vw;height:auto;display:block}
                                                                .print-body{position:relative;z-index:1}
                                                                @media print{ .watermark img{opacity:0.04} .print-header img{height:48px} .print-title{font-size:18px} }
                                                            </style>`);
                                                            printWindow.document.write('</head><body>');
                                                            const watermarkHtml = `<div class="watermark"><img src="${logoSrc}" alt="logo"></div>`;
                                                            printWindow.document.write(watermarkHtml + '<div class="print-body">' + headerHtml + tableHtml + '</div>');
                                                            printWindow.document.write('</body></html>');
                                                            printWindow.document.close();
                                                            printWindow.onload = function(){ setTimeout(()=>{ try{ printWindow.print(); printWindow.close(); } catch(e){} }, 700); };
                                                            setTimeout(()=>{ try{ printWindow.print(); printWindow.close(); } catch(e){} }, 1400);
                                                        }
                                                }
                                        }
                    
                ]
            });

            // Update current date/time
            function updateDateTime() {
                const now = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit'
                };
                $('#currentDateTime').text(now.toLocaleDateString('en-US', options));
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Update stats counters using DataTables API so counts reflect whole dataset (not just current page)
            function updateStats() {
                // total rows (respecting current filters/search)
                const total = table.rows({ search: 'applied' }).count();
                $('#totalAlumni').text(total);

                // status counts (use column data, strip HTML)
                const statusData = table.column(6, { search: 'applied' }).data().toArray();
                let validated = 0, notvalidated = 0;
                statusData.forEach(function(s){
                    // strip any HTML and normalize
                    const txt = $('<div>').html(s).text().trim().toLowerCase();
                    if (txt.indexOf('not validated') !== -1) notvalidated++;
                    else if (txt.indexOf('validated') !== -1) validated++;
                });
                $('#validatedCount').text(validated);
                $('#notValidatedCount').text(notvalidated);

                // course count: unique values in course column (index 3)
                const courses = new Set(table.column(3, { search: 'applied' }).data().toArray().map(function(c){
                    return $('<div>').html(c).text().trim();
                }).filter(function(v){ return v !== '' && v !== '—'; }));
                $('#courseCount').text(courses.size);
            }

            // Initialize stats and update on table draw
            updateStats();
            table.on('draw', function(){ updateStats(); });

            // Select All Functionality - selects ALL alumni in the current view
            $('#headerCheckbox, #selectAll').on('change', function() {
                const isChecked = $(this).prop('checked');
                
                // Select or deselect all visible rows in the current table view
                $('.row-checkbox').each(function() {
                    $(this).prop('checked', isChecked);
                });
                
                // Sync both checkboxes (header and select all button)
                $('#headerCheckbox, #selectAll').prop('checked', isChecked);
                updateSelectedCount();
            });

            // Program filter (College / Senior High) - use server-side param to load correct dataset
            // Initialize bottom selector to reflect current server-side program selection
            (function initProgramFilter(){
                $('#programFilter').val('<?php echo $selected_program; ?>' || 'all');
            })();

            $('#programFilter').on('change', function(){
                const val = $(this).val() || 'all';
                // reload page so server returns filtered rows, charts and counts
                window.location.href = 'alumni.php?program=' + val;
            });

            // Ensure column 3 (Course) original values are cached so we can switch between Course and Strand
            function cacheOriginalCourseValues() {
                $('#alumniTable tbody tr').each(function() {
                    const courseCell = $(this).find('td').eq(3);
                    if (!courseCell.attr('data-original-course')) {
                        courseCell.attr('data-original-course', courseCell.text().trim());
                    }
                });
            }

            // Swap Course column to display Strand when Senior High is selected
            function swapCourseAndStrandDisplay(program) {
                cacheOriginalCourseValues();
                const isSHS = String(program || '').toLowerCase() === 'senior high' || String(program || '').toLowerCase() === 'shs';

                // Update header text
                const th = $('#alumniTable thead th').eq(3);
                th.text(isSHS ? 'Strand' : 'Course');

                // Update each row's displayed value in the 4th column
        $('#alumniTable tbody tr').each(function() {
                    const row = $(this);
                    const courseCell = row.find('td').eq(3);
                    const originalCourse = courseCell.attr('data-original-course') || courseCell.text().trim();
                    if (isSHS) {
                        // Prefer data-strand attribute (set server-side), fallback to original course
            const strand = (row.data('strand') || row.attr('data-strand') || '').trim();
            courseCell.text(strand || originalCourse || '');
                    } else {
            courseCell.text(originalCourse || '');
                    }
                });

                // If switching to SHS, ensure course-based filters/search still operate on the visible Strand text
                // Re-draw table to make sure DataTables internal cache is updated
                try { table.columns.adjust().draw(false); } catch(e) { /* ignore if table not ready */ }
            }

            $(document).on('change', '.row-checkbox', function() {
                const allChecked = $('.row-checkbox:not(:checked)').length === 0;

            // Header View Archive button now opens in-page modal (handler attached later)
                $('#headerCheckbox, #selectAll').prop('checked', allChecked);
                updateSelectedCount();
            });

            // Make rows clickable to view alumni details in modal
            $(document).on('click', '.clickable-row', function(e){
                // ignore clicks on inputs/buttons/links or clicks that originate from validate/archive controls
                if ($(e.target).closest('input, button, a, .validate-btn, .archive-btn').length) return;
                const id = $(this).find('.row-checkbox').data('id');
                if (!id) return;
                // fetch data via AJAX
                $.getJSON('get_alumni_data.php', { id: id }).done(function(resp){
                    if (!resp || !resp.success) {
                        Swal.fire('Error', resp && resp.message ? resp.message : 'Failed to load details', 'error');
                        return;
                    }
                    const d = resp.data || {};

                    // Basic fields
                    $('#modalName').text(d.name || '');
                    $('#modalAlumniId').text(d.alumni_id || '');
                    $('#modalNameShort').text((d.name || '').split(' ').slice(0,2).join(' '));
                    $('#modalGender').text(d.gender || '');
                    $('#modalBirthdate').text(d.birthdate || '');
                    $('#modalContact').text(d.contact || '');
                    $('#modalEmail').text(d.email || '');
                    $('#modalBatch').text(d.batch || '');
                    $('#modalCompany').text(d.company_name || '');
                    // Prefer the normalized 'industry' field; fall back to connected_to for backwards compatibility
                    $('#modalConnected').text(d.industry || d.connected_to || '');

                    // Course / Strand combined display
                    let courseStr = '';
                    if (d.course && d.strand) courseStr = d.course + ' / ' + d.strand;
                    else if (d.course) courseStr = d.course;
                    else if (d.strand) courseStr = d.strand;
                    $('#modalCourse').text(courseStr);

                    // Image
                    if (d.img) {
                        $('#modalImg').attr('src', d.img).attr('alt', d.name || 'Profile');
                    } else {
                        $('#modalImg').attr('src', '').attr('alt', '');
                    }

                    // Address and Company Address: apply truncation with expand toggle when long
                    function applyTrunc($el, $expand, text){
                        $el.text(text || '');
                        $el.removeClass('truncate');
                        $expand.hide();
                        if ((text || '').length > 80){
                            $el.addClass('truncate');
                            $expand.show();
                            $expand.text('Show more');
                            $expand.off('click').on('click', function(){
                                if ($el.hasClass('truncate')){ $el.removeClass('truncate'); $expand.text('Show less'); }
                                else { $el.addClass('truncate'); $expand.text('Show more'); }
                            });
                        }
                    }
                    applyTrunc($('#modalAddress'), $('#addressExpand'), d.address || '');
                    applyTrunc($('#modalCompanyAddress'), $('#companyAddressExpand'), d.company_address || '');

                    // Status badges
                    const isValidated = (d.status == 1 || d.status === true);
                    $('#modalStatus').prop('checked', !!isValidated);
                    $('#statusLabel').text(isValidated ? 'Validated' : 'Not Validated');
                    $('#statusLabelSmall').html(isValidated ? '<span class="badge bg-success">Validated</span>' : '<span class="badge bg-warning text-dark">Not Validated</span>');

                    // Short name line
                    $('#modalNameShort').text((d.name || '').split(' ').slice(0,2).join(' '));

                    // Load employment history
                    loadEmploymentHistory(id);

                    const modal = new bootstrap.Modal(document.getElementById('alumniModal'));
                    modal.show();
                }).fail(function(){ Swal.fire('Error','Failed to fetch details','error'); });
            });

            // Function to load employment history for an alumni
            function loadEmploymentHistory(alumniId) {
                $('#employmentHistorySection').html('<div class="alert alert-info text-center"><i class="fas fa-spinner fa-spin me-2"></i>Loading employment information...</div>');
                
                $.ajax({
                    url: 'get_employment_history.php',
                    method: 'GET',
                    data: { alumni_id: alumniId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            let html = '';
                            response.data.forEach(function(emp, index) {
                                const statusBadge = getEmploymentStatusBadge(emp.employment_status);
                                const duration = calculateDuration(emp.date_started);
                                
                                html += `
                                    <div class="card mb-3 border-start border-4" style="border-color: var(--primary) !important;">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h6 class="card-title text-primary mb-2">
                                                        <i class="fas fa-building me-2"></i>${emp.company_name || 'Company Not Specified'}
                                                    </h6>
                                                    ${emp.current_position ? `<div class="mb-2"><strong>Position:</strong> <span class="text-info">${emp.current_position}</span></div>` : ''}
                                                    <div class="mb-2">
                                                        <strong>Employment Status:</strong> ${statusBadge}
                                                    </div>
                                                    ${emp.type_of_industry ? `<div class="mb-2"><strong>Industry:</strong> ${emp.type_of_industry}</div>` : ''}
                                                    ${emp.company_address ? `<div class="mb-2"><strong>Address:</strong> ${emp.company_address}</div>` : ''}
                                                    ${emp.company_contact_no ? `<div class="mb-2"><strong>Contact:</strong> ${emp.company_contact_no}</div>` : ''}
                                                    ${emp.company_email ? `<div class="mb-2"><strong>Email:</strong> ${emp.company_email}</div>` : ''}
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <div class="text-muted small">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        ${emp.date_started ? `Started: ${formatDate(emp.date_started)}` : 'Start date not specified'}
                                                    </div>
                                                    ${emp.duration ? `<div class="text-success small mt-1 fw-bold"><i class="fas fa-clock me-1"></i>Duration: ${emp.duration}</div>` : ''}
                                                    ${duration && !emp.duration ? `<div class="text-muted small mt-1"><i class="fas fa-clock me-1"></i>Duration: ${duration}</div>` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            $('#employmentHistorySection').html(html);
                        } else {
                            $('#employmentHistorySection').html(`
                                <div class="alert alert-light text-center border">
                                    <i class="fas fa-briefcase fa-2x text-muted mb-2"></i>
                                    <div><strong>No Employment History</strong></div>
                                    <small class="text-muted">This alumni hasn't added any employment information yet.</small>
                                </div>
                            `);
                        }
                    },
                    error: function() {
                        $('#employmentHistorySection').html(`
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Failed to load employment information. Please try again.
                            </div>
                        `);
                    }
                });
            }

            // Helper function to get employment status badge
            function getEmploymentStatusBadge(status) {
                const badges = {
                    'employed': '<span class="badge bg-success">Employed</span>',
                    'not_employed': '<span class="badge bg-danger">Not Employed</span>',
                    'student': '<span class="badge bg-info">Student / Continue Schooling</span>',
                    'self_employed': '<span class="badge bg-warning text-dark">Self-employed</span>',
                    'not_specified': '<span class="badge bg-secondary">Not Specified</span>'
                };
                return badges[status] || '<span class="badge bg-secondary">Not Specified</span>';
            }

            // Helper function to calculate duration from start date
            function calculateDuration(startDate) {
                if (!startDate) return '';
                
                const start = new Date(startDate);
                const now = new Date();
                const diffTime = Math.abs(now - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays < 30) {
                    return `${diffDays} day${diffDays !== 1 ? 's' : ''}`;
                } else if (diffDays < 365) {
                    const months = Math.floor(diffDays / 30);
                    return `${months} month${months !== 1 ? 's' : ''}`;
                } else {
                    const years = Math.floor(diffDays / 365);
                    const remainingMonths = Math.floor((diffDays % 365) / 30);
                    let duration = `${years} year${years !== 1 ? 's' : ''}`;
                    if (remainingMonths > 0) {
                        duration += `, ${remainingMonths} month${remainingMonths !== 1 ? 's' : ''}`;
                    }
                    return duration;
                }
            }

            // Helper function to format date
            function formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }

            function updateSelectedCount() {
                const count = $('.row-checkbox:checked').length;
                const validateBtn = $('#validateSelected');
                const archiveBtn = $('#archiveSelected');
                
                if(count > 0) {
                    validateBtn.html(`<i class="fas fa-check-circle"></i> Validate Selected (${count})`);
                    archiveBtn.html(`<i class="fas fa-archive"></i> Archive Selected (${count})`);
                } else {
                    validateBtn.html('<i class="fas fa-check-circle"></i> Validate Selected');
                    archiveBtn.html('<i class="fas fa-archive"></i> Archive Selected');
                }
            }

            // Validate Selected - validate all selected alumni regardless of current status
            $('#validateSelected').click(function() {
                // Get all checked rows
                const selectedRows = $('.row-checkbox:checked').closest('tr');

                if(selectedRows.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Selection',
                        text: 'Please select alumni to validate.'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirm Validation',
                    html: `Are you sure you want to validate <strong>${selectedRows.length}</strong> selected alumni?<br><small class="text-muted">This will send verification emails to all selected alumni.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, validate all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show progress dialog
                        Swal.fire({
                            title: 'Validating Alumni...',
                            html: 'Processing <span id="validation-progress">0</span> of ' + selectedRows.length + ' alumni',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        let completed = 0;
                        let successful = 0;
                        const promises = [];

                        selectedRows.each(function() {
                            const row = $(this);
                            const id = row.find('.validate-btn').data('id');

                            const promise = $.ajax({
                                url: 'update_status.php',
                                type: 'POST',
                                data: { id: id, status: 'true' },
                                dataType: 'json',
                                timeout: 10000,
                                success: function(resp) {
                                    try {
                                        const response = typeof resp === 'string' ? JSON.parse(resp) : resp;
                                        if (response.status === 'success') {
                                            // Only update Registration Status badge (column 6)
                                            row.find('td').eq(6).find('.badge')
                                                .removeClass('bg-warning')
                                                .addClass('bg-success')
                                                .text('Validated');
                                            successful++;
                                        }
                                    } catch (e) {
                                        // Handle legacy string response
                                        if (typeof resp === 'string' && resp.indexOf('success') !== -1) {
                                            row.find('td').eq(6).find('.badge')
                                                .removeClass('bg-warning')
                                                .addClass('bg-success')
                                                .text('Validated');
                                            successful++;
                                        }
                                    }
                                    row.find('.row-checkbox').prop('checked', false);
                                },
                                complete: function() {
                                    completed++;
                                    $('#validation-progress').text(completed);
                                }
                            });
                            promises.push(promise);
                        });

                        Promise.all(promises).then(() => {
                            updateStats();
                            updateSelectedCount();
                            
                            Swal.fire({
                                title: 'Validation Complete!',
                                html: `<strong>${successful}</strong> out of ${selectedRows.length} alumni have been validated successfully.<br><small class="text-muted">Verification emails have been sent.</small>`,
                                icon: successful === selectedRows.length ? 'success' : 'warning',
                                confirmButtonText: 'OK'
                            });
                        }).catch(() => {
                            Swal.fire('Error', 'Some validations failed. Please try again.', 'error');
                        });
                    }
                });
            });

            // helper: archive ids via POST and show SweetAlert results
            function doArchive(ids, onSuccess){
                if (!Array.isArray(ids) || ids.length === 0) {
                    Swal.fire('No Selection', 'Please select alumni to archive', 'info');
                    return;
                }

                Swal.fire({
                    title: 'Confirm Archive',
                    text: `Are you sure you want to archive ${ids.length} selected alumni?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, archive!'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    // Show loading state
                    Swal.fire({
                        title: 'Archiving...',
                        text: 'Please wait while we archive the selected alumni.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: 'archive_alumni.php',
                        type: 'POST',
                        data: { ids: ids },
                        timeout: 15000, // 15 second timeout
                        success: function(response) {
                            let res = response;
                            try { 
                                if (typeof response === 'string') res = JSON.parse(response); 
                            } catch(e) { 
                                console.error('JSON parse error:', e);
                                Swal.fire('Error', 'Invalid response from server', 'error');
                                return;
                            }

                            if (res.status === 'success') {
                                Swal.fire('Archived!', 'Selected alumni have been archived.', 'success');
                                if (typeof onSuccess === 'function') onSuccess(res.report || {});
                            } else if (res.status === 'already') {
                                Swal.fire('Already archived', res.message || 'Items were already archived', 'info');
                                if (typeof onSuccess === 'function') onSuccess(res.report || {});
                            } else if (res.status === 'partial') {
                                // partial success
                                const arch = res.report.archived || [];
                                const already = res.report.already || [];
                                const errors = res.report.error || {};
                                let msg = '';
                                if (arch.length) msg += `Successfully archived: ${arch.length}. `;
                                if (already.length) msg += `Already archived: ${already.length}. `;
                                if (Object.keys(errors).length) msg += `Failed: ${Object.keys(errors).length}.`;
                                Swal.fire('Partial Success', msg || 'Some items processed with issues', 'warning');
                                if (typeof onSuccess === 'function') onSuccess(res.report || {});
                            } else if (res.status === 'error' && res.report) {
                                // errors with report
                                const arch = res.report.archived || [];
                                const already = res.report.already || [];
                                const errors = res.report.error || {};
                                let msg = '';
                                if (arch.length) msg += `Archived: ${arch.length}. `;
                                if (already.length) msg += `Already archived: ${already.length}. `;
                                if (Object.keys(errors).length) msg += `Errors: ${Object.keys(errors).length}.`;
                                Swal.fire('Archive Issues', msg || 'Completed with issues', 'warning');
                                if (typeof onSuccess === 'function') onSuccess(res.report || {});
                            } else {
                                Swal.fire('Error', res.message || 'Archive operation failed', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Archive AJAX error:', status, error);
                            let errorMsg = 'Archive request failed.';
                            if (status === 'timeout') {
                                errorMsg = 'Archive request timed out. Please try again.';
                            } else if (xhr.status === 403) {
                                errorMsg = 'Access denied. Please check your permissions.';
                            } else if (xhr.status === 500) {
                                errorMsg = 'Server error occurred during archiving.';
                            }
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    });
                });
            }

            // Archive Selected uses doArchive and removes rows on success
            $('#archiveSelected').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = $(this);
                if (btn.prop('disabled')) return; // Prevent multiple clicks
                
                const selectedRows = $('.row-checkbox:checked').closest('tr');
                if (selectedRows.length === 0) {
                    Swal.fire('No Selection', 'Please select alumni to archive', 'info');
                    return;
                }
                
                // Disable button during operation
                btn.prop('disabled', true).addClass('processing');
                
                const ids = selectedRows.map(function() { return $(this).find('.archive-btn').data('id'); }).get();
                
                doArchive(ids, function(report){
                    try {
                        // remove rows for archived ids
                        (report.archived || []).forEach(function(id){
                            const rowToRemove = selectedRows.filter(function(){ 
                                return $(this).find('.archive-btn').data('id') == id; 
                            });
                            if (rowToRemove.length) {
                                try {
                                    table.row(rowToRemove).remove();
                                } catch(e) {
                                    rowToRemove.remove();
                                }
                            }
                        });
                        
                        // Redraw table and update stats
                        table.draw();
                        updateStats();
                        
                        // Clear selections
                        $('.row-checkbox:checked').prop('checked', false);
                        updateSelectedCount();
                    } catch(e) {
                        console.error('Error updating UI after archive:', e);
                    } finally {
                        // Re-enable button
                        btn.prop('disabled', false).removeClass('processing');
                    }
                });
            });

            // Individual validate button
            $(document).on('click', '.validate-btn', function(e) {
                e.stopPropagation();
                const btn = $(this);
                const row = btn.closest('tr');
                const id = btn.data('id');

                // Debug: Check if ID is valid
                if (!id) {
                    Swal.fire('Error', 'Alumni ID not found. Please refresh the page and try again.', 'error');
                    return;
                }

                console.log('Validating alumni with ID:', id); // Debug log

                // Show confirmation dialog
                Swal.fire({
                    title: 'Validate Alumni',
                    text: 'Are you sure you want to validate this alumni? This will send a verification email.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, validate!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button during processing
                        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                        
                        $.ajax({
                            url: 'update_status.php',
                            type: 'POST',
                            data: { 
                                id: id, 
                                status: 'true'  // Send as string to match PHP check
                            },
                            dataType: 'json', // Expect JSON response
                            timeout: 10000, // 10 second timeout
                            success: function(response) {
                                console.log('Validation response:', response); // Debug log
                                
                                if (response && response.status === 'success') {
                                    // Only update Registration Status badge (column 6)
                                    row.find('td').eq(6).find('.badge')
                                        .removeClass('bg-warning')
                                        .addClass('bg-success')
                                        .text('Validated');
                                    updateStats();
                                    
                                    // Show success message
                                    Swal.fire({
                                        title: 'Validated!',
                                        text: response.message || 'Alumni has been validated and notification email sent.',
                                        icon: 'success',
                                        timer: 3000
                                    });
                                } else {
                                    Swal.fire('Error', response.message || 'Could not validate the alumni.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error, xhr.responseText); // Debug log
                                
                                let errorMessage = 'Request failed.';
                                if (status === 'timeout') {
                                    errorMessage = 'Request timed out. Please try again.';
                                } else if (xhr.status === 403) {
                                    errorMessage = 'Access denied. Please check your permissions.';
                                } else if (xhr.status === 404) {
                                    errorMessage = 'Validation service not found.';
                                } else if (xhr.responseText) {
                                    try {
                                        const errorResp = JSON.parse(xhr.responseText);
                                        errorMessage = errorResp.message || errorMessage;
                                    } catch (e) {
                                        errorMessage = 'Server error: ' + xhr.responseText.substring(0, 100);
                                    }
                                }
                                
                                Swal.fire('Error', errorMessage, 'error');
                            },
                            complete: function() {
                                // Re-enable button
                                btn.prop('disabled', false).html('<i class="fas fa-check"></i>');
                            }
                        });
                    }
                });
            });

            // Individual archive button uses doArchive
            $(document).on('click', '.archive-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = $(this);
                if (btn.prop('disabled')) return; // Prevent multiple clicks
                
                const row = btn.closest('tr');
                const id = btn.data('id');
                
                if (!id) {
                    Swal.fire('Error', 'Invalid alumni ID', 'error');
                    return;
                }
                
                // Disable button during operation
                btn.prop('disabled', true).addClass('processing');
                
                doArchive([id], function(report){
                    try {
                        if ((report.archived || []).indexOf(id) !== -1) {
                            try { 
                                table.row(row).remove().draw(); 
                            } catch(e) { 
                                row.remove(); 
                            }
                            updateStats();
                        }
                    } catch(e) {
                        console.error('Error updating UI after individual archive:', e);
                    } finally {
                        // Re-enable button
                        btn.prop('disabled', false).removeClass('processing');
                    }
                });
            });

            // Stats card click handler - filter table directly (no modal)
            $('.clickable-stat').css('cursor', 'pointer').on('click', function() {
                const type = $(this).data('type');
                if (type === 'total') {
                    table.search('').columns().search('').draw();
                } else if (type === 'validated') {
                    // show only validated
                    table.column(6).search('Validated', false, true).draw();
                } else if (type === 'notvalidated') {
                    // show only not validated
                    table.column(6).search('Not Validated', false, true).draw();
                } else if (type === 'courses') {
                    // clear filters and let user click course chart or use Search
                    table.search('').columns().search('').draw();
                }
                // update stats counters in case filtering changed counts shown
                updateStats();
            });

            // Helper to build alumni table for modal
            function buildAlumniTable(rows) {
                if (rows.length === 0) return '<div class="alert alert-info">No records found.</div>';
                let html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>Name</th><th>Course</th><th>Batch</th><th>Email</th><th>Status</th></tr></thead><tbody>';
                rows.each(function(i) {
                    let tds = $(this).find('td');
                    html += '<tr>';
                    // Skip checkbox column (index 0) and actions column (index 7)
                    for (let j = 1; j < 7; j++) {
                        html += `<td>${tds.eq(j).html()}</td>`;
                    }
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
                return html;
            }

            // Initial setup
            table.buttons().container().appendTo('#alumniTable_wrapper .col-md-6:eq(0)');

            // Archive modal behavior: load archives when modal opens
            let archiveTableInstance = null;
            function loadArchives() {
                $('#archiveTable tbody').html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
                $.get('get_archives.php').done(function(res){
                    try {
                        const data = (typeof res === 'string') ? JSON.parse(res) : res;
                        if (data.status !== 'success') {
                            $('#archiveTable tbody').html('<tr><td colspan="8" class="text-danger text-center">Failed to load archives</td></tr>');
                            return;
                        }

                        const rows = data.data || [];
                        $('#archiveTotal').text(rows.length);
                        const tbody = rows.map(function(r, i){
                            const name = `${r.firstname} ${r.middlename || ''} ${r.lastname}`.replace(/  +/g,' ').trim();
                            const course = r.course_name || '';
                            const status = (r.status == 1) ? 'Validated' : 'Not Validated';
                            return `
                                <tr data-arch-id="${r.id}">
                                    <td><input type="checkbox" class="arch-row-chk" data-id="${r.id}"></td>
                                    <td>${name}</td>
                                    <td>${course}</td>
                                    <td>${r.batch || ''}</td>
                                    <td>${r.email || ''}</td>
                                    <td>${r.archived_date || ''}</td>
                                    <td>${r.archived_by_name || ''}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success restore-archived" data-id="${r.id}"><i class="fas fa-undo"></i> Restore</button>
                                    </td>
                                </tr>`;
                        }).join('');
                        $('#archiveTable tbody').html(rows.length ? rows.map((r,i)=>{
                            const name = `${r.firstname} ${r.middlename || ''} ${r.lastname}`.replace(/  +/g,' ').trim();
                            const course = r.course_name || '';
                            return `
                                <tr data-arch-id="${r.id}">
                                    <td><input type="checkbox" class="arch-row-chk" data-id="${r.id}"></td>
                                    <td>${name}</td>
                                    <td>${course}</td>
                                    <td>${r.batch || ''}</td>
                                    <td>${r.email || ''}</td>
                                    <td>${r.archived_date || ''}</td>
                                    <td>${r.archived_by_name || ''}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success restore-archived" data-id="${r.id}"><i class="fas fa-undo"></i> Restore</button>
                                    </td>
                                </tr>`;
                        }).join('') : '<tr><td colspan="8" class="text-center">No archived records</td></tr>');

                        // Initialize or re-draw simple DataTable for archives
                        if ($.fn.DataTable.isDataTable('#archiveTable')) {
                            try { $('#archiveTable').DataTable().destroy(); } catch(e){}
                        }
                        archiveTableInstance = $('#archiveTable').DataTable({
                            responsive: true,
                            paging: true,
                            ordering: true,
                            info: false,
                            lengthChange: false,
                            searching: true,
                            columnDefs: [{ targets: 0, orderable: false }]
                        });
                    } catch (e) {
                        $('#archiveTable tbody').html('<tr><td colspan="8" class="text-danger text-center">Invalid server response</td></tr>');
                    }
                }).fail(function(){
                    $('#archiveTable tbody').html('<tr><td colspan="8" class="text-danger text-center">Error contacting server</td></tr>');
                });
            }

            // Header View Archive button: show archive modal
            $('#viewArchiveBtn').off('click').on('click', function(e){
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
                modal.show();
                loadArchives();
            });

            // All Alumni button: navigate to all alumni page
            $('#allAlumniBtn').off('click').on('click', function(e){
                e.preventDefault();
                window.location.href = 'all_alumni.php';
            });

            // Logout confirmation handler (intercepts link and asks user to confirm)
            $(document).on('click', '#logoutBtn', function(e) {
                e.preventDefault();
                const href = $(this).data('href') || $(this).attr('href');
                Swal.fire({
                    title: 'Logout',
                    text: 'Are you sure you want to logout?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, logout',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33'
                }).then((res) => {
                    if (res.isConfirmed) {
                        // redirect to server-side logout which destroys session
                        window.location.href = href;
                    }
                });
            });

            // Enhanced Homecoming Invite Modal
            let homecomingData = {
                selectedCourses: [],
                coursesData: [],
                minLimit: 5,
                maxLimit: 10,
                currentStep: 1,
                recipients: []
            };

            $('#homecomingInviteBtn').on('click', function(){
                $('#homecomingInviteModal').modal('show');
                loadCoursesData();
                resetToStep1();
                // Initialize letter preview
                setTimeout(updateLetterPreview, 100);
            });

            function resetToStep1() {
                homecomingData.currentStep = 1;
                $('#step1-tab').tab('show');
                updateStepNavigation();
            }

            function loadCoursesData() {
                $('#coursesList').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading courses...</span></div></div>');
                
                $.post('send_invite_homecoming.php', { action: 'get_courses' })
                    .done(function(response) {
                        if (response.status === 'success') {
                            homecomingData.coursesData = response.courses;
                            renderCoursesList();
                        } else {
                            $('#coursesList').html('<div class="alert alert-danger">Failed to load courses</div>');
                        }
                    })
                    .fail(function() {
                        $('#coursesList').html('<div class="alert alert-danger">Error loading courses</div>');
                    });
            }

            function renderCoursesList() {
                let html = '';
                homecomingData.coursesData.forEach(course => {
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input course-checkbox" type="checkbox" 
                                   id="course_${course.id}" value="${course.id}" data-course-name="${course.course}" 
                                   data-alumni-count="${course.alumni_count}">
                            <label class="form-check-label" for="course_${course.id}">
                                <strong>${course.course}</strong>
                                <small class="text-muted d-block">${course.alumni_count} alumni with email</small>
                            </label>
                        </div>
                    `;
                });
                $('#coursesList').html(html);
                
                // Bind events
                $('.course-checkbox').on('change', updateCourseSelection);
                $('#selectAllCourses').on('change', function() {
                    $('.course-checkbox').prop('checked', $(this).is(':checked')).trigger('change');
                });
            }

            function updateCourseSelection() {
                homecomingData.selectedCourses = [];
                let totalAlumni = 0;
                
                $('.course-checkbox:checked').each(function() {
                    const courseId = $(this).val();
                    const courseName = $(this).data('course-name');
                    const alumniCount = parseInt($(this).data('alumni-count'));
                    
                    homecomingData.selectedCourses.push({
                        id: courseId,
                        name: courseName,
                        count: alumniCount
                    });
                    totalAlumni += alumniCount;
                });
                
                $('#selectedCourseCount').text(homecomingData.selectedCourses.length);
                $('#totalSelectedAlumni').text(totalAlumni);
                $('#alumniWithEmail').text(totalAlumni);
                
                renderSelectedCoursesList();
                updateLimitsPreview();
            }

            function renderSelectedCoursesList() {
                let html = '';
                if (homecomingData.selectedCourses.length > 0) {
                    html = '<h6 class="text-success mb-2">Selected Courses:</h6>';
                    homecomingData.selectedCourses.forEach(course => {
                        html += `
                            <div class="badge bg-primary me-2 mb-2">
                                ${course.name} (${course.count})
                            </div>
                        `;
                    });
                } else {
                    html = '<p class="text-muted">No courses selected</p>';
                }
                $('#selectedCoursesList').html(html);
            }

            // Step navigation
            $('#nextStepBtn').on('click', function() {
                if (homecomingData.currentStep < 4) {
                    if (validateCurrentStep()) {
                        homecomingData.currentStep++;
                        showStep(homecomingData.currentStep);
                        updateStepNavigation();
                    }
                }
            });

            $('#prevStepBtn').on('click', function() {
                if (homecomingData.currentStep > 1) {
                    homecomingData.currentStep--;
                    showStep(homecomingData.currentStep);
                    updateStepNavigation();
                }
            });

            function showStep(stepNumber) {
                $(`#step${stepNumber}-tab`).tab('show');
                
                if (stepNumber === 2) {
                    updateLimitsPreview();
                } else if (stepNumber === 3) {
                    updateLetterPreview();
                } else if (stepNumber === 4) {
                    prepareFinalReview();
                }
            }

            function updateStepNavigation() {
                $('#prevStepBtn').toggle(homecomingData.currentStep > 1);
                $('#nextStepBtn').toggle(homecomingData.currentStep < 4);
                
                if (homecomingData.currentStep === 4) {
                    $('#nextStepBtn').hide();
                } else {
                    $('#nextStepBtn').show();
                }
            }

            function validateCurrentStep() {
                if (homecomingData.currentStep === 1) {
                    if (homecomingData.selectedCourses.length === 0) {
                        Swal.fire('Selection Required', 'Please select at least one course', 'warning');
                        return false;
                    }
                }
                return true;
            }

            // Step 2: Limits handling
            $('#minAlumniLimit, #maxAlumniLimit').on('input', function() {
                homecomingData.minLimit = parseInt($('#minAlumniLimit').val()) || 5;
                homecomingData.maxLimit = parseInt($('#maxAlumniLimit').val()) || 10;
                
                if (homecomingData.minLimit > homecomingData.maxLimit) {
                    $('#maxAlumniLimit').val(homecomingData.minLimit);
                    homecomingData.maxLimit = homecomingData.minLimit;
                }
                
                updateLimitsPreview();
            });

            function updateLimitsPreview() {
                let html = '<h6>Preview:</h6>';
                let totalInvitations = 0;
                
                homecomingData.selectedCourses.forEach(course => {
                    let selectedCount = Math.min(Math.max(course.count, homecomingData.minLimit), homecomingData.maxLimit);
                    if (course.count < homecomingData.minLimit) {
                        selectedCount = course.count;
                    }
                    
                    totalInvitations += selectedCount;
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                            <div>
                                <strong>${course.name}</strong><br>
                                <small class="text-muted">${course.count} total alumni</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">${selectedCount} selected</span>
                            </div>
                        </div>
                    `;
                });
                
                $('#limitsPreview').html(html);
                $('#totalInvitations').text(totalInvitations);
                $('#summarySelectedCourses').text(homecomingData.selectedCourses.length);
                $('#avgPerCourse').text(homecomingData.selectedCourses.length > 0 ? 
                    Math.round(totalInvitations / homecomingData.selectedCourses.length) : 0);
            }

            // Step 3: Letter composition and preview
            $('#inviteSubjectLine, #inviteLetterContent').on('input', updateLetterPreview);

            function updateLetterPreview() {
                const subject = $('#inviteSubjectLine').val();
                const content = $('#inviteLetterContent').val();
                
                // Sample data for preview
                const sampleData = {
                    name: 'Juan Dela Cruz',
                    firstname: 'Juan',
                    lastname: 'Dela Cruz',
                    course: 'Bachelor of Science in Information Technology'
                };
                
                let previewContent = content;
                Object.keys(sampleData).forEach(key => {
                    const regex = new RegExp(`{{${key}}}`, 'g');
                    previewContent = previewContent.replace(regex, sampleData[key]);
                });
                
                $('#previewSubject').text(subject);
                $('#letterPreview').html(previewContent.replace(/\n/g, '<br>'));
            }

            // Test email functionality
            $('#sendTestPreview').on('click', function() {
                const testEmail = $('#testEmailAddress').val();
                if (!testEmail) {
                    Swal.fire('Email Required', 'Please enter a test email address', 'warning');
                    return;
                }
                
                const subject = $('#inviteSubjectLine').val();
                const content = $('#inviteLetterContent').val();
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
                
                $.post('send_invite_homecoming.php', {
                    action: 'send_selected',
                    recipients: JSON.stringify([{
                        firstname: 'Test',
                        lastname: 'User',
                        email: testEmail,
                        course: 'Sample Course'
                    }]),
                    subject: subject,
                    content: content
                })
                .done(function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Test Sent!', 'Test invitation sent successfully', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Failed to send test email', 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Request failed', 'error');
                })
                .always(function() {
                    $('#sendTestPreview').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Send Test Email');
                });
            });

            // Step 4: Final review and send
            function prepareFinalReview() {
                const selectedCourseNames = homecomingData.selectedCourses.map(c => c.name).join(', ');
                let totalInvitations = 0;
                
                homecomingData.selectedCourses.forEach(course => {
                    let selectedCount = Math.min(Math.max(course.count, homecomingData.minLimit), homecomingData.maxLimit);
                    if (course.count < homecomingData.minLimit) {
                        selectedCount = course.count;
                    }
                    totalInvitations += selectedCount;
                });
                
                $('#finalReview').html(`
                    <p><strong>Selected Courses:</strong> ${homecomingData.selectedCourses.length}</p>
                    <p><strong>Course Names:</strong> ${selectedCourseNames}</p>
                    <p><strong>Alumni Limits:</strong> ${homecomingData.minLimit} - ${homecomingData.maxLimit} per course</p>
                    <p><strong>Total Invitations:</strong> ${totalInvitations}</p>
                    <p><strong>Subject:</strong> ${$('#inviteSubjectLine').val()}</p>
                `);
            }

            $('#launchInvitations').on('click', function() {
                Swal.fire({
                    title: 'Confirm Send',
                    text: 'Are you sure you want to send all invitations? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendAllInvitations();
                    }
                });
            });

            function sendAllInvitations() {
                $('#sendingProgress').show();
                $('#launchInvitations').prop('disabled', true);
                
                // Get recipients based on selected courses and limits
                $.post('send_invite_homecoming.php', {
                    action: 'get_recipients',
                    courses: JSON.stringify(homecomingData.selectedCourses.map(c => c.id)),
                    limit: homecomingData.maxLimit,
                    min_limit: homecomingData.minLimit
                })
                .done(function(response) {
                    if (response.status === 'success') {
                        const recipients = response.recipients;
                        const subject = $('#inviteSubjectLine').val();
                        const content = $('#inviteLetterContent').val();
                        
                        $('#sendProgressCount').text(`0 / ${recipients.length} sent`);
                        
                        // Send invitations
                        $.post('send_invite_homecoming.php', {
                            action: 'send_selected',
                            recipients: JSON.stringify(recipients),
                            subject: subject,
                            content: content
                        })
                        .done(function(sendResponse) {
                            $('#sendProgressBar').css('width', '100%').text('100%');
                            $('#sendProgressText').text('Completed!');
                            $('#sendProgressCount').text(`${sendResponse.sent} / ${sendResponse.total} sent`);
                            
                            Swal.fire({
                                title: 'Invitations Sent!',
                                html: `
                                    <p><strong>Successfully sent:</strong> ${sendResponse.sent}</p>
                                    <p><strong>Failed:</strong> ${sendResponse.failed}</p>
                                    <p><strong>Total:</strong> ${sendResponse.total}</p>
                                `,
                                icon: 'success'
                            });
                        })
                        .fail(function() {
                            Swal.fire('Error', 'Failed to send invitations', 'error');
                        })
                        .always(function() {
                            $('#launchInvitations').prop('disabled', false);
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to get recipients', 'error');
                        $('#launchInvitations').prop('disabled', false);
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to get recipients', 'error');
                    $('#launchInvitations').prop('disabled', false);
                });
            }

            // RSVP Modal functionality
            $('#viewRSVPBtn').on('click', function() {
                $('#rsvpModal').modal('show');
                loadRSVPData();
            });

            // Speaker RSVP Modal functionality
            $('#viewSpeakerRSVPBtn').on('click', function() {
                $('#speakerRSVPModal').modal('show');
                loadSpeakerRSVPData();
            });

            function loadRSVPData() {
                // Show loading state
                $('#rsvpTable tbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
                
                $.post('get_rsvp_data.php')
                    .done(function(response) {
                        if (response.status === 'success') {
                            updateRSVPSummary(response.summary);
                            populateRSVPTables(response.data);
                        } else {
                            $('#rsvpTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Failed to load RSVP data</td></tr>');
                        }
                    })
                    .fail(function() {
                        $('#rsvpTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading RSVP data</td></tr>');
                    });
            }

            function updateRSVPSummary(summary) {
                $('#attendingCount').text(summary.attending || 0);
                $('#notAttendingCount').text(summary.not_attending || 0);
                $('#pendingCount').text(summary.pending || 0);
                $('#totalInvited').text(summary.total || 0);
            }

            function populateRSVPTables(data) {
                // All responses table
                let allHtml = '';
                let attendingHtml = '';
                let notAttendingHtml = '';
                let pendingHtml = '';

                data.forEach(function(item) {
                    const name = `${item.firstname} ${item.lastname}`;
                    const course = item.course || 'Unknown';
                    const email = item.email || 'No email';
                    
                    let statusBadge = '';
                    let responseDate = '';
                    
                    if (item.response === 'attending') {
                        statusBadge = '<span class="badge bg-success">Attending</span>';
                        responseDate = formatDate(item.responded_at);
                        attendingHtml += `
                            <tr>
                                <td>${name}</td>
                                <td>${course}</td>
                                <td>${email}</td>
                                <td>${responseDate}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary resend-invite" data-id="${item.alumni_id}">
                                        <i class="fas fa-envelope"></i> Resend
                                    </button>
                                </td>
                            </tr>
                        `;
                    } else if (item.response === 'not_attending') {
                        statusBadge = '<span class="badge bg-danger">Not Attending</span>';
                        responseDate = formatDate(item.responded_at);
                        notAttendingHtml += `
                            <tr>
                                <td>${name}</td>
                                <td>${course}</td>
                                <td>${email}</td>
                                <td>${responseDate}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary resend-invite" data-id="${item.alumni_id}">
                                        <i class="fas fa-envelope"></i> Resend
                                    </button>
                                </td>
                            </tr>
                        `;
                    } else {
                        statusBadge = '<span class="badge bg-warning text-dark">No Response</span>';
                        responseDate = 'Pending';
                        pendingHtml += `
                            <tr>
                                <td>${name}</td>
                                <td>${course}</td>
                                <td>${email}</td>
                                <td>${formatDate(item.created_at)}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary resend-invite" data-id="${item.alumni_id}">
                                        <i class="fas fa-envelope"></i> Resend
                                    </button>
                                </td>
                            </tr>
                        `;
                    }

                    allHtml += `
                        <tr>
                            <td>${name}</td>
                            <td>${course}</td>
                            <td>${email}</td>
                            <td>${statusBadge}</td>
                            <td>${responseDate}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary resend-invite" data-id="${item.alumni_id}">
                                    <i class="fas fa-envelope"></i> Resend
                                </button>
                            </td>
                        </tr>
                    `;
                });

                $('#rsvpTable tbody').html(allHtml || '<tr><td colspan="6" class="text-center">No RSVP data found</td></tr>');
                $('#attendingTable tbody').html(attendingHtml || '<tr><td colspan="5" class="text-center">No attending responses</td></tr>');
                $('#notAttendingTable tbody').html(notAttendingHtml || '<tr><td colspan="5" class="text-center">No declining responses</td></tr>');
                $('#pendingTable tbody').html(pendingHtml || '<tr><td colspan="5" class="text-center">No pending responses</td></tr>');
            }

            function formatDate(dateString) {
                if (!dateString) return 'N/A';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Resend invite functionality
            $(document).on('click', '.resend-invite', function() {
                const alumniId = $(this).data('id');
                const btn = $(this);
                
                Swal.fire({
                    title: 'Resend Invitation?',
                    text: 'This will send another homecoming invitation to this alumni.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, resend',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                        
                        $.post('resend_invite.php', { alumni_id: alumniId })
                            .done(function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Sent!', 'Invitation resent successfully', 'success');
                                } else {
                                    Swal.fire('Error', response.message || 'Failed to resend invitation', 'error');
                                }
                            })
                            .fail(function() {
                                Swal.fire('Error', 'Request failed', 'error');
                            })
                            .always(function() {
                                btn.prop('disabled', false).html('<i class="fas fa-envelope"></i> Resend');
                            });
                    }
                });
            });

            // Export RSVP data
            $('#exportRSVPBtn').on('click', function() {
                window.open('export_rsvp.php', '_blank');
            });

            // Load Speaker RSVP Data
            function loadSpeakerRSVPData() {
                $('#speakerRsvpTableBody').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                
                $.get('get_speaker_rsvp_data.php')
                    .done(function(response) {
                        if (response.status === 'success') {
                            updateSpeakerRSVPSummary(response.summary);
                            populateSpeakerRSVPTable(response.data);
                        } else {
                            $('#speakerRsvpTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load Speaker RSVP data</td></tr>');
                        }
                    })
                    .fail(function() {
                        $('#speakerRsvpTableBody').html('<tr><td colspan="8" class="text-center text-danger">Error loading Speaker RSVP data</td></tr>');
                    });
            }

            function updateSpeakerRSVPSummary(summary) {
                $('#speakerAcceptCount').text(summary.accept || 0);
                $('#speakerDeclineCount').text(summary.decline || 0);
                $('#speakerPendingCount').text(summary.pending || 0);
                $('#speakerTotalCount').text(summary.total || 0);
            }

            function populateSpeakerRSVPTable(data) {
                if (data.length === 0) {
                    $('#speakerRsvpTableBody').html('<tr><td colspan="8" class="text-center text-muted">No speaker invitations sent yet</td></tr>');
                    return;
                }

                let html = '';
                data.forEach(function(item) {
                    const name = `${item.firstname} ${item.lastname}`;
                    const email = item.email || 'No email';
                    const course = item.course || 'Unknown';
                    const batch = item.batch || 'N/A';
                    const eventDate = item.event_date || 'N/A';
                    const eventTopic = item.event_topic || 'N/A';
                    
                    let statusBadge = '';
                    let responseDate = '';
                    
                    if (item.response === 'accept') {
                        statusBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Accepted</span>';
                        responseDate = formatDate(item.updated_at);
                    } else if (item.response === 'decline') {
                        statusBadge = '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Declined</span>';
                        responseDate = formatDate(item.updated_at);
                    } else {
                        statusBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>';
                        responseDate = 'No response yet';
                    }

                    html += `
                        <tr>
                            <td><strong>${name}</strong></td>
                            <td>${email}</td>
                            <td>${course}</td>
                            <td>${batch}</td>
                            <td>${eventDate}</td>
                            <td>${eventTopic}</td>
                            <td>${statusBadge}</td>
                            <td>${responseDate}</td>
                        </tr>
                    `;
                });

                $('#speakerRsvpTableBody').html(html);
            }

            // Filter Speaker RSVP tabs
            $('#speaker-accept-tab').on('click', function() {
                filterSpeakerRSVPTable('accept');
            });
            $('#speaker-decline-tab').on('click', function() {
                filterSpeakerRSVPTable('decline');
            });
            $('#speaker-pending-tab').on('click', function() {
                filterSpeakerRSVPTable('pending');
            });
            $('#speaker-all-tab').on('click', function() {
                filterSpeakerRSVPTable('all');
            });

            function filterSpeakerRSVPTable(filter) {
                const rows = $('#speakerRsvpTableBody tr');
                rows.each(function() {
                    const row = $(this);
                    const badge = row.find('.badge');
                    
                    if (filter === 'all') {
                        row.show();
                    } else if (filter === 'accept' && badge.hasClass('bg-success')) {
                        row.show();
                    } else if (filter === 'decline' && badge.hasClass('bg-danger')) {
                        row.show();
                    } else if (filter === 'pending' && badge.hasClass('bg-warning')) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }

            // Export Speaker RSVP data
            $('#exportSpeakerRSVPBtn').on('click', function() {
                Swal.fire({
                    title: 'Export Speaker RSVP',
                    text: 'This feature will be available soon',
                    icon: 'info'
                });
            });


            // Search input binding
            $(document).on('input', '#archiveSearch', function(){
                const q = $(this).val();
                if (archiveTableInstance) archiveTableInstance.search(q).draw();
            });

            // Single restore
            $(document).on('click', '.restore-archived', function(){
                const btn = $(this);
                const id = btn.data('id');
                Swal.fire({
                    title: 'Restore?',
                    text: 'Are you sure you want to restore this alumni?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, restore',
                    confirmButtonColor: '#198754'
                }).then((res)=>{
                    if (!res.isConfirmed) return;
                    $.post('restore_alumni.php', { id: id }).done(function(resp){
                        const data = (typeof resp === 'string') ? JSON.parse(resp) : resp;
                        if (data.status === 'success') {
                            // remove row from archives table
                            const row = btn.closest('tr');
                            if (archiveTableInstance) {
                                archiveTableInstance.row(row).remove().draw();
                            } else row.remove();
                            // decrement count
                            const cur = parseInt($('#archiveTotal').text()||'0',10);
                            $('#archiveTotal').text(Math.max(0, cur-1));
                            // refresh main table (reload or ajax fetch) - simple approach: reload page
                            // but we'll try to remove the restored row from main table if present
                            try {
                                // If main table has a row for this alumni (id matches), remove it
                                table.rows().every(function(){
                                    const n = $(this.node());
                                    const aid = n.find('.row-checkbox').data('id');
                                    if (String(aid) === String(id)) {
                                        this.remove();
                                    }
                                });
                                table.draw(); updateStats();
                            } catch(e){}

                            Swal.fire('Restored', data.message || 'Alumni restored successfully', 'success');
                        } else {
                            Swal.fire('Error', data.message || 'Restore failed', 'error');
                        }
                    }).fail(function(){ Swal.fire('Error', 'Request failed', 'error'); });
                });
            });

            // Bulk restore
            $('#restoreSelectedArchived').on('click', function(){
                const ids = $('.arch-row-chk:checked').map(function(){ return $(this).data('id'); }).get();
                if (ids.length === 0) {
                    Swal.fire('No Selection', 'Please select archived alumni to restore', 'info');
                    return;
                }
                Swal.fire({
                    title: 'Restore selected?',
                    text: `Restore ${ids.length} selected archived alumni?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, restore',
                    confirmButtonColor: '#198754'
                }).then((res)=>{
                    if (!res.isConfirmed) return;
                    const promises = ids.map(id => {
                        return $.post('restore_alumni.php', { id: id });
                    });
                    Promise.all(promises).then(function(results){
                        let successCount = 0;
                        results.forEach(function(r){
                            const data = (typeof r === 'string') ? JSON.parse(r) : r;
                            if (data.status === 'success') successCount++;
                        });
                        // reload archives list
                        loadArchives();
                        // refresh main table
                        try { table.ajax && table.ajax.reload(); } catch(e) { /* best effort */ }
                        Swal.fire('Done', `${successCount} restored.`, 'success');
                    }).catch(function(){ Swal.fire('Error', 'Some restores failed', 'error'); });
                });
            });

            // ==================== GUEST SPEAKER INVITATION SYSTEM ====================
            const speakerData = {
                selectedAlumni: [],
                currentStep: 1,
                allCourses: []
            };

            // Open speaker invitation modal
            $('#speakerInviteBtn').on('click', function(){
                $('#speakerInviteModal').modal('show');
                loadSpeakerCourses();
                resetSpeakerToStep1();
                setTimeout(updateSpeakerLetterPreview, 100);
            });

            function resetSpeakerToStep1() {
                speakerData.currentStep = 1;
                speakerData.selectedAlumni = [];
                $('#speaker-step1-tab').tab('show');
                updateSpeakerStepNavigation();
                $('#selectedSpeakerCount').text('0');
                $('#speakerSearchInput').val('');
                $('#speakerCourseFilter').val('0');
                
                // Show helpful message with option to load all
                $('#alumniSearchResults').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Search for Alumni</h6>
                        <p class="text-muted small">Type in the search box above or click below to load all alumni</p>
                        <button class="btn btn-primary mt-2" onclick="searchSpeakerAlumni()">
                            <i class="fas fa-users me-1"></i>Load All Alumni
                        </button>
                    </div>
                `);
            }

            // Load courses for filtering
            function loadSpeakerCourses() {
                $.post('send_invite_speaker.php', { action: 'get_courses' })
                    .done(function(response) {
                        if (response.status === 'success') {
                            speakerData.allCourses = response.courses;
                            let options = '<option value="0">All Courses</option>';
                            response.courses.forEach(course => {
                                options += `<option value="${course.id}">${course.course} (${course.alumni_count})</option>`;
                            });
                            $('#speakerCourseFilter').html(options);
                        }
                    });
            }

            // Search alumni - Real-time search with debounce
            let searchTimeout;
            $('#speakerSearchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchSpeakerAlumni();
                }, 500); // Wait 500ms after user stops typing
            });
            
            $('#speakerSearchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    searchSpeakerAlumni();
                }
            });
            
            $('#searchAlumniBtn').on('click', function(e) {
                e.preventDefault();
                clearTimeout(searchTimeout);
                searchSpeakerAlumni();
            });
            
            // Course filter change triggers search
            $('#speakerCourseFilter').on('change', function() {
                searchSpeakerAlumni();
            });

            function searchSpeakerAlumni() {
                const search = $('#speakerSearchInput').val();
                const courseFilter = $('#speakerCourseFilter').val();
                
                $('#alumniSearchResults').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Searching alumni...</p></div>');
                
                $.ajax({
                    url: 'send_invite_speaker.php',
                    method: 'POST',
                    data: {
                        action: 'get_alumni',
                        search: search,
                        course_filter: courseFilter
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            if (response.alumni && response.alumni.length > 0) {
                                renderAlumniResults(response.alumni);
                            } else {
                                $('#alumniSearchResults').html(`
                                    <div class="text-center py-4">
                                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No alumni found matching your search criteria.</p>
                                        <small class="text-muted">Try adjusting your search terms or filters.</small>
                                    </div>
                                `);
                            }
                        } else {
                            $('#alumniSearchResults').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    ${response.message || 'Failed to load alumni'}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Search error:', {xhr, status, error});
                        console.error('Response text:', xhr.responseText);
                        
                        let errorMessage = 'Please try again later.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch(e) {
                            // If response is not JSON, show the raw text (truncated)
                            errorMessage = xhr.responseText ? xhr.responseText.substring(0, 200) : error;
                        }
                        
                        $('#alumniSearchResults').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Error loading alumni</strong><br>
                                <small class="d-block mt-2">${errorMessage}</small>
                                <button class="btn btn-sm btn-outline-danger mt-2" onclick="searchSpeakerAlumni()">
                                    <i class="fas fa-redo me-1"></i>Retry
                                </button>
                            </div>
                        `);
                    }
                });
            }

            function renderAlumniResults(alumni) {
                if (alumni.length === 0) {
                    $('#alumniSearchResults').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No alumni found</p>
                        </div>
                    `);
                    return;
                }
                
                let html = `
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="fas fa-users me-1"></i>${alumni.length} alumni found</small>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllAlumni">
                            <i class="fas fa-check-double me-1"></i>Select All
                        </button>
                    </div>
                    <div class="list-group">
                `;
                
                alumni.forEach((alumnus, index) => {
                    const isSelected = speakerData.selectedAlumni.some(a => a.id === alumnus.id);
                    const checkClass = isSelected ? 'checked' : '';
                    const selectedClass = isSelected ? 'border-primary bg-light' : '';
                    
                    html += `
                        <div class="list-group-item list-group-item-action ${selectedClass} alumni-item" data-alumni-id="${alumnus.id}">
                            <div class="form-check">
                                <input class="form-check-input alumni-speaker-checkbox" type="checkbox" 
                                       id="alumni-${alumnus.id}" value="${alumnus.id}" ${checkClass}
                                       data-alumni='${JSON.stringify(alumnus).replace(/'/g, "&apos;")}'>
                                <label class="form-check-label w-100 cursor-pointer" for="alumni-${alumnus.id}">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <strong class="text-dark">${alumnus.fullname || 'N/A'}</strong>
                                            </div>
                                            <div class="ms-4">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-graduation-cap me-1"></i>${alumnus.course || 'No course specified'}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-envelope me-1"></i>${alumnus.email || 'No email'}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end ms-2">
                                            <span class="badge bg-info text-white">${alumnus.batch_year || 'N/A'}</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#alumniSearchResults').html(html);
                
                // Select all functionality
                $('#selectAllAlumni').on('click', function() {
                    const allChecked = $('.alumni-speaker-checkbox:checked').length === alumni.length;
                    $('.alumni-speaker-checkbox').prop('checked', !allChecked).trigger('change');
                    $(this).html(allChecked ? 
                        '<i class="fas fa-check-double me-1"></i>Select All' : 
                        '<i class="fas fa-times me-1"></i>Deselect All'
                    );
                });
                
                // Bind checkbox events with real-time updates
                $('.alumni-speaker-checkbox').on('change', function() {
                    const alumniData = JSON.parse($(this).attr('data-alumni').replace(/&apos;/g, "'"));
                    const $item = $(this).closest('.alumni-item');
                    
                    if ($(this).is(':checked')) {
                        if (!speakerData.selectedAlumni.some(a => a.id === alumniData.id)) {
                            speakerData.selectedAlumni.push(alumniData);
                            $item.addClass('border-primary bg-light');
                        }
                    } else {
                        speakerData.selectedAlumni = speakerData.selectedAlumni.filter(a => a.id !== alumniData.id);
                        $item.removeClass('border-primary bg-light');
                    }
                    
                    // Update counter with animation
                    $('#selectedSpeakerCount').fadeOut(100, function() {
                        $(this).text(speakerData.selectedAlumni.length).fadeIn(100);
                    });
                });
                
                // Make label clickable
                $('.alumni-item label').css('cursor', 'pointer');
            }

            // Step navigation
            $('#speakerNextStepBtn').on('click', function() {
                if (speakerData.currentStep < 4) {
                    if (validateSpeakerCurrentStep()) {
                        speakerData.currentStep++;
                        showSpeakerStep(speakerData.currentStep);
                        updateSpeakerStepNavigation();
                    }
                }
            });

            $('#speakerPrevStepBtn').on('click', function() {
                if (speakerData.currentStep > 1) {
                    speakerData.currentStep--;
                    showSpeakerStep(speakerData.currentStep);
                    updateSpeakerStepNavigation();
                }
            });

            function showSpeakerStep(stepNumber) {
                $(`#speaker-step${stepNumber}-tab`).tab('show');
                
                if (stepNumber === 3) {
                    updateSpeakerLetterPreview();
                } else if (stepNumber === 4) {
                    prepareSpeakerFinalReview();
                }
            }

            function updateSpeakerStepNavigation() {
                $('#speakerPrevStepBtn').toggle(speakerData.currentStep > 1);
                $('#speakerNextStepBtn').toggle(speakerData.currentStep < 4);
            }

            function validateSpeakerCurrentStep() {
                if (speakerData.currentStep === 1) {
                    if (speakerData.selectedAlumni.length === 0) {
                        Swal.fire('Selection Required', 'Please select at least one alumni', 'warning');
                        return false;
                    }
                } else if (speakerData.currentStep === 2) {
                    if (!$('#speakerEventDate').val() || !$('#speakerEventTime').val() || 
                        !$('#speakerEventVenue').val() || !$('#speakerEventTopic').val()) {
                        Swal.fire('Required Fields', 'Please fill in all event details', 'warning');
                        return false;
                    }
                }
                return true;
            }

            // Letter preview
            $('#speakerSubjectLine, #speakerLetterContent, #speakerEventDate, #speakerEventTime, #speakerEventVenue, #speakerEventTopic').on('input', updateSpeakerLetterPreview);

            function updateSpeakerLetterPreview() {
                const subject = $('#speakerSubjectLine').val();
                const content = $('#speakerLetterContent').val();
                const eventDate = $('#speakerEventDate').val() || 'TBA';
                const eventTime = $('#speakerEventTime').val() || 'TBA';
                const eventVenue = $('#speakerEventVenue').val() || 'TBA';
                const eventTopic = $('#speakerEventTopic').val() || 'TBA';
                
                // Sample data for preview
                const sampleData = {
                    name: 'Juan Dela Cruz',
                    firstname: 'Juan',
                    lastname: 'Dela Cruz',
                    course: 'Bachelor of Science in Information Technology',
                    event_date: eventDate,
                    event_time: eventTime,
                    event_venue: eventVenue,
                    event_topic: eventTopic
                };
                
                let previewContent = content;
                Object.keys(sampleData).forEach(key => {
                    const regex = new RegExp(`{{${key}}}`, 'g');
                    previewContent = previewContent.replace(regex, sampleData[key]);
                });
                
                $('#speakerPreviewSubject').text(subject);
                $('#speakerLetterPreview').html(previewContent.replace(/\n/g, '<br>'));
            }

            // Test email
            $('#sendSpeakerTestPreview').on('click', function() {
                const testEmail = $('#speakerTestEmailAddress').val();
                if (!testEmail) {
                    Swal.fire('Email Required', 'Please enter a test email address', 'warning');
                    return;
                }
                
                const subject = $('#speakerSubjectLine').val();
                const content = $('#speakerLetterContent').val();
                const eventDate = $('#speakerEventDate').val();
                const eventTime = $('#speakerEventTime').val();
                const eventVenue = $('#speakerEventVenue').val();
                const eventTopic = $('#speakerEventTopic').val();
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
                
                $.post('send_invite_speaker.php', {
                    action: 'test',
                    test_email: testEmail,
                    subject: subject,
                    content: content,
                    event_date: eventDate,
                    event_time: eventTime,
                    event_venue: eventVenue,
                    event_topic: eventTopic
                })
                .done(function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Test Sent!', 'Test invitation sent successfully', 'success');
                    } else {
                        Swal.fire('Error', response.message || 'Failed to send test email', 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Request failed', 'error');
                })
                .always(function() {
                    $('#sendSpeakerTestPreview').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Send Test Email');
                });
            });

            // Final review
            function prepareSpeakerFinalReview() {
                const selectedNames = speakerData.selectedAlumni.map(a => a.fullname).join(', ');
                const eventDate = $('#speakerEventDate').val();
                const eventTime = $('#speakerEventTime').val();
                const eventVenue = $('#speakerEventVenue').val();
                const eventTopic = $('#speakerEventTopic').val();
                
                $('#speakerFinalReview').html(`
                    <p><strong>Selected Alumni:</strong> ${speakerData.selectedAlumni.length}</p>
                    <p><strong>Alumni Names:</strong> ${selectedNames}</p>
                    <p><strong>Event Date:</strong> ${eventDate}</p>
                    <p><strong>Event Time:</strong> ${eventTime}</p>
                    <p><strong>Event Venue:</strong> ${eventVenue}</p>
                    <p><strong>Topic:</strong> ${eventTopic}</p>
                    <p><strong>Subject:</strong> ${$('#speakerSubjectLine').val()}</p>
                `);
            }

            // Send invitations
            $('#launchSpeakerInvitations').on('click', function() {
                Swal.fire({
                    title: 'Confirm Send',
                    text: 'Are you sure you want to send speaker invitations? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0dcaf0',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send invitations!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendSpeakerInvitations();
                    }
                });
            });

            function sendSpeakerInvitations() {
                $('#speakerSendingProgress').show();
                $('#launchSpeakerInvitations').prop('disabled', true);
                
                const subject = $('#speakerSubjectLine').val();
                const content = $('#speakerLetterContent').val();
                const eventDate = $('#speakerEventDate').val();
                const eventTime = $('#speakerEventTime').val();
                const eventVenue = $('#speakerEventVenue').val();
                const eventTopic = $('#speakerEventTopic').val();
                
                const recipients = speakerData.selectedAlumni;
                let sent = 0;
                let failed = 0;
                let currentIndex = 0;
                
                $('#speakerSendProgressCount').text(`0 / ${recipients.length} sent`);
                $('#speakerSendProgressBar').css('width', '0%').text('0%');
                $('#speakerSendProgressText').text('Starting to send invitations...');
                
                // Send invitations one by one for real-time progress
                function sendNext() {
                    if (currentIndex >= recipients.length) {
                        // All done
                        $('#speakerSendProgressBar').css('width', '100%').text('100%');
                        $('#speakerSendProgressText').text('Completed!');
                        
                        Swal.fire({
                            title: 'Invitations Sent!',
                            html: `
                                <div style="text-align: left; padding: 10px;">
                                    <p style="font-size: 16px; margin: 10px 0;">
                                        <strong style="color: #10b981;">✅ Successfully sent:</strong> 
                                        <span style="font-size: 20px; font-weight: bold;">${sent}</span>
                                    </p>
                                    <p style="font-size: 16px; margin: 10px 0;">
                                        <strong style="color: #ef4444;">❌ Failed:</strong> 
                                        <span style="font-size: 20px; font-weight: bold;">${failed}</span>
                                    </p>
                                    <p style="font-size: 16px; margin: 10px 0;">
                                        <strong style="color: #3b82f6;">📊 Total:</strong> 
                                        <span style="font-size: 20px; font-weight: bold;">${recipients.length}</span>
                                    </p>
                                </div>
                            `,
                            icon: sent > 0 ? 'success' : 'error',
                            confirmButtonColor: '#1e3a8a'
                        }).then(() => {
                            $('#speakerInviteModal').modal('hide');
                            resetSpeakerToStep1();
                        });
                        
                        $('#launchSpeakerInvitations').prop('disabled', false);
                        setTimeout(() => {
                            $('#speakerSendingProgress').hide();
                            $('#speakerSendProgressBar').css('width', '0%').text('0%');
                        }, 2000);
                        return;
                    }
                    
                    const recipient = recipients[currentIndex];
                    const progress = Math.round(((currentIndex + 1) / recipients.length) * 100);
                    
                    $('#speakerSendProgressText').text(`Sending to ${recipient.firstname} ${recipient.lastname}...`);
                    $('#speakerSendProgressBar').css('width', progress + '%').text(progress + '%');
                    
                    $.post('send_invite_speaker.php', {
                        action: 'send_single',
                        recipient: JSON.stringify(recipient),
                        subject: subject,
                        content: content,
                        event_date: eventDate,
                        event_time: eventTime,
                        event_venue: eventVenue,
                        event_topic: eventTopic
                    })
                    .done(function(response) {
                        if (response && response.status === 'success') {
                            sent++;
                            console.log('✅ Sent to:', recipient.email);
                        } else {
                            failed++;
                            console.error('❌ Failed to send to:', recipient.email, 'Error:', response?.message || 'Unknown error');
                        }
                    })
                    .fail(function(xhr, status, error) {
                        failed++;
                        console.error('❌ Request failed for:', recipient.email, 'Status:', status, 'Error:', error);
                        
                        // Try to parse error response
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            console.error('Server error:', errorResponse.message);
                        } catch(e) {
                            console.error('Raw error:', xhr.responseText);
                        }
                    })
                    .always(function() {
                        currentIndex++;
                        $('#speakerSendProgressCount').text(`${sent + failed} / ${recipients.length} sent`);
                        // Small delay between sends to avoid overwhelming the server
                        setTimeout(sendNext, 300);
                    });
                }
                
                // Start sending
                sendNext();
            }
            // ==================== END GUEST SPEAKER INVITATION SYSTEM ====================

            // ==================== GMAIL-LIKE MESSAGING SYSTEM ====================
            
            const messageData = {
                selectedAlumni: [],
                currentStep: 1,
                templates: [],
                selectedTemplate: null
            };
            
            // Open compose message modal
            $('#composeMessageBtn').click(function() {
                messageData.selectedAlumni = [];
                messageData.currentStep = 1;
                
                // Make sure fields are editable
                $('#messageSubject').prop('readonly', false);
                $('#messageBody').prop('readonly', false);
                
                loadMessageCourses();
                loadEmailTemplates();
                $('#composeMessageModal').modal('show');
                resetMessageToStep1();
                
                console.log('Compose modal opened');
            });
            
            // Load courses for filtering
            function loadMessageCourses() {
                $.ajax({
                    url: 'send_message.php',
                    method: 'POST',
                    data: { action: 'get_courses' },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="0">All Courses</option>';
                            response.courses.forEach(course => {
                                options += `<option value="${course.id}">${course.course} (${course.alumni_count})</option>`;
                            });
                            $('#messageCourseFilter').html(options);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load courses:', status, error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }
            
            // Load email templates
            function loadEmailTemplates() {
                console.log('Loading email templates...');
                $.ajax({
                    url: 'send_message.php',
                    method: 'POST',
                    data: { action: 'get_templates' },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        console.log('Template response:', response);
                        if (response.status === 'success' && response.templates && response.templates.length > 0) {
                            messageData.templates = response.templates;
                            console.log('Loaded templates:', messageData.templates.length, 'templates');
                            
                            let options = '<option value="">-- Select a Template --</option>';
                            options += '<option value="custom">✏️ Custom Message (Type Your Own)</option>';
                            let currentCategory = '';
                            
                            response.templates.forEach(template => {
                                if (template.category !== currentCategory) {
                                    if (currentCategory !== '') options += '</optgroup>';
                                    options += `<optgroup label="${template.category.toUpperCase()}">`;
                                    currentCategory = template.category;
                                }
                                options += `<option value="${template.id}">${template.template_name}</option>`;
                            });
                            
                            if (currentCategory !== '') options += '</optgroup>';
                            $('#templateSelector').html(options);
                            console.log('Template dropdown populated');
                        } else {
                            console.error('Failed to load templates:', response.message);
                            messageData.templates = [];
                            
                            // Show only custom option
                            $('#templateSelector').html('<option value="custom">✏️ Custom Message (Type Your Own)</option>');
                            
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Templates Found',
                                html: `
                                    <p>${response.message || 'No templates available in database.'}</p>
                                    <p><strong>Solution:</strong></p>
                                    <ol style="text-align: left;">
                                        <li>Run <a href="setup_messaging_db.php" target="_blank">setup_messaging_db.php</a></li>
                                        <li>Or use "Custom Message" to write your own</li>
                                    </ol>
                                `,
                                confirmButtonColor: '#800000',
                                confirmButtonText: 'Use Custom Message'
                            }).then(() => {
                                $('#templateSelector').val('custom').trigger('change');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load templates:', status, error);
                        console.error('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to load templates. Please check your connection and try again.',
                            confirmButtonColor: '#800000'
                        });
                    }
                });
            }
            
            // Template selection
            $('#templateSelector').change(function() {
                const templateValue = $(this).val();
                console.log('Template selected:', templateValue);
                console.log('Available templates:', messageData.templates);
                
                if (templateValue === 'custom') {
                    // Custom message - clear fields for user to type
                    $('#messageSubject').val('').prop('readonly', false);
                    $('#messageBody').val('').prop('readonly', false);
                    updateMessagePreview();
                    console.log('Custom message selected');
                } else if (templateValue && templateValue !== '') {
                    const templateId = parseInt(templateValue);
                    console.log('Looking for template ID:', templateId);
                    console.log('All templates:', messageData.templates);
                    
                    // Log the structure of first template to see field names
                    if (messageData.templates.length > 0) {
                        console.log('First template structure:', messageData.templates[0]);
                        console.log('First template keys:', Object.keys(messageData.templates[0]));
                    }
                    
                    // Try to find template by id (convert both to int for comparison)
                    let template = messageData.templates.find(t => {
                        const tId = parseInt(t.id);
                        console.log('Checking template:', tId, 'vs', templateId, '=', tId === templateId);
                        return tId === templateId;
                    });
                    
                    // If not found, try by template_id field
                    if (!template) {
                        template = messageData.templates.find(t => parseInt(t.template_id) === templateId);
                    }
                    
                    console.log('Found template:', template);
                    
                    if (template) {
                        // Get subject and body (handle different field names)
                        const subject = template.template_subject || template.subject || '';
                        const body = template.template_body || template.body || '';
                        
                        console.log('Subject:', subject);
                        console.log('Body length:', body.length);
                        
                        $('#messageSubject').val(subject).prop('readonly', false);
                        $('#messageBody').val(body).prop('readonly', false);
                        updateMessagePreview();
                        console.log('Template applied successfully');
                        
                        // Show success notification
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Template loaded!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        console.error('Template not found!');
                        console.error('Searched for ID:', templateId);
                        console.error('Available templates:', messageData.templates.map(t => ({id: t.id, name: t.template_name})));
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Template Not Found',
                            html: `
                                <p>Could not load the selected template.</p>
                                <p><small>Template ID: ${templateId}</small></p>
                                <p><small>Available: ${messageData.templates.length} templates</small></p>
                            `,
                            confirmButtonColor: '#800000'
                        });
                    }
                } else {
                    // No selection - clear fields
                    $('#messageSubject').val('').prop('readonly', false);
                    $('#messageBody').val('').prop('readonly', false);
                    console.log('No template selected');
                }
            });
            
            // Real-time search for alumni
            let messageSearchTimeout;
            $('#messageSearchInput').on('input', function() {
                clearTimeout(messageSearchTimeout);
                messageSearchTimeout = setTimeout(() => {
                    searchMessageAlumni();
                }, 300);
            });
            
            $('#searchMessageAlumniBtn, #messageCourseFilter').click(function() {
                searchMessageAlumni();
            });
            
            function searchMessageAlumni() {
                const search = $('#messageSearchInput').val();
                const courseFilter = $('#messageCourseFilter').val();
                
                // Show loading indicator
                $('#alumniMessageResults').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Searching alumni...</p></div>');
                
                $.ajax({
                    url: 'send_message.php',
                    method: 'POST',
                    data: {
                        action: 'get_alumni',
                        search: search,
                        course_filter: courseFilter
                    },
                    dataType: 'json',
                    timeout: 15000,
                    success: function(response) {
                        if (response.status === 'success') {
                            displayMessageAlumniResults(response.alumni);
                        } else {
                            $('#alumniMessageResults').html(`<div class="alert alert-warning">${response.message || 'Failed to load alumni'}</div>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to search alumni:', status, error);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMsg = 'Failed to connect to server. ';
                        if (status === 'timeout') {
                            errorMsg += 'Request timed out. Please try again.';
                        } else if (xhr.status === 404) {
                            errorMsg += 'Server endpoint not found. Please check the file path.';
                        } else if (xhr.status === 500) {
                            errorMsg += 'Server error. Please check the error logs.';
                        } else {
                            errorMsg += 'Please check your connection and try again.';
                        }
                        
                        $('#alumniMessageResults').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error:</strong> ${errorMsg}
                                <br><small>Status: ${status}, Code: ${xhr.status}</small>
                            </div>
                        `);
                    }
                });
            }
            
            function displayMessageAlumniResults(alumni) {
                if (alumni.length === 0) {
                    $('#alumniMessageResults').html('<p class="text-muted text-center">No alumni found</p>');
                    return;
                }
                
                let html = '<div class="list-group">';
                alumni.forEach(alumnus => {
                    const isSelected = messageData.selectedAlumni.some(a => a.id === alumnus.id);
                    const checkedAttr = isSelected ? 'checked' : '';
                    
                    html += `
                        <label class="list-group-item list-group-item-action d-flex align-items-center">
                            <input type="checkbox" class="form-check-input me-3 alumni-message-checkbox" 
                                   value="${alumnus.id}" ${checkedAttr}
                                   data-alumni='${JSON.stringify(alumnus)}'>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${alumnus.firstname} ${alumnus.lastname}</div>
                                <small class="text-muted">${alumnus.email} • ${alumnus.course || 'N/A'} • Batch ${alumnus.batch || 'N/A'}</small>
                            </div>
                        </label>
                    `;
                });
                html += '</div>';
                
                $('#alumniMessageResults').html(html);
                
                // Handle checkbox changes
                $('.alumni-message-checkbox').change(function() {
                    const alumnus = JSON.parse($(this).attr('data-alumni'));
                    if ($(this).is(':checked')) {
                        if (!messageData.selectedAlumni.some(a => a.id === alumnus.id)) {
                            messageData.selectedAlumni.push(alumnus);
                        }
                    } else {
                        messageData.selectedAlumni = messageData.selectedAlumni.filter(a => a.id !== alumnus.id);
                    }
                    updateSelectedMessageCount();
                });
            }
            
            // Load all alumni button
            $('#loadAllMessageAlumni').click(function() {
                $('#messageSearchInput').val('');
                $('#messageCourseFilter').val('0');
                searchMessageAlumni();
            });
            
            function updateSelectedMessageCount() {
                $('#selectedMessageCount').text(messageData.selectedAlumni.length);
            }
            
            // Message preview - update on any field change (REAL-TIME)
            $('#messageSubject, #messageBody').on('input keyup paste', updateMessagePreview);
            $('#eventDate, #eventStartTime, #eventEndTime').on('change input', updateMessagePreview);
            $('#templateSelector').on('change', updateMessagePreview);
            
            function updateMessagePreview() {
                const subject = $('#messageSubject').val();
                const body = $('#messageBody').val();
                
                // Show loading state
                $('#messagePreview').css('opacity', '0.7');
                
                // Use first selected alumni for preview, or sample data
                const sampleRecipient = messageData.selectedAlumni.length > 0 
                    ? messageData.selectedAlumni[0]
                    : { firstname: 'John', lastname: 'Doe', email: 'john.doe@example.com', course: 'Sample Course', batch: '2020' };
                
                const previewSubject = replacePlaceholders(subject, sampleRecipient);
                const previewBody = replacePlaceholders(body, sampleRecipient);
                
                // Update preview with animation
                setTimeout(() => {
                    if (previewSubject) {
                        $('#messagePreviewSubject').text(previewSubject).css('color', '#800000');
                    } else {
                        $('#messagePreviewSubject').html('<em style="color: #999;">Subject Preview</em>');
                    }
                    
                    if (previewBody) {
                        $('#messagePreview').html(previewBody.replace(/\n/g, '<br>')).css('opacity', '1');
                    } else {
                        $('#messagePreview').html('<p style="color: #999; font-style: italic;">Your message preview will appear here...</p>').css('opacity', '1');
                    }
                }, 50);
                
                console.log('Preview updated');
            }
            
            function replacePlaceholders(text, recipient) {
                // Get event details if filled
                const eventDate = $('#eventDate').val() || 'TBD';
                const startTime = $('#eventStartTime').val() || '';
                const endTime = $('#eventEndTime').val() || '';
                
                // Format time display
                let eventTime = 'TBD';
                if (startTime && endTime) {
                    eventTime = formatTime(startTime) + ' - ' + formatTime(endTime);
                } else if (startTime) {
                    eventTime = formatTime(startTime);
                }
                
                // Format date display
                let formattedDate = eventDate;
                if (eventDate !== 'TBD') {
                    const dateObj = new Date(eventDate);
                    formattedDate = dateObj.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                }
                
                return text
                    .replace(/\{\{name\}\}/g, recipient.firstname + ' ' + recipient.lastname)
                    .replace(/\{\{firstname\}\}/g, recipient.firstname)
                    .replace(/\{\{lastname\}\}/g, recipient.lastname)
                    .replace(/\{\{email\}\}/g, recipient.email)
                    .replace(/\{\{course\}\}/g, recipient.course || 'N/A')
                    .replace(/\{\{batch\}\}/g, recipient.batch || 'N/A')
                    .replace(/\{\{event_date\}\}/g, formattedDate)
                    .replace(/\{\{event_time\}\}/g, eventTime)
                    .replace(/\{\{event_end_time\}\}/g, endTime ? formatTime(endTime) : 'TBD');
            }
            
            function formatTime(time) {
                if (!time) return '';
                const [hours, minutes] = time.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                return `${displayHour}:${minutes} ${ampm}`;
            }
            
            // Send test email
            $('#sendMessageTest').click(function() {
                const testEmail = $('#messageTestEmail').val();
                if (!testEmail) {
                    Swal.fire('Error', 'Please enter a test email address', 'error');
                    return;
                }
                
                const subject = $('#messageSubject').val();
                const body = $('#messageBody').val();
                
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sending...');
                
                $.post('send_message.php', {
                    action: 'send_single',
                    recipient: JSON.stringify({
                        id: 0,
                        firstname: 'Test',
                        lastname: 'User',
                        email: testEmail,
                        course: 'Test Course',
                        batch: '2024'
                    }),
                    subject: subject,
                    message_body: body,
                    send_email: 'true'
                })
                .done(function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success', 'Test email sent successfully!', 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                })
                .fail(function() {
                    Swal.fire('Error', 'Failed to send test email', 'error');
                })
                .always(function() {
                    $('#sendMessageTest').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Send Test');
                });
            });
            
            // Navigation between steps
            $('#messageNextStepBtn').click(function() {
                function goToMessageStep(step) {
                    if (step === 2) {
                        if (messageData.selectedAlumni.length === 0) {
                            Swal.fire('Error', 'Please select at least one recipient', 'error');
                            return;
                        }
                    }
                    
                    if (step === 3) {
                        // Check if template is selected
                        const templateValue = $('#templateSelector').val();
                        if (!templateValue) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Template Required',
                                text: 'Please select a message template or choose "Custom Message" to write your own.',
                                confirmButtonColor: '#800000'
                            });
                            return;
                        }
                        
                        const subject = $('#messageSubject').val().trim();
                        const body = $('#messageBody').val().trim();
                        if (!subject || !body) {
                            Swal.fire('Error', 'Please enter subject and message', 'error');
                            return;
                        }
                        
                        // Show final review
                        $('#messageFinalReview').html(`
                            <p><strong>Recipients:</strong> ${messageData.selectedAlumni.length} alumni</p>
                            <p><strong>Subject:</strong> ${subject}</p>
                            <p><strong>Message Preview:</strong></p>
                            <div class="border rounded p-2 mt-2" style="max-height: 200px; overflow-y: auto;">
                                ${body.substring(0, 200)}${body.length > 200 ? '...' : ''}
                            </div>
                        `);
                    }
                    
                    $('#messageSteps .nav-link').removeClass('active');
                    $(`#message-step${step}-tab`).addClass('active');
                    
                    // Update content
                    $('.tab-pane').removeClass('show active');
                    $(`#message-step${step}`).addClass('show active');
                    
                    // Update buttons
                    if (step === 1) {
                        $('#messagePrevStepBtn').hide();
                        $('#messageNextStepBtn').show().html('Next <i class="fas fa-arrow-right ms-1"></i>');
                    } else if (step === 2) {
                        $('#messagePrevStepBtn').show();
                        $('#messageNextStepBtn').show().html('Review <i class="fas fa-arrow-right ms-1"></i>');
                    } else if (step === 3) {
                        $('#messagePrevStepBtn').show();
                        $('#messageNextStepBtn').hide();
                    }
                }
                
                if (messageData.currentStep === 1) {
                    goToMessageStep(2);
                } else if (messageData.currentStep === 2) {
                    goToMessageStep(3);
                }
            });
            
            $('#messagePrevStepBtn').click(function() {
                goToMessageStep(messageData.currentStep - 1);
            });
            
            function goToMessageStep(step) {
                messageData.currentStep = step;
                
                // Update tabs
                $('#messageSteps .nav-link').removeClass('active');
                $(`#message-step${step}-tab`).addClass('active');
                
                // Update content
                $('.tab-pane').removeClass('show active');
                $(`#message-step${step}`).addClass('show active');
                
                // Update buttons
                if (step === 1) {
                    $('#messagePrevStepBtn').hide();
                    $('#messageNextStepBtn').show().html('Next <i class="fas fa-arrow-right ms-1"></i>');
                } else if (step === 2) {
                    $('#messagePrevStepBtn').show();
                    $('#messageNextStepBtn').show().html('Review <i class="fas fa-arrow-right ms-1"></i>');
                } else if (step === 3) {
                    $('#messagePrevStepBtn').show();
                    $('#messageNextStepBtn').hide();
                }
            }
            
            function showMessageFinalReview() {
                const subject = $('#messageSubject').val();
                const sendEmail = $('#sendEmailNotification').is(':checked');
                
                $('#messageFinalReview').html(`
                    <p><strong>Recipients:</strong> ${messageData.selectedAlumni.length} alumni</p>
                    <p><strong>Subject:</strong> ${subject}</p>
                    <p><strong>Email Notification:</strong> ${sendEmail ? 'Yes' : 'No (System message only)'}</p>
                    <div class="mt-3">
                        <strong>Selected Alumni:</strong>
                        <div class="mt-2" style="max-height: 200px; overflow-y: auto;">
                            ${messageData.selectedAlumni.map(a => `
                                <span class="badge bg-primary me-1 mb-1">${a.firstname} ${a.lastname}</span>
                            `).join('')}
                        </div>
                    </div>
                `);
            }
            
            // Send messages
            $('#launchMessages').click(function() {
                if (messageData.selectedAlumni.length === 0) {
                    Swal.fire('Error', 'No recipients selected', 'error');
                    return;
                }
                
                $(this).prop('disabled', true);
                $('#messageSendingProgress').show();
                
                const subject = $('#messageSubject').val();
                const body = $('#messageBody').val();
                const sendEmail = $('#sendEmailNotification').is(':checked');
                const recipients = messageData.selectedAlumni;
                
                let sent = 0;
                let failed = 0;
                let currentIndex = 0;
                
                $('#messageSendProgressCount').text(`0 / ${recipients.length} sent`);
                $('#messageSendProgressBar').css('width', '0%').text('0%');
                $('#messageSendProgressText').text('Starting to send messages...');
                
                function sendNext() {
                    if (currentIndex >= recipients.length) {
                        $('#messageSendProgressBar').css('width', '100%').text('100%');
                        $('#messageSendProgressText').text('Completed!');
                        
                        Swal.fire({
                            title: 'Messages Sent!',
                            html: `
                                <div style="text-align: left; padding: 10px;">
                                    <p style="font-size: 16px; margin: 10px 0;">
                                        <strong style="color: #10b981;">✅ Successfully sent:</strong> 
                                        <span style="font-size: 20px; font-weight: bold;">${sent}</span>
                                    </p>
                                    <p style="font-size: 16px; margin: 10px 0;">
                                        <strong style="color: #ef4444;">❌ Failed:</strong> 
                                        <span style="font-size: 20px; font-weight: bold;">${failed}</span>
                                    </p>
                                    <p style="font-size: 16px; margin: 10px 0;">
                                        <strong style="color: #3b82f6;">📊 Total:</strong> 
                                        <span style="font-size: 20px; font-weight: bold;">${recipients.length}</span>
                                    </p>
                                </div>
                            `,
                            icon: sent > 0 ? 'success' : 'error',
                            confirmButtonColor: '#800000'
                        }).then(() => {
                            $('#composeMessageModal').modal('hide');
                            resetMessageToStep1();
                        });
                        
                        $('#launchMessages').prop('disabled', false);
                        setTimeout(() => {
                            $('#messageSendingProgress').hide();
                            $('#messageSendProgressBar').css('width', '0%').text('0%');
                        }, 2000);
                        return;
                    }
                    
                    const recipient = recipients[currentIndex];
                    const progress = Math.round(((currentIndex + 1) / recipients.length) * 100);
                    
                    $('#messageSendProgressText').text(`Sending to ${recipient.firstname} ${recipient.lastname}...`);
                    $('#messageSendProgressBar').css('width', progress + '%').text(progress + '%');
                    
                    // Get event data
                    const eventDate = $('#eventDate').val();
                    const eventStartTime = $('#eventStartTime').val();
                    const eventEndTime = $('#eventEndTime').val();
                    
                    $.post('send_message.php', {
                        action: 'send_single',
                        recipient: JSON.stringify(recipient),
                        subject: subject,
                        message_body: body,
                        template_id: messageData.selectedTemplate,
                        send_email: sendEmail ? 'true' : 'false',
                        event_date: eventDate,
                        event_start_time: eventStartTime,
                        event_end_time: eventEndTime
                    })
                    .done(function(response) {
                        if (response && response.status === 'success') {
                            sent++;
                        } else {
                            failed++;
                        }
                    })
                    .fail(function() {
                        failed++;
                    })
                    .always(function() {
                        currentIndex++;
                        $('#messageSendProgressCount').text(`${sent + failed} / ${recipients.length} sent`);
                        setTimeout(sendNext, 300);
                    });
                }
                
                sendNext();
            });
            
            function resetMessageToStep1() {
                messageData.selectedAlumni = [];
                messageData.currentStep = 1;
                messageData.selectedTemplate = null;
                
                $('#messageSearchInput').val('');
                $('#messageCourseFilter').val('0');
                $('#messageSubject').val('');
                $('#messageBody').val('');
                $('#templateSelector').val('');
                $('#sendEmailNotification').prop('checked', true);
                $('#alumniMessageResults').html('<p class="text-muted text-center">Use the search above or click "Load All Alumni"</p>');
                updateSelectedMessageCount();
                goToMessageStep(1);
            }
            
            // Save template - Using Bootstrap Modal (More Reliable)
            $('#saveAsTemplate').click(function() {
                const currentSubject = $('#messageSubject').val();
                const currentBody = $('#messageBody').val();
                
                if (!currentSubject || !currentBody) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Content',
                        text: 'Please enter subject and message before saving as template',
                        confirmButtonColor: '#800000'
                    });
                    return;
                }
                
                // Create modal HTML
                const modalHtml = `
                    <div class="modal fade" id="saveTemplateModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white;">
                                    <h5 class="modal-title"><i class="fas fa-save me-2"></i>Save as Template</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-tag text-danger me-1"></i>Template Name: <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="modalTemplateName" class="form-control form-control-lg" 
                                               placeholder="e.g., Monthly Newsletter" autofocus>
                                        <small class="text-muted">Enter a descriptive name for your template</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fas fa-folder text-primary me-1"></i>Category:
                                        </label>
                                        <select id="modalTemplateCategory" class="form-select form-select-lg">
                                            <option value="general">📄 General</option>
                                            <option value="events">🎉 Events</option>
                                            <option value="announcements">📢 Announcements</option>
                                            <option value="surveys">📊 Surveys</option>
                                            <option value="opportunities">💼 Opportunities</option>
                                        </select>
                                    </div>
                                    <div class="alert alert-info">
                                        <strong><i class="fas fa-eye me-1"></i>Preview:</strong><br>
                                        <small>
                                            <strong>Subject:</strong> ${currentSubject.substring(0, 50)}${currentSubject.length > 50 ? '...' : ''}<br>
                                            <strong>Body:</strong> ${currentBody.substring(0, 80)}${currentBody.length > 80 ? '...' : ''}
                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </button>
                                    <button type="button" class="btn btn-primary" id="confirmSaveTemplate" style="background: #800000; border-color: #800000;">
                                        <i class="fas fa-save me-1"></i>Save Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                $('#saveTemplateModal').remove();
                
                // Add modal to body
                $('body').append(modalHtml);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('saveTemplateModal'));
                modal.show();
                
                // Focus input after modal is shown
                $('#saveTemplateModal').on('shown.bs.modal', function() {
                    $('#modalTemplateName').focus();
                    console.log('Template modal opened');
                });
                
                // Handle save button
                $('#confirmSaveTemplate').off('click').on('click', function() {
                    const name = $('#modalTemplateName').val().trim();
                    const category = $('#modalTemplateCategory').val();
                    
                    console.log('Save clicked, name:', name);
                    
                    if (!name) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Template Name Required',
                            text: 'Please enter a template name',
                            confirmButtonColor: '#800000'
                        });
                        return;
                    }
                    
                    if (name.length < 3) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Name Too Short',
                            text: 'Template name must be at least 3 characters',
                            confirmButtonColor: '#800000'
                        });
                        return;
                    }
                    
                    // Close modal
                    modal.hide();
                    
                    // Show loading
                    Swal.fire({
                        title: 'Saving Template...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Save template
                    $.post('send_message.php', {
                        action: 'save_template',
                        template_name: name,
                        template_subject: currentSubject,
                        template_body: currentBody,
                        category: category
                    })
                    .done(function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Template "' + name + '" saved successfully!',
                                confirmButtonColor: '#800000'
                            });
                            loadEmailTemplates();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to save template',
                                confirmButtonColor: '#800000'
                            });
                        }
                    })
                    .fail(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Failed to connect to server',
                            confirmButtonColor: '#800000'
                        });
                    });
                });
                
                // Handle enter key in input
                $('#modalTemplateName').on('keypress', function(e) {
                    if (e.key === 'Enter') {
                        $('#confirmSaveTemplate').click();
                    }
                });
            });
            
            // ==================== END GMAIL-LIKE MESSAGING SYSTEM ====================

        });
    </script>
</body>
</html>