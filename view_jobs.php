<?php include 'admin/db_connect.php' ?>
<?php
if(isset($_GET['id'])){
	$id_param = intval($_GET['id']);
	$stmt = $conn->prepare("SELECT * FROM careers WHERE id = ?");
	$stmt->bind_param("i", $id_param);
	$stmt->execute();
	$qry = $stmt->get_result()->fetch_assoc();
	if($qry) foreach($qry as $k =>$v){ $$k = $v; }
}

?>
<div class="container-fluid">
	<p>Company: <b><?php echo htmlspecialchars(ucwords($company ?? '')) ?></b></p>
	<p>Job Title: <b><?php echo htmlspecialchars(ucwords($job_title ?? '')) ?></b></p>
	<p>Location: <i class="fa fa-map-marker"></i> <b><?php echo htmlspecialchars($location ?? '') ?></b></p>
	<hr class="divider">
	<?php echo html_entity_decode($description) ?>
</div>
<div class="modal-footer display">
	<div class="row">
		<div class="col-md-12">
			<button class="btn float-right btn-secondary" type="button" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
<style>
	p{
		margin:unset;
	}
	#uni_modal .modal-footer{
		display: none;
	}
	#uni_modal .modal-footer.display {
		display: block;
	}
	/* ── Mobile Responsiveness ── */
	@media (max-width: 768px) {
		.container-fluid { padding: 12px; }
		.container-fluid p { font-size: 14px; word-wrap: break-word; }
		.container-fluid large { font-size: 14px; }
		.modal-footer .btn { min-height: 44px; width: 100%; }
	}
	@media (max-width: 480px) {
		.container-fluid { padding: 8px; font-size: 13px; }
	}
</style>
<script>
	$('.text-jqte').jqte();
	$('#manage-career').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'admin/ajax.php?action=save_career',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved.",'success')
					setTimeout(function(){
						location.reload()
					},1000)
				}
			}
		})
	})
</script>