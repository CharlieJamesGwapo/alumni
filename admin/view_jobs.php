<?php include 'db_connect.php' ?>
<?php
$job = null;
if(isset($_GET['id'])){
	$id_param = intval($_GET['id']);
	$stmt = $conn->prepare("SELECT c.*, COALESCE(u.name, 'Admin') as poster_name FROM careers c LEFT JOIN users u ON u.id = c.user_id WHERE c.id = ?");
	$stmt->bind_param("i", $id_param);
	$stmt->execute();
	$job = $stmt->get_result()->fetch_assoc();
	$stmt->close();
}
if(!$job){
	echo '<div class="alert alert-warning">Job not found.</div>';
	exit;
}
$img_path = '../uploads/jobs/' . ($job['image'] ?? '');
$has_image = !empty($job['image']) && file_exists($img_path);
?>
<div class="container-fluid">
	<?php if($has_image): ?>
	<div class="text-center mb-3">
		<img src="<?php echo htmlspecialchars($img_path); ?>" alt="Job Banner" style="max-width:100%;max-height:250px;object-fit:cover;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,0.1);">
	</div>
	<?php endif; ?>

	<h5 style="color:#800000;font-weight:700;margin-bottom:4px;"><?php echo htmlspecialchars(ucwords($job['job_title'])); ?></h5>
	<p style="font-size:1.05rem;font-weight:600;color:#555;margin-bottom:8px;"><?php echo htmlspecialchars(ucwords($job['company'])); ?></p>

	<div class="d-flex flex-wrap gap-3 mb-3" style="font-size:0.9rem;">
		<?php if(!empty($job['location'])): ?>
		<span><i class="fa fa-map-marker-alt text-danger"></i> <?php echo htmlspecialchars($job['location']); ?></span>
		<?php endif; ?>
		<?php if(!empty($job['job_type'])): ?>
		<span><i class="fa fa-briefcase text-primary"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
		<?php endif; ?>
		<?php if(!empty($job['salary'])): ?>
		<span><i class="fa fa-money-bill-wave text-success"></i> <?php echo htmlspecialchars($job['salary']); ?></span>
		<?php endif; ?>
	</div>

	<div class="d-flex justify-content-between text-muted mb-3" style="font-size:0.8rem;">
		<span><i class="fa fa-user"></i> Posted by: <b><?php echo htmlspecialchars($job['poster_name']); ?></b></span>
		<span><i class="fa fa-calendar"></i> <?php echo date('F j, Y', strtotime($job['date_created'])); ?></span>
	</div>

	<hr>
	<div style="line-height:1.7;">
		<?php echo nl2br(html_entity_decode($job['description'])); ?>
	</div>
</div>

<style>
	#uni_modal .modal-footer { display: none; }
</style>
