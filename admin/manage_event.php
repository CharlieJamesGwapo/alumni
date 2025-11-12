<?php include 'db_connect.php' ?>
<?php
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM events where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<style>
	
	.jqte_editor{
		min-height: 30vh !important
	}
	#drop {
   	min-height: 15vh;
    max-height: 30vh;
    overflow: auto;
    width: calc(100%);
    border: 5px solid #929292;
    margin: 10px;
    border-style: dashed;
    padding: 10px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}
	#uploads {
		min-height: 15vh;
	width: calc(100%);
	margin: 10px;
	padding: 10px;
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	}
	#uploads .img-holder{
	    position: relative;
	    margin: 1em;
	    cursor: pointer;
	}
	#uploads .img-holder:hover{
	    background: #0095ff1f;
	}
	#uploads .img-holder .form-check{
	    display: none;
	}
	#uploads .img-holder.checked .form-check{
	    display: block;
	}
	#uploads .img-holder.checked{
	    background: #0095ff1f;
	}
	#uploads .img-holder img {
		height: 39vh;
    width: 22vw;
    margin: .5em;
		}
	#uploads .img-holder span{
	    position: absolute;
	    top: -.5em;
	    left: -.5em;
	}
	#dname{
		margin: auto 
	}
img.imgDropped {
    height: 16vh;
    width: 7vw;
    margin: 1em;
}
.imgF {
    border: 1px solid #0000ffa1;
    border-style: dashed;
    position: relative;
    margin: 1em;
}
span.rem.badge.badge-primary {
    position: absolute;
    top: -.5em;
    left: -.5em;
    cursor: pointer;
}
label[for="chooseFile"]{
	color: #0000ff94;
	cursor: pointer;
}
label[for="chooseFile"]:hover{
	color: #0000ffba;
}
.opts {
    position: absolute;
    top: 0;
    right: 0;
    background: #00000094;
    width: calc(100%);
    height: calc(100%);
    justify-items: center;
    display: flex;
    opacity: 0;
    transition: all .5s ease;
}
.img-holder:hover .opts{
    opacity: 1;

}
	input[type=checkbox]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  transform: scale(1.5);
  padding: 10px;
}
button.btn.btn-sm.btn-rounded.btn-sm.btn-dark {
    margin: auto;
}
img#banner-field{
		max-height: 15vh;
		max-width: 8vw;
	}
</style>
<div class="container-fluid">
	<form id="manage-event" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Event Details</h3>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label>Title</label>
							<input type="text" name="title" class="form-control" value="<?php echo isset($title) ? $title :'' ?>" required>
						</div>
						<div class="form-group">
							<label>Schedule</label>
							<input type="text" class="form-control datetimepicker" name="schedule"  value="<?php echo isset($schedule) ? date("Y-m-d H:i",strtotime($schedule)) :'' ?>" required autocomplete="off">
						</div>
						<div class="form-group">
							<label>Banner Image</label>
							<input type="file" class="form-control" name="banner" onchange="displayImg2(this,$(this))">
						</div>
						<div class="form-group">
							<label>Content</label>
							<textarea name="content" id="content" class="form-control jqte" cols="30" rows="5" required><?php echo isset($content) ? html_entity_decode($content) : '' ?></textarea>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" name="save" class="btn btn-primary">Save Event</button>
						<a href="index.php?page=events" class="btn btn-default">Cancel</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<div class="imgF" style="display: none " id="img-clone">
			<span class="rem badge badge-primary" onclick="rem_func($(this))"><i class="fa fa-times"></i></span>
	</div>
