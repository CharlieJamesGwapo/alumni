<?php 
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php');

// Check if user is admin
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 1){
    header('location:login.php');
    exit;
}

if(isset($_GET['id'])){
    $user = $conn->query("SELECT * FROM users WHERE id = ".(int)$_GET['id']);
    if($user->num_rows > 0){
        foreach($user->fetch_array() as $k => $v){
            $meta[$k] = $v;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage User - MOIST Alumni</title>
    <?php include 'header.php'; ?>
    <!-- SweetAlert2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-user-edit"></i> <?php echo isset($meta['id']) ? 'Edit User' : 'Add New User'; ?></h4>
                <a href="users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="msg"></div>
	
	<form id="manage-user" novalidate>
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="name" class="font-weight-bold">
						<i class="fas fa-user"></i> Full Name <span class="text-danger">*</span>
					</label>
					<input type="text" name="name" id="name" class="form-control" 
						   value="<?php echo isset($meta['name']) ? htmlspecialchars($meta['name']) : '' ?>" 
						   required placeholder="Enter full name">
					<div class="invalid-feedback">Please provide a valid name.</div>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="form-group">
					<label for="username" class="font-weight-bold">
						<i class="fas fa-at"></i> Username <span class="text-danger">*</span>
					</label>
					<input type="text" name="username" id="username" class="form-control" 
						   value="<?php echo isset($meta['username']) ? htmlspecialchars($meta['username']) : '' ?>" 
						   required autocomplete="off" placeholder="Enter username">
					<div class="invalid-feedback">Please provide a valid username.</div>
					<small class="form-text text-muted">Username must be unique and contain no spaces.</small>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="password" class="font-weight-bold">
						<i class="fas fa-lock"></i> Password 
						<?php if(!isset($meta['id'])): ?><span class="text-danger">*</span><?php endif; ?>
					</label>
					<div class="input-group">
						<input type="password" name="password" id="password" class="form-control" 
							   autocomplete="new-password" placeholder="Enter password"
							   <?php if(!isset($meta['id'])): ?>required<?php endif; ?>>
						<div class="input-group-append">
							<button class="btn btn-outline-secondary" type="button" id="togglePassword">
								<i class="fas fa-eye"></i>
							</button>
						</div>
					</div>
					<?php if(isset($meta['id'])): ?>
						<small class="form-text text-muted">Leave blank to keep current password.</small>
					<?php else: ?>
						<small class="form-text text-muted">Password must be at least 6 characters long.</small>
					<?php endif; ?>
					<div class="invalid-feedback">Password must be at least 6 characters long.</div>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="form-group">
					<label for="type" class="font-weight-bold">
						<i class="fas fa-user-tag"></i> User Type <span class="text-danger">*</span>
					</label>
					<select name="type" id="type" class="form-control" required>
						<option value="">Select User Type</option>
						<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>
							<i class="fas fa-crown"></i> Admin
						</option>
						<option value="4" <?php echo isset($meta['type']) && $meta['type'] == 4 ? 'selected': '' ?>>
							<i class="fas fa-clipboard-list"></i> Registrar
						</option>
					</select>
					<div class="invalid-feedback">Please select a user type.</div>
					<small class="form-text text-muted">
						Admin: Full system access | Registrar: Alumni management access
					</small>
				</div>
			</div>
		</div>
		
		<div class="row mt-4">
			<div class="col-12">
				<div class="d-flex justify-content-end">
					<a href="users.php" class="btn btn-secondary mr-2">
						<i class="fas fa-times"></i> Cancel
					</a>
					<button type="submit" class="btn btn-primary" id="submitBtn">
						<i class="fas fa-save"></i> 
						<?php echo isset($meta['id']) ? 'Update User' : 'Create User' ?>
					</button>
				</div>
			</div>
		</div>
	</form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<style>
.form-group label {
	color: #495057;
	font-size: 14px;
}
.input-group-text {
	background-color: #f8f9fa;
	border-right: none;
}
.form-control:focus {
	border-color: #007bff;
	box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.invalid-feedback {
	display: block;
}
.was-validated .form-control:invalid {
	border-color: #dc3545;
}
.was-validated .form-control:valid {
	border-color: #28a745;
}
</style>

<script>
$(document).ready(function() {
	// Toggle password visibility
	$('#togglePassword').click(function() {
		const passwordField = $('#password');
		const icon = $(this).find('i');
		
		if (passwordField.attr('type') === 'password') {
			passwordField.attr('type', 'text');
			icon.removeClass('fa-eye').addClass('fa-eye-slash');
		} else {
			passwordField.attr('type', 'password');
			icon.removeClass('fa-eye-slash').addClass('fa-eye');
		}
	});

	// Username validation - remove spaces and convert to lowercase
	$('#username').on('input', function() {
		let value = $(this).val().toLowerCase().replace(/\s+/g, '');
		$(this).val(value);
	});

	// Real-time validation
	$('#name').on('input', function() {
		validateField($(this), $(this).val().trim().length >= 2);
	});

	$('#username').on('input', function() {
		const username = $(this).val().trim();
		const isValid = username.length >= 3 && /^[a-zA-Z0-9_]+$/.test(username);
		validateField($(this), isValid);
	});

	$('#password').on('input', function() {
		const password = $(this).val();
		const isEdit = $('input[name="id"]').val() !== '';
		const isValid = isEdit ? (password === '' || password.length >= 6) : password.length >= 6;
		validateField($(this), isValid);
	});

	$('#type').on('change', function() {
		validateField($(this), $(this).val() !== '');
	});

	function validateField(field, isValid) {
		if (isValid) {
			field.removeClass('is-invalid').addClass('is-valid');
		} else {
			field.removeClass('is-valid').addClass('is-invalid');
		}
	}

	// Form submission
	$('#manage-user').on('submit', function(e) {
		e.preventDefault();
		
		// Clear previous messages
		$('#msg').empty();
		
		// Validate form
		const form = this;
		if (!form.checkValidity()) {
			$(form).addClass('was-validated');
			return false;
		}

		// Additional custom validation
		const name = $('#name').val().trim();
		const username = $('#username').val().trim();
		const password = $('#password').val();
		const type = $('#type').val();
		const isEdit = $('input[name="id"]').val() !== '';

		if (name.length < 2) {
			showError('Name must be at least 2 characters long.');
			return false;
		}

		if (username.length < 3 || !/^[a-zA-Z0-9_]+$/.test(username)) {
			showError('Username must be at least 3 characters and contain only letters, numbers, and underscores.');
			return false;
		}

		if (!isEdit && password.length < 6) {
			showError('Password must be at least 6 characters long.');
			return false;
		}

		if (isEdit && password !== '' && password.length < 6) {
			showError('Password must be at least 6 characters long.');
			return false;
		}

		if (!type) {
			showError('Please select a user type.');
			return false;
		}

		// Show loading state
		const submitBtn = $('#submitBtn');
		const originalText = submitBtn.html();
		submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

		// Submit form
		$.ajax({
			url: 'ajax.php?action=save_user',
			method: 'POST',
			data: $(this).serialize(),
			timeout: 15000,
			success: function(resp) {
				if (resp == 1) {
					// Success
					Swal.fire({
						title: 'Success!',
						text: isEdit ? 'User updated successfully!' : 'User created successfully!',
						icon: 'success',
						showConfirmButton: false,
						timer: 1500
					}).then(() => {
						window.location.href = 'users.php';
					});
				} else if (resp == 2) {
					// Username already exists
					showError('Username already exists. Please choose a different username.');
					submitBtn.prop('disabled', false).html(originalText);
				} else {
					// Other error
					showError('An error occurred while saving the user. Please try again.');
					submitBtn.prop('disabled', false).html(originalText);
				}
			},
			error: function(xhr, status, error) {
				console.error('Save error:', error);
				showError('Network error occurred. Please check your connection and try again.');
				submitBtn.prop('disabled', false).html(originalText);
			}
		});
	});

	function showError(message) {
		$('#msg').html(`<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="fas fa-exclamation-triangle"></i> ${message}
			<button type="button" class="close" data-dismiss="alert">
				<span>&times;</span>
			</button>
		</div>`);
		
		// Scroll to top of modal to show error
		$('.modal-body').animate({ scrollTop: 0 }, 300);
	}
});
</script>