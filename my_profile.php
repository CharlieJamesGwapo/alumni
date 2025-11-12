<?php
// my_profile.php
// Private editable profile page — only the logged-in user can view full details.
// Requires admin/db_connect.php which sets $conn (mysqli).

session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

define('UPLOAD_DIR', __DIR__ . '/uploads/');
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
define('MAX_UPLOAD_BYTES', 8 * 1024 * 1024); // 8MB

// CSRF token
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
$csrf = $_SESSION['csrf_token'];

function safe_filename($name){ return preg_replace('/[^A-Za-z0-9\.\-_]/','_',$name); }
function jsonResponse($ok, $msg = '', $data = []){ header('Content-Type: application/json; charset=utf-8'); echo json_encode(array_merge(['ok'=>$ok,'msg'=>$msg], $data)); exit; }
function avatar_url($img){
  if (!empty($img) && file_exists(__DIR__.'/uploads/'.$img)) return 'uploads/' . rawurlencode($img);
  if (file_exists(__DIR__.'/uploads/default_avatar.jpg')) return 'uploads/default_avatar.jpg';
  return 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=640&q=60&auto=format&fit=crop';
}

// Simple GD-based optimizer & thumb
function create_thumb($path, $dest, $thumbPath, $maxW = 1200, $thumb = 160){
    $info = @getimagesize($path);
    if (!$info) return false;
    list($w,$h,$type) = $info;
    $mime = image_type_to_mime_type($type);
    switch($mime){
        case 'image/jpeg': $src = imagecreatefromjpeg($path); break;
        case 'image/png': $src = imagecreatefrompng($path); break;
        case 'image/gif': $src = imagecreatefromgif($path); break;
        case 'image/webp': $src = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false; break;
        default: return false;
    }
    if (!$src) return false;
    // resize main
    if ($w > $maxW){
        $ratio = $maxW / $w; $nw = (int)($w * $ratio); $nh = (int)($h * $ratio);
    } else { $nw = $w; $nh = $h; }
    $dst = imagecreatetruecolor($nw, $nh);
    if (in_array($mime,['image/png','image/gif','image/webp'])){ imagecolortransparent($dst, imagecolorallocatealpha($dst,0,0,0,127)); imagealphablending($dst,false); imagesavealpha($dst,true); }
    imagecopyresampled($dst, $src, 0,0,0,0, $nw, $nh, $w, $h);
    switch($mime){
        case 'image/jpeg': imagejpeg($dst, $dest, 85); break;
        case 'image/png': imagepng($dst, $dest); break;
        case 'image/gif': imagegif($dst, $dest); break;
        case 'image/webp': if (function_exists('imagewebp')) imagewebp($dst, $dest, 85); break;
    }
    imagedestroy($dst);

    // thumb center crop
    $info2 = @getimagesize($dest);
    if ($info2) list($w2,$h2) = $info2; else { $w2=$nw; $h2=$nh; }
    switch($mime){
        case 'image/jpeg': $src2 = imagecreatefromjpeg($dest); break;
        case 'image/png': $src2 = imagecreatefrompng($dest); break;
        case 'image/gif': $src2 = imagecreatefromgif($dest); break;
        case 'image/webp': $src2 = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($dest) : false; break;
    }
    if ($src2){
        $tr = $thumb / $thumb;
        $srcRatio = $w2/$h2; $thumbRatio = 1; // square
        if ($srcRatio > $thumbRatio){ $cropH = $h2; $cropW = (int)($h2 * $thumbRatio); $sx = (int)(($w2 - $cropW)/2); $sy = 0; }
        else { $cropW = $w2; $cropH = (int)($w2 / $thumbRatio); $sx = 0; $sy = (int)(($h2 - $cropH)/2); }
        $dst2 = imagecreatetruecolor($thumb, $thumb);
        if (in_array($mime,['image/png','image/gif','image/webp'])){ imagecolortransparent($dst2, imagecolorallocatealpha($dst2,0,0,0,127)); imagealphablending($dst2,false); imagesavealpha($dst2,true); }
        imagecopyresampled($dst2, $src2, 0,0, $sx,$sy, $thumb,$thumb, $cropW,$cropH);
        switch($mime){
            case 'image/jpeg': imagejpeg($dst2, $thumbPath, 85); break;
            case 'image/png': imagepng($dst2, $thumbPath); break;
            case 'image/gif': imagegif($dst2, $thumbPath); break;
            case 'image/webp': if (function_exists('imagewebp')) imagewebp($dst2, $thumbPath, 85); break;
        }
        imagedestroy($dst2);
        imagedestroy($src2);
    }
    imagedestroy($src);
    return true;
}

