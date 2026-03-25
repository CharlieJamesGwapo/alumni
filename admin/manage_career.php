<?php include 'db_connect.php' ?>
<?php
$id = '';
$company = '';
$job_title = '';
$location = '';
$description = '';
$salary = '';
$job_type = 'Full-time';
$image = '';

if(isset($_GET['id'])){
	$id = intval($_GET['id']);
	$stmt = $conn->prepare("SELECT * FROM careers WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$qry = $stmt->get_result()->fetch_assoc();
	if($qry) {
		$company = $qry['company'] ?? '';
		$job_title = $qry['job_title'] ?? '';
		$location = $qry['location'] ?? '';
		$description = $qry['description'] ?? '';
		$salary = $qry['salary'] ?? '';
		$job_type = $qry['job_type'] ?? 'Full-time';
		$image = $qry['image'] ?? '';
	}
}
?>
<div class="container-fluid">
	<form action="" id="manage-career" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

		<div class="row">
			<div class="col-md-8">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Company <span class="text-danger">*</span></label>
					<input type="text" name="company" class="form-control" required value="<?php echo htmlspecialchars($company); ?>" placeholder="e.g. Google Philippines">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Job Type</label>
					<select name="job_type" class="form-control">
						<option value="Full-time" <?php echo $job_type === 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
						<option value="Part-time" <?php echo $job_type === 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
						<option value="Contract" <?php echo $job_type === 'Contract' ? 'selected' : ''; ?>>Contract</option>
						<option value="Freelance" <?php echo $job_type === 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
						<option value="Internship" <?php echo $job_type === 'Internship' ? 'selected' : ''; ?>>Internship</option>
						<option value="Remote" <?php echo $job_type === 'Remote' ? 'selected' : ''; ?>>Remote</option>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Job Title <span class="text-danger">*</span></label>
					<input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($job_title); ?>" placeholder="e.g. Software Engineer">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Location</label>
					<input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>" placeholder="e.g. Cagayan de Oro">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Salary Range</label>
					<input type="text" name="salary" class="form-control" value="<?php echo htmlspecialchars($salary); ?>" placeholder="e.g. ₱25,000 - ₱40,000">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Job Image/Banner</label>
					<input type="file" name="job_image" id="job_image" class="form-control" accept="image/*">
					<small class="text-muted">Optional. Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
					<?php if($image && file_exists('../uploads/jobs/' . $image)): ?>
					<div class="mt-2" id="current-image-preview">
						<img src="../uploads/jobs/<?php echo htmlspecialchars($image); ?>" alt="Current image" style="max-height:120px;border-radius:8px;border:2px solid #ddd;">
						<br><small class="text-success">Current image</small>
						<button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeImage()">Remove</button>
						<input type="hidden" name="remove_image" id="remove_image" value="0">
					</div>
					<?php endif; ?>
					<div id="image-preview" class="mt-2" style="display:none;">
						<img id="preview-img" src="" alt="Preview" style="max-height:120px;border-radius:8px;border:2px solid #ddd;">
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="form-group mb-3">
					<label class="control-label fw-bold">Description</label>
					<textarea name="description" class="text-jqte"><?php echo $description; ?></textarea>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
	$('.text-jqte').jqte();

	// Image preview
	$('#job_image').on('change', function(){
		var file = this.files[0];
		if(file){
			if(file.size > 5 * 1024 * 1024){
				alert('Image must be under 5MB');
				this.value = '';
				return;
			}
			var reader = new FileReader();
			reader.onload = function(e){
				$('#preview-img').attr('src', e.target.result);
				$('#image-preview').show();
			};
			reader.readAsDataURL(file);
		} else {
			$('#image-preview').hide();
		}
	});

	function removeImage(){
		$('#remove_image').val('1');
		$('#current-image-preview').hide();
	}

	$('#manage-career').submit(function(e){
		e.preventDefault();
		start_load();

		var formData = new FormData(this);

		$.ajax({
			url:'ajax.php?action=save_career',
			method:'POST',
			data: formData,
			contentType: false,
			processData: false,
			success:function(resp){
				if(resp == 1){
					alert_toast("Job successfully saved.",'success');
					setTimeout(function(){
						location.reload();
					},1000);
				} else {
					alert_toast("Failed to save job.",'danger');
					end_load();
				}
			},
			error:function(){
				alert_toast("Server error.",'danger');
				end_load();
			}
		});
	});
</script>
