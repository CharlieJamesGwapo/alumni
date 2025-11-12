<?php
// view_profile.php
// Public limited profile view. If the visitor is the owner (logged-in and same id), show full info.

session_start();
include 'admin/db_connect.php';
define('UPLOAD_DIR', __DIR__ . '/uploads/');

function avatar_url($img){
    if (!empty($img) && file_exists(__DIR__.'/uploads/'.$img)) return 'uploads/' . rawurlencode($img);
    return 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=640&q=60&auto=format&fit=crop';
}

$viewId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($viewId <= 0) { echo "Invalid profile."; exit; }

$stmt = $conn->prepare("SELECT a.*, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
$stmt->bind_param("i",$viewId); $stmt->execute(); $user = $stmt->get_result()->fetch_assoc();
if (!$user) { echo "Profile not found."; exit; }

$isOwner = (isset($_SESSION['login_id']) && intval($_SESSION['login_id']) === intval($user['id']));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($user['firstname'].' '.$user['lastname']); ?> - AlumniGram</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
body{font-family:Inter,system-ui, -apple-system, 'Segoe UI', Roboto, Arial;background:#f7fbff;color:#0b1220}
.container-main{max-width:900px;margin:24px auto;padding:0 16px 60px;}
.panel{background:#fff;padding:14px;border-radius:10px;box-shadow:0 6px 20px rgba(2,6,23,0.04)}
.avatar{width:120px;height:120px;border-radius:50%;object-fit:cover}
.small-muted{color:#6b7280}
</style>
</head>
<body>
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="directory.php">AlumniGram</a>
    <div>
      <a href="directory.php" class="btn btn-outline-secondary btn-sm me-2">Back</a>
      <?php if (isset($_SESSION['login_id'])): ?><a href="my_profile.php" class="btn btn-outline-primary btn-sm">My Profile</a><?php else: ?><a href="login.php" class="btn btn-outline-primary btn-sm">Login</a><?php endif; ?>
    </div>
  </div>
</nav>

<main class="container-main">
  <div class="panel d-flex gap-3 align-items-center">
    <img src="<?php echo avatar_url($user['img']); ?>" class="avatar" alt="avatar">
    <div style="flex:1">
      <h3 class="mb-1"><?php echo htmlspecialchars($user['firstname'].' '.($user['middlename']?:'').' '.$user['lastname']); ?></h3>
      <div class="small-muted"><?php echo htmlspecialchars(($user['course_name'] ?? 'N/A').' • Batch '.($user['batch'] ?? 'N/A')); ?></div>
      <div class="mt-3">
        <?php if ($isOwner): ?>
          <!-- Owner sees full details -->
          <div><strong>Alumni ID:</strong> <?php echo htmlspecialchars($user['alumni_id']); ?></div>
          <div><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
          <div><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact_no']); ?></div>
          <div><strong>Birthdate:</strong> <?php echo htmlspecialchars($user['birthdate']); ?></div>
          <div><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></div>
          <div><strong>Company:</strong> <?php echo htmlspecialchars($user['company_address']); ?> — <?php echo htmlspecialchars($user['company_email']); ?></div>
          <div><strong>Academic Honor:</strong> <?php echo htmlspecialchars($user['academic_honor']); ?></div>
        <?php else: ?>
          <!-- Public limited info -->
          <div class="small-muted">This is a public profile. Contact details and other private info are hidden.</div>
          <div class="mt-2"><strong>Course:</strong> <?php echo htmlspecialchars($user['course_name']); ?></div>
          <div><strong>Batch:</strong> <?php echo htmlspecialchars($user['batch']); ?></div>
        <?php endif; ?>
      </div>
    </div>
    <div>
      <?php if ($isOwner): ?>
        <a href="my_profile.php" class="btn btn-primary btn-sm">Edit Profile</a>
      <?php endif; ?>
    </div>
  </div>
</main>
</body>
</html>
