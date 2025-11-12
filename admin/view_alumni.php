<?php
include('db_connect.php');

if (!isset($conn) || !$conn) {
    echo '<div class="container"><div class="alert alert-danger">Database connection error.</div></div>';
    exit;
}

// accept id from GET or POST (AJAX/modal may send either)
$id = null;
if (isset($_GET['id']) && $_GET['id'] !== '') $id = intval($_GET['id']);
elseif (isset($_POST['id']) && $_POST['id'] !== '') $id = intval($_POST['id']);
elseif (isset($_REQUEST['id']) && $_REQUEST['id'] !== '') $id = intval($_REQUEST['id']);

if ($id === null) {
    echo '<div class="container"><div class="alert alert-danger">Invalid request. Missing ID.</div></div>';
    exit;
}

$sql = "SELECT a.*, c.*, s.*, m.*,
           CONCAT_WS(' ', a.firstname, a.middlename, a.lastname) as fullname
        FROM alumnus_bio a
        LEFT JOIN courses c ON c.id = a.course_id
        LEFT JOIN strands s ON s.id = a.strand_id
        LEFT JOIN majors m ON m.id = a.major_id
        WHERE a.id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo '<div class="container"><div class="alert alert-danger">Query prepare failed.</div></div>';
    exit;
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo '<div class="container"><div class="alert alert-warning">Record not found.</div></div>';
    exit;
}
$row = $result->fetch_assoc();

// resolve upload paths (robust fallback if realpath fails)
$upload_dir = realpath(__DIR__ . '/../uploads');
if ($upload_dir === false) $upload_dir = __DIR__ . '/../uploads';
$upload_dir = rtrim($upload_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$upload_url = '../uploads/';
$default_img = $upload_url . '1602813060_no-image-available.png';

$img_url = $default_img;
if (!empty($row['img'])) {
    $bn = basename($row['img']); // ensure we only use filename
    $candidate = $upload_dir . $bn;
    if (file_exists($candidate) && is_file($candidate)) {
        $img_url = $upload_url . rawurlencode($bn);
    }
}
if ($img_url === $default_img && !empty($row['avatar'])) {
    $bn = basename($row['avatar']);
    $candidate = $upload_dir . $bn;
    if (file_exists($candidate) && is_file($candidate)) {
        $img_url = $upload_url . rawurlencode($bn);
    }
}

// Resolve course/strand/major display (handles different schema column names)
$course_val = '';
foreach (['course','course_name','title','name'] as $k) {
    if (!empty($row[$k])) { $course_val = trim($row[$k]); break; }
}
$strand_val = '';
foreach (['strand','strand_name'] as $k) {
    if (!empty($row[$k])) { $strand_val = trim($row[$k]); break; }
}
$major_val = '';
foreach (['major','major_name'] as $k) {
    if (!empty($row[$k])) { $major_val = trim($row[$k]); break; }
}

$course_strand = '';
if ($course_val !== '') {
    $course_strand = htmlspecialchars($course_val);
    if ($strand_val !== '') $course_strand .= ' / ' . htmlspecialchars($strand_val);
    elseif ($major_val !== '') $course_strand .= ' / ' . htmlspecialchars($major_val);
} else {
    if ($strand_val !== '') $course_strand = htmlspecialchars($strand_val);
    elseif ($major_val !== '') $course_strand = htmlspecialchars($major_val);
    else $course_strand = 'N/A';
}

// Small presentable output (used inside uni_modal)
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
            <img src="<?php echo htmlspecialchars($img_url); ?>" alt="photo" class="img-fluid" style="max-width:160px;height:160px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
        </div>
        <div class="col-12 col-md-9">
            <h4 class="mb-1"><?php echo ucwords(htmlspecialchars($row['fullname'] ?? ($row['firstname'].' '.$row['lastname']))); ?></h4>
            <div class="row">
                <div class="col-12 col-sm-6">
                    <p class="mb-1"><strong>ID:</strong> <?php echo htmlspecialchars($row['alumni_id'] ?? ''); ?></p>
                    <p class="mb-1"><strong>Course / Strand:</strong> <?php echo $course_strand; ?></p>
                    <p class="mb-1"><strong>Batch:</strong> <?php echo htmlspecialchars($row['batch'] ?? ''); ?></p>
                </div>
                <div class="col-12 col-sm-6">
                    <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($row['email'] ?? $row['company_email'] ?? ''); ?></p>
                    <p class="mb-1"><strong>Gender:</strong> <?php echo htmlspecialchars($row['gender'] ?? ''); ?></p>
                    <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_no'] ?? ''); ?></p>
                </div>
                <div class="col-12 mt-2">
                    <p class="mb-0"><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($row['address'] ?? '')); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
//
