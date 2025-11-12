<?php
// directory.php
// Public alumni directory: lists all alumni with name, course, batch, avatar.
// AJAX search endpoint (POST with action=search) returns HTML results.

session_start();
include 'admin/db_connect.php';
define('UPLOAD_DIR', __DIR__ . '/uploads/');

function avatar_url($img){
    if (!empty($img) && file_exists(__DIR__.'/uploads/'.$img)) return 'uploads/' . rawurlencode($img);
    return 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=640&q=60&auto=format&fit=crop';
}

// If AJAX search request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    $name = trim($_POST['name'] ?? '');
    $course = intval($_POST['course'] ?? 0);
    $batch = trim($_POST['batch'] ?? '');

    $sql = "SELECT a.id, a.firstname, a.lastname, a.img, a.batch, c.course as course_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id WHERE 1=1";
    $params = []; $types = '';
    if ($name !== '') { $sql .= " AND (a.firstname LIKE CONCAT('%',?,'%') OR a.lastname LIKE CONCAT('%',?,'%') OR a.alumni_id LIKE CONCAT('%',?,'%'))"; $params[] = $name; $params[] = $name; $params[] = $name; $types .= 'sss'; }
    if ($course) { $sql .= " AND a.course_id = ?"; $params[] = $course; $types .= 'i'; }
    if ($batch !== '') { $sql .= " AND a.batch = ?"; $params[] = $batch; $types .= 's'; }
    $sql .= " ORDER BY a.lastname, a.firstname LIMIT 200";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $bind_names[] = $types;
        for ($i=0;$i<count($params);$i++) $bind_names[] = &$params[$i];
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) { echo '<div class="alert alert-secondary">No results.</div>'; exit; }
    echo '<div class="row g-3">';
    while ($r = $res->fetch_assoc()){
        echo '<div class="col-md-3 col-sm-6">';
        echo '<div class="card h-100 text-center" style="padding:12px;border-radius:10px;">';
        echo '<a href="view_profile.php?id='.intval($r['id']).'"><img src="'.htmlspecialchars(avatar_url($r['img'])).'" style="width:88px;height:88px;border-radius:50%;object-fit:cover;margin:auto;display:block"></a>';
        echo '<div style="margin-top:8px;font-weight:700;color:#2D6CDF">'.htmlspecialchars($r['firstname'].' '.$r['lastname']).'</div>';
        echo '<div class="small text-muted">'.htmlspecialchars($r['course_name']).'</div>';
        echo '<div class="small text-danger">Batch '.htmlspecialchars($r['batch']).'</div>';
        echo '<div class="mt-2"><a class="btn btn-sm btn-outline-primary" href="view_profile.php?id='.intval($r['id']).'">View</a></div>';
        echo '</div></div>';
    }
    echo '</div>';
    exit;
}

// Page load: get courses and batches for filters
$courses = $conn->query("SELECT id, course FROM courses ORDER BY course ASC");
$batch_years = []; $bres = $conn->query("SELECT DISTINCT batch FROM alumnus_bio ORDER BY batch DESC"); while ($b = $bres->fetch_assoc()) $batch_years[] = $b['batch'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Alumni Directory - AlumniGram</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<style>
body{font-family:Inter,system-ui, -apple-system, 'Segoe UI', Roboto, Arial;background:#f7fbff;color:#0b1220}
.container-main{max-width:1100px;margin:24px auto;padding:0 16px 60px;}
.panel{background:#fff;padding:14px;border-radius:10px;box-shadow:0 6px 20px rgba(2,6,23,0.04)}
.card-tile{border:none}
</style>
</head>
<body>
<nav class="navbar navbar-light bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="directory.php">AlumniGram</a>
    <div>
      <a href="my_profile.php" class="btn btn-outline-primary btn-sm me-2">My Profile</a>
      <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<main class="container-main">
  <div class="panel mb-3">
    <h5 class="mb-2">Alumni Directory</h5>
    <div class="row g-2 align-items-center">
      <div class="col-md-5">
        <input id="qName" class="form-control" placeholder="Search by name or alumni ID">
      </div>
      <div class="col-md-3">
        <select id="qCourse" class="form-select"><option value="">All Courses</option><?php $courses->data_seek(0); while($c = $courses->fetch_assoc()): ?><option value="<?php echo intval($c['id']); ?>"><?php echo htmlspecialchars($c['course']); ?></option><?php endwhile; ?></select>
      </div>
      <div class="col-md-2">
        <select id="qBatch" class="form-select"><option value="">All Batches</option><?php foreach($batch_years as $by): ?><option><?php echo htmlspecialchars($by); ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-2">
        <button id="searchBtn" class="btn btn-primary w-100"><i class="fa fa-search"></i> Search</button>
      </div>
    </div>
  </div>

  <div id="results" class="panel">
    <div class="small-muted">Type a name or filter and click Search to find alumni.</div>
  </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
  function runSearch(){
    $('#results').html('<div class="text-muted">Searching...</div>');
    $.post('directory.php', {action:'search', name: $('#qName').val(), course: $('#qCourse').val(), batch: $('#qBatch').val()}, function(html){
      $('#results').html(html);
    }).fail(function(){ $('#results').html('<div class="text-danger">Search failed.</div>'); });
  }
  $('#searchBtn').on('click', runSearch);
  // realtime search as you type (debounced)
  let timer = null;
  $('#qName').on('input', function(){ clearTimeout(timer); timer = setTimeout(runSearch, 500); });
});
</script>
</body>
</html>
