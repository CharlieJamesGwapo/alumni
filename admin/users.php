<?php 
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Check if user is logged in and has admin privileges
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 1){
    header('location:login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
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
				<h4 class="mb-0"><i class="fas fa-users-cog"></i> Admin & Registrar Management</h4>
				<button class="btn btn-primary btn-sm" id="new_user">
					<i class="fas fa-plus"></i> Add New User
				</button>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-12">
			<div class="card shadow-sm">
				<div class="card-header bg-primary text-white">
					<h6 class="mb-0"><i class="fas fa-table"></i> System Users</h6>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered" id="usersTable">
							<thead class="thead-dark">
								<tr>
									<th class="text-center" width="5%">#</th>
									<th class="text-center" width="30%">Full Name</th>
									<th class="text-center" width="25%">Username</th>
									<th class="text-center" width="20%">User Type</th>
									<th class="text-center" width="20%">Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$type = array("","Admin","Alumni Officer","Alumnus/Alumna","Registrar");
									// Only show Admin (type=1) and Registrar (type=4) users
									$users = $conn->query("SELECT * FROM users WHERE type IN (1,4) ORDER BY name ASC");
									$i = 1;
									while($row = $users->fetch_assoc()):
								?>
								<tr>
									<td class="text-center">
										<span class="badge badge-secondary"><?php echo $i++ ?></span>
									</td>
									<td>
										<div class="d-flex align-items-center">
											<div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 35px; height: 35px; margin-right: 10px;">
												<i class="fas fa-user"></i>
											</div>
											<strong><?php echo ucwords($row['name']) ?></strong>
										</div>
									</td>
									<td class="text-center">
										<code><?php echo $row['username'] ?></code>
									</td>
									<td class="text-center">
										<?php if($row['type'] == 1): ?>
											<span class="badge badge-danger"><i class="fas fa-crown"></i> Admin</span>
										<?php elseif($row['type'] == 4): ?>
											<span class="badge badge-info"><i class="fas fa-clipboard-list"></i> Registrar</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<div class="btn-group" role="group">
											<button type="button" class="btn btn-sm btn-outline-primary edit_user" data-id="<?php echo $row['id'] ?>" title="Edit User">
												<i class="fas fa-edit"></i>
											</button>
											<?php if($row['id'] != $_SESSION['login_id']): // Prevent self-deletion ?>
											<button type="button" class="btn btn-sm btn-outline-danger delete_user" data-id="<?php echo $row['id'] ?>" title="Delete User">
												<i class="fas fa-trash"></i>
											</button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.avatar-sm {
	font-size: 14px;
}
.table th {
	vertical-align: middle;
}
.table td {
	vertical-align: middle;
}
.btn-group .btn {
	margin: 0 2px;
}
.card {
	border: none;
	border-radius: 10px;
}
.card-header {
	border-radius: 10px 10px 0 0 !important;
}
</style>

</body>
</html>

<script>
$(document).ready(function() {
	// Initialize DataTable with enhanced features
	$('#usersTable').DataTable({
		"responsive": true,
		"lengthChange": false,
		"autoWidth": false,
		"pageLength": 10,
		"language": {
			"search": "Search users:",
			"emptyTable": "No users found",
			"info": "Showing _START_ to _END_ of _TOTAL_ users",
			"paginate": {
				"first": "First",
				"last": "Last",
				"next": "Next",
				"previous": "Previous"
			}
		},
		"columnDefs": [
			{ "orderable": false, "targets": [0, 4] } // Disable sorting for # and Actions columns
		]
	});

	// Add new user
	$('#new_user').click(function(){
		window.location.href = 'manage_user.php';
	});

	// Edit user - using event delegation for dynamic content
	$(document).on('click', '.edit_user', function(){
		const userId = $(this).attr('data-id');
		window.location.href = 'manage_user.php?id=' + userId;
	});

	// Delete user - using event delegation for dynamic content
	$(document).on('click', '.delete_user', function(){
		const userId = $(this).attr('data-id');
		const userName = $(this).closest('tr').find('td:nth-child(2) strong').text();
		
		Swal.fire({
			title: 'Delete User?',
			text: `Are you sure you want to delete user "${userName}"? This action cannot be undone.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#dc3545',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			if (result.isConfirmed) {
				delete_user(userId);
			}
		});
	});

	// Delete user function
	function delete_user(userId) {
		// Show loading
		Swal.fire({
			title: 'Deleting...',
			text: 'Please wait while we delete the user.',
			allowOutsideClick: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: 'ajax.php?action=delete_user',
			method: 'POST',
			data: { id: userId },
			timeout: 10000,
			success: function(resp) {
				if(resp == 1) {
					Swal.fire({
						title: 'Deleted!',
						text: 'User has been successfully deleted.',
						icon: 'success',
						showConfirmButton: false,
						timer: 1500
					}).then(() => {
						location.reload();
					});
				} else {
					Swal.fire({
						title: 'Error!',
						text: 'Failed to delete user. Please try again.',
						icon: 'error'
					});
				}
			},
			error: function(xhr, status, error) {
				console.error('Delete error:', error);
				Swal.fire({
					title: 'Error!',
					text: 'Network error occurred. Please check your connection and try again.',
					icon: 'error'
				});
			}
		});
	}

	// Make delete_user function globally accessible
	window.delete_user = delete_user;
});
</script>