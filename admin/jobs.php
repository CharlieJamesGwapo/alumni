<?php include('db_connect.php');?>

<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
						<b style="font-size:1.1rem;">Jobs List</b>
						<button class="btn btn-primary btn-sm" type="button" id="new_career">
							<i class="fa fa-plus"></i> New
						</button>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover" id="jobsTable">
							<thead>
								<tr>
									<th class="text-center" style="width:40px;">#</th>
									<th style="width:60px;">Image</th>
									<th>Company</th>
									<th>Job Title</th>
									<th>Type</th>
									<th>Salary</th>
									<th>Posted By</th>
									<th>Date</th>
									<th class="text-center" style="width:180px;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$i = 1;
								$jobs = $conn->query("SELECT c.*, COALESCE(u.name, 'Admin') as poster_name FROM careers c LEFT JOIN users u ON u.id = c.user_id ORDER BY c.id DESC");
								while($row = $jobs->fetch_assoc()):
								$img_path = '../uploads/jobs/' . ($row['image'] ?? '');
								$has_image = !empty($row['image']) && file_exists($img_path);
								?>
								<tr>
									<td class="text-center" data-label="#"><?php echo $i++ ?></td>
									<td class="text-center" data-label="Image">
										<?php if($has_image): ?>
											<img src="<?php echo htmlspecialchars($img_path); ?>" alt="Job" style="width:50px;height:50px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="viewer_modal('<?php echo htmlspecialchars($img_path); ?>')">
										<?php else: ?>
											<span class="text-muted" style="font-size:0.75rem;">No image</span>
										<?php endif; ?>
									</td>
									<td data-label="Company"><b><?php echo htmlspecialchars(ucwords($row['company'])) ?></b></td>
									<td data-label="Job Title"><b><?php echo htmlspecialchars(ucwords($row['job_title'])) ?></b></td>
									<td data-label="Type">
										<span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['job_type'] ?? 'Full-time'); ?></span>
									</td>
									<td data-label="Salary"><?php echo htmlspecialchars($row['salary'] ?? '-'); ?></td>
									<td data-label="Posted By"><?php echo htmlspecialchars(ucwords($row['poster_name'])) ?></td>
									<td data-label="Date"><?php echo date('M j, Y', strtotime($row['date_created'])); ?></td>
									<td class="text-center" data-label="Action">
										<div class="btn-group btn-group-sm">
											<button class="btn btn-outline-info view_career" type="button" data-id="<?php echo $row['id'] ?>" title="View"><i class="fa fa-eye"></i></button>
											<button class="btn btn-outline-primary edit_career" type="button" data-id="<?php echo $row['id'] ?>" title="Edit"><i class="fa fa-edit"></i></button>
											<button class="btn btn-outline-danger delete_career" type="button" data-id="<?php echo $row['id'] ?>" title="Delete"><i class="fa fa-trash"></i></button>
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
	td { vertical-align: middle !important; }
	td p { margin: unset; }
	@media (max-width: 768px) {
		.card-header { flex-wrap: wrap; gap: 8px; }
		.table { font-size: 0.85rem; }
		td, th { padding: 6px 8px !important; }
	}
	@media (max-width: 576px) {
		.table thead { display: none; }
		.table tr { display: block; margin-bottom: 12px; border: 1px solid #dee2e6; border-radius: 8px; }
		.table td { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px !important; border: none; border-bottom: 1px solid #f0f0f0; }
		.table td::before { content: attr(data-label); font-weight: 600; color: #333; margin-right: 8px; }
	}
</style>
<script>
	$(document).ready(function(){
		$('#jobsTable').dataTable();
	});

	$('#new_career').click(function(){
		uni_modal("New Job Post","manage_career.php",'mid-large');
	});

	$('.edit_career').click(function(){
		uni_modal("Edit Job Post","manage_career.php?id="+$(this).attr('data-id'),'mid-large');
	});
	$('.view_career').click(function(){
		uni_modal("Job Details","view_jobs.php?id="+$(this).attr('data-id'),'mid-large');
	});
	$('.delete_career').click(function(){
		_conf("Are you sure you want to delete this job post?","delete_career",[$(this).attr('data-id')],'mid-large');
	});

	function delete_career($id){
		start_load();
		$.ajax({
			url:'ajax.php?action=delete_career',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Job deleted successfully",'success');
					setTimeout(function(){
						location.reload();
					},1500);
				}
			}
		});
	}
</script>
