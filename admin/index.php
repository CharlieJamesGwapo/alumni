<!DOCTYPE html>
<html lang="en">
	
<?php
session_start();

// Check if user is logged in (use login_id as the authoritative flag)
if(!isset($_SESSION['login_id'])) {
    header("location:login.php");
    exit;
}

// Verify user still exists in database
include 'db_connect.php';
$check_stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$check_stmt->bind_param("i", $_SESSION['login_id']);
$check_stmt->execute();
$check = $check_stmt->get_result();
if($check->num_rows == 0) {
    session_destroy();
    header("location:login.php");
    exit;
}
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : '' ?></title>
  <!-- Add favicon -->
  <link rel="icon" type="image/png" href="assets/img/logo.png"/>
  <link rel="shortcut icon" type="image/png" href="assets/img/logo.png"/>
 	

<?php
 include('./header.php'); 
 // include('./auth.php'); 
 ?>

</head>
<style>
    body {
        background: #f0f2f5;
        min-height: 100vh;
        margin: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #1e293b;
    }

    /* Cards - clean white */
    .card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        color: #1e293b;
    }
    .card .card-body { padding: 1.25rem 1.5rem; }
    .card .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #1e293b;
        border-radius: 12px 12px 0 0 !important;
        padding: 0.85rem 1.5rem;
    }
    .card .card-title { color: #1e293b; font-weight: 600; }
    .card .card-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    /* Tables - readable dark text */
    .table { color: #334155; }
    .table thead th {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .table td {
        border-color: #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }
    .table-striped tbody tr:nth-of-type(odd) { background-color: #f8fafc; }
    .table-hover tbody tr:hover { background-color: #eff6ff; }
    .thead-dark th { background: #1e293b !important; color: white !important; }

    /* Buttons */
    .btn { border-radius: 8px; font-weight: 500; font-size: 0.9rem; }
    .btn-primary {
        background: #4f46e5;
        border-color: #4f46e5;
    }
    .btn-primary:hover, .btn-primary:focus {
        background: #4338ca;
        border-color: #4338ca;
        box-shadow: 0 4px 12px rgba(79,70,229,0.3);
    }
    .btn-success { background: #059669; border-color: #059669; color: white; }
    .btn-success:hover { background: #047857; border-color: #047857; }
    .btn-danger { background: #dc2626; border-color: #dc2626; }
    .btn-danger:hover { background: #b91c1c; border-color: #b91c1c; }
    .btn-info { background: #0891b2; border-color: #0891b2; color: white; }
    .btn-info:hover { background: #0e7490; border-color: #0e7490; color: white; }
    .btn-warning { background: #d97706; border-color: #d97706; color: white; }
    .btn-warning:hover { background: #b45309; border-color: #b45309; color: white; }
    .btn-secondary { background: #64748b; border-color: #64748b; }
    .btn-secondary:hover { background: #475569; border-color: #475569; }
    .btn-default { background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; }
    .btn-default:hover { background: #e2e8f0; }
    .btn-sm { font-size: 0.82rem; padding: 0.3rem 0.65rem; }

    /* Form controls */
    .form-control, .form-select, select.form-control {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        border-radius: 8px;
        font-size: 0.9rem;
    }
    .form-control:focus, .form-select:focus, select.form-control:focus {
        background: #ffffff;
        border-color: #4f46e5;
        color: #1e293b;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }
    .form-control::placeholder { color: #94a3b8; }
    .form-label, .control-label { color: #475569; font-weight: 500; font-size: 0.85rem; }
    .form-group label { color: #475569; font-weight: 500; }
    textarea.form-control { min-height: 80px; }

    /* Badges */
    .badge { font-weight: 500; font-size: 0.78rem; padding: 0.35em 0.65em; border-radius: 6px; }

    /* Toast */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        border-radius: 10px;
        border: none;
        min-width: 280px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    /* Modals - white background, readable */
    .modal-content {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        color: #1e293b;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
    }
    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 0.85rem 1.5rem;
        background: #f8fafc;
    }
    .modal-title { font-weight: 600; color: #1e293b; }
    .modal-body { color: #334155; }
    .modal-body .form-control, .modal-body .form-select,
    .modal-body select.form-control {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e293b;
    }
    .modal-body label { color: #475569; }

    /* DataTables - light theme */
    .dataTables_wrapper .dataTables_filter input {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        border-radius: 8px;
        padding: 6px 12px;
    }
    .dataTables_wrapper .dataTables_length select {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #1e293b;
        border-radius: 8px;
    }
    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_length label {
        color: #64748b;
        font-size: 0.85rem;
    }
    .dataTables_wrapper .dataTables_info { color: #64748b; font-size: 0.85rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        border-radius: 6px !important;
        margin: 0 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #4f46e5 !important;
        border-color: #4f46e5 !important;
        color: white !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        color: #1e293b !important;
        border-color: #cbd5e1 !important;
    }

    /* Alerts */
    .alert { border-radius: 10px; border: none; }

    /* Text utilities fix */
    .text-muted { color: #64748b !important; }
    h1, h2, h3, h4, h5, h6 { color: #1e293b; }
    p { color: #475569; }
    a { color: #4f46e5; }
    a:hover { color: #4338ca; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Select2 fix */
    .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        height: 38px;
        background: #ffffff;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b;
        line-height: 36px;
    }

    .modal-dialog.large { width: 80% !important; max-width: unset; }
    .modal-dialog.mid-large { width: 50% !important; max-width: unset; }

    @media (max-width: 768px) {
        .modal-dialog.large, .modal-dialog.mid-large { width: 95% !important; margin: 10px auto; }
        .modal-dialog { margin: 10px auto; max-width: 95%; }
        .modal-body { padding: 1rem; }
        #viewer_modal .modal-dialog { width: 95%; height: auto; max-height: 90vh; }
    }
    @media (max-width: 576px) {
        .modal-dialog.large, .modal-dialog.mid-large { width: 100% !important; margin: 0; border-radius: 0; }
        .modal-content { border-radius: 0; }
    }

    #viewer_modal .btn-close {
        position: absolute; z-index: 999999;
        background: unset; color: white; border: unset;
        font-size: 27px; top: 0;
    }
    #viewer_modal .modal-dialog {
        width: 80%; max-width: unset;
        height: calc(90%); max-height: unset;
    }
    #viewer_modal .modal-content {
        background: black; border: unset;
        height: calc(100%); display: flex;
        align-items: center; justify-content: center;
    }
    #viewer_modal img, #viewer_modal video {
        max-height: calc(100%); max-width: calc(100%);
    }
</style>

<body>
	<?php include 'topbar.php' ?>
	<?php include 'navbar.php' ?>
  <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-white">
    </div>
  </div>
  <main id="view-panel" >
      <?php
      $page = isset($_GET['page']) ? $_GET['page'] : 'home';
      // Whitelist allowed pages to prevent path traversal
      $allowed_pages = ['home','courses','alumni','jobs','events','backup','activity_log','users','site_settings','forums','gallery','audience','archive','view_alumni','view_jobs','manage_career','manage_event','manage_forum','manage_register','dashboard'];
      if (!in_array($page, $allowed_pages)) {
          $page = 'home';
      }
      $page_file = __DIR__ . '/' . $page . '.php';
      if (file_exists($page_file)) {
          include $page_file;
      } else {
          echo '<div class="container mt-5"><div class="alert alert-warning">Page not found.</div></div>';
      }
    ?>
  	

  </main>

  <div id="preloader"></div>
  <a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>

<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
</body>
<script>
	 window.start_load = function(){
    $('body').prepend('<di id="preloader2"></di>')
  }
  window.end_load = function(){
    $('#preloader2').fadeOut('fast', function() {
        $(this).remove();
      })
  }
 window.viewer_modal = function($src = ''){
    start_load()
    var t = $src.split('.')
    t = t[1]
    if(t =='mp4'){
      var view = $("<video src='"+$src+"' controls autoplay></video>")
    }else{
      var view = $("<img src='"+$src+"' />")
    }
    $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
    $('#viewer_modal .modal-content').append(view)
    $('#viewer_modal').modal({
            show:true,
            backdrop:'static',
            keyboard:false,
            focus:true
          })
          end_load()  

}
  window.uni_modal = function($title = '' , $url='',$size=""){
    start_load()
    $.ajax({
        url:$url,
        error:err=>{
            console.log()
            alert("An error occured")
        },
        success:function(resp){
            if(resp){
                $('#uni_modal .modal-title').html($title)
                $('#uni_modal .modal-body').html(resp)
                if($size != ''){
                    $('#uni_modal .modal-dialog').addClass($size)
                }else{
                    $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
                }
                $('#uni_modal').modal({
                  show:true,
                  backdrop:'static',
                  keyboard:false,
                  focus:true
                })
                end_load()
            }
        }
    })
}
window._conf = function($msg='',$func='',$params = []){
     $('#confirm_modal #confirm').attr('onclick',$func+"("+$params.join(',')+")")
     $('#confirm_modal .modal-body').html($msg)
     $('#confirm_modal').modal('show')
  }
   window.alert_toast= function($msg = 'TEST',$bg = 'success'){
      $('#alert_toast').removeClass('bg-success')
      $('#alert_toast').removeClass('bg-danger')
      $('#alert_toast').removeClass('bg-info')
      $('#alert_toast').removeClass('bg-warning')

    if($bg == 'success')
      $('#alert_toast').addClass('bg-success')
    if($bg == 'danger')
      $('#alert_toast').addClass('bg-danger')
    if($bg == 'info')
      $('#alert_toast').addClass('bg-info')
    if($bg == 'warning')
      $('#alert_toast').addClass('bg-warning')
    $('#alert_toast .toast-body').html($msg)
    $('#alert_toast').toast({delay:3000}).toast('show');
  }
  $(document).ready(function(){
    $('#preloader').fadeOut('fast', function() {
        $(this).remove();
      })
    // Show backup prompt when admin panel loads
    showBackupPrompt();
  })
  
  function showBackupPrompt() {
    // Check if backup prompt was already shown today
    var lastPrompt = localStorage.getItem('backup_prompt_date');
    var today = new Date().toDateString();
    
    if (lastPrompt !== today) {
      setTimeout(function() {
        if (confirm('Would you like to backup your database? Regular backups help protect your data.')) {
          window.location.href = 'index.php?page=backup';
        }
        // Remember that we showed the prompt today
        localStorage.setItem('backup_prompt_date', today);
      }, 2000); // Show prompt 2 seconds after page load
    }
  }
  $('.datetimepicker').datetimepicker({
      format:'Y/m/d H:i',
      startDate: '+3d'
  })
  $('.select2').select2({
    placeholder:"Please select here",
    width: "100%"
  })
</script>	
</html>