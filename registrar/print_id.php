<?php
// print_id.php
// Render a polished two-sided MOIST alumni ID card. QR points to local privacy prompt for scanning.
session_start();
include __DIR__ . '/../admin/db_connect.php';

// Validate id
if (!isset($_GET['id'])) {
  http_response_code(400);
  echo 'No ID provided.';
  exit;
}

$id = intval($_GET['id']);

// Fetch alumnus record securely
$stmt = $conn->prepare("SELECT * FROM alumnus_bio WHERE id = ? LIMIT 1");
if (!$stmt) {
  http_response_code(500);
  echo 'Database error';
  exit;
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  http_response_code(404);
  echo 'Alumnus not found.';
  exit;
}
$row = $result->fetch_assoc();

// Fields
$firstname = trim($row['firstname'] ?? '');
$middlename = trim($row['middlename'] ?? '');
$lastname = trim($row['lastname'] ?? '');
$suffix = trim($row['suffixname'] ?? '');
$fullname = trim($firstname . ' ' . ($middlename ? $middlename : '') . ' ' . $lastname . ($suffix ? ' ' . $suffix : ''));
// Enhanced photo resolution system - check multiple possible locations
$photo = '../assets/img/default-avatar.jpg'; // Default fallback

// Function to resolve profile image path
function resolve_alumni_image($row) {
    $filename = '';
    // Check both img and avatar columns
    if (!empty($row['avatar'])) $filename = $row['avatar'];
    if (empty($filename) && !empty($row['img'])) $filename = $row['img'];
    
    if (!empty($filename)) {
        // Check if it's already a full URL
        if (filter_var($filename, FILTER_VALIDATE_URL)) {
            return $filename;
        }
        
        // Check multiple possible upload directories
        $candidates = [
            __DIR__ . '/../admin/assets/uploads/' . $filename,
            __DIR__ . '/../assets/uploads/' . $filename,
            __DIR__ . '/../uploads/' . $filename,
            __DIR__ . '/../assets/img/' . $filename
        ];
        
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                // Convert absolute path to relative web path
                $webPath = str_replace('\\', '/', str_replace(__DIR__, '', $path));
                if ($webPath === '' || $webPath[0] !== '.') {
                    $webPath = '.' . $webPath;
                }
                return $webPath;
            }
        }
        
        // If file not found but we have a filename, try direct relative paths
        $relativePaths = [
            '../admin/assets/uploads/' . $filename,
            '../assets/uploads/' . $filename,
            '../uploads/' . $filename,
            '../assets/img/' . $filename
        ];
        
        foreach ($relativePaths as $relPath) {
            $checkPath = __DIR__ . '/' . str_replace('../', '', $relPath);
            if (file_exists($checkPath)) {
                return $relPath;
            }
        }
    }
    
    return '../assets/img/default-avatar.jpg';
}

$photo = resolve_alumni_image($row);
$alumni_id_display = !empty($row['alumni_id']) ? $row['alumni_id'] : str_pad($row['id'], 8, '0', STR_PAD_LEFT);
$batch = $row['batch'] ?? 'N/A';
$date_created = !empty($row['date_created']) ? date('Y-m-d', strtotime($row['date_created'])) : date('Y-m-d');

$address = $row['address'] ?? ($row['permanent_address'] ?? ($row['home_address'] ?? 'N/A'));
$birthdate = !empty($row['birthdate']) ? date('Y-m-d', strtotime($row['birthdate'])) : ($row['dob'] ?? 'N/A');
$sex = $row['sex'] ?? ($row['gender'] ?? 'N/A');
$contact = $row['contact_no'] ?? ($row['telephone'] ?? ($row['mobile'] ?? 'N/A'));
$email = $row['email'] ?? 'N/A';

// Course lookup
$course = 'N/A';
if (!empty($row['course_id'])) {
  $cq = $conn->prepare("SELECT course FROM courses WHERE id = ? LIMIT 1");
  if ($cq) {
    $cq->bind_param('i', $row['course_id']);
    $cq->execute();
    $cres = $cq->get_result();
    $course = $cres->fetch_assoc()['course'] ?? 'N/A';
  }
}

