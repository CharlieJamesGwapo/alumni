<?php

session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

// === CONFIG ===
define('UPLOAD_DIR', __DIR__ . '/uploads/');
if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);

// Admin user IDs (change to actual admin user IDs). Default: user id 1.
$ADMIN_IDS = [1];

// Maximum upload size (bytes)
define('MAX_UPLOAD_BYTES', 8 * 1024 * 1024);

// Enable progressive JPEG for Imagick output
define('PROGRESSIVE_JPEG', true);

// === HELPERS ===
function safe_filename($name) {
    $name = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $name);
    return $name;
}

function jsonResponse($ok, $msg, $data = null) {
    // Clear any previous output
    if (ob_get_level()) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok, 'msg' => $msg, 'data' => $data]);
    exit;
}

// Return a safe avatar URL, prefer uploaded file, no fallback image
function avatar_url($img){
  if (!empty($img)) {
    $path = UPLOAD_DIR . $img;
    if (file_exists($path)) return 'uploads/' . rawurlencode($img);
  }
  // Return empty string if no image - will be handled by frontend
  return '';
}

// Time ago function
function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);
    
    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return ($days == 1) ? "Yesterday" : "$days days ago";
    } else if ($weeks <= 4.3) {
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "1 year ago" : "$years years ago";
    }
}

// Image processing: try Imagick, fall back to GD
function optimize_image($srcPath, $dstPath, $maxW=1200, $maxH=1200, $quality=85) {
    // Use Imagick if available
    if (class_exists('Imagick')) {
        try {
            $img = new Imagick($srcPath);
            $img->stripImage(); // remove metadata
            $img->setImageCompressionQuality($quality);
            $img->setInterlaceScheme(Imagick::INTERLACE_PLANE); // progressive
            $img->resizeImage($maxW, $maxH, Imagick::FILTER_LANCZOS, 1, true);
            // if jpeg, set progressive
            $format = strtolower($img->getImageFormat());
            if (in_array($format, ['jpeg','jpg'])) {
                $img->setImageFormat('jpeg');
            }
            $img->writeImage($dstPath);
            $img->clear();
            $img->destroy();
            @chmod($dstPath, 0644);
            return true;
        } catch (Exception $e) {
            // fall through to GD
        }
    }
    // GD fallback
    list($width, $height, $type) = @getimagesize($srcPath);
    if (!$width) return false;
    $ratio = min($maxW / $width, $maxH / $height, 1);
    $newW = max(1, (int)($width * $ratio));
    $newH = max(1, (int)($height * $ratio));
    $mime = image_type_to_mime_type($type);
    switch ($mime) {
        case 'image/jpeg': $src = @imagecreatefromjpeg($srcPath); break;
        case 'image/png':  $src = @imagecreatefrompng($srcPath); break;
        case 'image/gif':  $src = @imagecreatefromgif($srcPath); break;
        case 'image/webp': $src = @imagecreatefromwebp($srcPath); break;
        default: return false;
    }
    if (!$src) return false;
    $dst = imagecreatetruecolor($newW, $newH);
    // preserve transparency for png/gif
    if ($mime === 'image/png' || $mime === 'image/gif' || $mime === 'image/webp') {
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0,0,0,127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0,0,0,0, $newW, $newH, $width, $height);
    // save
    if ($mime === 'image/jpeg') {
        imagejpeg($dst, $dstPath, $quality);
    } elseif ($mime === 'image/png') {
        // quality mapping
        $pngq = (int) round((100-$quality)/10);
        imagepng($dst, $dstPath, min(9, max(0, $pngq)));
    } elseif ($mime === 'image/gif') {
        imagegif($dst, $dstPath);
    } elseif ($mime === 'image/webp') {
        imagewebp($dst, $dstPath, $quality);
    }
    imagedestroy($src);
    imagedestroy($dst);
    @chmod($dstPath, 0644);
    return true;
}

// CSRF
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf_token = $_SESSION['csrf_token'];

// Current user
$userId = intval($_SESSION['login_id']);

// is admin?
function is_admin($userId, $ADMIN_IDS) {
    return in_array($userId, $ADMIN_IDS);
}
$IS_ADMIN = is_admin($userId, $ADMIN_IDS);