// Handle AJAX profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'save_my_profile') {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid token');
    $uid = intval($_SESSION['login_id']);

    // sanitize inputs
    $fields = [
        'alumni_id','lastname','firstname','middlename','suffixname','birthdate','address',
        'gender','batch','course_id','connected_to','contact_no','company_address','company_email','email','academic_honor'
    ];
    $vals = [];
    foreach($fields as $f) $vals[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : null;

    if (empty($vals['firstname']) || empty($vals['lastname']) || empty($vals['email'])) jsonResponse(false, 'Firstname, Lastname and Email required.');

    $imgName = null;
    if (!empty($_FILES['profile_img']['name'])) {
        $orig = safe_filename(basename($_FILES['profile_img']['name']));
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) jsonResponse(false, 'Invalid image type.');
        if ($_FILES['profile_img']['size'] > MAX_UPLOAD_BYTES) jsonResponse(false, 'Image too large.');
        $imgName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $tmp = $_FILES['profile_img']['tmp_name'];
        $dest = UPLOAD_DIR . $imgName;
        if (!move_uploaded_file($tmp, $dest)) jsonResponse(false, 'Failed to move upload.');
        create_thumb($dest, $dest, UPLOAD_DIR . 'thumb_' . $imgName, 1200, 160);
    }

    // Update DB preserving your column names
    if ($imgName) {
        $stmt = $conn->prepare("UPDATE alumnus_bio SET alumni_id=?, lastname=?, firstname=?, middlename=?, suffixname=?, birthdate=?, address=?, gender=?, batch=?, course_id=?, connected_to=?, contact_no=?, company_address=?, company_email=?, email=?, img=?, academic_honor=? WHERE id = ?");
        $stmt->bind_param("ssssssssissssssssi",
            $vals['alumni_id'],$vals['lastname'],$vals['firstname'],$vals['middlename'],$vals['suffixname'],$vals['birthdate'],
            $vals['address'],$vals['gender'],$vals['batch'],$vals['course_id'],$vals['connected_to'],$vals['contact_no'],
            $vals['company_address'],$vals['company_email'],$vals['email'],$imgName,$vals['academic_honor'],$uid
        );
    } else {
        $stmt = $conn->prepare("UPDATE alumnus_bio SET alumni_id=?, lastname=?, firstname=?, middlename=?, suffixname=?, birthdate=?, address=?, gender=?, batch=?, course_id=?, connected_to=?, contact_no=?, company_address=?, company_email=?, email=?, academic_honor=? WHERE id = ?");
        $stmt->bind_param("ssssssssisssssssi",
            $vals['alumni_id'],$vals['lastname'],$vals['firstname'],$vals['middlename'],$vals['suffixname'],$vals['birthdate'],
            $vals['address'],$vals['gender'],$vals['batch'],$vals['course_id'],$vals['connected_to'],$vals['contact_no'],
            $vals['company_address'],$vals['company_email'],$vals['email'],$vals['academic_honor'],$uid
        );
    }
    if (!$stmt->execute()) jsonResponse(false, 'DB update failed: '.$conn->error);
    // return updated row
    $stmt2 = $conn->prepare("SELECT a.*, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
    $stmt2->bind_param("i",$uid); $stmt2->execute(); $user = $stmt2->get_result()->fetch_assoc();
    jsonResponse(true, 'Profile saved', ['user'=>$user]);
}

