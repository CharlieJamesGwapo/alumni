<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
// Optional debug logger: visit admin/login.php?debug_admin=1 to record session state for troubleshooting redirects
if (isset($_GET['debug_admin']) && $_GET['debug_admin'] == '1') {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    $logfile = $logDir . '/admin_redirect_debug.log';
    $session_snapshot = [];
    if (!empty($_SESSION)) {
        $session_snapshot = array_intersect_key($_SESSION, array_flip(['login_id','login_name','login_type']));
    }
    $entry = date('c') . " | URI=" . ($_SERVER['REQUEST_URI'] ?? '') . " | SESSION=" . json_encode($session_snapshot) . "\n";
    @file_put_contents($logfile, $entry, FILE_APPEND | LOCK_EX);
}
include('./db_connect.php');
include('./recaptcha_config.php');
ob_start();
if(!isset($_SESSION['system'])){
	$system = $conn->query("SELECT * FROM system_settings LIMIT 1")->fetch_array();
	foreach($system as $k => $v){
		$_SESSION['system'][$k] = $v;
	}
}
ob_end_flush();
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['system']['name'] ?> - Admin Login</title>
  <?php include('./header.php'); ?>
  <?php 
  if(isset($_SESSION['login_id']))
    header("location:index.php?page=home");
  ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* Fullscreen Background */
        #main {
            height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: url('../assets/img/moist12.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Responsive background adjustments */
        @media (max-width: 768px) {
            #main {
                background-attachment: scroll;
                background-size: cover;
            }
        }

        /* Login Card */
        .card {
            width: 350px;
            background: rgba(255, 255, 255, 0.98);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            display: block;
            max-width: 70px;
            height: auto;
            margin: 0 auto 15px;
        }

        .login-title {
            color: #333;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 10px 12px;
        }

        .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
        }

        .btn-primary {
            background: #8B4513;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: #A0522D;
        }

        .g-recaptcha {
            margin: 15px 0;
            display: flex;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .brand-logo {
                max-width: 50px;
            }
            .card {
                width: 90%;
                padding: 20px;
            }
        }

    </style>
</head>
<body>

<main id="main">
    <!-- Login Form -->
	<div class="card">
	    <div class="card-body text-center">
	        <img src="assets/img/logo.png" alt="Logo" class="brand-logo">
	        <h3 class="login-title">Admin Login</h3>
	        <form id="login-form">
	            <div class="mb-3 text-start">
	                <label for="username" class="form-label fw-semibold">Username</label>
	                <input type="text" id="username" name="username" class="form-control" placeholder="Enter username" required>
	            </div>
	            <div class="mb-3 text-start">
	                <label for="password" class="form-label fw-semibold">Password</label>
	                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
	            </div>
	            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
	            <button type="submit" class="btn btn-primary w-100">Login</button>
	        </form>
	    </div>
	</div>

</main>

<script>
	$('#login-form').submit(function(e){
		e.preventDefault();
		
		// Skip reCAPTCHA check for testing
		var recaptchaResponse = '';
		$('#login-form button').attr('disabled',true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Logging in...');
		
		if($(this).find('.alert-danger').length > 0 )
			$(this).find('.alert-danger').remove();
			
		$.ajax({
			url: 'ajax.php?action=login',
			method: 'POST',
			data: $(this).serialize(),
			error: err => {
				console.log(err);
				$('#login-form button').removeAttr('disabled').html('Login');
				// grecaptcha.reset();
			},
			success: function(resp) {
				console.log('Login response:', resp);
				resp = resp.toString().trim();
				if(resp == '1'){
					location.href ='index.php?page=home';
				} else if(resp == 'recaptcha_failed') {
					$('#login-form').prepend('<div class="alert alert-danger">reCAPTCHA verification failed. Please try again.</div>');
					$('#login-form button').removeAttr('disabled').html('Login');
				} else {
					$('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
					$('#login-form button').removeAttr('disabled').html('Login');
				}
			}
		});
	});
</script>

</body>
</html> 