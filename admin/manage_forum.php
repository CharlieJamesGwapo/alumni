<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
	$id_param = intval($_GET['id']);
	$stmt = $conn->prepare("SELECT * FROM forum_topics WHERE id = ?");
	$stmt->bind_param("i", $id_param);
	$stmt->execute();
	$qry = $stmt->get_result()->fetch_assoc();
	if($qry) foreach($qry as $k =>$v){ $$k = $v; }
}

?>
<div class="container-fluid">
	<form action="" id="manage-forum">
				<input type="hidden" name="id" value="<?php echo isset($id_param) ? (int)$id_param : '' ?>" class="form-control">
		<div class="row form-group">
			<div class="col-md-8">
				<label class="control-label">Title</label>
				<input type="text" name="title" class="form-control" value="<?php echo isset($title) ? $title:'' ?>">
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
				<label class="control-label">Description</label>
				<textarea name="description" class="text-jqte"><?php echo isset($description) ? $description : '' ?></textarea>
			</div>
		</div>
	</form>
</div>

<script>
	$('.text-jqte').jqte();
	$('#manage-forum').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_forum',
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