// Load current user data for display
$uid = intval($_SESSION['login_id']);
$stmt = $conn->prepare("SELECT a.*, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
$stmt->bind_param("i",$uid); $stmt->execute(); $user = $stmt->get_result()->fetch_assoc();
if (!$user) { echo "User not found."; exit; }

// Safely determine admin flag to prevent undefined variable notices
$IS_ADMIN = false;
if (!empty($_SESSION['is_admin'])) {
  $IS_ADMIN = true;
} elseif (!empty($user['is_admin']) || (!empty($user['role']) && strtolower($user['role']) === 'admin')) {
  $IS_ADMIN = true;
}

// small helpers for UI
$courses = $conn->query("SELECT id, course FROM courses ORDER BY course ASC");
$batch_years = []; $resb = $conn->query("SELECT DISTINCT batch FROM alumnus_bio ORDER BY batch DESC"); while ($r = $resb->fetch_assoc()) $batch_years[] = $r['batch'];

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>My Profile - AlumniGram</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<!-- Favicon / mobile icon -->
<link rel="icon" type="image/png" sizes="32x32" href="assets/img/icon.png">
<link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon.png">
<style>
:root{
  --maroon: #800000;
  --maroon-r: 128,0,0;
  --white: #ffffff;
  --bg: #fffdfc;
  --card: #ffffff;
  --text: #0b1220;
  --muted: #6b7280;
  --glass: rgba(128,0,0,0.04);
}

[data-theme="dark"]{
  --bg: #071018; /* neutral dark background */
  --card: #0f1720;
  --text: #ffffff;
  --muted: #9ca3af;
}

.logo-img{width:40px;height:40px;object-fit:contain;border-radius:8px}
body{background:var(--bg);font-family:Inter,system-ui, -apple-system, 'Segoe UI', Roboto, Arial;color:var(--text);transition:background .25s,color .25s}
.container-main{max-width:1100px;margin:28px auto;padding:0 16px 80px}
.profile-hero{display:flex;gap:22px;align-items:center;margin-bottom:18px}
.profile-cover{background:linear-gradient(90deg,var(--glass),transparent);padding:18px;border-radius:14px;display:flex;align-items:center;gap:18px}
.avatar-wrap{position:relative;width:140px}
.avatar{width:140px;height:140px;border-radius:50%;object-fit:cover;border:6px solid var(--white);box-shadow:0 14px 40px rgba(2,6,23,0.06);background:var(--white)}
.avatar-edit{position:absolute;right:6px;bottom:6px;background:linear-gradient(180deg,var(--maroon),rgba(var(--maroon-r),0.85));color:var(--white);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(0,0,0,0.12);border:3px solid var(--white)}
.panel{background:var(--card);padding:18px;border-radius:12px;box-shadow:0 8px 30px rgba(2,6,23,0.04)}
@media(max-width:576px){.profile-hero{flex-direction:column;align-items:flex-start}.avatar{width:96px;height:96px}}
.small-muted{color:var(--muted);font-size:.95rem}
.info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
.details-card{background:var(--card);padding:14px;border-radius:10px;border:1px solid rgba(15,23,36,0.04)}
.detail-label{font-size:.82rem;color:var(--muted);font-weight:700}
.detail-value{font-size:0.98rem;color:var(--text);margin-top:6px}
.action-row{display:flex;gap:10px;align-items:center}
.action-btn{border-radius:8px}
.copied-badge{display:inline-block;margin-left:8px;color:green;font-weight:600}
.events-grid .card{border:0;border-radius:10px}
.footer-site{background:var(--card);border-top:1px solid rgba(15,23,36,0.02);padding:18px;margin-top:32px;border-radius:10px}
.username-large{font-size:1.4rem;font-weight:800}

/* Maroon button utilities */
.btn-primary{background:var(--maroon);border-color:var(--maroon)}
.btn-primary:hover{background:rgba(var(--maroon-r),0.95);border-color:rgba(var(--maroon-r),0.95)}
.btn-outline-maroon{border:1px solid var(--maroon);color:var(--maroon);background:transparent}
.btn-outline-maroon:hover{background:var(--maroon);color:var(--white)}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
  <div class="container">
  <a class="navbar-brand fw-bold" href="home.php">AlumniGram</a>
    <div>
    </div>
  </div>
</nav>

<main class="container-main">
  <section class="profile-hero" aria-label="Profile summary">
    <div class="profile-cover panel" style="flex:1">
      <div class="avatar-wrap">
        <img src="<?php echo avatar_url($user['img']); ?>" class="avatar" id="meAvatar" alt="avatar">
        <a class="avatar-edit" href="edit_profile.php" title="Edit profile"><i class="fa-regular fa-pen-to-square"></i></a>
      </div>
      <div style="flex:1">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
          <div>
            <div class="username-large"><?php echo htmlspecialchars(trim($user['firstname'].' '.($user['middlename']?:'').' '.$user['lastname'])); ?></div>
            <div class="small-muted"><?php echo htmlspecialchars(($user['course_name'] ?? 'N/A').' • Batch '.($user['batch'] ?? 'N/A')); ?></div>
            <div class="mt-2 small-muted">A short summary of your profile appears here. Keep your contact info up to date so batchmates can reach you.</div>
          </div>
          <div class="action-row">
            <a class="btn btn-primary action-btn" href="edit_profile.php"><i class="fa-regular fa-pen-to-square me-1"></i> Edit Profile</a>
          </div>
        </div>

        <div class="mt-3 details-card" role="region" aria-label="Profile details">
          <div class="info-grid">
            <div>
              <div class="detail-label">Alumni ID</div>
              <div class="detail-value d-flex align-items-center justify-content-between">
                <div id="alumniIdVal"><?php echo htmlspecialchars($user['alumni_id'] ?: '—'); ?></div>
                <?php if(!empty($user['alumni_id'])): ?>
                  <button id="copyId" class="btn btn-outline-secondary btn-sm ms-2" title="Copy Alumni ID"><i class="fa-regular fa-copy"></i></button>
                <?php endif; ?>
              </div>
            </div>

            <div>
              <div class="detail-label">Email</div>
              <div class="detail-value"><?php if(!empty($user['email'])): ?><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></a><?php else: echo '—'; endif; ?></div>
            </div>

            <div>
              <div class="detail-label">Contact</div>
              <div class="detail-value"><?php if(!empty($user['contact_no'])): ?><a href="tel:<?php echo htmlspecialchars($user['contact_no']); ?>"><?php echo htmlspecialchars($user['contact_no']); ?></a><?php else: echo '—'; endif; ?></div>
            </div>

            <div>
              <div class="detail-label">Address</div>
              <div class="detail-value"><?php echo htmlspecialchars($user['address'] ?: '—'); ?></div>
            </div>

            <div>
              <div class="detail-label">Company</div>
              <div class="detail-value"><?php echo htmlspecialchars($user['company_address'] ?: '—'); if(!empty($user['company_email'])) echo ' <small class="text-muted">— <a href="mailto:'.htmlspecialchars($user['company_email']).'">'.htmlspecialchars($user['company_email']).'</a></small>'; ?></div>
            </div>

            <div>
              <div class="detail-label">Academic Honor</div>
              <div class="detail-value"><?php echo htmlspecialchars($user['academic_honor'] ?: '—'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!--<section class="panel mt-3">
    <h5 class="mb-3">Timeline & Events (Public)</h5>
    <?php
      $evs = $conn->query("SELECT * FROM events ORDER BY schedule DESC LIMIT 8");
      if ($evs->num_rows == 0) echo '<div class="small-muted">No events yet.</div>';
      else {
        echo '<div class="row g-3">';
        while ($e = $evs->fetch_assoc()){
          $banner = (!empty($e['banner']) && file_exists(UPLOAD_DIR.$e['banner'])) ? 'uploads/'.rawurlencode($e['banner']) : 'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=1200&q=60&auto=format&fit=crop';
          echo '<div class="col-md-6"><div class="card"><img src="'.htmlspecialchars($banner).'" class="card-img-top" style="height:180px;object-fit:cover"><div class="card-body"><h6 class="card-title">'.htmlspecialchars($e['title']).'</h6><p class="small-muted">'.htmlspecialchars(date("F j, Y g:ia", strtotime($e['schedule']))).'</p><p class="small-muted">'.htmlspecialchars(strip_tags($e['content'] ? strip_tags($e['content']) : '')).'</p></div></div></div>';
        }
        echo '</div>';
      }
    ?>
  </section-->
</main>

<!-- Edit moved to standalone page: edit_profile.php -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  $('#copyId').on('click', function(){
    const val = $('#alumniIdVal').text().trim();
    if (!val || val === '—') return;
    // fallback for browsers without clipboard API
    const write = (txt) => navigator.clipboard?.writeText(txt) || new Promise((res, rej) => { try { const ta = document.createElement('textarea'); ta.value = txt; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove(); res(); } catch(e){ rej(e) } });
    write(val).then(function(){
      const $b = $('<span class="copied-badge">Copied</span>');
      $('#copyId').after($b);
      setTimeout(()=> $b.fadeOut(300,function(){ $(this).remove(); }),1200);
    }).catch(function(){ alert('Copy failed'); });
  });
});
</script>
<script>
$(function(){
  $('#profile_img').on('change', function(){
    const f = this.files[0]; if (!f) return;
    if (f.size > <?php echo MAX_UPLOAD_BYTES; ?>) { alert('Image too large'); this.value=''; return; }
    const r = new FileReader(); r.onload = e => $('#previewImg').attr('src', e.target.result); r.readAsDataURL(f);
  });

  $('#editForm').on('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    $('#saveBtn').prop('disabled', true).text('Saving...');
    $.ajax({
      url: '',
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      dataType: 'json'
    }).done(function(resp){
      if (resp.ok) location.reload();
      else { $('#editAlert').removeClass().addClass('alert alert-danger').text(resp.msg).show(); }
    }).fail(function(){ alert('Save failed'); }).always(function(){ $('#saveBtn').prop('disabled', false).text('Save'); });
  });
});
</script>

</main>

<?php
// reuse the site footer for consistency and dynamic content
?>
</body>
</html>