// === AJAX endpoints (must be handled before HTML output) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    // Clear any output buffers first
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    $action = $_POST['ajax_action'];

    // ---------- fetch_comments ----------
    if ($action === 'fetch_comments') {
        $event_id = intval($_POST['event_id'] ?? 0);
        if ($event_id <= 0) jsonResponse(false, 'Invalid event id');
        $stmt = $conn->prepare("SELECT ec.id, ec.comment, ec.created_at, ec.user_id, a.firstname, a.lastname, a.img as user_img FROM event_comments ec LEFT JOIN alumnus_bio a ON ec.user_id = a.id WHERE ec.event_id = ? ORDER BY ec.created_at DESC");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $comments = [];
        while ($r = $res->fetch_assoc()) {
            $comments[] = $r;
        }
        jsonResponse(true, 'ok', ['comments' => $comments]);
    }

    // ---------- post_comment ----------
    if ($action === 'post_comment') {
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');
        $event_id = intval($_POST['event_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if ($event_id <= 0 || $comment === '') jsonResponse(false, 'Invalid data');
        $stmt = $conn->prepare("INSERT INTO event_comments (event_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $event_id, $userId, $comment);
        if (!$stmt->execute()) jsonResponse(false, 'Failed to save comment: ' . $conn->error);
        $id = $stmt->insert_id;
        // return the new comment
        $stmt2 = $conn->prepare("SELECT ec.id, ec.comment, ec.created_at, ec.user_id, a.firstname, a.lastname, a.img as user_img FROM event_comments ec LEFT JOIN alumnus_bio a ON ec.user_id = a.id WHERE ec.id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $new = $stmt2->get_result()->fetch_assoc();
        jsonResponse(true, 'Comment posted', ['comment' => $new]);
    }

    // ---------- edit_comment ----------
    if ($action === 'edit_comment') {
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');
        $comment_id = intval($_POST['comment_id'] ?? 0);
        $text = trim($_POST['comment'] ?? '');
        if ($comment_id <= 0 || $text === '') jsonResponse(false, 'Invalid data');
        // check ownership or admin
        $stmt = $conn->prepare("SELECT user_id FROM event_comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) jsonResponse(false, 'Comment not found');
        if ($row['user_id'] != $userId && !$IS_ADMIN) jsonResponse(false, 'Not allowed');
        $stmt2 = $conn->prepare("UPDATE event_comments SET comment = ? WHERE id = ?");
        $stmt2->bind_param("si", $text, $comment_id);
        if (!$stmt2->execute()) jsonResponse(false, 'Failed to update');
        jsonResponse(true, 'Comment edited');
    }

    // ---------- delete_comment ----------
    if ($action === 'delete_comment') {
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');
        $comment_id = intval($_POST['comment_id'] ?? 0);
        if ($comment_id <= 0) jsonResponse(false, 'Invalid');
        $stmt = $conn->prepare("SELECT user_id FROM event_comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id); $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) jsonResponse(false, 'Not found');
        if ($row['user_id'] != $userId && !$IS_ADMIN) jsonResponse(false, 'Not allowed');
        $stmt2 = $conn->prepare("DELETE FROM event_comments WHERE id = ?");
        $stmt2->bind_param("i", $comment_id); $stmt2->execute();
        jsonResponse(true, 'Deleted');
    }

    // ---------- toggle_like ----------
    if ($action === 'toggle_like') {
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');
        $event_id = intval($_POST['event_id'] ?? 0);
        if ($event_id <= 0) jsonResponse(false, 'Invalid event');
        $stmt = $conn->prepare("SELECT id FROM event_likes WHERE event_id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param("ii", $event_id, $userId); $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        if ($exists) {
            $stmt2 = $conn->prepare("DELETE FROM event_likes WHERE id = ?");
            $stmt2->bind_param("i", $exists['id']); $stmt2->execute();
            $stmtc = $conn->prepare("SELECT COUNT(*) as c FROM event_likes WHERE event_id = ?");
            $stmtc->bind_param("i", $event_id); $stmtc->execute(); $count = $stmtc->get_result()->fetch_assoc()['c'];
            jsonResponse(true, 'unliked', ['liked'=>false, 'count'=>intval($count)]);
        } else {
            $stmt3 = $conn->prepare("INSERT INTO event_likes (event_id, user_id) VALUES (?, ?)");
            $stmt3->bind_param("ii", $event_id, $userId); $stmt3->execute();
            $stmtc = $conn->prepare("SELECT COUNT(*) as c FROM event_likes WHERE event_id = ?");
            $stmtc->bind_param("i", $event_id); $stmtc->execute(); $count = $stmtc->get_result()->fetch_assoc()['c'];
            jsonResponse(true, 'liked', ['liked'=>true, 'count'=>intval($count)]);
        }
    }

    // ---------- toggle_bookmark ----------
    if ($action === 'toggle_bookmark') {
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');
        $event_id = intval($_POST['event_id'] ?? 0);
        if ($event_id <= 0) jsonResponse(false, 'Invalid event');
        $stmt = $conn->prepare("SELECT id FROM event_bookmarks WHERE event_id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param("ii", $event_id, $userId); $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        if ($exists) {
            $stmt2 = $conn->prepare("DELETE FROM event_bookmarks WHERE id = ?");
            $stmt2->bind_param("i", $exists['id']); $stmt2->execute();
            jsonResponse(true, 'unbookmarked', ['bookmarked'=>false]);
        } else {
            $stmt3 = $conn->prepare("INSERT INTO event_bookmarks (event_id, user_id) VALUES (?, ?)");
            $stmt3->bind_param("ii", $event_id, $userId); $stmt3->execute();
            jsonResponse(true, 'bookmarked', ['bookmarked'=>true]);
        }
    }

    // ---------- create_job (admin only) ----------
    if ($action === 'create_job') {
        if (!$IS_ADMIN) jsonResponse(false, 'Not allowed');
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');

        $job_title = trim($_POST['job_title'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($job_title) || empty($company) || empty($description)) {
            jsonResponse(false, 'Job title, company, and description are required');
        }

        try {
            $stmt = $conn->prepare("INSERT INTO careers (company, location, job_title, description, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $company, $location, $job_title, $description, $userId);

            if ($stmt->execute()) {
                jsonResponse(true, 'Job posted successfully!');
            } else {
                jsonResponse(false, 'Failed to post job: ' . $conn->error);
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error posting job: ' . $e->getMessage());
        }
    }

    // ---------- fetch_timeline (paginated) ----------
    if ($action === 'fetch_timeline') {
        $page = max(1, intval($_POST['page'] ?? 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;
        $stmt = $conn->prepare("SELECT * FROM events ORDER BY date_created DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $res = $stmt->get_result();
        $events = [];
        while ($r = $res->fetch_assoc()) $events[] = $r;

        // Render HTML snippets (same format as page)
        $fallbacks = [
            'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=1200&q=60&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1523906630133-f6934a84cf15?w=1200&q=60&auto=format&fit=crop'
        ];
        $html = '';
        foreach ($events as $i => $ev) {
            $banner = (!empty($ev['banner']) && file_exists(UPLOAD_DIR . $ev['banner'])) ? 'uploads/'.htmlspecialchars($ev['banner']) : $fallbacks[$i % count($fallbacks)];
            $fullContent = html_entity_decode($ev['content']);
            $plain = trim(strip_tags($fullContent));
            $preview = mb_strlen($plain) > 220 ? mb_substr($plain,0,220).'...' : $plain;
            $isFuture = !empty($ev['schedule']) ? strtotime($ev['schedule']) > time() : false;

            // counts and user status
            $stmtc = $conn->prepare("SELECT COUNT(*) as c FROM event_likes WHERE event_id = ?");
            $stmtc->bind_param("i", $ev['id']); $stmtc->execute(); $likes = intval($stmtc->get_result()->fetch_assoc()['c']);
            $stmtb = $conn->prepare("SELECT 1 FROM event_bookmarks WHERE event_id = ? AND user_id = ? LIMIT 1");
            $stmtb->bind_param("ii", $ev['id'], $userId); $stmtb->execute(); $booked = (bool)$stmtb->get_result()->fetch_assoc();
            $stmtl = $conn->prepare("SELECT 1 FROM event_likes WHERE event_id = ? AND user_id = ? LIMIT 1");
            $stmtl->bind_param("ii", $ev['id'], $userId); $stmtl->execute(); $liked = (bool)$stmtl->get_result()->fetch_assoc();

            ob_start();
            ?>
            <article class="card-tile" data-event-id="<?php echo intval($ev['id']); ?>">
              <img src="<?php echo $banner; ?>" class="banner" alt="">
              <div class="tile-body">
                <div style="display:flex;justify-content:space-between;">
                  <div>
                    <div class="card-title"><?php echo htmlspecialchars($ev['title']); ?></div>
                    <div class="tile-meta small-muted"><?php echo !empty($ev['schedule']) ? date("F j, Y, g:ia", strtotime($ev['schedule'])) . ' • ' . ($isFuture ? 'Upcoming' : 'Past Event') : date("F j, Y, g:ia", strtotime($ev['date_created'])) . ' • Post'; ?></div>
                  </div>
                  <div class="small-muted">Posted <?php echo date("M j, Y", strtotime($ev['date_created'])); ?></div>
                </div>

                <div class="preview-text" data-full="<?php echo htmlspecialchars($plain); ?>"><?php echo htmlspecialchars($preview); ?></div>
                <?php if (mb_strlen($plain) > 220): ?>
                  <div class="mt-2"><button class="btn btn-sm btn-link see-more">See more</button></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <div>
                    <button class="btn btn-sm btn-outline-primary like-toggle" data-event="<?php echo intval($ev['id']); ?>">
                      <i class="<?php echo $liked ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>"></i>
                      <span class="likes-count"><?php echo $likes; ?></span>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary bookmark-toggle" data-event="<?php echo intval($ev['id']); ?>">
                      <i class="<?php echo $booked ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark'; ?>"></i>
                    </button>
                  </div>
                  <div>
                    <button class="btn btn-sm btn-light comment-open" data-event="<?php echo intval($ev['id']); ?>">Comments</button>
                  </div>
                </div>
              </div>
            </article>
            <?php
            $html .= ob_get_clean();
        }
        $hasMore = count($events) === $limit;
        jsonResponse(true, 'ok', ['html'=>$html, 'hasMore'=>$hasMore]);
    }

    // ---------- like_user ----------
    if ($action === 'like_user') {
        $target_user_id = intval($_POST['user_id'] ?? 0);
        if ($target_user_id <= 0) jsonResponse(false, 'Invalid user ID');
        
        // Check if already liked
        $check_stmt = $conn->prepare("SELECT id FROM user_likes WHERE user_id = ? AND target_user_id = ?");
        $check_stmt->bind_param("ii", $userId, $target_user_id);
        $check_stmt->execute();
        $existing = $check_stmt->get_result()->fetch_assoc();
        
        if ($existing) {
            // Unlike
            $delete_stmt = $conn->prepare("DELETE FROM user_likes WHERE user_id = ? AND target_user_id = ?");
            $delete_stmt->bind_param("ii", $userId, $target_user_id);
            $delete_stmt->execute();
            $liked = false;
        } else {
            // Like
            $insert_stmt = $conn->prepare("INSERT INTO user_likes (user_id, target_user_id, created_at) VALUES (?, ?, NOW())");
            $insert_stmt->bind_param("ii", $userId, $target_user_id);
            $insert_stmt->execute();
            $liked = true;
        }
        
        // Get total likes count
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_likes WHERE target_user_id = ?");
        $count_stmt->bind_param("i", $target_user_id);
        $count_stmt->execute();
        $count = $count_stmt->get_result()->fetch_assoc()['count'];
        
        jsonResponse(true, 'ok', ['liked' => $liked, 'count' => $count]);
    }

    // ---------- add_comment ----------
    if ($action === 'add_comment') {
        $target_user_id = intval($_POST['user_id'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        if ($target_user_id <= 0) jsonResponse(false, 'Invalid user ID');
        if (empty($comment)) jsonResponse(false, 'Comment cannot be empty');
        
        $insert_stmt = $conn->prepare("INSERT INTO user_comments (user_id, target_user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        $insert_stmt->bind_param("iis", $userId, $target_user_id, $comment);
        $insert_stmt->execute();
        
        // Get updated comments
        $comments_stmt = $conn->prepare("SELECT uc.*, a.firstname, a.lastname, a.img FROM user_comments uc JOIN alumnus_bio a ON uc.user_id = a.id WHERE uc.target_user_id = ? ORDER BY uc.created_at DESC LIMIT 10");
        $comments_stmt->bind_param("i", $target_user_id);
        $comments_stmt->execute();
        $comments_result = $comments_stmt->get_result();
        
        $comments = [];
        while ($comment_row = $comments_result->fetch_assoc()) {
            $comments[] = $comment_row;
        }
        
        jsonResponse(true, 'Comment added', ['comments' => $comments]);
    }

    // ---------- get_comments ----------
    if ($action === 'get_comments') {
        $target_user_id = intval($_POST['user_id'] ?? 0);
        if ($target_user_id <= 0) jsonResponse(false, 'Invalid user ID');
        
        $comments_stmt = $conn->prepare("SELECT uc.*, a.firstname, a.lastname, a.img FROM user_comments uc JOIN alumnus_bio a ON uc.user_id = a.id WHERE uc.target_user_id = ? ORDER BY uc.created_at DESC LIMIT 10");
        $comments_stmt->bind_param("i", $target_user_id);
        $comments_stmt->execute();
        $comments_result = $comments_stmt->get_result();
        
        $comments = [];
        while ($comment_row = $comments_result->fetch_assoc()) {
            $comments[] = $comment_row;
        }
        
        jsonResponse(true, 'ok', ['comments' => $comments]);
    }

    // ---------- get_likes ----------
    if ($action === 'get_likes') {
        $target_user_id = intval($_POST['user_id'] ?? 0);
        if ($target_user_id <= 0) jsonResponse(false, 'Invalid user ID');
        
        // Get total likes count
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM user_likes WHERE target_user_id = ?");
        $count_stmt->bind_param("i", $target_user_id);
        $count_stmt->execute();
        $count = $count_stmt->get_result()->fetch_assoc()['count'];
        
        // Check if current user liked
        $check_stmt = $conn->prepare("SELECT id FROM user_likes WHERE user_id = ? AND target_user_id = ?");
        $check_stmt->bind_param("ii", $userId, $target_user_id);
        $check_stmt->execute();
        $liked = $check_stmt->get_result()->fetch_assoc() ? true : false;
        
        jsonResponse(true, 'ok', ['count' => $count, 'liked' => $liked]);
    }

    // ---------- search_alumni (paginated, uses prepared statements + FULLTEXT when term present) ----------
    if ($action === 'search_alumni') {
        $name = trim($_POST['name'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $batch = trim($_POST['batch'] ?? '');
        $page = max(1, intval($_POST['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $params = [];
        $types = '';

        // Get current user's education level to filter results
        $current_user_stmt = $conn->prepare("SELECT strand_id, course_id FROM alumnus_bio WHERE id = ?");
        $current_user_stmt->bind_param("i", $userId);
        $current_user_stmt->execute();
        $current_user_data = $current_user_stmt->get_result()->fetch_assoc();

        $is_current_shs = !empty($current_user_data['strand_id']) && $current_user_data['strand_id'] > 0;

        if ($is_current_shs) {
            // SHS users see only SHS alumni
            $sql = "SELECT a.id, a.firstname, a.lastname, a.img, a.batch, s.name as strand_name FROM alumnus_bio a LEFT JOIN strands s ON a.strand_id = s.id WHERE a.strand_id IS NOT NULL AND a.strand_id > 0";
        } else {
            // College users see only college alumni
            $sql = "SELECT a.id, a.firstname, a.lastname, a.img, a.batch, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.course_id IS NOT NULL AND a.course_id > 0 AND (a.strand_id IS NULL OR a.strand_id = 0)";
        }

        if ($name !== '') {
            // if fulltext available, we could use MATCH...AGAINST; fallback to LIKE
            $like = '%' . $name . '%';
            $sql .= " AND (a.firstname LIKE ? OR a.lastname LIKE ? OR CONCAT(a.firstname, ' ', a.lastname) LIKE ?)";
            $types .= 'sss';
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        if ($course !== '' && !$is_current_shs) {
            $sql .= " AND a.course_id = ?";
            $types .= 'i'; $params[] = intval($course);
        }
        if ($batch !== '') {
            $sql .= " AND a.batch = ?";
            $types .= 's'; $params[] = $batch;
        }
        $sql .= " ORDER BY a.lastname, a.firstname LIMIT ? OFFSET ?";
        $types .= 'ii'; $params[] = $limit; $params[] = $offset;

        $stmt = $conn->prepare($sql);
        if ($types !== '') $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = '';
        $count = 0;
        while ($r = $res->fetch_assoc()) {
            $count++;
            $img_url = '';
            if (!empty($r['img']) && file_exists(UPLOAD_DIR.$r['img'])) {
                $img_url = 'uploads/'.htmlspecialchars($r['img']);
            }
            ob_start(); ?>
            <div class="col-12 mb-3">
              <div class="card p-3">
                <div class="d-flex align-items-center gap-3">
                  <?php if ($img_url): ?>
                    <img src="<?php echo $img_url; ?>" alt="avatar" style="width:80px;height:80px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                  <?php else: ?>
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:20px;flex-shrink:0;">
                      <?php echo strtoupper(substr($r['firstname'], 0, 1) . substr($r['lastname'], 0, 1)); ?>
                    </div>
                  <?php endif; ?>
                  <div class="flex-grow-1">
                    <div style="font-weight:700;color:var(--maroon);font-size:1.1rem;"><?php echo htmlspecialchars($r['firstname'].' '.$r['lastname']); ?></div>
                    <div class="small-muted mb-1"><?php echo htmlspecialchars($r['course_name'] ?? $r['strand_name'] ?? 'N/A'); ?></div>
                    <div class="text-danger small mb-2">Batch <?php echo htmlspecialchars($r['batch']); ?></div>
                    <div class="d-flex gap-2 align-items-center">
                      <a class="btn btn-sm btn-success" href="cv.php?id=<?php echo intval($r['id']); ?>" target="_blank">
                        <i class="fa-solid fa-file-lines me-1"></i>CV
                      </a>
                      <a class="btn btn-sm btn-primary" href="view_profile.php?id=<?php echo intval($r['id']); ?>">
                        <i class="fa-solid fa-user me-1"></i>View Profile
                      </a>
                      <button class="btn btn-sm btn-outline-danger like-btn" data-user-id="<?php echo intval($r['id']); ?>">
                        <i class="fa-regular fa-heart me-1"></i><span class="like-count">0</span>
                      </button>
                      <button class="btn btn-sm btn-outline-primary comment-btn" data-user-id="<?php echo intval($r['id']); ?>">
                        <i class="fa-regular fa-comment me-1"></i><span class="comment-count">0</span>
                      </button>
                    </div>
                  </div>
                </div>
                <!-- Comments Section -->
                <div class="comments-section mt-3" id="comments-<?php echo intval($r['id']); ?>" style="display:none;">
                  <div class="border-top pt-3">
                    <div class="comments-list mb-3"></div>
                    <div class="comment-form">
                      <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm comment-input" placeholder="Write a comment..." data-user-id="<?php echo intval($r['id']); ?>">
                        <button class="btn btn-sm btn-primary send-comment-btn" data-user-id="<?php echo intval($r['id']); ?>">
                          <i class="fa-solid fa-paper-plane"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
            $out .= ob_get_clean();
        }
        if ($count === 0) {
            $out = '<div class="col-12"><div class="alert alert-info mb-0">No alumni found. Try different filters.</div></div>';
        }
        jsonResponse(true, 'ok', ['html'=>$out, 'hasMore'=> $count === $limit]);
    }

    // ---------- fetch_profile (return limited HTML snippet for preview) ----------
    if ($action === 'fetch_profile') {
        $pid = intval($_POST['id'] ?? 0);
        if ($pid <= 0) jsonResponse(false, 'Invalid id');
        $stmt = $conn->prepare("SELECT a.*, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ? LIMIT 1");
        $stmt->bind_param("i", $pid); $stmt->execute(); $p = $stmt->get_result()->fetch_assoc();
        if (!$p) jsonResponse(false, 'Profile not found');
        ob_start();
        ?>
        <div class="p-3">
          <div class="d-flex gap-3 align-items-center">
            <?php 
            $img_url = '';
            if (!empty($p['img']) && file_exists(UPLOAD_DIR.$p['img'])) {
                $img_url = 'uploads/'.htmlspecialchars($p['img']);
            }
            ?>
            <?php if ($img_url): ?>
                <img src="<?php echo $img_url; ?>" alt="avatar" style="width:64px;height:64px;border-radius:50%;object-fit:cover;">
            <?php else: ?>
                <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:18px;">
                  <?php echo strtoupper(substr($p['firstname'], 0, 1) . substr($p['lastname'], 0, 1)); ?>
                </div>
            <?php endif; ?>
            <div style="flex:1;">
              <div style="font-weight:800;font-size:1.05rem"><?php echo htmlspecialchars($p['firstname'].' '.$p['lastname']); ?></div>
              <div class="small-muted"><?php echo htmlspecialchars(($p['course_name'] ?? 'N/A').' • Batch '.($p['batch'] ?? 'N/A')); ?></div>
              <div class="mt-2 small-muted">Public profile — limited information to protect privacy.</div>
            </div>
          </div>
          <div class="mt-3">
            <a href="view_profile.php?id=<?php echo intval($p['id']); ?>" class="btn btn-primary btn-sm" target="_blank">Open Full Profile</a>
            <?php if (isset($_SESSION['login_id']) && intval($_SESSION['login_id']) === intval($p['id'])): ?>
              <a href="my_profile.php" class="btn btn-outline-primary btn-sm ms-2">Edit Profile</a>
            <?php endif; ?>
          </div>
        </div>
        <?php
        $html = ob_get_clean();
        jsonResponse(true, 'ok', ['html'=>$html]);
    }

    // ---------- fetch_jobs ----------
    if ($action === 'fetch_jobs') {
        // Clear any output buffers to prevent HTML interference
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $page = max(1, intval($_POST['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        try {
            // First ensure careers table exists
            $conn->query("CREATE TABLE IF NOT EXISTS careers (
                id INT(30) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company VARCHAR(250) NOT NULL,
                location TEXT NOT NULL,
                job_title VARCHAR(250) NOT NULL,
                description TEXT NOT NULL,
                user_id INT(30) NOT NULL,
                date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Also ensure users table exists for the join
            $conn->query("CREATE TABLE IF NOT EXISTS users (
                id INT(30) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(250) NOT NULL,
                username VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                type TINYINT(1) NOT NULL DEFAULT 2
            )");
            
            // Try to fetch jobs with LEFT JOIN to handle missing users
            $stmt = $conn->prepare("SELECT c.*, COALESCE(u.name, 'Admin') as posted_by FROM careers c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.id DESC LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $res = $stmt->get_result();
            
            $jobs = [];
            while ($r = $res->fetch_assoc()) {
                $jobs[] = $r;
            }

            $html = '';
            foreach ($jobs as $job) {
                $description = html_entity_decode($job['description'] ?? '');
                $plain = trim(strip_tags($description));
                $preview = mb_strlen($plain) > 150 ? mb_substr($plain, 0, 150) . '...' : $plain;
                $date = date('M j, Y', strtotime($job['date_created']));

                $html .= '<div class="job-card">';
                $html .= '<div class="job-header">';
                $html .= '<h5 class="job-title">' . htmlspecialchars($job['job_title']) . '</h5>';
                $html .= '<div class="job-company">' . htmlspecialchars($job['company']) . '</div>';
                $html .= '</div>';

                $html .= '<div class="job-meta">';
                if (!empty($job['location'])) {
                    $html .= '<div class="job-location">';
                    $html .= '<i class="fa-solid fa-location-dot"></i> ' . htmlspecialchars($job['location']);
                    $html .= '</div>';
                }
                $html .= '<div class="job-posted-by">';
                $html .= '<i class="fa-solid fa-user"></i> ' . htmlspecialchars($job['posted_by']);
                $html .= '</div>';
                $html .= '</div>';

                if (!empty($preview)) {
                    $html .= '<div class="job-description">' . htmlspecialchars($preview) . '</div>';
                }

                $html .= '<div class="job-actions">';
                $html .= '<div class="job-date">' . $date . '</div>';
                $html .= '<button class="btn-job view-job-btn" data-job-id="' . intval($job['id']) . '">';
                $html .= '<i class="fa-solid fa-eye"></i> View Details';
                $html .= '</button>';
                $html .= '</div>';
                $html .= '</div>';
            }

            if (empty($jobs)) {
                $html = '<div class="alert alert-info text-center" id="noJobsAlert">';
                $html .= '<i class="fa-solid fa-briefcase fa-2x mb-2 text-muted"></i><br>';
                $html .= '<strong>No job postings available</strong><br>';
                $html .= '<small class="text-muted">Check back later for new opportunities!</small>';
                $html .= '</div>';
            }
            
            $hasMore = count($jobs) === $limit;
            jsonResponse(true, 'Jobs loaded successfully', ['html' => $html, 'hasMore' => $hasMore]);
            
        } catch (Exception $e) {
            jsonResponse(false, 'Error loading jobs: ' . $e->getMessage());
        }
    }

    // ---------- get_job_details ----------
    if ($action === 'get_job_details') {
        // Clear any output buffers to prevent HTML interference
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $job_id = intval($_POST['job_id'] ?? 0);
        if ($job_id <= 0) jsonResponse(false, 'Invalid job ID');
        
        try {
            $stmt = $conn->prepare("SELECT c.*, COALESCE(u.name, 'Admin') as posted_by FROM careers c LEFT JOIN users u ON u.id = c.user_id WHERE c.id = ?");
            $stmt->bind_param("i", $job_id);
            $stmt->execute();
            $job = $stmt->get_result()->fetch_assoc();
            
            if (!$job) jsonResponse(false, 'Job not found');
            
            $description = html_entity_decode($job['description'] ?? '');
            
            ob_start();
            ?>
            <div class="p-3">
                <h4 class="text-primary mb-1"><?php echo htmlspecialchars($job['job_title']); ?></h4>
                <h5 class="text-muted mb-3"><?php echo htmlspecialchars($job['company']); ?></h5>
                
                <?php if (!empty($job['location'])): ?>
                    <p class="mb-3"><i class="fa-solid fa-location-dot me-2"></i><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                <?php endif; ?>
                
                <div class="mb-3">
                    <strong>Job Description:</strong>
                    <div class="mt-2"><?php echo nl2br(htmlspecialchars($description)); ?></div>
                </div>
                
                <div class="text-muted small">
                    <i class="fa-solid fa-user me-1"></i>Posted by: <?php echo htmlspecialchars($job['posted_by']); ?>
                    <br>
                    <i class="fa-solid fa-calendar me-1"></i>Date Posted: <?php echo date('F j, Y', strtotime($job['date_created'])); ?>
                </div>
            </div>
            <?php
            $html = ob_get_clean();
            jsonResponse(true, 'Job details loaded', ['html' => $html]);
            
        } catch (Exception $e) {
            jsonResponse(false, 'Error loading job details: ' . $e->getMessage());
        }
    }

    // ---------- create_post ----------
    if ($action === 'create_post') {
        try {
            $content = trim($_POST['content'] ?? '');
            if (empty($content)) {
                jsonResponse(false, 'Please enter some content');
            }
            
            // Check if table exists
            $table_check = $conn->query("SHOW TABLES LIKE 'user_posts'");
            
            if ($table_check->num_rows == 0) {
                // Create user_posts table without foreign key constraints
                $create_table_sql = "CREATE TABLE user_posts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    content TEXT NOT NULL,
                    image VARCHAR(255) DEFAULT NULL,
                    media_type ENUM('image', 'video') DEFAULT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                
                if (!$conn->query($create_table_sql)) {
                    jsonResponse(false, 'Failed to create table: ' . $conn->error);
                }
            } else {
                // Check if table has foreign key constraints and remove them
                $fk_check = $conn->query("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'user_posts' 
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                ");
                
                if ($fk_check && $fk_check->num_rows > 0) {
                    while ($fk = $fk_check->fetch_assoc()) {
                        $conn->query("ALTER TABLE user_posts DROP FOREIGN KEY " . $fk['CONSTRAINT_NAME']);
                    }
                }
            }
            
            // Handle media upload (image or video) if present
            $image_filename = null;
            $media_type = null;
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/posts/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Check file size (25MB limit)
                $max_size = 25 * 1024 * 1024; // 25MB in bytes
                if ($_FILES['image']['size'] > $max_size) {
                    jsonResponse(false, 'File size exceeds 25MB limit. Please choose a smaller file.');
                }
                
                $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_images = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                $allowed_videos = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
                $allowed_ext = array_merge($allowed_images, $allowed_videos);
                
                if (in_array($file_ext, $allowed_ext)) {
                    // Determine media type
                    if (in_array($file_ext, $allowed_images)) {
                        $media_type = 'image';
                    } else {
                        $media_type = 'video';
                    }
                    
                    $image_filename = 'post_' . $userId . '_' . time() . '_' . uniqid() . '.' . $file_ext;
                    $target_path = $upload_dir . $image_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                        // Media uploaded successfully
                    } else {
                        jsonResponse(false, 'Failed to upload file. Please try again.');
                    }
                } else {
                    jsonResponse(false, 'Invalid file type. Allowed: Images (jpg, png, gif, webp) and Videos (mp4, webm, mov)');
                }
            }
            
            // Insert post with media type
            $stmt = $conn->prepare("INSERT INTO user_posts (user_id, content, image, media_type) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $userId, $content, $image_filename, $media_type);
            
            if ($stmt->execute()) {
                $post_id = $conn->insert_id;
                jsonResponse(true, 'Post created successfully!', ['post_id' => $post_id, 'media_type' => $media_type]);
            } else {
                jsonResponse(false, 'Failed to create post: ' . $conn->error);
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- delete_post ----------
    if ($action === 'delete_post') {
        try {
            $post_id = intval($_POST['post_id'] ?? 0);
            if ($post_id <= 0) {
                jsonResponse(false, 'Invalid post ID');
            }
            
            // Get post image to delete file
            $stmt = $conn->prepare("SELECT image FROM user_posts WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $post = $result->fetch_assoc();
            
            if (!$post) {
                jsonResponse(false, 'Post not found or you don\'t have permission to delete it');
            }
            
            // Delete the post
            $stmt = $conn->prepare("DELETE FROM user_posts WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $userId);
            
            if ($stmt->execute()) {
                // Delete image file if exists
                if (!empty($post['image'])) {
                    $image_path = 'uploads/posts/' . $post['image'];
                    if (file_exists($image_path)) {
                        @unlink($image_path);
                    }
                }
                jsonResponse(true, 'Post deleted successfully!');
            } else {
                jsonResponse(false, 'Failed to delete post');
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- edit_post ----------
    if ($action === 'edit_post') {
        try {
            $post_id = intval($_POST['post_id'] ?? 0);
            $content = trim($_POST['content'] ?? '');
            
            if ($post_id <= 0) {
                jsonResponse(false, 'Invalid post ID');
            }
            if (empty($content)) {
                jsonResponse(false, 'Please enter some content');
            }
            
            // Get current post
            $stmt = $conn->prepare("SELECT image, media_type FROM user_posts WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_post = $result->fetch_assoc();
            
            if (!$current_post) {
                jsonResponse(false, 'Post not found or you don\'t have permission to edit it');
            }
            
            $image_filename = $current_post['image'];
            $media_type = $current_post['media_type'];
            
            // Handle new media upload (image or video)
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/posts/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Check file size (25MB limit)
                $max_size = 25 * 1024 * 1024;
                if ($_FILES['image']['size'] > $max_size) {
                    jsonResponse(false, 'File size exceeds 25MB limit. Please choose a smaller file.');
                }
                
                $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed_images = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
                $allowed_videos = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
                $allowed_ext = array_merge($allowed_images, $allowed_videos);
                
                if (in_array($file_ext, $allowed_ext)) {
                    // Delete old media if exists
                    if (!empty($image_filename)) {
                        $old_path = $upload_dir . $image_filename;
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                    
                    // Determine media type
                    if (in_array($file_ext, $allowed_images)) {
                        $media_type = 'image';
                    } else {
                        $media_type = 'video';
                    }
                    
                    $image_filename = 'post_' . $userId . '_' . time() . '_' . uniqid() . '.' . $file_ext;
                    $target_path = $upload_dir . $image_filename;
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                        $image_filename = $current_post['image'];
                        $media_type = $current_post['media_type'];
                    }
                } else {
                    jsonResponse(false, 'Invalid file type. Allowed: Images (jpg, png, gif, webp) and Videos (mp4, webm, mov)');
                }
            }
            
            // Check if user wants to remove media
            if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
                if (!empty($image_filename)) {
                    $old_path = 'uploads/posts/' . $image_filename;
                    if (file_exists($old_path)) {
                        @unlink($old_path);
                    }
                }
                $image_filename = null;
                $media_type = null;
            }
            
            // Update post with media type
            $stmt = $conn->prepare("UPDATE user_posts SET content = ?, image = ?, media_type = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sssii", $content, $image_filename, $media_type, $post_id, $userId);
            
            if ($stmt->execute()) {
                jsonResponse(true, 'Post updated successfully!');
            } else {
                jsonResponse(false, 'Failed to update post: ' . $conn->error);
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- fetch_user_posts ----------
    if ($action === 'fetch_user_posts') {
        try {
            $page = max(1, intval($_POST['page'] ?? 1));
            $limit = 10;
            $offset = ($page - 1) * $limit;
            
            $stmt = $conn->prepare("
                SELECT up.*, ab.firstname, ab.lastname, ab.img,
                       (SELECT COUNT(*) FROM post_likes WHERE post_id = up.id) as like_count,
                       (SELECT COUNT(*) FROM post_likes WHERE post_id = up.id AND user_id = ?) as user_liked,
                       (SELECT COUNT(*) FROM post_comments WHERE post_id = up.id) as comment_count
                FROM user_posts up 
                LEFT JOIN alumnus_bio ab ON up.user_id = ab.id 
                ORDER BY up.created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("iii", $userId, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $posts = [];
            while ($row = $result->fetch_assoc()) {
                // Format the post data
                $row['avatar_url'] = avatar_url($row['img']);
                $row['full_name'] = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                $row['time_ago'] = time_ago($row['created_at']);
                $row['is_owner'] = ($row['user_id'] == $userId);
                
                // Add media URL and type if exists
                if (!empty($row['image'])) {
                    $row['image_url'] = 'uploads/posts/' . $row['image'];
                    $row['media_type'] = $row['media_type'] ?? 'image'; // Default to image for old posts
                } else {
                    $row['image_url'] = null;
                    $row['media_type'] = null;
                }
                
                $posts[] = $row;
            }
            
            // Get total count for pagination
            $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM user_posts");
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $total = $count_result->fetch_assoc()['total'];
            
            jsonResponse(true, 'Posts fetched', [
                'posts' => $posts,
                'total' => $total,
                'page' => $page,
                'has_more' => ($offset + $limit) < $total
            ]);
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- toggle_post_like ----------
    if ($action === 'toggle_post_like') {
        try {
            $post_id = intval($_POST['post_id'] ?? 0);
            if ($post_id <= 0) {
                jsonResponse(false, 'Invalid post ID');
            }
            
            // Create table if not exists
            $conn->query("CREATE TABLE IF NOT EXISTS post_likes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (post_id, user_id),
                INDEX idx_post_id (post_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            
            // Check if already liked
            $stmt = $conn->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $post_id, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Unlike
                $stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
                $stmt->bind_param("ii", $post_id, $userId);
                $stmt->execute();
                $action_type = 'unliked';
            } else {
                // Like
                $stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $post_id, $userId);
                $stmt->execute();
                $action_type = 'liked';
            }
            
            // Get new like count
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $count_result = $stmt->get_result();
            $like_count = $count_result->fetch_assoc()['count'];
            
            jsonResponse(true, 'Post ' . $action_type, ['like_count' => $like_count, 'liked' => ($action_type === 'liked')]);
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- add_post_comment ----------
    if ($action === 'add_post_comment') {
        try {
            $post_id = intval($_POST['post_id'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');
            
            if ($post_id <= 0) {
                jsonResponse(false, 'Invalid post ID');
            }
            if (empty($comment)) {
                jsonResponse(false, 'Comment cannot be empty');
            }
            
            // Create table if not exists
            $conn->query("CREATE TABLE IF NOT EXISTS post_comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                comment TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_post_id (post_id),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            
            // Insert comment
            $stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $post_id, $userId, $comment);
            
            if ($stmt->execute()) {
                $comment_id = $conn->insert_id;
                
                // Get comment with user info
                $stmt = $conn->prepare("
                    SELECT pc.*, ab.firstname, ab.lastname, ab.img 
                    FROM post_comments pc 
                    LEFT JOIN alumnus_bio ab ON pc.user_id = ab.id 
                    WHERE pc.id = ?
                ");
                $stmt->bind_param("i", $comment_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $comment_data = $result->fetch_assoc();
                
                $comment_data['avatar_url'] = avatar_url($comment_data['img']);
                $comment_data['full_name'] = htmlspecialchars($comment_data['firstname'] . ' ' . $comment_data['lastname']);
                $comment_data['time_ago'] = time_ago($comment_data['created_at']);
                $comment_data['is_owner'] = ($comment_data['user_id'] == $userId);
                
                jsonResponse(true, 'Comment added', ['comment' => $comment_data]);
            } else {
                jsonResponse(false, 'Failed to add comment');
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- fetch_post_comments ----------
    if ($action === 'fetch_post_comments') {
        try {
            $post_id = intval($_POST['post_id'] ?? 0);
            if ($post_id <= 0) {
                jsonResponse(false, 'Invalid post ID');
            }
            
            $stmt = $conn->prepare("
                SELECT pc.*, ab.firstname, ab.lastname, ab.img 
                FROM post_comments pc 
                LEFT JOIN alumnus_bio ab ON pc.user_id = ab.id 
                WHERE pc.post_id = ? 
                ORDER BY pc.created_at ASC
            ");
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $comments = [];
            while ($row = $result->fetch_assoc()) {
                $row['avatar_url'] = avatar_url($row['img']);
                $row['full_name'] = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                $row['time_ago'] = time_ago($row['created_at']);
                $row['is_owner'] = ($row['user_id'] == $userId);
                $comments[] = $row;
            }
            
            jsonResponse(true, 'Comments fetched', ['comments' => $comments]);
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    
    // ---------- delete_post_comment ----------
    if ($action === 'delete_post_comment') {
        try {
            $comment_id = intval($_POST['comment_id'] ?? 0);
            if ($comment_id <= 0) {
                jsonResponse(false, 'Invalid comment ID');
            }
            
            // Delete comment (only if owner)
            $stmt = $conn->prepare("DELETE FROM post_comments WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $comment_id, $userId);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                jsonResponse(true, 'Comment deleted');
            } else {
                jsonResponse(false, 'Failed to delete comment or you don\'t have permission');
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    // ---------- update_profile ----------
    if ($action === 'update_profile') {
        if (!hash_equals($csrf_token, $_POST['csrf_token'] ?? '')) jsonResponse(false, 'Invalid CSRF');
        
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $middlename = trim($_POST['middlename'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $gender = $_POST['gender'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $employment_status = $_POST['employment_status'] ?? '';
        $connected_to = trim($_POST['connected_to'] ?? '');
        $company_address = trim($_POST['company_address'] ?? '');
        $company_email = trim($_POST['company_email'] ?? '');
        
        if (empty($firstname) || empty($lastname) || empty($email)) {
            jsonResponse(false, 'Required fields cannot be empty');
        }
        
        // Handle image upload
        $img_filename = null;
        if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = UPLOAD_DIR;
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $file_tmp = $_FILES['profileImage']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $img_filename = time() . '_' . safe_filename($_FILES['profileImage']['name']);
                $img_path = $upload_dir . $img_filename;
                
                if (move_uploaded_file($file_tmp, $img_path)) {
                    // Optimize image
                    optimize_image($img_path, $img_path, 400, 400, 85);
                } else {
                    $img_filename = null;
                }
            }
        }
        
        // Update profile
        if ($img_filename) {
            $stmt = $conn->prepare("UPDATE alumnus_bio SET firstname=?, lastname=?, middlename=?, email=?, contact_no=?, gender=?, birthdate=?, address=?, employment_status=?, connected_to=?, company_address=?, company_email=?, img=? WHERE id=?");
            $stmt->bind_param("sssssssssssssi", $firstname, $lastname, $middlename, $email, $contact_no, $gender, $birthdate, $address, $employment_status, $connected_to, $company_address, $company_email, $img_filename, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE alumnus_bio SET firstname=?, lastname=?, middlename=?, email=?, contact_no=?, gender=?, birthdate=?, address=?, employment_status=?, connected_to=?, company_address=?, company_email=? WHERE id=?");
            $stmt->bind_param("ssssssssssssi", $firstname, $lastname, $middlename, $email, $contact_no, $gender, $birthdate, $address, $employment_status, $connected_to, $company_address, $company_email, $userId);
        }
        
        if ($stmt->execute()) {
            // Get updated user data
            $stmt2 = $conn->prepare("SELECT * FROM alumnus_bio WHERE id = ?");
            $stmt2->bind_param("i", $userId);
            $stmt2->execute();
            $updated_user = $stmt2->get_result()->fetch_assoc();
            
            jsonResponse(true, 'Profile updated successfully', ['user' => $updated_user]);
        } else {
            jsonResponse(false, 'Failed to update profile');
        }
    }

    // Unknown action
    jsonResponse(false, 'Unknown action');
}
// End AJAX endpoints
// ===========================================

// === Page load: fetch initial DB data ===
$stmt = $conn->prepare("SELECT a.*, c.course as course_name, s.name as strand_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id LEFT JOIN strands s ON a.strand_id = s.id WHERE a.id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { echo "User not found"; exit; }

// Determine if current user is SHS or College
$is_shs_user = !empty($user['strand_id']) && $user['strand_id'] > 0;
$is_college_user = !empty($user['course_id']) && $user['course_id'] > 0 && (empty($user['strand_id']) || $user['strand_id'] == 0);

// classmates - filter by education level
$classmates = [];
if ($is_shs_user) {
    // SHS users see only SHS classmates (same strand and batch)
    $stmt = $conn->prepare("SELECT a.id, a.firstname, a.lastname, a.img, a.batch, s.name as strand_name FROM alumnus_bio a LEFT JOIN strands s ON a.strand_id = s.id WHERE a.strand_id = ? AND a.batch = ? AND a.id != ? ORDER BY a.lastname, a.firstname");
    $stmt->bind_param("isi", $user['strand_id'], $user['batch'], $userId);
} else {
    // College users see only college classmates (same course and batch)
    $stmt = $conn->prepare("SELECT a.id, a.firstname, a.lastname, a.img, a.batch, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.course_id = ? AND a.batch = ? AND a.id != ? AND (a.strand_id IS NULL OR a.strand_id = 0) ORDER BY a.lastname, a.firstname");
    $stmt->bind_param("isi", $user['course_id'], $user['batch'], $userId);
}
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) $classmates[] = $r;

// batch years - filter by education level
$batch_years = [];
if ($is_shs_user) {
    // SHS users see only SHS batch years
    $res = $conn->query("SELECT DISTINCT batch FROM alumnus_bio WHERE strand_id IS NOT NULL AND strand_id > 0 ORDER BY batch DESC");
} else {
    // College users see only college batch years
    $res = $conn->query("SELECT DISTINCT batch FROM alumnus_bio WHERE course_id IS NOT NULL AND course_id > 0 AND (strand_id IS NULL OR strand_id = 0) ORDER BY batch DESC");
}
while ($row = $res->fetch_assoc()) $batch_years[] = $row['batch'];

// batchmates - filter by education level
$selected_batch = isset($_GET['batch']) ? $_GET['batch'] : $user['batch'];
$batchmates = [];
if ($is_shs_user) {
    // SHS users see only SHS batchmates
    $stmt = $conn->prepare("SELECT a.id, a.firstname, a.lastname, a.img, s.name as strand_name, a.batch FROM alumnus_bio a LEFT JOIN strands s ON a.strand_id = s.id WHERE a.batch = ? AND a.id != ? AND a.strand_id IS NOT NULL AND a.strand_id > 0 ORDER BY a.lastname, a.firstname");
    $stmt->bind_param("si", $selected_batch, $userId);
} else {
    // College users see only college batchmates
    $stmt = $conn->prepare("SELECT a.id, a.firstname, a.lastname, a.img, c.course as course_name, a.batch FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE a.batch = ? AND a.id != ? AND a.course_id IS NOT NULL AND a.course_id > 0 AND (a.strand_id IS NULL OR a.strand_id = 0) ORDER BY a.lastname, a.firstname");
    $stmt->bind_param("si", $selected_batch, $userId);
}
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) $batchmates[] = $r;

// initial events (page 1)
$limit_init = 6;
$stmt = $conn->prepare("SELECT * FROM events ORDER BY schedule DESC LIMIT ? OFFSET 0");
$stmt->bind_param("i", $limit_init);
$stmt->execute();
$res = $stmt->get_result();
$initial_events = [];
while ($r = $res->fetch_assoc()) $initial_events[] = $r;

// courses for search
$courses = $conn->query("SELECT id, course FROM courses ORDER BY course ASC");

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>AlumniGram — <?php echo htmlspecialchars($user['firstname']); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Favicon / mobile icon -->
  <link rel="icon" type="image/png" sizes="32x32" href="assets/img/icon.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icon.png">
  <style>
  :root{
    --maroon: #800000;
    --maroon-r: 128,0,0; /* r,g,b for rgba variants */
    --white: #ffffff;
    --bg: var(--white);
    --card: var(--white);
    --text: var(--maroon);
    --muted: rgba(var(--maroon-r),0.65);
    --glass: rgba(255,255,255,0.95);
  }
  body{ background: var(--bg); font-family:Inter, "Segoe UI", Arial, sans-serif; color:var(--text); }
  .app-nav{ background:var(--card); border-bottom:1px solid rgba(var(--maroon-r),0.06); position:sticky; top:0; z-index:999; backdrop-filter: blur(4px); }
  .brand{ font-weight:800; font-size:1.1rem; display:flex;align-items:center;gap:10px; }
  .container-main{ max-width:1100px; margin:18px auto; padding:0 16px 96px; }
  .profile-hero{ display:flex; gap:18px; align-items:center; margin-bottom:12px; }
  .avatar{ width:110px; height:110px; border-radius:16px; object-fit:cover; border:3px solid var(--white); box-shadow:0 10px 30px rgba(0,0,0,0.08); }
  .meta h1{ margin:0; font-size:1.35rem; color:var(--maroon); }
  .sub{ color:var(--muted); margin-top:6px; }
  .bio-card{ background:var(--card); padding:14px; border-radius:12px; box-shadow:0 8px 26px rgba(0,0,0,0.06); }
  .nav-tabs .nav-link{ color:var(--muted); font-weight:600; }
  .nav-tabs .nav-link.active{ color:var(--maroon); border-bottom-color:var(--maroon); }

  /* single column layout for events */
  .masonry{ display:flex; flex-direction:column; gap:18px; }
  .card-tile{ background:var(--card); border-radius:14px; overflow:hidden; border:1px solid rgba(var(--maroon-r),0.06); box-shadow:0 8px 30px rgba(0,0,0,0.06); transition:transform .18s ease, box-shadow .18s ease; display:flex; flex-direction:column; }
  .card-tile:hover{ transform:translateY(-6px); box-shadow:0 18px 48px rgba(0,0,0,0.08); }
  .banner{ width:100%; height:320px; object-fit:cover; display:block; background:linear-gradient(180deg, rgba(var(--maroon-r),0.03), rgba(var(--maroon-r),0.01)); }
  .tile-body{ padding:12px 14px; flex:1 1 auto; display:flex; flex-direction:column; }
  .card-title{ font-weight:700; color:var(--maroon); margin-bottom:4px; font-size:1.02rem; }
  .tile-meta{ font-size:.88rem; color:var(--muted); margin-bottom:8px; }
  .preview-text{ color:var(--maroon); line-height:1.5; flex:1 1 auto; }
  .see-more{ color:var(--muted); font-weight:700; cursor:pointer; user-select:none; border:none; background:transparent; padding:0; }
  @media (max-width:992px){ .banner{ height:220px; } }
  @media (max-width:576px){ .profile-hero{ flex-direction:column; align-items:flex-start; } .avatar{ width:84px; height:84px; } .banner{ height:200px; } }

  .search-filters{ display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px; align-items:center; }
  .filter-input{ min-width:160px; flex:1 1 200px; }
  .result-card{ width:calc(33.333% - 8px); }
  .loading { text-align:center; padding:18px; }
  .icon-btn { border:1px solid rgba(var(--maroon-r),0.08); background:var(--white); padding:6px 8px; border-radius:10px; cursor:pointer;}
  .small-muted{ color:var(--muted); font-size:.92rem; }
  
  /* Hover Shadow Effect */
  .hover-shadow {
    transition: all 0.3s ease;
  }
  
  .hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.12) !important;
  }
  
  /* Responsive Grid Improvements */
  @media (max-width: 768px) {
    .col-lg-4, .col-md-6 {
      padding-left: 8px;
      padding-right: 8px;
    }
    
    .bio-card {
      padding: 12px;
    }
    
    .search-filters {
      flex-direction: column;
    }
    
    .filter-input {
      width: 100%;
    }
  }
  
  /* Alumni Card Animations */
  .classmate-card, .batchmate-card {
    animation: fadeInUp 0.4s ease;
  }
  
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* Search Result Card */
  .search-result-card {
    background: var(--card);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    border: 1px solid rgba(var(--maroon-r),0.06);
    transition: all 0.3s ease;
  }
  
  .search-result-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  }

  /* Jobs Grid Styles */
  .jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  .job-card {
    background: var(--card);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: 1px solid rgba(var(--maroon-r),0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .job-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }

  .job-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--maroon), rgba(var(--maroon-r),0.6));
  }

  .job-header {
    margin-bottom: 1rem;
  }

  .job-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--maroon);
    margin: 0 0 0.5rem 0;
    line-height: 1.3;
  }

  .job-company {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--muted);
    margin: 0 0 0.25rem 0;
  }

  .job-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .job-location {
    font-size: 0.85rem;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .job-posted-by {
    font-size: 0.8rem;
    color: rgba(var(--maroon-r),0.6);
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }

  .job-description {
    color: var(--maroon);
    line-height: 1.5;
    margin-bottom: 1.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .job-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
  }

  .job-date {
    font-size: 0.75rem;
    color: rgba(var(--maroon-r),0.5);
  }

  .btn-job {
    background: var(--maroon);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btn-job:hover {
    background: rgba(var(--maroon-r),0.9);
    color: white;
    transform: translateY(-1px);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .jobs-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }

    .job-card {
      padding: 1rem;
    }

    .job-actions {
      flex-direction: column;
      gap: 0.75rem;
      align-items: stretch;
    }

    .job-actions .btn-job {
      justify-content: center;
    }
  }

  @media (max-width: 480px) {
    .job-meta {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.25rem;
    }
  }

  /* action row */
  .action-row{ display:flex; gap:8px; align-items:center; }
  .action-row .btn{ border-radius:10px; }

  /* maroon button helpers */
  /* maroon / white buttons */
  .btn-maroon{ background:var(--maroon); color:var(--white); border-color:var(--maroon); }
  .btn-outline-maroon{ color:var(--maroon); background:transparent; border:1px solid var(--maroon); }
  .text-maroon{ color:var(--maroon) !important; }

  /* override some Bootstrap primary variants to use maroon */
  .btn-primary{ background:var(--maroon) !important; border-color:var(--maroon) !important; color:var(--white) !important; }
  .btn-outline-primary{ color:var(--maroon); border-color:var(--maroon); }

  /* logo */
  .brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
  .brand .logo-img{ width:48px; height:48px; object-fit:contain; border-radius:8px; }
  .brand .brand-text{ font-weight:800; color:var(--maroon); font-size:1.05rem; }
  .logo-img{ width:40px; height:40px; object-fit:contain; }
  /* card positioning for overlay heart */
  .card-tile{ position:relative; }
  .heart-overlay{ position:absolute; left:50%; top:40%; transform:translate(-50%,-50%); font-size:84px; color:rgba(255,255,255,0.95); text-shadow:0 6px 18px rgba(0,0,0,0.35); pointer-events:none; opacity:0; animation: pop .9s forwards; }
  @keyframes pop{ 0%{ transform:translate(-50%,-50%) scale(0.2); opacity:0 } 50%{ transform:translate(-50%,-50%) scale(1.08); opacity:1 } 100%{ transform:translate(-50%,-50%) scale(1); opacity:0 } }

  /* bottom mobile nav */
  .bottom-nav{ display:none; position:fixed; bottom:12px; left:50%; transform:translateX(-50%); background:var(--card); border-radius:999px; box-shadow:0 10px 30px rgba(0,0,0,0.08); padding:6px; z-index:1200; }
  .bottom-nav .nav-item{ padding:8px 12px; color:var(--muted); }
  @media(max-width:768px){ .bottom-nav{ display:flex; gap:6px; } .container-main{ padding-bottom:140px; } }
  </style>
</head>
<body>
  <nav class="app-nav py-2">
    <div class="container-fluid px-3">
      <div class="d-flex justify-content-between align-items-center">
        <a class="brand text-decoration-none" href="home.php">
          <img src="assets/img/logo.png" class="logo-img" alt="logo">
          <span class="brand-text">AlumniGram</span>
        </a>
        <div class="d-flex gap-3 align-items-center">
          <a href="home.php" class="text-muted"><i class="fa-solid fa-house"></i></a>
          <a href="#" class="text-muted"><i class="fa-solid fa-magnifying-glass"></i></a>
          <a href="#" id="previewMe" class="text-muted" title="Preview my profile"><i class="fa-solid fa-user"></i></a>
          <?php if ($IS_ADMIN): ?>
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#createEventModal"><i class="fa-solid fa-plus"></i> Create Event</button>
          <?php endif; ?>
          <a href="logout.php" class="text-muted"><i class="fa-solid fa-right-from-bracket"></i></a>
          <button id="themeToggle" class="btn btn-sm btn-outline-maroon ms-2 d-flex align-items-center" aria-pressed="false" title="Toggle theme">
            <i id="themeIcon" class="fa-solid fa-moon"></i>
          </button>
        </div>
      </div>
    </div>
  </nav>

  <main class="container-main">
    <section class="profile-hero">
      <div class="bio-card d-flex align-items-center gap-3 w-100">
        <a href="my_profile.php" title="My Profile">
          <?php $user_avatar = avatar_url($user['img']); ?>
          <?php if ($user_avatar): ?>
            <img src="<?php echo $user_avatar; ?>" alt="avatar" class="avatar" id="profileAvatar">
          <?php else: ?>
            <div class="avatar-placeholder" id="profileAvatar" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:24px;">
              <?php echo strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)); ?>
            </div>
          <?php endif; ?>
        </a>
        <div class="flex-fill d-flex justify-content-between align-items-center">
          <div>
            <h1 class="mb-0"><a href="my_profile.php" class="text-decoration-none" style="color:inherit"><?php echo htmlspecialchars($user['firstname'].' '.$user['lastname']); ?></a></h1>
            <div class="sub small-muted"><?php echo htmlspecialchars(($user['course_name'] ?? 'N/A').' • Batch '.($user['batch'] ?? 'N/A')); ?></div>
          </div>
          <div class="action-row">
            <a href="cv.php" class="btn btn-sm btn-success me-1" target="_blank"><i class="fa-solid fa-file-lines"></i> My CV</a>
            <a href="my_profile.php" class="btn btn-sm btn-primary me-1"><i class="fa-solid fa-user"></i> View</a>
            <button id="editProfileBtn" class="btn btn-sm btn-outline-primary me-1" type="button"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
            <button id="changePasswordBtn" class="btn btn-sm btn-outline-danger" type="button"><i class="fa-solid fa-key"></i> Change Password</button>
          </div>
        </div>
      </div>
    </section>

    <ul class="nav nav-tabs mt-4">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#timeline">Timeline</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#jobs">Jobs</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#myinfo">My Info</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#classmates">Classmates</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#batchmates">Batchmates</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#search">Search Alumni</button></li>
    </ul>

    <div class="tab-content mt-3">
      <!-- TIMELINE -->
      <div class="tab-pane fade show active" id="timeline">
        <!-- What's on your mind section -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
              <?php 
              $user_img_url = '';
              if (!empty($user['img']) && file_exists(UPLOAD_DIR.$user['img'])) {
                  $user_img_url = 'uploads/'.htmlspecialchars($user['img']);
              }
              ?>
              <?php if ($user_img_url): ?>
                <img src="<?php echo $user_img_url; ?>" alt="avatar" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
              <?php else: ?>
                <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:16px;">
                  <?php echo strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)); ?>
                </div>
              <?php endif; ?>
              <div class="flex-grow-1">
                <button class="btn btn-light w-100 text-start" id="openPostModal" style="border-radius:25px; padding:12px 20px; background:#f8f9fa; border:1px solid #dee2e6;">
                  What's on your mind, <?php echo htmlspecialchars($user['firstname']); ?>?
                </button>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-primary btn-sm" id="postTextBtn">
                <i class="fa-solid fa-pen me-1"></i>Create Post
              </button>
            </div>
          </div>
        </div>
        
        <div id="timelineContainer">
          <div class="masonry" id="masonryFeed">
            <?php
            // Fetch user posts first
            $userPostsQuery = "SELECT up.*, ab.firstname, ab.lastname, ab.img 
                              FROM user_posts up 
                              LEFT JOIN alumnus_bio ab ON up.user_id = ab.id 
                              ORDER BY up.created_at DESC LIMIT 5";
            $userPostsResult = $conn->query($userPostsQuery);
            $userPosts = [];
            if ($userPostsResult) {
                while ($row = $userPostsResult->fetch_assoc()) {
                    $userPosts[] = $row;
                }
            }
            
            // Display user posts
            foreach ($userPosts as $post) {
                $postDate = date('M j, Y g:i A', strtotime($post['created_at']));
                $authorName = htmlspecialchars($post['firstname'] . ' ' . $post['lastname']);
                $postContent = htmlspecialchars($post['content']);
                $isOwnPost = ($post['user_id'] == $userId);
                
                echo '<div class="card mb-3 shadow-sm">';
                echo '<div class="card-body">';
                echo '<div class="d-flex justify-content-between align-items-start mb-2">';
                echo '<div class="d-flex align-items-center gap-2">';
                
                if (!empty($post['img'])) {
                    echo '<img src="uploads/' . htmlspecialchars($post['img']) . '" alt="avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">';
                } else {
                    $initials = strtoupper(substr($post['firstname'], 0, 1) . substr($post['lastname'], 0, 1));
                    echo '<div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:12px;">' . $initials . '</div>';
                }
                
                echo '<div>';
                echo '<strong>' . $authorName . '</strong>';
                echo '<div class="text-muted small">Posted ' . $postDate . '</div>';
                echo '</div>';
                echo '</div>';
                
                if ($isOwnPost) {
                    echo '<div class="dropdown">';
                    echo '<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">';
                    echo '<i class="fa-solid fa-ellipsis"></i>';
                    echo '</button>';
                    echo '<ul class="dropdown-menu">';
                    echo '<li><a class="dropdown-item edit-post" href="#" data-post-id="' . $post['id'] . '" data-content="' . htmlspecialchars($post['content']) . '"><i class="fa-solid fa-edit me-1"></i>Edit</a></li>';
                    echo '<li><a class="dropdown-item text-danger delete-post" href="#" data-post-id="' . $post['id'] . '"><i class="fa-solid fa-trash me-1"></i>Delete</a></li>';
                    echo '</ul>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '<p class="mb-0">' . nl2br($postContent) . '</p>';
                echo '</div>';
                echo '</div>';
            }
            
            $fallbacks = [
              'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=1200&q=60&auto=format&fit=crop',
              'https://images.unsplash.com/photo-1523906630133-f6934a84cf15?w=1200&q=60&auto=format&fit=crop'
            ];
            foreach ($initial_events as $i => $ev) {
                $banner = (!empty($ev['banner']) && file_exists(UPLOAD_DIR.$ev['banner'])) ? 'uploads/'.htmlspecialchars($ev['banner']) : $fallbacks[$i % count($fallbacks)];
                $fullContent = html_entity_decode($ev['content']);
                $plain = trim(strip_tags($fullContent));
                $preview = mb_strlen($plain) > 220 ? mb_substr($plain,0,220).'...' : $plain;
                $isFuture = strtotime($ev['schedule']) > time();

                // counts and status
                $stmtc = $conn->prepare("SELECT COUNT(*) as c FROM event_likes WHERE event_id = ?");
                $stmtc->bind_param("i", $ev['id']); $stmtc->execute(); $likes = intval($stmtc->get_result()->fetch_assoc()['c']);
                $stmtb = $conn->prepare("SELECT 1 FROM event_bookmarks WHERE event_id = ? AND user_id = ? LIMIT 1");
                $stmtb->bind_param("ii", $ev['id'], $userId); $stmtb->execute(); $booked = (bool)$stmtb->get_result()->fetch_assoc();
                $stmtl = $conn->prepare("SELECT 1 FROM event_likes WHERE event_id = ? AND user_id = ? LIMIT 1");
                $stmtl->bind_param("ii", $ev['id'], $userId); $stmtl->execute(); $liked = (bool)$stmtl->get_result()->fetch_assoc();

                // preview of comments (3)
                $stmtcom = $conn->prepare("SELECT ec.*, a.firstname, a.lastname, a.img as user_img FROM event_comments ec LEFT JOIN alumnus_bio a ON ec.user_id = a.id WHERE ec.event_id = ? ORDER BY ec.created_at DESC LIMIT 3");
                $stmtcom->bind_param("i", $ev['id']); $stmtcom->execute(); $cres = $stmtcom->get_result();
                $comments = []; while ($cc = $cres->fetch_assoc()) $comments[] = $cc;
            ?>
            <article class="card-tile" data-event-id="<?php echo intval($ev['id']); ?>">
              <img src="<?php echo $banner; ?>" class="banner" alt="">
              <div class="tile-body">
                <div style="display:flex;justify-content:space-between;">
                  <div>
                    <div class="card-title"><?php echo htmlspecialchars($ev['title']); ?></div>
                    <div class="tile-meta small-muted"><?php echo date("F j, Y, g:ia", strtotime($ev['schedule'])); ?> • <?php echo $isFuture ? 'Upcoming' : 'Past Event'; ?></div>
                  </div>
                  <div class="small-muted">Posted <?php echo date("M j, Y", strtotime($ev['date_created'])); ?></div>
                </div>

                <div class="preview-text" data-full="<?php echo htmlspecialchars($plain); ?>"><?php echo htmlspecialchars($preview); ?></div>
                <?php if (mb_strlen($plain) > 220): ?>
                  <div class="mt-2"><button class="btn btn-sm btn-link see-more">See more</button></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <div>
                    <button class="btn btn-sm btn-outline-primary like-toggle" data-event="<?php echo intval($ev['id']); ?>">
                      <i class="<?php echo $liked ? 'fa-solid fa-heart' : 'fa-regular fa-heart'; ?>"></i>
                      <span class="likes-count"><?php echo $likes; ?></span>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary bookmark-toggle" data-event="<?php echo intval($ev['id']); ?>">
                      <i class="<?php echo $booked ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark'; ?>"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary share-btn" data-event="<?php echo intval($ev['id']); ?>" title="Share"><i class="fa-regular fa-share-from-square"></i></button>
                  </div>
                  <div>
                    <button class="btn btn-sm btn-light comment-open" data-event="<?php echo intval($ev['id']); ?>">Comments (<?php echo count($comments); ?>)</button>
                  </div>
                </div>
                <div class="heart-overlay" aria-hidden="true"><i class="fa-solid fa-heart"></i></div>
                <div class="card-data" data-event-id="<?php echo intval($ev['id']); ?>"></div>

                <div class="comments-preview mt-2">
                  <?php if (count($comments) === 0): ?>
                    <div class="small-muted">No comments yet.</div>
                  <?php else: foreach ($comments as $cm): ?>
                    <div style="display:flex;gap:8px;align-items:flex-start;margin-top:8px;">
                      <?php 
                      $cimg_url = '';
                      if (!empty($cm['user_img']) && file_exists(UPLOAD_DIR.$cm['user_img'])) {
                          $cimg_url = 'uploads/'.htmlspecialchars($cm['user_img']);
                      }
                      ?>
                      <a href="view_profile.php?id=<?php echo intval($cm['user_id']); ?>" title="View profile">
                        <?php if ($cimg_url): ?>
                          <img src="<?php echo $cimg_url; ?>" alt="user" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        <?php else: ?>
                          <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:12px;">
                            <?php echo strtoupper(substr($cm['firstname'], 0, 1) . substr($cm['lastname'], 0, 1)); ?>
                          </div>
                        <?php endif; ?>
                      </a>
                      <div style="flex:1;">
                        <div style="font-weight:600;"><a href="view_profile.php?id=<?php echo intval($cm['user_id']); ?>" style="color:inherit;text-decoration:none"><?php echo htmlspecialchars($cm['firstname'].' '.$cm['lastname']); ?></a> <span class="small-muted" style="font-weight:400;">• <?php echo date("M j, Y g:ia", strtotime($cm['created_at'])); ?></span></div>
                        <div><?php echo htmlspecialchars(mb_strlen($cm['comment'])>300 ? mb_substr($cm['comment'],0,300).'...' : $cm['comment']); ?></div>
                      </div>
                    </div>
                  <?php endforeach; endif; ?>
                </div>

              </div>
            </article>
            <?php } // end foreach initial events ?>
          </div>

          <div id="timelineLoading" class="loading" style="display:none;"><div class="small-muted"><span class="spinner-border spinner-border-sm"></span> Loading more...</div></div>
          <div id="timelineEnd" class="loading small-muted" style="display:none;">No more events.</div>
        </div>
      </div>

      <!-- JOBS -->
      <div class="tab-pane fade" id="jobs">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="mb-1">Job Postings</h4>
            <small class="text-muted">Discover career opportunities from fellow alumni</small>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" id="refreshJobs" title="Refresh jobs">
              <i class="fa-solid fa-refresh me-1"></i>Refresh
            </button>
            <?php if ($IS_ADMIN): ?>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createJobModal" title="Post a job">
              <i class="fa-solid fa-plus me-1"></i>Post Job
            </button>
            <?php endif; ?>
          </div>
        </div>

        <div id="jobsContainer" class="jobs-grid">
          <!-- Jobs will be loaded here via AJAX -->
        </div>

        <div class="text-center mt-4" id="loadMoreJobsContainer" style="display:none;">
          <button class="btn btn-outline-primary" id="loadMoreJobs">
            <i class="fa-solid fa-plus me-1"></i>Load More Jobs
          </button>
        </div>

        <div class="text-center mt-4" id="noJobsMessage" style="display:none;">
          <div class="alert alert-info">
            <i class="fa-solid fa-briefcase fa-2x mb-3 text-muted"></i><br>
            <strong>No job postings available</strong><br>
            <small class="text-muted">Check back later for new career opportunities!</small>
          </div>
        </div>
      </div>
      <!-- END JOBS -->

      <!-- MY INFO -->
      <div class="tab-pane fade" id="myinfo">
        <div class="bio-card">
          <h5>Profile Information</h5>
          <dl class="row">
            <dt class="col-sm-3">Full name</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['firstname'].' '.$user['middlename'].' '.$user['lastname'].' '.$user['suffixname']); ?></dd>
            <dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></dd>
            <dt class="col-sm-3">Batch</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['batch']); ?></dd>
            <dt class="col-sm-3">Address</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['address']); ?></dd>
            <dt class="col-sm-3">Company Email</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['company_email']); ?></dd>
            <dt class="col-sm-3">Academic Honors</dt><dd class="col-sm-9"><?php echo htmlspecialchars($user['academic_honor']); ?></dd>
          </dl>
        </div>
      </div>

      <!-- CLASSMATES -->
      <div class="tab-pane fade" id="classmates">
        <div class="bio-card mb-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-user-friends text-primary"></i> My Classmates</h5>
            <span class="badge bg-primary" id="classmates-count"><?php echo count($classmates); ?> found</span>
          </div>
          
          <!-- Search Filter -->
          <div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="classmates-search" placeholder="Search classmates by name...">
            <button class="btn btn-outline-secondary" type="button" id="classmates-clear">
              <i class="fas fa-times"></i> Clear
            </button>
          </div>
        </div>

        <div id="classmates-container">
          <?php if (count($classmates) === 0): ?>
            <div class="bio-card text-center py-5">
              <i class="fas fa-users fa-3x text-muted mb-3"></i>
              <p class="text-muted">No classmates found in your course.</p>
            </div>
          <?php else: ?>
            <div class="row g-3" id="classmates-grid">
              <?php foreach ($classmates as $mate): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 classmate-card" data-name="<?php echo strtolower($mate['firstname'].' '.$mate['lastname']); ?>">
                  <div class="bio-card h-100 hover-shadow">
                    <div class="d-flex gap-3 align-items-start">
                      <?php 
                      $mate_img_url = '';
                      if (!empty($mate['img']) && file_exists(UPLOAD_DIR.$mate['img'])) {
                          $mate_img_url = 'uploads/'.htmlspecialchars($mate['img']);
                      }
                      ?>
                      <?php if ($mate_img_url): ?>
                        <img src="<?php echo $mate_img_url; ?>" alt="" class="rounded-circle" style="width:70px;height:70px;object-fit:cover;border:3px solid #800000;">
                      <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:70px;height:70px;background:linear-gradient(135deg,#800000,#600000);color:white;font-weight:bold;font-size:20px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                          <?php echo strtoupper(substr($mate['firstname'], 0, 1) . substr($mate['lastname'], 0, 1)); ?>
                        </div>
                      <?php endif; ?>
                      <div class="flex-grow-1">
                        <h6 class="mb-1" style="color:var(--maroon);"><?php echo htmlspecialchars($mate['firstname'].' '.$mate['lastname']); ?></h6>
                        <p class="small text-muted mb-2">
                          <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($mate['course_name'] ?? $mate['strand_name'] ?? 'N/A'); ?>
                        </p>
                        <div class="d-flex gap-2">
                          <a href="cv.php?id=<?php echo intval($mate['id']); ?>" class="btn btn-sm btn-success" target="_blank" title="View CV">
                            <i class="fas fa-file-alt"></i> CV
                          </a>
                          <a href="view_profile.php?id=<?php echo intval($mate['id']); ?>" class="btn btn-sm btn-outline-primary" title="View Profile">
                            <i class="fas fa-eye"></i> Profile
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        
        <div id="classmates-no-results" class="bio-card text-center py-5" style="display:none;">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <p class="text-muted">No classmates found matching your search.</p>
        </div>
      </div>

      <!-- BATCHMATES -->
      <div class="tab-pane fade" id="batchmates">
        <div class="bio-card mb-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <h5 class="mb-0"><i class="fas fa-user-graduate text-success"></i> My Batchmates</h5>
              <small class="text-muted">Alumni from the same graduation year</small>
            </div>
            <div class="d-flex gap-2 align-items-center">
              <label class="mb-0 fw-bold">Batch Year:</label>
              <select id="batch-select" class="form-select form-select-sm" style="min-width:120px;">
                <?php foreach ($batch_years as $yr): ?>
                  <option value="<?php echo htmlspecialchars($yr); ?>" <?php echo $yr == $selected_batch ? 'selected' : ''; ?>><?php echo htmlspecialchars($yr); ?></option>
                <?php endforeach; ?>
              </select>
              <span class="badge bg-success" id="batchmates-count"><?php echo count($batchmates); ?> found</span>
            </div>
          </div>
          
          <!-- Search Filter -->
          <div class="input-group mt-3">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" id="batchmates-search" placeholder="Search batchmates by name or course...">
            <button class="btn btn-outline-secondary" type="button" id="batchmates-clear">
              <i class="fas fa-times"></i> Clear
            </button>
          </div>
        </div>

        <div id="batchmates-container">
          <?php if (count($batchmates) === 0): ?>
            <div class="bio-card text-center py-5">
              <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
              <p class="text-muted">No batchmates found for this year.</p>
            </div>
          <?php else: ?>
            <div class="row g-3" id="batchmates-grid">
              <?php foreach ($batchmates as $mate): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 batchmate-card" data-name="<?php echo strtolower($mate['firstname'].' '.$mate['lastname']); ?>" data-course="<?php echo strtolower($mate['course_name'] ?? $mate['strand_name'] ?? ''); ?>">
                  <div class="bio-card h-100 hover-shadow">
                    <div class="d-flex gap-3 align-items-start">
                      <?php 
                      $img_url = '';
                      if (!empty($mate['img']) && file_exists(UPLOAD_DIR.$mate['img'])) {
                          $img_url = 'uploads/'.htmlspecialchars($mate['img']);
                      }
                      ?>
                      <?php if ($img_url): ?>
                        <img src="<?php echo $img_url; ?>" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid #28a745;">
                      <?php else: ?>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:linear-gradient(135deg,#28a745,#20c997);color:white;font-weight:bold;font-size:24px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                          <?php echo strtoupper(substr($mate['firstname'], 0, 1) . substr($mate['lastname'], 0, 1)); ?>
                        </div>
                      <?php endif; ?>
                      <div class="flex-grow-1">
                        <h6 class="mb-1" style="color:var(--maroon);"><?php echo htmlspecialchars($mate['firstname'].' '.$mate['lastname']); ?></h6>
                        <p class="small text-muted mb-1">
                          <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($mate['course_name'] ?? $mate['strand_name'] ?? 'N/A'); ?>
                        </p>
                        <p class="small text-muted mb-2">
                          <i class="fas fa-calendar-alt"></i> Batch <?php echo htmlspecialchars($mate['batch']); ?>
                        </p>
                        <div class="d-flex gap-2">
                          <a href="cv.php?id=<?php echo intval($mate['id']); ?>" class="btn btn-sm btn-success" target="_blank" title="View CV">
                            <i class="fas fa-file-alt"></i> CV
                          </a>
                          <a href="view_profile.php?id=<?php echo intval($mate['id']); ?>" class="btn btn-sm btn-outline-primary" title="View Profile">
                            <i class="fas fa-eye"></i> Profile
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        
        <div id="batchmates-no-results" class="bio-card text-center py-5" style="display:none;">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <p class="text-muted">No batchmates found matching your search.</p>
        </div>
      </div>

      <!-- SEARCH -->
      <div class="tab-pane fade" id="search">
        <div class="bio-card mb-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h5 class="mb-0"><i class="fas fa-search text-warning"></i> Search Alumni</h5>
              <small class="text-muted">Find alumni by name, course, or batch year</small>
            </div>
            <span class="badge bg-warning text-dark" id="search-count">0 results</span>
          </div>
          
          <!-- Advanced Search Filters -->
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold"><i class="fas fa-user"></i> Name</label>
              <input id="search-name" class="form-control" placeholder="Enter name to search...">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold"><i class="fas fa-graduation-cap"></i> Course</label>
              <select id="search-course" class="form-select">
                <option value="">All Courses</option>
                <?php 
                $courses->data_seek(0); // Reset pointer
                while ($row = $courses->fetch_assoc()): 
                ?>
                  <option value="<?php echo intval($row['id']); ?>"><?php echo htmlspecialchars($row['course']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold"><i class="fas fa-calendar-alt"></i> Batch</label>
              <select id="search-batch" class="form-select">
                <option value="">All Batches</option>
                <?php foreach ($batch_years as $yr): ?>
                  <option value="<?php echo htmlspecialchars($yr); ?>"><?php echo htmlspecialchars($yr); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          
          <div class="d-flex gap-2 mt-3">
            <button id="search-btn" class="btn btn-primary">
              <i class="fas fa-search"></i> Search
            </button>
            <button id="search-clear" class="btn btn-outline-secondary">
              <i class="fas fa-redo"></i> Clear Filters
            </button>
          </div>
        </div>

        <!-- Search Results -->
        <div id="searchResults"></div>
        
        <!-- No Results Message -->
        <div id="search-no-results" class="bio-card text-center py-5" style="display:none;">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <p class="text-muted mb-2">No alumni found matching your search criteria.</p>
          <small class="text-muted">Try adjusting your filters or search terms.</small>
        </div>
        
        <!-- Load More -->
        <div id="searchMore" class="mt-3 text-center" style="display:none;">
          <button id="loadMoreSearch" class="btn btn-outline-primary">
            <i class="fas fa-chevron-down"></i> Load More Results
          </button>
        </div>
        
        <!-- Loading Indicator -->
        <div id="search-loading" class="bio-card text-center py-5" style="display:none;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-3">Searching alumni...</p>
        </div>
      </div>

    </div> <!-- tab-content -->

  </main>

  <!-- Edit Profile: replaced by standalone page at edit_profile.php -->

  <!-- Create Job Modal (admin only) -->
  <div class="modal fade" id="createJobModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form id="createJobForm">
          <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
          <div class="modal-header">
            <h5 class="modal-title">Post a Job</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="createJobAlert" style="display:none;" class="alert"></div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Job Title *</label>
                <input name="job_title" class="form-control" required placeholder="e.g. Software Developer">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Company *</label>
                <input name="company" class="form-control" required placeholder="e.g. Tech Solutions Inc.">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Location</label>
              <input name="location" class="form-control" placeholder="e.g. Remote, New York, NY">
            </div>

            <div class="mb-3">
              <label class="form-label">Job Description *</label>
              <textarea name="description" class="form-control" rows="4" required placeholder="Describe the job responsibilities, requirements, and benefits..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" id="createJobBtn">Post Job</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Create Post Modal -->
  <div class="modal fade" id="createPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold">Create Post</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="createPostForm" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
              <?php if ($user_img_url): ?>
                <img src="<?php echo $user_img_url; ?>" alt="avatar" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;border:2px solid #800000;">
              <?php else: ?>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:linear-gradient(135deg,#800000,#600000);color:white;font-weight:bold;font-size:18px;border:2px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                  <?php echo strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1)); ?>
                </div>
              <?php endif; ?>
              <div>
                <strong class="d-block"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></strong>
                <small class="text-muted"><i class="fas fa-globe-americas"></i> Public</small>
              </div>
            </div>
            
            <textarea name="content" id="postContent" class="form-control border-0 mb-3" rows="5" placeholder="What's on your mind, <?php echo htmlspecialchars($user['firstname']); ?>?" required style="resize:none;font-size:1.1rem;"></textarea>
            
            <!-- Image Preview -->
            <div id="imagePreviewContainer" class="mb-3" style="display:none;">
              <div class="position-relative d-inline-block">
                <img id="imagePreview" class="img-fluid rounded" style="max-height:300px;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="removeImage">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
            
            <!-- Post Options -->
            <div class="border rounded p-3">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Add to your post</span>
                <div class="d-flex gap-2">
                  <label for="postImage" class="btn btn-light btn-sm" title="Photo/Video">
                    <i class="fas fa-photo-video text-success"></i>
                    <input type="file" id="postImage" name="image" accept="image/*,video/*" class="d-none">
                  </label>
                  <button type="button" class="btn btn-light btn-sm" title="Feeling/Activity" onclick="addFeeling()">
                    <i class="fas fa-smile text-warning"></i>
                  </button>
                  <button type="button" class="btn btn-light btn-sm" title="Tag People" onclick="tagPeople()">
                    <i class="fas fa-user-tag text-primary"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="submit" class="btn btn-primary w-100 fw-bold" id="submitPost">
              <i class="fas fa-paper-plane"></i> Post
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Edit Post Modal -->
  <div class="modal fade" id="editPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold">Edit Post</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editPostForm" enctype="multipart/form-data">
          <input type="hidden" id="editPostId" name="post_id">
          <div class="modal-body">
            <textarea name="content" id="editPostContent" class="form-control border-0 mb-3" rows="5" placeholder="What's on your mind?" required style="resize:none;font-size:1.1rem;"></textarea>
            
            <!-- Current Image Display -->
            <div id="currentImageContainer" class="mb-3" style="display:none;">
              <div class="position-relative d-inline-block">
                <img id="currentImage" class="img-fluid rounded" style="max-height:300px;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="removeCurrentImage">
                  <i class="fas fa-times"></i> Remove
                </button>
              </div>
            </div>
            
            <!-- New Image Upload -->
            <div class="border rounded p-3">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Update media</span>
                <label for="editPostImage" class="btn btn-light btn-sm">
                  <i class="fas fa-photo-video text-success"></i> Change Photo/Video
                  <input type="file" id="editPostImage" name="image" accept="image/*,video/*" class="d-none">
                </label>
              </div>
            </div>
            
            <!-- New Image Preview -->
            <div id="editImagePreviewContainer" class="mt-3" style="display:none;">
              <div class="position-relative d-inline-block">
                <img id="editImagePreview" class="img-fluid rounded" style="max-height:300px;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="removeEditImage">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary fw-bold" id="updatePost">
              <i class="fas fa-save"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Job Details Modal -->
  <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Job Details</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="jobDetailsContent">
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Comments Modal -->
  <div class="modal fade" id="commentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Comments</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div id="commentsList" style="max-height:360px; overflow:auto;"></div>
          <div class="mt-3">
            <textarea id="commentText" class="form-control" placeholder="Write a comment..." rows="3"></textarea>
            <div class="d-flex justify-content-end mt-2">
              <button id="postCommentBtn" class="btn btn-primary">Post Comment</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <!-- Help floating button -->
  <button id="helpFab" title="Help Center" style="position:fixed;right:18px;bottom:18px;background:var(--maroon);color:var(--white);border:none;border-radius:50%;width:56px;height:56px;box-shadow:0 10px 24px rgba(0,0,0,0.18);z-index:1400;display:flex;align-items:center;justify-content:center;font-size:20px;">?
  </button>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  (function($){
    const csrf = '<?php echo $csrf_token; ?>';
    let timelinePage = 1, timelineLoading = false, timelineHasMore = true;

  // See more toggle
    $(document).on('click', '.see-more', function(){
      const $btn = $(this), $tile = $btn.closest('.card-tile'), $preview = $tile.find('.preview-text'), full = $preview.data('full');
      if ($btn.data('open')) { $preview.text(full.substring(0,220) + '...'); $btn.data('open', false).text('See more'); }
      else { $preview.text(full); $btn.data('open', true).text('See less'); }
    });

    // Profile image preview
    $('#profile_img').on('change', function(){
      const input = this;
      if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > <?php echo MAX_UPLOAD_BYTES; ?>) { alert('Image too large'); input.value=''; return; }
        const reader = new FileReader();
        reader.onload = function(e){ $('#previewImage').attr('src', e.target.result); };
        reader.readAsDataURL(file);
      }
    });

    // Save profile via AJAX (full page reload not required)
    $('#editProfileForm').on('submit', function(e){
      e.preventDefault();
      const fd = new FormData(this);
      fd.append('ajax_action', 'update_profile'); // handled by same file
      fd.append('csrf_token', csrf);
      $('#saveProfileBtn').prop('disabled', true).text('Saving...');
      $.ajax({
        url: '',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
      }).done(function(resp){
        if (resp.ok) {
          // Update profile elements without reload
          if (resp.data && resp.data.user) {
            const user = resp.data.user;
            
            // Update avatar images
            if (user.img) {
              const avatarUrl = 'uploads/' + user.img + '?v=' + Date.now();
              $('#profileAvatar').attr('src', avatarUrl);
              $('#previewImage').attr('src', avatarUrl);
            }
            
            // Update name displays
            const fullName = user.firstname + ' ' + user.lastname;
            $('.user-name').text(fullName);
            $('#userName').text(fullName);
            
            // Update other profile fields in modal
            $('#editModal input[name="firstname"]').val(user.firstname);
            $('#editModal input[name="lastname"]').val(user.lastname);
            $('#editModal input[name="middlename"]').val(user.middlename);
            $('#editModal input[name="email"]').val(user.email);
            $('#editModal input[name="contact_no"]').val(user.contact_no);
            $('#editModal select[name="gender"]').val(user.gender);
            $('#editModal input[name="birthdate"]').val(user.birthdate);
            $('#editModal input[name="address"]').val(user.address);
            $('#editModal select[name="employment_status"]').val(user.employment_status);
            $('#editModal input[name="connected_to"]').val(user.connected_to);
            $('#editModal input[name="company_address"]').val(user.company_address);
            $('#editModal input[name="company_email"]').val(user.company_email);
          }
          
          $('#editModal').modal('hide');
          
          // Show success message
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Profile updated successfully!',
            confirmButtonColor: '#800000',
            timer: 2000,
            showConfirmButton: false
          });
        } else { 
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: resp.msg || 'Failed to update profile',
            confirmButtonColor: '#800000'
          });
        }
      }).fail(function(){ 
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Save failed - please try again',
          confirmButtonColor: '#800000'
        });
      }).always(function(){ $('#saveProfileBtn').prop('disabled', false).text('Save changes'); });
    });

    // Infinite scroll for timeline
    function loadMoreTimeline(){
      if (timelineLoading || !timelineHasMore) return;
      timelineLoading = true; timelinePage++;
      $('#timelineLoading').show();
      $.post('', { ajax_action:'fetch_timeline', page: timelinePage }, function(resp){
        if (resp.ok) {
          if (resp.html) $('#masonryFeed').append(resp.html);
          timelineHasMore = resp.hasMore;
          if (!timelineHasMore) $('#timelineEnd').show();
        } else console.error(resp.msg);
      }, 'json').always(function(){ timelineLoading = false; $('#timelineLoading').hide(); });
    }
    $(window).on('scroll', function(){
      if ($(window).scrollTop() + $(window).height() > $(document).height() - 220) loadMoreTimeline();
    });

    // Like toggle
    $(document).on('click', '.like-toggle', function(){
      const $btn = $(this), eventId = $btn.data('event');
      $btn.prop('disabled', true);
      $.post('', { ajax_action:'toggle_like', event_id:eventId, csrf_token:csrf }, function(resp){
        if (resp.ok) {
          $btn.find('.likes-count').text(resp.count || 0);
          const icon = $btn.find('i');
          if (resp.liked) icon.removeClass('fa-regular').addClass('fa-solid');
          else icon.removeClass('fa-solid').addClass('fa-regular');
        } else alert(resp.msg);
      }, 'json').always(function(){ $btn.prop('disabled', false); });
    });

    // Bookmark toggle
    $(document).on('click', '.bookmark-toggle', function(){
      const $btn = $(this), eventId = $btn.data('event');
      $btn.prop('disabled', true);
      $.post('', { ajax_action:'toggle_bookmark', event_id:eventId, csrf_token:csrf }, function(resp){
        if (resp.ok) {
          const icon = $btn.find('i'); if (resp.bookmarked) icon.removeClass('fa-regular').addClass('fa-solid'); else icon.removeClass('fa-solid').addClass('fa-regular');
        } else alert(resp.msg);
      }, 'json').always(function(){ $btn.prop('disabled', false); });
    });

    // Comments: open modal and load comments
    let activeEvent = 0;
    $(document).on('click', '.comment-open', function(){
      activeEvent = $(this).data('event');
      $('#commentsList').html('<div class="small-muted">Loading...</div>');
      $('#commentsModal').modal('show');
      loadComments(activeEvent);
    });
    function loadComments(eventId){
      $.post('', { ajax_action:'fetch_comments', event_id: eventId }, function(resp){
        if (resp.ok) {
          let html = '';
          const comments = resp.comments;
          if (comments.length === 0) html = '<div class="small-muted">No comments yet.</div>';
          else {
            comments.forEach(c => {
              const userimg = c.user_img ? 'uploads/' + c.user_img : '';
              const initials = (c.firstname.charAt(0) + c.lastname.charAt(0)).toUpperCase();
              html += `<div class="d-flex gap-2 mb-2 align-items-start">
                ${userimg ? 
                  `<img src="${userimg}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">` :
                  `<div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:14px;">${initials}</div>`
                }
                <div style="flex:1;">
                  <div style="font-weight:700;">${escapeHtml(c.firstname + ' ' + c.lastname)} <span class="small-muted" style="font-weight:400;">• ${c.created_at}</span></div>
                  <div>${escapeHtml(c.comment)}</div>
                </div>
                <?php // permissions for edit/delete handled client-side UI; actual checks server-side ?>
                <div class="ms-2">
                  <button class="btn btn-sm btn-link edit-comment-btn" data-id="${c.id}">Edit</button>
                  <button class="btn btn-sm btn-link text-danger delete-comment-btn" data-id="${c.id}">Delete</button>
                </div>
              </div>`;
            });
          }
          $('#commentsList').html(html);
        } else $('#commentsList').html('<div class="text-danger">Failed to load comments</div>');
      }, 'json').fail(function(){ $('#commentsList').html('<div class="text-danger">Failed to load comments</div>'); });
    }

    // Post comment
    $('#postCommentBtn').on('click', function(){
      const txt = $('#commentText').val().trim();
      if (!txt) return alert('Write a comment.');
      $(this).prop('disabled', true).text('Posting...');
      $.post('', { ajax_action:'post_comment', event_id: activeEvent, comment: txt, csrf_token: csrf }, function(resp){
        if (resp.ok) { $('#commentText').val(''); loadComments(activeEvent); }
        else alert(resp.msg);
      }, 'json').fail(function(){ alert('Failed'); }).always(function(){ $('#postCommentBtn').prop('disabled', false).text('Post Comment'); });
    });

    // Edit comment (opens prompt; submit to server)
    $(document).on('click', '.edit-comment-btn', function(){
      const id = $(this).data('id');
      const current = $(this).closest('div.d-flex').find('div').eq(1).text().trim();
      const newText = prompt('Edit your comment:', current);
      if (newText === null) return;
      if (newText.trim() === '') return alert('Empty comment not allowed');
      $.post('', { ajax_action:'edit_comment', comment_id: id, comment: newText, csrf_token: csrf }, function(resp){
        if (resp.ok) loadComments(activeEvent); else alert(resp.msg);
      }, 'json');
    });

    // Delete comment
    $(document).on('click', '.delete-comment-btn', function(){
      if (!confirm('Delete this comment?')) return;
      const id = $(this).data('id');
      $.post('', { ajax_action:'delete_comment', comment_id: id, csrf_token: csrf }, function(resp){
        if (resp.ok) loadComments(activeEvent); else alert(resp.msg);
      }, 'json');
    });

    // Create job (admin)
    $('#createJobForm').on('submit', function(e){
      e.preventDefault();
      const fd = new FormData(this);
      fd.append('ajax_action','create_job');
      fd.append('csrf_token', csrf);
      $('#createJobBtn').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Posting...');

      $.ajax({
        url: window.location.href,
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json'
      }).done(function(resp){
        if (resp.ok) {
          $('#createJobModal').modal('hide');
          $('#createJobForm')[0].reset();
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Job posted successfully!',
            confirmButtonColor: '#800000',
            timer: 2000,
            showConfirmButton: false
          });
          // Refresh jobs
          jobsLoaded = false;
          loadJobs(1);
        } else {
          $('#createJobAlert').removeClass('alert-success').addClass('alert-danger').text(resp.msg).show();
        }
      }).fail(function(){
        $('#createJobAlert').removeClass('alert-success').addClass('alert-danger').text('Failed to post job').show();
      }).always(function(){
        $('#createJobBtn').prop('disabled', false).html('Post Job');
      });
    });

    // Hide alert when modal is closed
    $('#createJobModal').on('hidden.bs.modal', function(){
      $('#createJobAlert').hide();
    });

    // Search (debounce)
    let searchPage = 1, searchHasMore = false, searchTimer;
    function doSearch(reset=true) {
      if (reset) { searchPage = 1; $('#searchResults').html('<div class="small-muted"><span class="spinner-border spinner-border-sm"></span> Searching...</div>'); }
      const name = $('#search-name').val(), course = $('#search-course').val(), batch = $('#search-batch').val();
      $.post(window.location.href, { ajax_action:'search_alumni', name: name, course: course, batch: batch, page: searchPage }, function(resp){
        if (resp.ok) {
          if (searchPage === 1) $('#searchResults').html('<div class="row">'+resp.html+'</div>');
          else $('#searchResults .row').append(resp.html);
          searchHasMore = resp.hasMore;
          $('#searchMore').toggle(searchHasMore);
        } else $('#searchResults').html('<div class="text-danger">Search failed</div>');
      }, 'json');
    }
    $('#search-name').on('input', function(){ clearTimeout(searchTimer); searchTimer = setTimeout(()=>doSearch(true), 450); });
    $('#search-course, #search-batch').on('change', function(){ doSearch(true); });
    $('#search-btn').on('click', function(){ doSearch(true); });
    $('#search-clear').on('click', function(){ $('#search-name').val(''); $('#search-course').val(''); $('#search-batch').val(''); $('#searchResults').html(''); $('#searchMore').hide(); });

    $('#loadMoreSearch').on('click', function(){ searchPage++; doSearch(false); });

    // helper escape
    function escapeHtml(s) { return $('<div>').text(s).html(); }

    // --- SweetAlert2: Edit profile iframe ---
    function openEditProfilePopup(autoCloseAfterSave = true){
      Swal.fire({
        title: 'Edit Profile',
        html: `<iframe src="edit_profile.php" id="swalEditFrame" frameborder="0" style="width:100%;height:520px;border-radius:8px;"></iframe>`,
        width: 800,
        showCloseButton: true,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
          // Listen for profile update messages from iframe
          window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'profileUpdated' && event.data.success) {
              // Close the modal
              Swal.close();
              
              // Show success notification
              Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: 'Your profile has been successfully updated.',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
              }).then(() => {
                // Reload the page to show updated profile
                location.reload();
              });
            }
          });
        }
      });
    }

  $('#editProfileBtn').on('click', function(){ openEditProfilePopup(); });
  // If a modal-based Edit button exists, reuse the same edit popup handler
  $('#modalEditBtn').on('click', function(){ $('#editProfileBtn').trigger('click'); });

    // Auto-open after login if session flag is set (server-side control)
    <?php if (!empty($_SESSION['show_edit_on_login'])): ?>
      $(function(){ openEditProfilePopup(); });
      <?php unset($_SESSION['show_edit_on_login']); ?>
    <?php endif; ?>

    // ============================================
    // CHANGE PASSWORD FUNCTIONALITY
    // ============================================
    
    $('#changePasswordBtn').on('click', function() {
      openChangePasswordModal();
    });

    function openChangePasswordModal() {
      Swal.fire({
        title: '<i class="fas fa-key text-danger"></i> Change Password',
        html: `
          <div style="text-align: left; padding: 10px;">
            <p class="text-muted mb-4">Keep your account secure by using a strong password.</p>
            
            <form id="changePasswordForm">
              <!-- Current Password -->
              <div class="mb-3">
                <label class="form-label fw-bold">Current Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                  <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password" required>
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('currentPassword')">
                    <i class="fas fa-eye" id="currentPassword-icon"></i>
                  </button>
                </div>
                <small class="text-muted">Enter your current password to verify it's you</small>
              </div>

              <!-- New Password -->
              <div class="mb-3">
                <label class="form-label fw-bold">New Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-key"></i></span>
                  <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required>
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPassword')">
                    <i class="fas fa-eye" id="newPassword-icon"></i>
                  </button>
                </div>
                <div id="passwordStrength" class="mt-2"></div>
              </div>

              <!-- Confirm New Password -->
              <div class="mb-3">
                <label class="form-label fw-bold">Confirm New Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                  <input type="password" class="form-control" id="confirmPassword" placeholder="Re-enter new password" required>
                  <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">
                    <i class="fas fa-eye" id="confirmPassword-icon"></i>
                  </button>
                </div>
                <div id="passwordMatch" class="mt-1"></div>
              </div>

              <!-- Password Requirements -->
              <div class="alert alert-info" style="font-size: 0.85rem;">
                <strong><i class="fas fa-info-circle"></i> Password Requirements:</strong>
                <ul class="mb-0 mt-2" style="padding-left: 20px;">
                  <li id="req-length">At least 8 characters</li>
                  <li id="req-uppercase">One uppercase letter</li>
                  <li id="req-lowercase">One lowercase letter</li>
                  <li id="req-number">One number</li>
                </ul>
              </div>
            </form>
          </div>
        `,
        width: 600,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Change Password',
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        confirmButtonColor: '#800000',
        cancelButtonColor: '#6c757d',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: () => {
          const currentPassword = document.getElementById('currentPassword').value;
          const newPassword = document.getElementById('newPassword').value;
          const confirmPassword = document.getElementById('confirmPassword').value;

          // Validation
          if (!currentPassword || !newPassword || !confirmPassword) {
            Swal.showValidationMessage('Please fill in all fields');
            return false;
          }

          if (newPassword.length < 8) {
            Swal.showValidationMessage('New password must be at least 8 characters');
            return false;
          }

          if (newPassword !== confirmPassword) {
            Swal.showValidationMessage('New passwords do not match');
            return false;
          }

          if (currentPassword === newPassword) {
            Swal.showValidationMessage('New password must be different from current password');
            return false;
          }

          // Check password strength
          const hasUppercase = /[A-Z]/.test(newPassword);
          const hasLowercase = /[a-z]/.test(newPassword);
          const hasNumber = /[0-9]/.test(newPassword);

          if (!hasUppercase || !hasLowercase || !hasNumber) {
            Swal.showValidationMessage('Password must contain uppercase, lowercase, and numbers');
            return false;
          }

          // Send AJAX request
          return fetch('change_password.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              current_password: currentPassword,
              new_password: newPassword,
              confirm_password: confirmPassword
            })
          })
          .then(response => response.json())
          .then(data => {
            if (!data.success) {
              throw new Error(data.message || 'Failed to change password');
            }
            return data;
          })
          .catch(error => {
            Swal.showValidationMessage(error.message);
          });
        },
        didOpen: () => {
          // Password strength indicator
          $('#newPassword').on('input', function() {
            const password = $(this).val();
            const strength = checkPasswordStrength(password);
            updatePasswordStrength(strength);
            checkPasswordRequirements(password);
          });

          // Password match indicator
          $('#confirmPassword').on('input', function() {
            const newPass = $('#newPassword').val();
            const confirmPass = $(this).val();
            updatePasswordMatch(newPass, confirmPass);
          });
        }
      }).then((result) => {
        if (result.isConfirmed && result.value) {
          // Success notification
          Swal.fire({
            icon: 'success',
            title: 'Password Changed!',
            html: '<p>Your password has been successfully updated.</p><p class="text-muted">You can now use your new password to log in.</p>',
            confirmButtonText: 'Got it!',
            confirmButtonColor: '#800000',
            timer: 3000,
            timerProgressBar: true
          });
        }
      });
    }

    // Toggle password visibility
    window.togglePassword = function(fieldId) {
      const field = document.getElementById(fieldId);
      const icon = document.getElementById(fieldId + '-icon');
      
      if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    };

    // Check password strength
    function checkPasswordStrength(password) {
      let strength = 0;
      
      if (password.length >= 8) strength++;
      if (password.length >= 12) strength++;
      if (/[a-z]/.test(password)) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[^a-zA-Z0-9]/.test(password)) strength++;
      
      return strength;
    }

    // Update password strength indicator
    function updatePasswordStrength(strength) {
      const strengthDiv = $('#passwordStrength');
      let html = '<div class="progress" style="height: 8px;">';
      let color, text;
      
      if (strength <= 2) {
        color = 'danger';
        text = 'Weak';
      } else if (strength <= 4) {
        color = 'warning';
        text = 'Medium';
      } else {
        color = 'success';
        text = 'Strong';
      }
      
      const percentage = (strength / 6) * 100;
      html += '<div class="progress-bar bg-' + color + '" style="width: ' + percentage + '%"></div>';
      html += '</div>';
      html += '<small class="text-' + color + '">Password Strength: ' + text + '</small>';
      
      strengthDiv.html(html);
    }

    // Check password requirements
    function checkPasswordRequirements(password) {
      const requirements = {
        'req-length': password.length >= 8,
        'req-uppercase': /[A-Z]/.test(password),
        'req-lowercase': /[a-z]/.test(password),
        'req-number': /[0-9]/.test(password)
      };
      
      for (const [id, met] of Object.entries(requirements)) {
        const elem = document.getElementById(id);
        if (elem) {
          if (met) {
            elem.style.color = 'green';
            elem.innerHTML = elem.innerHTML.replace(/^/, '✓ ');
          } else {
            elem.style.color = '#666';
            elem.innerHTML = elem.innerHTML.replace('✓ ', '');
          }
        }
      }
    }

    // Update password match indicator
    function updatePasswordMatch(newPass, confirmPass) {
      const matchDiv = $('#passwordMatch');
      
      if (!confirmPass) {
        matchDiv.html('');
        return;
      }
      
      if (newPass === confirmPass) {
        matchDiv.html('<small class="text-success"><i class="fas fa-check-circle"></i> Passwords match</small>');
      } else {
        matchDiv.html('<small class="text-danger"><i class="fas fa-times-circle"></i> Passwords do not match</small>');
      }
    }

    // ============================================
    // CLASSMATES REAL-TIME SEARCH
    // ============================================
    
    $('#classmates-search').on('input', function() {
      const searchTerm = $(this).val().toLowerCase().trim();
      filterClassmates(searchTerm);
    });
    
    $('#classmates-clear').on('click', function() {
      $('#classmates-search').val('');
      filterClassmates('');
    });
    
    function filterClassmates(searchTerm) {
      const $cards = $('.classmate-card');
      let visibleCount = 0;
      
      $cards.each(function() {
        const name = $(this).data('name');
        if (!searchTerm || name.includes(searchTerm)) {
          $(this).show();
          visibleCount++;
        } else {
          $(this).hide();
        }
      });
      
      // Update count
      $('#classmates-count').text(visibleCount + ' found');
      
      // Show/hide no results message
      if (visibleCount === 0 && searchTerm) {
        $('#classmates-no-results').show();
        $('#classmates-container').hide();
      } else {
        $('#classmates-no-results').hide();
        $('#classmates-container').show();
      }
    }
    
    // ============================================
    // BATCHMATES REAL-TIME SEARCH & FILTER
    // ============================================
    
    $('#batchmates-search').on('input', function() {
      const searchTerm = $(this).val().toLowerCase().trim();
      filterBatchmates(searchTerm);
    });
    
    $('#batchmates-clear').on('click', function() {
      $('#batchmates-search').val('');
      filterBatchmates('');
    });
    
    function filterBatchmates(searchTerm) {
      const $cards = $('.batchmate-card');
      let visibleCount = 0;
      
      $cards.each(function() {
        const name = $(this).data('name');
        const course = $(this).data('course');
        const searchData = name + ' ' + course;
        
        if (!searchTerm || searchData.includes(searchTerm)) {
          $(this).show();
          visibleCount++;
        } else {
          $(this).hide();
        }
      });
      
      // Update count
      $('#batchmates-count').text(visibleCount + ' found');
      
      // Show/hide no results message
      if (visibleCount === 0 && searchTerm) {
        $('#batchmates-no-results').show();
        $('#batchmates-container').hide();
      } else {
        $('#batchmates-no-results').hide();
        $('#batchmates-container').show();
      }
    }
    
    // Batch year selector - AJAX load
    $('#batch-select').on('change', function() {
      const selectedBatch = $(this).val();
      loadBatchmates(selectedBatch);
    });
    
    function loadBatchmates(batch) {
      const $container = $('#batchmates-grid');
      const $noResults = $('#batchmates-no-results');
      
      // Show loading
      $container.html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div><p class="text-muted mt-3">Loading batchmates...</p></div>');
      
      $.ajax({
        url: 'ajax/get_batchmates.php',
        method: 'GET',
        data: { batch: batch },
        dataType: 'json',
        success: function(response) {
          if (response.success && response.data.length > 0) {
            let html = '';
            response.data.forEach(function(mate) {
              const initials = mate.firstname.charAt(0) + mate.lastname.charAt(0);
              const imgHtml = mate.img ? 
                `<img src="uploads/${mate.img}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid #28a745;">` :
                `<div class="rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:linear-gradient(135deg,#28a745,#20c997);color:white;font-weight:bold;font-size:24px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);">${initials.toUpperCase()}</div>`;
              
              html += `
                <div class="col-lg-4 col-md-6 col-sm-12 batchmate-card" data-name="${mate.firstname.toLowerCase()} ${mate.lastname.toLowerCase()}" data-course="${(mate.course_name || '').toLowerCase()}">
                  <div class="bio-card h-100 hover-shadow">
                    <div class="d-flex gap-3 align-items-start">
                      ${imgHtml}
                      <div class="flex-grow-1">
                        <h6 class="mb-1" style="color:var(--maroon);">${mate.firstname} ${mate.lastname}</h6>
                        <p class="small text-muted mb-1">
                          <i class="fas fa-graduation-cap"></i> ${mate.course_name || 'N/A'}
                        </p>
                        <p class="small text-muted mb-2">
                          <i class="fas fa-calendar-alt"></i> Batch ${mate.batch}
                        </p>
                        <div class="d-flex gap-2">
                          <a href="cv.php?id=${mate.id}" class="btn btn-sm btn-success" target="_blank" title="View CV">
                            <i class="fas fa-file-alt"></i> CV
                          </a>
                          <a href="view_profile.php?id=${mate.id}" class="btn btn-sm btn-outline-primary" title="View Profile">
                            <i class="fas fa-eye"></i> Profile
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              `;
            });
            $container.html(html);
            $('#batchmates-count').text(response.data.length + ' found');
            $noResults.hide();
          } else {
            $container.html('');
            $noResults.show();
            $('#batchmates-count').text('0 found');
          }
        },
        error: function() {
          $container.html('<div class="col-12 text-center py-5 text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p>Error loading batchmates. Please try again.</p></div>');
        }
      });
    }
    
    // ============================================
    // SEARCH ALUMNI FUNCTIONALITY
    // ============================================
    
    let searchOffset = 0;
    const searchLimit = 12;
    
    $('#search-btn').on('click', function() {
      searchOffset = 0;
      performSearch();
    });
    
    $('#search-clear').on('click', function() {
      $('#search-name').val('');
      $('#search-course').val('');
      $('#search-batch').val('');
      $('#searchResults').html('');
      $('#search-no-results').hide();
      $('#searchMore').hide();
      $('#search-count').text('0 results');
    });
    
    // Real-time search on Enter key
    $('#search-name').on('keypress', function(e) {
      if (e.which === 13) {
        searchOffset = 0;
        performSearch();
      }
    });
    
    $('#loadMoreSearch').on('click', function() {
      searchOffset += searchLimit;
      performSearch(true);
    });
    
    function performSearch(append = false) {
      const name = $('#search-name').val().trim();
      const course = $('#search-course').val();
      const batch = $('#search-batch').val();
      
      // Show loading
      if (!append) {
        $('#searchResults').html('');
        $('#search-loading').show();
        $('#search-no-results').hide();
        $('#searchMore').hide();
      } else {
        $('#loadMoreSearch').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');
      }
      
      $.ajax({
        url: 'ajax/search_alumni.php',
        method: 'GET',
        data: {
          name: name,
          course: course,
          batch: batch,
          offset: searchOffset,
          limit: searchLimit
        },
        dataType: 'json',
        success: function(response) {
          $('#search-loading').hide();
          
          if (response.success) {
            const results = response.data;
            const total = response.total || 0;
            
            if (results.length > 0) {
              let html = '';
              
              if (!append) {
                html += '<div class="row g-3">';
              }
              
              results.forEach(function(alumni) {
                const initials = alumni.firstname.charAt(0) + alumni.lastname.charAt(0);
                const imgHtml = alumni.img ? 
                  `<img src="uploads/${alumni.img}" class="rounded-circle" style="width:70px;height:70px;object-fit:cover;border:3px solid #ffc107;">` :
                  `<div class="rounded-circle d-flex align-items-center justify-content-center" style="width:70px;height:70px;background:linear-gradient(135deg,#ffc107,#ff9800);color:white;font-weight:bold;font-size:20px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.1);">${initials.toUpperCase()}</div>`;
                
                html += `
                  <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="bio-card h-100 hover-shadow">
                      <div class="d-flex gap-3 align-items-start">
                        ${imgHtml}
                        <div class="flex-grow-1">
                          <h6 class="mb-1" style="color:var(--maroon);">${alumni.firstname} ${alumni.lastname}</h6>
                          <p class="small text-muted mb-1">
                            <i class="fas fa-graduation-cap"></i> ${alumni.course_name || 'N/A'}
                          </p>
                          <p class="small text-muted mb-2">
                            <i class="fas fa-calendar-alt"></i> Batch ${alumni.batch}
                          </p>
                          <div class="d-flex gap-2">
                            <a href="cv.php?id=${alumni.id}" class="btn btn-sm btn-success" target="_blank" title="View CV">
                              <i class="fas fa-file-alt"></i> CV
                            </a>
                            <a href="view_profile.php?id=${alumni.id}" class="btn btn-sm btn-outline-primary" title="View Profile">
                              <i class="fas fa-eye"></i> Profile
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                `;
              });
              
              if (!append) {
                html += '</div>';
                $('#searchResults').html(html);
              } else {
                $('#searchResults .row').append(html);
              }
              
              // Update count
              const currentCount = searchOffset + results.length;
              $('#search-count').text(currentCount + ' of ' + total + ' results');
              
              // Show/hide load more button
              if (currentCount < total) {
                $('#searchMore').show();
                $('#loadMoreSearch').prop('disabled', false).html('<i class="fas fa-chevron-down"></i> Load More Results');
              } else {
                $('#searchMore').hide();
              }
              
              $('#search-no-results').hide();
            } else {
              if (!append) {
                $('#search-no-results').show();
                $('#search-count').text('0 results');
              }
              $('#searchMore').hide();
            }
          } else {
            if (!append) {
              $('#search-no-results').show();
              $('#search-count').text('0 results');
            }
          }
        },
        error: function() {
          $('#search-loading').hide();
          Swal.fire({
            icon: 'error',
            title: 'Search Error',
            text: 'Failed to search alumni. Please try again.',
            confirmButtonColor: '#800000'
          });
        }
      });
    }

    // Help Center: topics and simple search
    const HELP_TOPICS = [
      {cat:'Account & Profile', q:'How do I update my profile?', a:'Go to My Profile and click Edit. You can update your details and upload a profile image.'},
      {cat:'Account & Profile', q:'I forgot my password', a:'Use the Reset Password link on the login page to request a password reset email.'},
      {cat:'Events & Activities', q:'How do I register for events?', a:'Open an event on the Timeline and click Register or contact the event organizer.'},
      {cat:'Events & Activities', q:'Can I propose an alumni event?', a:'Yes — contact the site admin or use the Create Event option if you are an admin.'},
      {cat:'Networking', q:'How do I find my batchmates?', a:'Use the Batchmates or Search features to filter by batch year and course.'},
      {cat:'Networking', q:'How do I join discussion forums?', a:'Go to the Forum page and create or reply to threads relevant to your interests.'},
      {cat:'Career Services', q:'How do I post a job opportunity?', a:'Use the Jobs section (for authorized users) or contact site admin to post job listings.'},
      {cat:'Career Services', q:'How can I find mentorship opportunities?', a:'Search for alumni in the Directory and reach out via their contact details or forum messages.'}
    ];

    function openHelpCenter(){
      Swal.fire({
        title: 'How can we help you?',
        html: `
          <div style="text-align:left;">
            <input id="helpSearch" class="form-control mb-2" placeholder="Search for help topics..." style="width:100%;">
            <div id="helpList" style="max-height:320px;overflow:auto;text-align:left;"></div>
          </div>
        `,
        width: 700,
        showConfirmButton: false,
        didOpen: () => {
          const $list = $('#helpList');
          function render(items){
            if (!items.length) { $list.html('<div class="small-muted">No topics found.</div>'); return; }
            let html = '';
            items.forEach(it => {
              html += `<div style="padding:10px;border-bottom:1px solid rgba(0,0,0,0.04);"><div style="font-weight:700">${it.q}</div><div style="color:var(--muted);margin-top:6px">${it.a}</div><div style="font-size:12px;color:rgba(var(--maroon-r),0.45);margin-top:6px">Category: ${it.cat}</div></div>`;
            });
            $list.html(html);
          }
          render(HELP_TOPICS);
          $('#helpSearch').on('input', function(){
            const v = $(this).val().toLowerCase().trim();
            if (!v) return render(HELP_TOPICS);
            const filtered = HELP_TOPICS.filter(t => (t.q + ' ' + t.a + ' ' + t.cat).toLowerCase().includes(v));
            render(filtered);
          }).focus();
        }
      });
    }

    $('#helpFab').on('click', openHelpCenter);

    // Real-time likes and comments functionality
    $(document).ready(function() {
      // Initialize likes and comments for visible cards
      function initializeCard(userId) {
        // Load initial likes count
        $.post(window.location.href, {ajax_action: 'get_likes', user_id: userId}, function(resp) {
          if (resp.ok) {
            const $likeBtn = $(`.like-btn[data-user-id="${userId}"]`);
            $likeBtn.find('.like-count').text(resp.count);
            if (resp.liked) {
              $likeBtn.removeClass('btn-outline-danger').addClass('btn-danger');
              $likeBtn.find('i').removeClass('fa-regular').addClass('fa-solid');
            }
          }
        });

        // Load initial comments
        $.post(window.location.href, {ajax_action: 'get_comments', user_id: userId}, function(resp) {
          if (resp.ok) {
            $(`.comment-btn[data-user-id="${userId}"] .comment-count`).text(resp.comments.length);
            renderComments(userId, resp.comments);
          }
        });
      }

      // Render comments
      function renderComments(userId, comments) {
        const $commentsList = $(`#comments-${userId} .comments-list`);
        let html = '';
        
        comments.forEach(comment => {
          const userImg = comment.img ? `uploads/${comment.img}` : '';
          const initials = (comment.firstname.charAt(0) + comment.lastname.charAt(0)).toUpperCase();
          const timeAgo = new Date(comment.created_at).toLocaleString();
          
          html += `
            <div class="d-flex gap-2 mb-2">
              ${userImg ? 
                `<img src="${userImg}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;flex-shrink:0;">` :
                `<div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#800000,#600000);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:12px;flex-shrink:0;">${initials}</div>`
              }
              <div class="flex-grow-1">
                <div style="font-weight:600;font-size:0.9rem;">${comment.firstname} ${comment.lastname}</div>
                <div style="font-size:0.85rem;color:#666;margin-bottom:2px;">${timeAgo}</div>
                <div style="font-size:0.9rem;">${comment.comment}</div>
              </div>
            </div>
          `;
        });
        
        $commentsList.html(html || '<div class="small-muted">No comments yet.</div>');
      }

      // Like button click handler
      $(document).on('click', '.like-btn', function() {
        const userId = $(this).data('user-id');
        const $btn = $(this);
        
        $.post(window.location.href, {ajax_action: 'like_user', user_id: userId}, function(resp) {
          if (resp.ok) {
            $btn.find('.like-count').text(resp.count);
            if (resp.liked) {
              $btn.removeClass('btn-outline-danger').addClass('btn-danger');
              $btn.find('i').removeClass('fa-regular').addClass('fa-solid');
            } else {
              $btn.removeClass('btn-danger').addClass('btn-outline-danger');
              $btn.find('i').removeClass('fa-solid').addClass('fa-regular');
            }
          }
        });
      });

      // Comment button click handler
      $(document).on('click', '.comment-btn', function() {
        const userId = $(this).data('user-id');
        const $commentsSection = $(`#comments-${userId}`);
        
        if ($commentsSection.is(':visible')) {
          $commentsSection.slideUp();
        } else {
          $commentsSection.slideDown();
          // Focus on comment input
          $commentsSection.find('.comment-input').focus();
        }
      });

      // Send comment handler
      $(document).on('click', '.send-comment-btn', function() {
        const userId = $(this).data('user-id');
        const $input = $(`.comment-input[data-user-id="${userId}"]`);
        const comment = $input.val().trim();
        
        if (!comment) return;
        
        $.post(window.location.href, {ajax_action: 'add_comment', user_id: userId, comment: comment}, function(resp) {
          if (resp.ok) {
            $input.val('');
            $(`.comment-btn[data-user-id="${userId}"] .comment-count`).text(resp.comments.length);
            renderComments(userId, resp.comments);
          } else {
            alert(resp.msg || 'Error adding comment');
          }
        });
      });

      // Enter key to send comment
      $(document).on('keypress', '.comment-input', function(e) {
        if (e.which === 13) {
          $(this).siblings('.send-comment-btn').click();
        }
      });

      // Initialize cards when search results are loaded
      $(document).on('DOMNodeInserted', function(e) {
        if ($(e.target).hasClass('col-12') && $(e.target).find('.like-btn').length) {
          const userId = $(e.target).find('.like-btn').data('user-id');
          if (userId) {
            setTimeout(() => initializeCard(userId), 100);
          }
        }
      });

      // Initialize existing cards on page load
      $('.like-btn').each(function() {
        const userId = $(this).data('user-id');
        if (userId) {
          initializeCard(userId);
        }
      });

      // Auto-refresh likes and comments every 30 seconds
      setInterval(function() {
        $('.like-btn').each(function() {
          const userId = $(this).data('user-id');
          if (userId) {
            // Only refresh if comments section is visible
            if ($(`#comments-${userId}`).is(':visible')) {
              $.post(window.location.href, {ajax_action: 'get_comments', user_id: userId}, function(resp) {
                if (resp.ok) {
                  $(`.comment-btn[data-user-id="${userId}"] .comment-count`).text(resp.comments.length);
                  renderComments(userId, resp.comments);
                }
              });
            }
            
            // Refresh likes count
            $.post(window.location.href, {ajax_action: 'get_likes', user_id: userId}, function(resp) {
              if (resp.ok) {
                $(`.like-btn[data-user-id="${userId}"] .like-count`).text(resp.count);
              }
            });
          }
        });
      }, 30000);
    });

  })(jQuery);
  </script>
  <!-- Profile preview modal -->
  <div class="modal fade" id="profilePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Profile Preview</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-0" style="height:calc(100vh - 72px)">
          <iframe id="profilePreviewFrame" src="" style="width:100%;height:100%;border:0;border-radius:0"></iframe>
        </div>
        <div class="modal-footer">
          <div class="me-auto d-flex align-items-center gap-2">
            <button id="profileCopyLink" class="btn btn-outline-secondary btn-sm">Copy link</button>
            <button id="profileShowQR" class="btn btn-outline-secondary btn-sm">Show QR</button>
            <div id="profileQRWrap" style="display:none;width:88px;height:88px;padding:6px;background:var(--card);border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.08)"></div>
          </div>
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Avatar preview modal (small) -->
  <div class="modal fade" id="avatarPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content text-center p-3">
        <div class="modal-body">
          <img id="avatarPreviewImg" src="" alt="avatar" style="width:160px;height:160px;border-radius:50%;object-fit:cover;box-shadow:0 12px 36px rgba(2,6,23,0.12);">
          <div class="mt-3">
            <a href="#" id="viewFullProfileFromAvatar" class="btn btn-primary btn-sm me-2">View Profile</a>
            <button id="avatarCopyLink" class="btn btn-outline-secondary btn-sm me-2">Copy link</button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  (function($){
    // Open profile preview (current user)
  $('#previewMe').on('click', function(e){ e.preventDefault(); var uid = '<?php echo intval($userId); ?>'; var url = 'view_profile.php?id='+uid+'&embed=1'; $('#profilePreviewFrame').attr('src', url); $('#profilePreviewModal').data('profileUrl', 'view_profile.php?id='+uid); var m = new bootstrap.Modal(document.getElementById('profilePreviewModal')); m.show(); });

    // theme toggle with icon
    function applyTheme(t){ const $icon = $('#themeIcon'); if (t === 'dark') {
        // Neutral dark background (not maroon), high-contrast white text
        const darkBg = '#071018'; // deep charcoal/blue-black for dark mode
        document.documentElement.style.setProperty('--bg', darkBg);
        document.documentElement.style.setProperty('--card', darkBg);
        document.documentElement.style.setProperty('--text', 'var(--white)');
        document.documentElement.style.setProperty('--muted', 'rgba(255,255,255,0.75)');
        document.body.style.background = darkBg;
        document.body.style.color = 'var(--white)';
        $icon.removeClass('fa-sun').addClass('fa-moon');
        $('#themeToggle').attr('aria-pressed','true');
      } else {
        // White background, maroon text (default)
        document.documentElement.style.setProperty('--bg', 'var(--white)');
        document.documentElement.style.setProperty('--card', 'var(--white)');
        document.documentElement.style.setProperty('--text', 'var(--maroon)');
        document.documentElement.style.setProperty('--muted', 'rgba(var(--maroon-r),0.65)');
        document.body.style.background = 'var(--white)';
        document.body.style.color = 'var(--maroon)';
        $icon.removeClass('fa-moon').addClass('fa-sun');
        $('#themeToggle').attr('aria-pressed','false');
      } }
    var theme = localStorage.getItem('alumni_theme') || 'light'; applyTheme(theme);
    $('#themeToggle').on('click', function(){ theme = (theme === 'dark') ? 'light' : 'dark'; localStorage.setItem('alumni_theme', theme); applyTheme(theme); });

    // avatar preview: open modal with current avatar src
    $('#profileAvatar, #profileAvatar').on('click', function(e){ e.preventDefault(); const src = $(this).attr('src') || $(this).find('img').attr('src'); if (!src) return; $('#avatarPreviewImg').attr('src', src); $('#viewFullProfileFromAvatar').attr('href','view_profile.php?id=<?php echo intval($userId); ?>'); $('#avatarPreviewModal').data('profileUrl','view_profile.php?id=<?php echo intval($userId); ?>'); var m = new bootstrap.Modal(document.getElementById('avatarPreviewModal')); m.show(); });

    // Copy link and QR generation for profile preview
    function copyToClipboard(text){
      try{ navigator.clipboard.writeText(text); return true; }catch(e){
        // fallback
        var ta = document.createElement('textarea'); ta.value = text; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); document.body.removeChild(ta); return true;}catch(e2){ document.body.removeChild(ta); return false; }
      }
    }

    // tiny QR generator using canvas + QR code library (minimal implementation)
    function renderQR(container, text, size){
      container.innerHTML = '';
      var canvas = document.createElement('canvas'); canvas.width = size; canvas.height = size;
      container.appendChild(canvas);
      // use simple library (qrious-like minimal) - generate basic QR via external small algorithm
      // We'll use a lightweight implementation: create a temporary img using Google Charts API as fallback if allowed; but to avoid external calls, attempt to use third-party-free approach: fallback to using data URL from Google Charts if allowed.
      try{
        // Prefer to draw using an offscreen QR generator if present (not included). Fallback: use chart.googleapis.com
        var src = 'https://chart.googleapis.com/chart?cht=qr&chs='+size+'x'+size+'&chl='+encodeURIComponent(text)+'&chld=L|1';
        var img = new Image(); img.crossOrigin = 'anonymous'; img.onload = function(){
          var ctx = canvas.getContext('2d'); ctx.drawImage(img,0,0);
        };
        img.onerror = function(){ container.innerHTML = '<div style="font-size:12px;">QR not available</div>'; };
        img.src = src;
      }catch(e){ container.innerHTML = '<div style="font-size:12px;">QR not available</div>'; }
    }

    $('#profileCopyLink').on('click', function(){ var url = $('#profilePreviewModal').data('profileUrl') || $('#profilePreviewFrame').attr('src'); if (!url) return; if (copyToClipboard(url)) { alert('Link copied to clipboard'); } else alert('Copy failed'); });
    $('#avatarCopyLink').on('click', function(){ var url = $('#avatarPreviewModal').data('profileUrl'); if (!url) return; if (copyToClipboard(url)) { alert('Link copied to clipboard'); } else alert('Copy failed'); });
    $('#profileShowQR').on('click', function(){ var url = $('#profilePreviewModal').data('profileUrl') || $('#profilePreviewFrame').attr('src'); if (!url) return; var wrap = document.getElementById('profileQRWrap'); if (!wrap) return; if (wrap.style.display === 'none') { renderQR(wrap, location.origin + '/' + url, 256); wrap.style.display = 'block'; } else { wrap.style.display = 'none'; } });
  })(jQuery);
  </script>
  <script>
  (function($){
    // double-click to like with heart animation
    $(document).on('dblclick', '.card-tile .banner', function(){
      const $card = $(this).closest('.card-tile');
      const eventId = $card.find('.card-data').data('event-id');
      // animate heart
      const $heart = $card.find('.heart-overlay');
      $heart.css('opacity',1).addClass('pop');
      $heart.show().removeClass('pop'); // trigger animation via CSS
      void $heart[0].offsetWidth; // force reflow
      $heart.addClass('pop');
      // trigger like via AJAX
      const csrf = '<?php echo $csrf_token; ?>';
      $.post(window.location.href, { ajax_action:'toggle_like', event_id:eventId, csrf_token:csrf }, function(resp){
        if (resp.ok) {
          const $btn = $('.like-toggle[data-event="'+eventId+'"]');
          $btn.find('.likes-count').text(resp.count || 0);
          const icon = $btn.find('i'); if (resp.liked) icon.removeClass('fa-regular').addClass('fa-solid'); else icon.removeClass('fa-solid').addClass('fa-regular');
        }
      }, 'json');
      setTimeout(function(){ $heart.fadeOut(400, function(){ $(this).removeClass('pop'); }); }, 900);
    });

    // share button: open native share when available or copy link
    $(document).on('click', '.share-btn', function(){
      const eventId = $(this).data('event');
      const shareUrl = window.location.origin + window.location.pathname + '?event=' + eventId;
      if (navigator.share) {
        navigator.share({ title: document.title, url: shareUrl }).catch(()=>{});
      } else {
        // copy to clipboard fallback
        const ta = document.createElement('textarea'); ta.value = shareUrl; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
        alert('Event link copied to clipboard');
      }
    });

    // small UX: clicking the avatar in header opens my profile modal if present otherwise navigates
    $('#profileAvatar').on('click', function(e){ e.preventDefault(); const href = $(this).closest('a').attr('href'); if ($('#profileModal').length){ $('#profileModal').modal('show'); } else window.location = href; });
  })(jQuery);
  </script>
  
  <script>
  (function($){
    // Jobs functionality
    let jobsPage = 1, jobsLoading = false, jobsLoaded = false;
    
    // Load jobs function
    function loadJobs(page) {
      if (jobsLoading) return;
      jobsLoading = true;
      
      // Show loading state
      if (page === 1) {
        $('#jobsContainer').html(`
          <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Loading job postings...</p>
          </div>
        `);
        $('#noJobsMessage').hide();
      }

      $.post(window.location.href, {
        ajax_action: 'fetch_jobs',
        page: page
      }, function(response) {
        jobsLoading = false;

        if (response.ok) {
          if (page === 1) {
            $('#jobsContainer').html(response.data.html);
          } else {
            $('#jobsContainer').append(response.data.html);
          }

          if (response.data.hasMore) {
            $('#loadMoreJobsContainer').show();
          } else {
            $('#loadMoreJobsContainer').hide();
          }

          if (response.data.html.includes('noJobsAlert')) {
            $('#noJobsMessage').show();
          }

          jobsLoaded = true;
        } else {
          console.error('Jobs loading error:', response.msg);
          $('#jobsContainer').html('<div class="alert alert-danger">Error loading jobs: ' + response.msg + '</div>');
        }
      }, 'json').fail(function(xhr, status, error) {
        jobsLoading = false;
        console.log('Jobs AJAX Error:', status, error);
        console.log('Response:', xhr.responseText);

        let errorMessage = 'Network error loading jobs. Please try again.';
        if (xhr.responseText && xhr.responseText.indexOf('<') === 0) {
          errorMessage += '<br><small>This usually means there\'s a server configuration issue. Please check the server logs.</small>';
        }
        errorMessage += '<br><small>Error: ' + error + '</small>';

        $('#jobsContainer').html('<div class="alert alert-danger">' + errorMessage + '</div>');
      });
    }
    
    // Load jobs when Jobs tab is shown
    $('button[data-bs-target="#jobs"]').on('shown.bs.tab', function() {
      if (!jobsLoaded) {
        loadJobs(1);
      }
    });
    
    // Refresh jobs button
    $('#refreshJobs').on('click', function() {
      jobsLoaded = false;
      jobsPage = 1;
      loadJobs(1);
    });
    
    // Load more jobs button
    $('#loadMoreJobs').on('click', function() {
      jobsPage++;
      loadJobs(jobsPage);
    });
    
    // View job details
    $(document).on('click', '.view-job-btn', function() {
      const jobId = $(this).data('job-id');
      showJobDetails(jobId);
    });
    
    function showJobDetails(jobId) {
      $('#jobDetailsContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
      
      const modal = new bootstrap.Modal(document.getElementById('jobDetailsModal'));
      modal.show();
      
      $.post(window.location.href, {
        ajax_action: 'get_job_details',
        job_id: jobId
      }, function(response) {
        if (response.ok) {
          $('#jobDetailsContent').html(response.html);
        } else {
          $('#jobDetailsContent').html('<div class="alert alert-danger">Error loading job details: ' + response.msg + '</div>');
        }
      }, 'json').fail(function() {
        $('#jobDetailsContent').html('<div class="alert alert-danger">Error loading job details. Please try again.</div>');
      });
    }
    
  })(jQuery);
  </script>
  
  <script>
  (function($){
    // Post modals
    let createPostModal, editPostModal;
    
    // ============================================
    // POST CRUD OPERATIONS (Facebook-Style)
    // ============================================
    
    // Open create post modal
    $('#openPostModal, #postTextBtn').on('click', function() {
      if (!createPostModal) {
        createPostModal = new bootstrap.Modal(document.getElementById('createPostModal'));
      }
      $('#createPostForm')[0].reset();
      $('#imagePreviewContainer').hide();
      createPostModal.show();
    });
    
    // Media preview for create post (image or video)
    $('#postImage').on('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Check file size (25MB limit)
        const maxSize = 25 * 1024 * 1024; // 25MB
        if (file.size > maxSize) {
          Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'File size exceeds 25MB limit. Please choose a smaller file.',
            confirmButtonColor: '#800000'
          });
          $(this).val('');
          return;
        }
        
        const fileType = file.type.split('/')[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const previewContainer = $('#imagePreviewContainer');
          const previewElement = $('#imagePreview');
          
          if (fileType === 'video') {
            // Replace img with video element
            previewElement.replaceWith(`
              <video id="imagePreview" class="img-fluid rounded" style="max-height:300px;" controls>
                <source src="${e.target.result}" type="${file.type}">
                Your browser does not support video playback.
              </video>
            `);
          } else {
            // Ensure it's an img element
            if (previewElement.prop('tagName') === 'VIDEO') {
              previewElement.replaceWith('<img id="imagePreview" class="img-fluid rounded" style="max-height:300px;">');
            }
            $('#imagePreview').attr('src', e.target.result);
          }
          previewContainer.show();
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Remove image preview
    $('#removeImage').on('click', function() {
      $('#postImage').val('');
      $('#imagePreviewContainer').hide();
    });
    
    // Submit new post with image
    $('#createPostForm').on('submit', function(e) {
      e.preventDefault();
      
      const content = $('#postContent').val().trim();
      if (!content) {
        Swal.fire({
          icon: 'warning',
          title: 'Empty Post',
          text: 'Please enter some content for your post.',
          confirmButtonColor: '#800000'
        });
        return;
      }
      
      const formData = new FormData(this);
      formData.append('ajax_action', 'create_post');
      
      $('#submitPost').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Posting...');
      
      $.ajax({
        url: window.location.href,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
          if (response.ok) {
            Swal.fire({
              icon: 'success',
              title: 'Posted!',
              text: 'Your post has been published successfully.',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
            }).then(() => {
              $('#createPostForm')[0].reset();
              $('#imagePreviewContainer').hide();
              createPostModal.hide();
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.msg || 'Failed to create post',
              confirmButtonColor: '#800000'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Please check your connection and try again.',
            confirmButtonColor: '#800000'
          });
        },
        complete: function() {
          $('#submitPost').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Post');
        }
      });
    });
    
    // Edit post - open modal
    window.editPost = function(postId, content, image) {
      $('#editPostId').val(postId);
      $('#editPostContent').val(content);
      
      // Show current image if exists
      if (image) {
        $('#currentImage').attr('src', image);
        $('#currentImageContainer').show();
      } else {
        $('#currentImageContainer').hide();
      }
      
      $('#editImagePreviewContainer').hide();
      
      if (!editPostModal) {
        editPostModal = new bootstrap.Modal(document.getElementById('editPostModal'));
      }
      editPostModal.show();
    };
    
    // Media preview for edit post (image or video)
    $('#editPostImage').on('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Check file size (25MB limit)
        const maxSize = 25 * 1024 * 1024; // 25MB
        if (file.size > maxSize) {
          Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'File size exceeds 25MB limit. Please choose a smaller file.',
            confirmButtonColor: '#800000'
          });
          $(this).val('');
          return;
        }
        
        const fileType = file.type.split('/')[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          const previewContainer = $('#editImagePreviewContainer');
          const previewElement = $('#editImagePreview');
          
          if (fileType === 'video') {
            // Replace img with video element
            previewElement.replaceWith(`
              <video id="editImagePreview" class="img-fluid rounded" style="max-height:300px;" controls>
                <source src="${e.target.result}" type="${file.type}">
                Your browser does not support video playback.
              </video>
            `);
          } else {
            // Ensure it's an img element
            if (previewElement.prop('tagName') === 'VIDEO') {
              previewElement.replaceWith('<img id="editImagePreview" class="img-fluid rounded" style="max-height:300px;">');
            }
            $('#editImagePreview').attr('src', e.target.result);
          }
          previewContainer.show();
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Remove current image
    $('#removeCurrentImage').on('click', function() {
      $('#currentImageContainer').hide();
      // Add hidden field to mark image for deletion
      if (!$('#removeImageFlag').length) {
        $('#editPostForm').append('<input type="hidden" id="removeImageFlag" name="remove_image" value="1">');
      }
    });
    
    // Remove new image preview
    $('#removeEditImage').on('click', function() {
      $('#editPostImage').val('');
      $('#editImagePreviewContainer').hide();
    });
    
    // Submit edit post
    $('#editPostForm').on('submit', function(e) {
      e.preventDefault();
      
      const content = $('#editPostContent').val().trim();
      if (!content) {
        Swal.fire({
          icon: 'warning',
          title: 'Empty Post',
          text: 'Please enter some content.',
          confirmButtonColor: '#800000'
        });
        return;
      }
      
      const formData = new FormData(this);
      formData.append('ajax_action', 'edit_post');
      
      $('#updatePost').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
      
      $.ajax({
        url: window.location.href,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
          if (response.ok) {
            Swal.fire({
              icon: 'success',
              title: 'Updated!',
              text: 'Your post has been updated successfully.',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
            }).then(() => {
              editPostModal.hide();
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.msg || 'Failed to update post',
              confirmButtonColor: '#800000'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Please check your connection and try again.',
            confirmButtonColor: '#800000'
          });
        },
        complete: function() {
          $('#updatePost').prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
        }
      });
    });
    
    // Delete post
    window.deletePost = function(postId) {
      Swal.fire({
        title: 'Delete Post?',
        text: 'Are you sure you want to delete this post? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Yes, delete it',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading
          Swal.fire({
            title: 'Deleting...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          $.post(window.location.href, {
            ajax_action: 'delete_post',
            post_id: postId
          }, function(response) {
            if (response.ok) {
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Your post has been deleted.',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msg || 'Failed to delete post',
                confirmButtonColor: '#800000'
              });
            }
          }, 'json').fail(function() {
            Swal.fire({
              icon: 'error',
              title: 'Network Error',
              text: 'Please check your connection and try again.',
              confirmButtonColor: '#800000'
            });
          });
        }
      });
    };
    
    // Helper functions for post options
    window.addFeeling = function() {
      Swal.fire({
        title: 'How are you feeling?',
        input: 'select',
        inputOptions: {
          'happy': '😊 Happy',
          'excited': '🎉 Excited',
          'blessed': '🙏 Blessed',
          'loved': '❤️ Loved',
          'grateful': '🙌 Grateful',
          'motivated': '💪 Motivated',
          'relaxed': '😌 Relaxed',
          'sad': '😢 Sad',
          'tired': '😴 Tired'
        },
        confirmButtonColor: '#800000',
        showCancelButton: true
      }).then((result) => {
        if (result.isConfirmed && result.value) {
          const feeling = result.value;
          const currentText = $('#postContent').val();
          $('#postContent').val(currentText + ' — feeling ' + feeling);
        }
      });
    };
    
    window.tagPeople = function() {
      Swal.fire({
        icon: 'info',
        title: 'Tag People',
        text: 'This feature is coming soon!',
        confirmButtonColor: '#800000'
      });
    };
    
    // Delete post
    $(document).on('click', '.delete-post', function(e) {
      e.preventDefault();
      const postId = $(this).data('post-id');
      
      if (confirm('Are you sure you want to delete this post?')) {
        $.post(window.location.href, {
          ajax_action: 'delete_post',
          post_id: postId
        }, function(response) {
          if (response.ok) {
            alert('Post deleted successfully!');
            location.reload();
          } else {
            alert('Error: ' + response.msg);
          }
        }, 'json').fail(function() {
          alert('Network error. Please try again.');
        });
      }
    });
    
  })(jQuery);
  </script>
</body>
</html>
