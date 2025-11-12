<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * from system_settings limit 1");
if($qry->num_rows > 0){
	foreach($qry->fetch_array() as $k => $val){
		$meta[$k] = $val;
	}
}
 ?>
<div class="container-fluid">
	
	<div class="card col-lg-12">
		<div class="card-body">
			<form action="" id="manage-settings">
				<div class="form-group">
					<label for="name" class="control-label">System Name</label>
					<input type="text" class="form-control" id="name" name="name" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
				</div>
				<div class="form-group">
					<label for="email" class="control-label">Email</label>
					<input type="email" class="form-control" id="email" name="email" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required>
				</div>
				<div class="form-group">
					<label for="contact" class="control-label">Contact</label>
					<input type="text" class="form-control" id="contact" name="contact" value="<?php echo isset($meta['contact']) ? $meta['contact'] : '' ?>" required>
				</div>
				<div class="form-group">
					<label for="about" class="control-label">About Content</label>
					<textarea name="about" class="text-jqte"><?php echo isset($meta['about_content']) ? $meta['about_content'] : '' ?></textarea>

				</div>
				<div class="form-group">
					<label for="" class="control-label">Image</label>
					<input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
				</div>
				<div class="form-group">
					<img src="<?php echo isset($meta['cover_img']) ? 'assets/uploads/'.$meta['cover_img'] :'' ?>" alt="" id="cimg">
				</div>
				<center>
					<button class="btn btn-info btn-primary btn-block col-md-2">Save</button>
				</center>
			</form>
		</div>
	</div>
	
	<!-- Database Restore Section -->
	<div class="card col-lg-12 mt-3">
		<div class="card-body">
			<h4><i class="fa fa-database"></i> Database Restore</h4>
			<p class="text-muted">Restore your database from a backup file. This is useful if the database was accidentally dropped or corrupted.</p>
			
			<?php if(isset($GLOBALS['db_missing']) && $GLOBALS['db_missing']): ?>
			<div class="alert alert-danger">
				<h5><i class="fa fa-exclamation-triangle"></i> Database Not Found!</h5>
				<p class="mb-0">The <strong>alumni_db</strong> database does not exist. Please restore from a backup file below.</p>
			</div>
			<?php endif; ?>
			
			<div class="alert alert-warning">
				<i class="fa fa-exclamation-triangle"></i> <strong>Warning:</strong>
				<ul class="mb-0 mt-2">
					<li>This will <strong>overwrite all existing data</strong></li>
					<li>Automatic backup will be created before importing (if database exists)</li>
					<li>Only upload SQL files from trusted sources</li>
					<li>Maximum file size: 50MB</li>
					<li><strong>Works even if database is dropped in phpMyAdmin!</strong></li>
				</ul>
			</div>
			
			<form id="restore-form" enctype="multipart/form-data">
				<div class="form-group">
					<label for="restore-file-input" class="form-label">
						<i class="fa fa-file"></i> Select SQL Backup File
					</label>
					<input type="file" class="form-control" id="restore-file-input" name="sql_file" accept=".sql" required>
					<small class="form-text text-muted">Maximum file size: 50MB. Only .sql files are supported.</small>
				</div>
				
				<button type="button" class="btn btn-success btn-lg" id="restore-database">
					<i class="fa fa-upload"></i> Restore Database
				</button>
				<button type="button" class="btn btn-secondary" id="reset-restore-form">
					<i class="fa fa-refresh"></i> Reset
				</button>
			</form>
			
			<div id="restore-progress" class="mt-3" style="display: none;">
				<div class="progress mb-2">
					<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
				</div>
				<small class="text-muted">Restoring database... Please do not close this page.</small>
			</div>
			
			<div id="restore-status" class="mt-3" style="display: none;">
				<div class="alert" id="restore-alert">
					<i class="fa fa-check-circle"></i> <span id="restore-message">Restore completed successfully!</span>
				</div>
			</div>
		</div>
	</div>
	
	<style>
	img#cimg{
		max-height: 10vh;
		max-width: 6vw;
	}
	
	#restore-database {
		background: linear-gradient(135deg, #28a745, #20c997);
		border: none;
		padding: 12px 30px;
		font-weight: bold;
	}
	
	#restore-database:hover {
		background: linear-gradient(135deg, #20c997, #28a745);
		transform: translateY(-2px);
		box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
	}
	
	#restore-database:disabled {
		background: #6c757d;
		transform: none;
		box-shadow: none;
	}
	
	.progress {
		height: 8px;
		border-radius: 4px;
	}
	
	.progress-bar {
		background: linear-gradient(135deg, #8B0000, #A0522D);
	}
</style>

<script>
	function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
	$('.text-jqte').jqte();

	$('#manage-settings').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_settings',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			error:err=>{
				console.log(err)
			},
			success:function(resp){
				if(resp == 1){
					alert_toast('Data successfully saved.','success')
					setTimeout(function(){
						location.reload()
					},1000)
				}
			}
		})

	})
	
	// Database Restore Functionality
	$('#restore-database').click(function() {
		var fileInput = $('#restore-file-input')[0];
		var file = fileInput.files[0];
		
		if (!file) {
			alert('Please select an SQL file first.');
			return;
		}
		
		// Validate file type
		if (!file.name.toLowerCase().endsWith('.sql')) {
			alert('Please select a valid SQL file.');
			return;
		}
		
		// Validate file size (50MB limit)
		var maxSize = 50 * 1024 * 1024; // 50MB in bytes
		if (file.size > maxSize) {
			alert('File size exceeds 50MB limit.');
			return;
		}
		
		// Show confirmation dialog
		if (!confirm('WARNING: This will replace ALL existing data in the database. Are you sure you want to continue?')) {
			return;
		}
		
		performRestore(file);
	});
	
	// Reset form handler
	$('#reset-restore-form').click(function() {
		$('#restore-form')[0].reset();
		$('#restore-progress').hide();
		$('#restore-status').hide();
		$('#restore-database').prop('disabled', false);
	});
	
	function performRestore(file) {
		var formData = new FormData();
		formData.append('sql_file', file);
		formData.append('action', 'import_database');
		
		// Show progress and disable button
		$('#restore-progress').show();
		$('#restore-database').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Restoring...');
		$('#restore-status').hide();
		
		var progressBar = $('.progress-bar');
		var progress = 0;
		
		// Simulate progress
		var progressInterval = setInterval(function() {
			progress += Math.random() * 10;
			if (progress > 90) progress = 90;
			progressBar.css('width', progress + '%');
		}, 1000);
		
		$.ajax({
			url: 'ajax.php',
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			timeout: 300000, // 5 minutes timeout
			success: function(resp) {
				clearInterval(progressInterval);
				progressBar.css('width', '100%');
				
				setTimeout(function() {
					$('#restore-progress').hide();
					
					try {
						var response = typeof resp === 'string' ? JSON.parse(resp) : resp;
						
						if (response.success) {
							showRestoreStatus('success', response.message || 'Database restored successfully!');
							$('#restore-form')[0].reset();
							
							// Reload the page after 3 seconds
							setTimeout(function() {
								location.reload();
							}, 3000);
						} else {
							showRestoreStatus('error', response.message || 'Restore failed. Please try again.');
						}
					} catch (e) {
						showRestoreStatus('error', 'Invalid response from server. Error: ' + e.message);
					}
					
					$('#restore-database').prop('disabled', false).html('<i class="fa fa-upload"></i> Restore Database');
				}, 1000);
			},
			error: function(xhr, status, error) {
				clearInterval(progressInterval);
				$('#restore-progress').hide();
				
				var errorMessage = 'Restore failed. ';
				if (status === 'timeout') {
					errorMessage += 'The operation timed out. Large files may take longer to process.';
				} else {
					errorMessage += 'Please check your file and try again. Error: ' + error;
				}
				
				showRestoreStatus('error', errorMessage);
				$('#restore-database').prop('disabled', false).html('<i class="fa fa-upload"></i> Restore Database');
			}
		});
	}
	
	function showRestoreStatus(type, message) {
		var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
		var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
		
		$('#restore-alert').removeClass('alert-success alert-danger').addClass(alertClass);
		$('#restore-alert i').removeClass('fa-check-circle fa-exclamation-triangle').addClass(icon);
		$('#restore-message').text(message);
		$('#restore-status').show();
		
		// Auto-hide success messages after 5 seconds
		if (type === 'success') {
			setTimeout(function() {
				$('#restore-status').fadeOut();
			}, 5000);
		}
	}
</script>
<style>
	
</style>
</div>