// Additional fields
$current_company = $row['current_company'] ?? ($row['company'] ?? 'N/A');
$current_position = $row['current_position'] ?? ($row['position'] ?? 'N/A');
$academic_honor = $row['academic_honor'] ?? '';

// Debug: Log the resolved photo path for troubleshooting
error_log("Alumni ID {$row['id']}: Resolved photo path = {$photo}");
error_log("Alumni ID {$row['id']}: img column = " . ($row['img'] ?? 'NULL'));
error_log("Alumni ID {$row['id']}: avatar column = " . ($row['avatar'] ?? 'NULL'));

// QR: point to local index page for direct access
$local_privacy = "http://10.165.101.17/alumni/privacy_prompt.php?id=" . urlencode($row['id']);
$local_index = "http://10.165.101.17/alumni/index.php?id=" . urlencode($row['id']);

// Generate QR code using phpqrcode-master library
$qr_back = '';
$qr_front = '';
$qr_data = $local_index; // Changed to use index.php instead of privacy_prompt.php
$qr_local_path = __DIR__ . '/../uploads/qr_' . $row['id'] . '.png';
$qr_local_rel = '../uploads/qr_' . $row['id'] . '.png';

// Use phpqrcode-master library
$phpqrcode_lib = __DIR__ . '/../phpqrcode-master/phpqrcode.php';
$qr_generated = false;

if (file_exists($phpqrcode_lib)) {
    require_once $phpqrcode_lib;
    
    try {
        // Create uploads directory if it doesn't exist
        $uploads_dir = __DIR__ . '/../uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0755, true);
        }
        
        // Generate QR code with high quality settings
        // Parameters: data, filename, error_correction_level, size, margin
        QRcode::png($qr_data, $qr_local_path, QR_ECLEVEL_M, 8, 2);
        
        if (file_exists($qr_local_path)) {
            $qr_back = $qr_local_rel;
            $qr_front = $qr_local_rel;
            $qr_generated = true;
        }
    } catch (Exception $e) {
        error_log("QR Code generation failed: " . $e->getMessage());
    }
}

// Fallback to external service if local generation fails
if (!$qr_generated) {
    $qr_back = "https://api.qrserver.com/v1/create-qr-code/?size=600x600&format=png&margin=1&data=" . urlencode($qr_data);
    $qr_front = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=png&margin=1&data=" . urlencode($qr_data);
}

