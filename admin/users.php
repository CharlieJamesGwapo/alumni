<?php
// When included from index.php, session is already started and db_connect is already included
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($conn)) {
    include 'db_connect.php';
}

// Check if user has admin privileges
if(!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 1){
    echo '<div class="container mt-5"><div class="alert alert-danger">Access denied. Admin privileges required.</div></div>';
    return;
}
?>

<!-- SweetAlert2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
										<code><?php echo htmlspecialchars($row['username']) ?></code>
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
											<?php if($row['id'] != $_SESSION['login_id']): ?>
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

@media (max-width: 768px) {
	h4 { font-size: 1.1rem; }
	.d-flex.justify-content-between { flex-direction: column; gap: 8px; }
	.d-flex.justify-content-between .btn { width: 100%; }
	.table { font-size: 0.8rem; }
	.btn-group .btn { font-size: 0.75rem; padding: 4px 8px; }
	.avatar-sm { width: 28px !important; height: 28px !important; }
}

@media (max-width: 576px) {
	.table thead { display: none; }
	.table tr { display: block; margin-bottom: 12px; border: 1px solid #dee2e6; border-radius: 8px; background: #fff; }
	.table td { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px !important; border: none; border-bottom: 1px solid #f0f0f0; }
	.table td::before { content: attr(data-label); font-weight: 600; margin-right: 8px; }
	.badge { font-size: 0.7rem; }
}
</style>

<script>
$(document).ready(function() {
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
			{ "orderable": false, "targets": [0, 4] }
		]
	});

	$('#new_user').click(function(){
		uni_modal('Add New User', 'manage_user.php');
	});

	$(document).on('click', '.edit_user', function(){
		const userId = $(this).attr('data-id');
		uni_modal('Edit User', 'manage_user.php?id=' + userId);
	});

	$(document).on('click', '.delete_user', function(){
		const userId = $(this).attr('data-id');
		const userName = $(this).closest('tr').find('td:nth-child(2) strong').text();

		Swal.fire({
			title: 'Delete User?',
			text: 'Are you sure you want to delete user "' + userName + '"? This action cannot be undone.',
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

	function delete_user(userId) {
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
				Swal.fire({
					title: 'Error!',
					text: 'Network error occurred. Please check your connection and try again.',
					icon: 'error'
				});
			}
		});
	}

	window.delete_user = delete_user;
});
</script>