<script>
	$('#payment_status').on('change keypress keyup',function(){
		if($(this).prop('checked') == true){
			$('#amount').closest('.form-group').hide()
		}else{
			$('#amount').closest('.form-group').show()
		}
	})
	$('.jqte').jqte();

	$('#manage-event').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_event',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				end_load()
				if(resp==1){
					Swal.fire({
						title: 'Success!',
						text: 'Event successfully added',
						icon: 'success',
						confirmButtonText: 'OK'
					}).then(() => {
						location.href = "index.php?page=events"
					})
				} else if(resp==2){
					Swal.fire({
						title: 'Success!',
						text: 'Event successfully updated',
						icon: 'success',
						confirmButtonText: 'OK'
					}).then(() => {
						location.href = "index.php?page=events"
					})
				} else {
					Swal.fire({
						title: 'Error!',
						text: 'Error saving event. Please try again.',
						icon: 'error',
						confirmButtonText: 'OK'
					})
				}
			},
			error: function(){
				end_load()
				Swal.fire({
					title: 'Error!',
					text: 'Network error. Please check your connection.',
					icon: 'error',
					confirmButtonText: 'OK'
				})
			}
		})
	})
	if (window.FileReader) {
  var drop;
  addEventHandler(window, 'load', function() {
    var status = document.getElementById('status');
    drop = document.getElementById('drop');
    var dname = document.getElementById('dname');
    var list = document.getElementById('list');

    function cancel(e) {
      if (e.preventDefault) {
        e.preventDefault();
      }
      return false;
    }

    // Tells the browser that we *can* drop on this target
    addEventHandler(drop, 'dragover', cancel);
    addEventHandler(drop, 'dragenter', cancel);

    addEventHandler(drop, 'drop', function(e) {
      e = e || window.event; // get window.event if e argument missing (in IE)   
      if (e.preventDefault) {
        e.preventDefault();
      } // stops the browser from redirecting off to the image.
      $('#dname').remove();
      var dt = e.dataTransfer;
      var files = dt.files;
      for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var reader = new FileReader();

        //attach event handlers here...

        reader.readAsDataURL(file);
        addEventHandler(reader, 'loadend', function(e, file) {
          var bin = this.result;
          var imgF = document.getElementById('img-clone');
          	imgF = imgF.cloneNode(true);
          imgF.removeAttribute('id')
          imgF.removeAttribute('style')

          var img = document.createElement("img");
          var fileinput = document.createElement("input");
          var fileinputName = document.createElement("input");
          fileinput.setAttribute('type','hidden')
          fileinputName.setAttribute('type','hidden')
          fileinput.setAttribute('name','img[]')
          fileinputName.setAttribute('name','imgName[]')
          fileinput.value = bin
          fileinputName.value = file.name
          img.classList.add("imgDropped")
          img.file = file;
          img.src = bin;
          imgF.appendChild(fileinput);
          imgF.appendChild(fileinputName);
          imgF.appendChild(img);
          drop.appendChild(imgF)
        }.bindToEventHandler(file));
      }
      return false;

    });

    Function.prototype.bindToEventHandler = function bindToEventHandler() {
      var handler = this;
      var boundParameters = Array.prototype.slice.call(arguments);
      return function(e) {
        e = e || window.event; // get window.event if e argument missing (in IE)   
        boundParameters.unshift(e);
        handler.apply(this, boundParameters);
      }
    };
  });
} else {
  document.getElementById('status').innerHTML = 'Your browser does not support the HTML5 FileReader.';
}

function addEventHandler(obj, evt, handler) {
  if (obj.addEventListener) {
    // W3C method
    obj.addEventListener(evt, handler, false);
  } else if (obj.attachEvent) {
    // IE method.
    obj.attachEvent('on' + evt, handler);
  } else {
    // Old school method.
    obj['on' + evt] = handler;
  }
}
function displayIMG(input){

    	if (input.files) {
	if($('#dname').length > 0)
		$('#dname').remove();

    			Object.keys(input.files).map(function(k){
    				var reader = new FileReader();
				        reader.onload = function (e) {
				        	// $('#cimg').attr('src', e.target.result);
          				var bin = e.target.result;
          				var fname = input.files[k].name;
          				var imgF = document.getElementById('img-clone');
						  	imgF = imgF.cloneNode(true);
						  imgF.removeAttribute('id')
						  imgF.removeAttribute('style')
				        	var img = document.createElement("img");
					          var fileinput = document.createElement("input");
					          var fileinputName = document.createElement("input");
					          fileinput.setAttribute('type','hidden')
					          fileinputName.setAttribute('type','hidden')
					          fileinput.setAttribute('name','img[]')
					          fileinputName.setAttribute('name','imgName[]')
					          fileinput.value = bin
					          fileinputName.value = fname
					          img.classList.add("imgDropped")
					          img.src = bin;
					          imgF.appendChild(fileinput);
					          imgF.appendChild(fileinputName);
					          imgF.appendChild(img);
					          drop.appendChild(imgF)
				        }
		        reader.readAsDataURL(input.files[k]);
    			})
    			
rem_func()

    }
    }
function displayImg2(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$('#banner-field').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
function rem_func(_this){
		_this.closest('.imgF').remove()
		if($('#drop .imgF').length <= 0){
			$('#drop').append('<span id="dname" class="text-center">Drop Files Here</label></span>')
		}
}
</script>