// Logo
$logo_path = '../assets/img/logo.png';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MOIST Alumni ID - <?php echo htmlspecialchars($fullname, ENT_QUOTES); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --maroon: #800000;
      --maroon-dark: #5b0000;
      --maroon-light: #a52a2a;
      --gold: #ffd700;
      --gold-dark: #daa520;
      --white: #ffffff;
      --light-gray: #f8f9fa;
      --muted: #6b7280;
      --dark: #1a1a1a;
      --bg: linear-gradient(135deg, #faf7f7 0%, #f0f0f0 100%);
      --shadow: 0 8px 20px rgba(0,0,0,0.08);
      --shadow-hover: 0 12px 25px rgba(0,0,0,0.12);
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: var(--bg);
      min-height: 100vh;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    /* ISO/IEC 7810 ID-1 Standard Card Dimensions */
    .card-container {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      justify-content: center;
      align-items: flex-start;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .id-card {
      width: 85.6mm;  /* ISO/IEC 7810 ID-1 width */
      height: 54mm;   /* ISO/IEC 7810 ID-1 height */
      background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
      border-radius: 12px;
      border: 2px solid rgba(128, 0, 0, 0.1);
      box-shadow: var(--shadow);
      overflow: hidden;
      position: relative;
      transition: all 0.3s ease;
      transform-style: preserve-3d;
    }
    
    .id-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('../assets/img/logo.png');
      background-size: 120px 120px;
      background-position: center;
      background-repeat: no-repeat;
      opacity: 0.1;
      z-index: 1;
      pointer-events: none;
    }
    
    .id-card:hover {
      box-shadow: var(--shadow-hover);
      transform: translateY(-2px);
    }
    
    /* Card Header with Gradient */
    .card-header {
      background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 50%, var(--maroon-light) 100%);
      color: var(--white);
      padding: 6px 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
      z-index: 2;
    }
    
    .card-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
      animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    
    .logo-container {
      width: 45px;
      height: 45px;
      background: rgba(255,255,255,0.2);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.3);
      flex-shrink: 0;
    }
    
    .logo {
      width: 38px;
      height: 38px;
      background-image: url('<?php echo htmlspecialchars($logo_path, ENT_QUOTES); ?>');
      background-size: contain;
      background-position: center;
      background-repeat: no-repeat;
      border-radius: 4px;
    }
    
    .logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 4px;
    }
    
    .org-info {
      flex: 1;
      line-height: 1.2;
    }
    
    .org-address {
      font-size: 8px;
      opacity: 0.9;
      font-weight: 500;
      line-height: 1.1;
      margin-bottom: 2px;
    }
    
    .org-title {
      font-weight: 700;
      font-size: 11px;
      letter-spacing: 0.4px;
      text-shadow: 0 1px 2px rgba(0,0,0,0.3);
      line-height: 1.1;
    }
    
    .org-subtitle {
      font-size: 8px;
      opacity: 0.95;
      font-weight: 500;
      line-height: 1.1;
    }
    
    /* Front Card Body */
    .card-body {
      padding: 12px 10px;
      height: calc(100% - 48px);
      display: flex;
      gap: 10px;
      position: relative;
      z-index: 2;
      align-items: center;
    }
    
    .photo-section {
      width: 38%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 4px;
    }
    
    .photo-frame {
      width: 100%;
      aspect-ratio: 3/4;
      border-radius: 8px;
      overflow: hidden;
      background: linear-gradient(145deg, #f0f0f0, #ffffff);
      border: 2px solid rgba(128, 0, 0, 0.1);
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
      position: relative;
    }
    
    .photo-frame::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
      pointer-events: none;
    }
    
    .photo-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .info-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 2px 4px;
      gap: 6px;
    }
    
    .name-section {
      margin-bottom: 4px;
    }
    
    .full-name {
      font-size: 12px;
      font-weight: 800;
      color: var(--maroon);
      text-transform: uppercase;
      letter-spacing: 0.4px;
      line-height: 1.2;
      margin-bottom: 5px;
      text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    
    .details-grid {
      display: grid;
      gap: 3px;
    }
    
    .detail-item {
      font-size: 9px;
      color: var(--dark);
      line-height: 1.3;
      font-weight: 500;
    }
    
    .detail-label {
      font-weight: 700;
      color: var(--maroon);
      display: inline-block;
      min-width: 50px;
    }
    
    .id-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 4px;
    }
    
    .id-number {
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
      color: var(--maroon);
      padding: 4px 10px;
      border-radius: 6px;
      font-weight: 800;
      font-size: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.15);
      border: 1px solid rgba(128, 0, 0, 0.2);
      letter-spacing: 0.3px;
    }
    
    .issue-date {
      font-size: 8px;
      color: var(--muted);
      font-weight: 600;
    }
    
    .signature-area {
      margin-top: 8px;
      text-align: center;
    }
    
    .signature-line {
      border-top: 1.5px solid var(--muted);
      width: 100%;
      margin: 4px 0 2px 0;
    }
    
    .signature-label {
      font-size: 7px;
      color: var(--muted);
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.3px;
    }
    
    /* Back Card Styles - Professional Design */
    .back-body {
      padding: 8px;
      height: calc(100% - 48px);
      display: flex;
      gap: 8px;
      position: relative;
      z-index: 2;
    }
    
    .info-section-back {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 3px;
      padding: 2px;
    }
    
    .info-row {
      display: flex;
      gap: 4px;
    }
    
    .info-item {
      flex: 1;
      min-width: 0;
      background: rgba(128, 0, 0, 0.02);
      padding: 3px 5px;
      border-radius: 4px;
      border-left: 2px solid rgba(128, 0, 0, 0.2);
    }
    
    .info-label-back {
      font-size: 6.5px;
      font-weight: 700;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.4px;
      margin-bottom: 1px;
      line-height: 1;
    }
    
    .info-value-back {
      font-size: 8.5px;
      font-weight: 600;
      color: var(--dark);
      line-height: 1.2;
      word-break: break-word;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .qr-section-back {
      width: 32%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      padding: 4px;
      gap: 4px;
    }
    
    .qr-frame-back {
      width: 100%;
      aspect-ratio: 1;
      border-radius: 8px;
      overflow: hidden;
      background: var(--white);
      border: 2px solid rgba(128, 0, 0, 0.15);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 6px;
    }
    
    .qr-frame-back img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    
    .qr-text-back {
      font-size: 7px;
      color: var(--muted);
      text-align: center;
      line-height: 1.3;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.2px;
    }
    
    .signature-area-back {
      width: 100%;
      margin-top: auto;
    }
    
    .signature-line-back {
      border-top: 1.5px solid var(--muted);
      width: 100%;
      margin: 2px 0;
    }
    
    .signature-label-back {
      font-size: 6px;
      color: var(--muted);
      text-transform: uppercase;
      font-weight: 700;
      letter-spacing: 0.3px;
      text-align: center;
    }
    
    .card-footer {
      background: linear-gradient(90deg, rgba(128, 0, 0, 0.05) 0%, transparent 100%);
      padding: 5px 8px;
      font-size: 6px;
      color: var(--muted);
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid rgba(0,0,0,0.05);
      position: relative;
      z-index: 2;
    }
    
    .security-text {
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    
    /* Print Styles - Different layouts for button vs Ctrl+P */
    @media print {
      @page {
        size: A4;
        margin: 10mm;
      }
      
      body {
        background: white !important;
        padding: 0;
        margin: 0;
        font-size: 12px;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
      }
      
      /* Default Ctrl+P behavior - side by side */
      .card-container {
        display: flex;
        flex-direction: row;
        gap: 20mm;
        justify-content: center;
        align-items: flex-start;
        margin: 0;
        padding: 20mm 0;
      }
      
      .id-card {
        width: 85.6mm !important;
        height: 54mm !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        border: 1px solid #ccc !important;
        page-break-inside: avoid;
        margin: 0;
        border-radius: 8px;
        transform: none !important;
        transition: none !important;
        background: white !important;
        flex-shrink: 0;
      }
      
      /* Back-to-back layout when print button is clicked */
      body.back-to-back-print .card-container {
        display: block;
        margin: 0;
        padding: 0;
      }
      
      body.back-to-back-print .id-card {
        margin: 0 auto 10mm auto;
        display: block;
      }
      
      body.back-to-back-print .id-card:first-child {
        margin-top: 20mm;
      }
      
      body.back-to-back-print .id-card:last-child {
        margin-bottom: 20mm;
      }
      
      .id-card:hover {
        transform: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
      }
      
      .id-card::before {
        opacity: 0.08 !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
      }
      
      .card-header {
        background: linear-gradient(135deg, #800000 0%, #5b0000 50%, #a52a2a 100%) !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
      }
      
      .card-header::before {
        display: none;
      }
      
      .logo {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
      }
      
      .logo img {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
      }
      
      .id-number {
        background: linear-gradient(135deg, #ffd700 0%, #daa520 100%) !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
      }
      
      .print-button {
        display: none !important;
      }
      
      /* Ensure all colors print correctly */
      * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .card-container {
        flex-direction: column;
        gap: 20px;
      }
      
      .id-card {
        width: 90vw;
        max-width: 400px;
        height: auto;
        aspect-ratio: 85.6/54;
      }
    }
    
    /* Print Controls */
    .print-controls {
      margin-top: 30px;
      text-align: center;
    }
    
    .print-button {
      padding: 12px 24px;
      background: linear-gradient(135deg, var(--maroon) 0%, var(--maroon-dark) 100%);
      color: var(--white);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      min-width: 200px;
    }
    
    .print-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(128, 0, 0, 0.4);
    }
    
    .print-button:active {
      transform: translateY(0);
    }
    
    /* Print Instructions */
    .print-instructions {
      margin-top: 20px;
      padding: 15px;
      background: rgba(128, 0, 0, 0.05);
      border-radius: 8px;
      font-size: 12px;
      color: var(--muted);
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .print-instructions h4 {
      color: var(--maroon);
      margin-bottom: 10px;
      font-size: 14px;
    }
    
    .print-instructions ul {
      margin: 0;
      padding-left: 20px;
    }
    
    .print-instructions li {
      margin-bottom: 5px;
    }
    
    .print-help {
      margin-top: 15px;
      padding: 15px;
      background: rgba(255, 215, 0, 0.1);
      border-radius: 8px;
      border-left: 4px solid var(--gold);
    }
    
    .print-help h5 {
      color: var(--maroon);
      margin-bottom: 8px;
      font-size: 14px;
    }
    
    .print-help p {
      font-size: 12px;
      color: var(--muted);
      margin: 5px 0;
    }
    
    /* Notification Animations */
    @keyframes slideIn {
      from {
        transform: translateX(400px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(400px);
        opacity: 0;
      }
    }
    
    .tracking-notification {
      font-family: 'Inter', sans-serif;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    @media print {
      .tracking-notification {
        display: none !important;
      }
    }
  </style>
</head>
<body>

<div class="card-container">
  <!-- Front Side of ID Card -->
  <div class="id-card">
    <div class="card-header">
      <div class="logo-container">
        <div class="logo">
          <img src="<?php echo htmlspecialchars($logo_path, ENT_QUOTES); ?>" 
               alt="MOIST Logo" 
               onerror="this.style.display='none'">
        </div>
      </div>
      <div class="org-info">
        <div class="org-address">Sta. Cruz, Cogon, Balingasag Misamis Oriental</div>
        <div class="org-title">MOIST ALUMNI ASSOCIATION</div>
        <div class="org-subtitle">Misamis Oriental Institute of Science and Technology</div>
      </div>
    </div>

    <div class="card-body">
      <div class="photo-section">
        <div class="photo-frame">
          <img src="<?php echo htmlspecialchars($photo, ENT_QUOTES); ?>" 
               alt="<?php echo htmlspecialchars($fullname, ENT_QUOTES); ?>" 
               onerror="this.src='../assets/img/default-avatar.jpg'; console.log('Image failed to load: <?php echo htmlspecialchars($photo, ENT_QUOTES); ?>');"
               onload="console.log('Image loaded successfully: <?php echo htmlspecialchars($photo, ENT_QUOTES); ?>');">
        </div>
      </div>

      <div class="info-section">
        <div class="name-section">
          <div class="full-name"><?php echo htmlspecialchars(strtoupper($fullname), ENT_QUOTES); ?></div>
          <div class="details-grid">
            <div class="detail-item">
              <span class="detail-label">Course:</span> <?php echo htmlspecialchars($course, ENT_QUOTES); ?>
            </div>
            <div class="detail-item">
              <span class="detail-label">Batch:</span> <?php echo htmlspecialchars($batch, ENT_QUOTES); ?>
            </div>
            <?php if(!empty($current_company) && $current_company !== 'N/A'): ?>
            <div class="detail-item">
              <span class="detail-label">Company:</span> <?php echo htmlspecialchars($current_company, ENT_QUOTES); ?>
            </div>
            <?php endif; ?>
            <?php if(!empty($current_position) && $current_position !== 'N/A'): ?>
            <div class="detail-item">
              <span class="detail-label">Position:</span> <?php echo htmlspecialchars($current_position, ENT_QUOTES); ?>
            </div>
            <?php endif; ?>
            <?php if(!empty($academic_honor)): ?>
            <div class="detail-item">
              <span class="detail-label">Honor:</span> <?php echo htmlspecialchars($academic_honor, ENT_QUOTES); ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="id-footer">
          <div class="id-number">ID: <?php echo htmlspecialchars($alumni_id_display, ENT_QUOTES); ?></div>
          <div class="issue-date">ISSUED: <?php echo htmlspecialchars($date_created, ENT_QUOTES); ?></div>
        </div>
        
        <div class="signature-area">
          <div class="signature-line"></div>
          <div class="signature-label">President Signature</div>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <div class="security-text">Official Identification Card • Not Transferable</div>
      <div style="font-weight: 700;">Alumni Affairs</div>
    </div>
  </div>

  <!-- Back Side of ID Card -->
  <div class="id-card">
    <div class="card-header">
      <div class="logo-container">
        <div class="logo">
          <img src="<?php echo htmlspecialchars($logo_path, ENT_QUOTES); ?>" 
               alt="MOIST Logo" 
               onerror="this.style.display='none'">
        </div>
      </div>
      <div class="org-info">
        <div class="org-address">Sta. Cruz, Cogon, Balingasag Misamis Oriental</div>
        <div class="org-title">ALUMNI VERIFICATION</div>
        <div class="org-subtitle">Personal Details & Contact Information</div>
      </div>
    </div>

    <div class="back-body">
      <div class="info-section-back">
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">FIRST NAME</div>
            <div class="info-value-back"><?php echo htmlspecialchars($firstname, ENT_QUOTES); ?></div>
          </div>
        </div>
        
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">MIDDLE NAME</div>
            <div class="info-value-back"><?php echo htmlspecialchars($middlename ?: 'N/A', ENT_QUOTES); ?></div>
          </div>
        </div>
        
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">BIRTHDATE</div>
            <div class="info-value-back"><?php echo htmlspecialchars($birthdate, ENT_QUOTES); ?></div>
          </div>
          <div class="info-item">
            <div class="info-label-back">SEX</div>
            <div class="info-value-back"><?php echo htmlspecialchars($sex, ENT_QUOTES); ?></div>
          </div>
        </div>
        
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">CONTACT</div>
            <div class="info-value-back"><?php echo htmlspecialchars($contact, ENT_QUOTES); ?></div>
          </div>
        </div>
        
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">EMAIL</div>
            <div class="info-value-back"><?php echo htmlspecialchars($email, ENT_QUOTES); ?></div>
          </div>
        </div>
        
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">ADDRESS</div>
            <div class="info-value-back"><?php echo htmlspecialchars($address, ENT_QUOTES); ?></div>
          </div>
        </div>
        
        <div class="info-row">
          <div class="info-item">
            <div class="info-label-back">COURSE</div>
            <div class="info-value-back"><?php echo htmlspecialchars($course, ENT_QUOTES); ?></div>
          </div>
          <div class="info-item">
            <div class="info-label-back">BATCH</div>
            <div class="info-value-back"><?php echo htmlspecialchars($batch, ENT_QUOTES); ?></div>
          </div>
        </div>
      </div>

      <div class="qr-section-back">
        <div class="qr-frame-back">
          <img src="<?php echo htmlspecialchars($qr_back, ENT_QUOTES); ?>" 
               alt="QR Code"
               title="Scan to access alumni portal">
        </div>
        <div class="qr-text-back">Scan to access<br>alumni portal</div>
        
        <div class="signature-area-back">
          <div class="signature-line-back"></div>
          <div class="signature-label-back">ALUMNI SIGNATURE</div>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <div>ID: <?php echo htmlspecialchars($alumni_id_display, ENT_QUOTES); ?> • ISSUED: <?php echo htmlspecialchars($date_created, ENT_QUOTES); ?></div>
      <div style="font-weight: 600;">MOIST Alumni</div>
    </div>
  </div>
</div>

<div class="print-controls">
  <button class="print-button" onclick="printBackToBack()">🖨️ Print Back-to-Back ID Card</button>
</div>

<script>
function printBackToBack() {
  // Add class to body for back-to-back print layout
  document.body.classList.add('back-to-back-print');
  
  // Track the ID release
  trackIdRelease('print_button');
  
  // Print the page
  window.print();
  
  // Remove class after printing (when print dialog closes)
  setTimeout(() => {
    document.body.classList.remove('back-to-back-print');
  }, 1000);
}

// Handle Ctrl+P to ensure side-by-side layout and track release
document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
    // Ensure back-to-back class is removed for Ctrl+P
    document.body.classList.remove('back-to-back-print');
    
    // Track the ID release for Ctrl+P
    trackIdRelease('ctrl_p');
  }
});

// Also handle browser print events
window.addEventListener('beforeprint', function() {
  // If back-to-back class is not present, ensure side-by-side layout
  if (!document.body.classList.contains('back-to-back-print')) {
    document.body.classList.remove('back-to-back-print');
  }
});

// Function to track ID release with real-time feedback
function trackIdRelease(method) {
  const alumniId = <?php echo $id; ?>;
  
  // Show tracking notification
  showTrackingNotification('Tracking print...', 'info');
  
  fetch('track_id_release.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `alumni_id=${alumniId}&release_method=${method}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log(`ID release tracked: ${data.new_count} times (${data.release_method})`);
      showTrackingNotification(`✓ Print tracked successfully! (Count: ${data.new_count})`, 'success');
      
      // Update parent window if opened from alumni.php
      if (window.opener && !window.opener.closed) {
        window.opener.postMessage({
          type: 'id_printed',
          alumniId: alumniId,
          count: data.new_count,
          method: method
        }, '*');
      }
    } else {
      console.error('Failed to track ID release:', data.message);
      showTrackingNotification('⚠ Tracking failed', 'error');
    }
  })
  .catch(error => {
    console.error('Error tracking ID release:', error);
    showTrackingNotification('⚠ Tracking error', 'error');
  });
}

// Show tracking notification
function showTrackingNotification(message, type) {
  // Remove existing notification
  const existing = document.querySelector('.tracking-notification');
  if (existing) existing.remove();
  
  // Create notification
  const notification = document.createElement('div');
  notification.className = `tracking-notification tracking-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    font-size: 14px;
    font-weight: 600;
    z-index: 10000;
    animation: slideIn 0.3s ease;
  `;
  
  document.body.appendChild(notification);
  
  // Auto remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}
</script>

<div class="print-instructions">
  <h4>📋 Professional PVC ID Card Printing Instructions:</h4>
  <ul>
    <li><strong>Paper Size:</strong> A4 (210mm × 297mm) - Cards will be centered and properly sized</li>
    <li><strong>Orientation:</strong> Portrait</li>
    <li><strong>Print Quality:</strong> High quality/Best for professional results</li>
    <li><strong>Material:</strong> Use PVC card stock or high-quality photo paper</li>
    <li><strong>Colors:</strong> Enable color printing and ensure "Print backgrounds" is checked</li>
    <li><strong>Browser Settings:</strong> Enable "More settings" → "Background graphics"</li>
    <li><strong>Dimensions:</strong> Cards print at exact ISO/IEC 7810 ID-1 standard (85.6mm × 54mm)</li>
  </ul>
  
  <div class="print-help">
    <h5>💡 Two Print Options Available:</h5>
    <p><strong>🖨️ Print Button:</strong> Creates back-to-back layout (cards stacked vertically) - Perfect for duplex printing</p>
    <p><strong>⌨️ Ctrl+P:</strong> Shows front and back side-by-side - Great for viewing both sides at once</p>
    <p><strong>Chrome/Edge:</strong> Press Ctrl+P → More settings → Check "Background graphics"</p>
    <p><strong>Firefox:</strong> Press Ctrl+P → More settings → Check "Print backgrounds"</p>
    <p><strong>For best results:</strong> Use "Print to PDF" first, then print the PDF for consistent quality</p>
    <p><strong>Duplex Printing:</strong> Use Print Button + "Print on both sides" in printer settings</p>
  </div>
</div>

</body>
</html>

