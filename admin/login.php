<!DOCTYPE html>
<html lang="en">
<?php
session_start();
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
if(isset($_SESSION['login_id'])) {
    header("location:index.php?page=home");
    exit;
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($_SESSION['system']['name']); ?> - Admin Login</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Left panel - background image with overlay */
        .login-left {
            flex: 1.2;
            position: relative;
            background: url('../assets/img/moist12.jpg') center/cover no-repeat;
            display: flex;
            align-items: flex-end;
            padding: 3rem;
        }

        .login-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(26, 26, 46, 0.85), rgba(15, 52, 96, 0.7));
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 500px;
        }

        .login-left-content h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }

        .login-left-content p {
            font-size: 1rem;
            opacity: 0.85;
            line-height: 1.6;
        }

        /* Right panel - login form */
        .login-right {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8f9fc;
            min-width: 420px;
        }

        .login-form-container {
            width: 100%;
            max-width: 380px;
        }

        .login-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 50%;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            margin-bottom: 1.25rem;
            background: white;
            padding: 4px;
        }

        .login-form-container h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 0.25rem;
        }

        .login-form-container .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            background: white;
            transition: all 0.2s ease;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: #1a1a2e;
            box-shadow: 0 0 0 3px rgba(26, 26, 46, 0.08);
        }

        .input-wrapper input:focus + i.input-icon,
        .input-wrapper input:focus ~ i.input-icon {
            color: #1a1a2e;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: #374151; }

        .g-recaptcha {
            margin: 1rem 0;
            display: flex;
            justify-content: center;
            transform: scale(0.92);
            transform-origin: center;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: white;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26, 26, 46, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Alert */
        .login-alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideDown 0.3s ease;
        }

        .login-alert.alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .login-alert.alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer link */
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .login-footer a {
            color: #1a1a2e;
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .login-left { display: none; }
            .login-right {
                flex: 1;
                min-width: unset;
                background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
                position: relative;
            }
            .login-right::before {
                content: '';
                position: absolute;
                inset: 0;
                background: url('../assets/img/moist12.jpg') center/cover no-repeat;
                opacity: 0.15;
            }
            .login-form-container {
                position: relative;
                z-index: 2;
                background: rgba(255, 255, 255, 0.97);
                padding: 2rem;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
        }

        @media (max-width: 480px) {
            .login-right { padding: 1rem; }
            .login-form-container {
                padding: 1.5rem;
                max-width: 100%;
            }
            .login-logo { width: 60px; height: 60px; }
            .login-form-container h2 { font-size: 1.35rem; }
            .g-recaptcha { transform: scale(0.82); }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <!-- Left Panel -->
    <div class="login-left">
        <div class="login-left-content">
            <h1><?php echo htmlspecialchars($_SESSION['system']['name']); ?></h1>
            <p>Manage alumni records, events, job postings, and system settings from your administration dashboard.</p>
        </div>
    </div>

    <!-- Right Panel - Login Form -->
    <div class="login-right">
        <div class="login-form-container">
            <img src="assets/img/logo.png" alt="Logo" class="login-logo">
            <h2>Admin Login</h2>
            <p class="subtitle">Sign in to access the admin dashboard</p>

            <div id="alert-container"></div>

            <form id="login-form" autocomplete="off">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
                        <i class="fa-regular fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <i class="fa-solid fa-lock input-icon"></i>
                        <button type="button" class="toggle-password" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>

                <button type="submit" class="btn-login" id="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Sign In</span>
                </button>
            </form>

            <div class="login-footer">
                <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Back to main site</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        var input = $(this).siblings('input');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Show alert helper
    function showAlert(message, type) {
        var icon = type === 'danger' ? 'fa-circle-exclamation' : 'fa-circle-check';
        $('#alert-container').html(
            '<div class="login-alert alert-' + type + '">' +
            '<i class="fa-solid ' + icon + '"></i> ' + message +
            '</div>'
        );
    }

    // Login form submit
    $('#login-form').on('submit', function(e) {
        e.preventDefault();

        var username = $('#username').val().trim();
        var password = $('#password').val();

        if (!username || !password) {
            showAlert('Please fill in all fields.', 'danger');
            return;
        }

        $('#alert-container').empty();
        $('#btn-login').prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm"></span> <span>Signing in...</span>'
        );

        $.ajax({
            url: 'ajax.php?action=login',
            method: 'POST',
            data: $(this).serialize(),
            error: function() {
                showAlert('Connection error. Please try again.', 'danger');
                $('#btn-login').prop('disabled', false).html(
                    '<i class="fa-solid fa-right-to-bracket"></i> <span>Sign In</span>'
                );
                if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
            },
            success: function(resp) {
                resp = resp.toString().trim();
                if (resp == '1') {
                    showAlert('Login successful! Redirecting...', 'success');
                    setTimeout(function() {
                        location.href = 'index.php?page=home';
                    }, 500);
                } else if (resp == 'recaptcha_failed') {
                    showAlert('reCAPTCHA verification failed. Please try again.', 'danger');
                    $('#btn-login').prop('disabled', false).html(
                        '<i class="fa-solid fa-right-to-bracket"></i> <span>Sign In</span>'
                    );
                    if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                } else {
                    showAlert('Invalid username or password.', 'danger');
                    $('#btn-login').prop('disabled', false).html(
                        '<i class="fa-solid fa-right-to-bracket"></i> <span>Sign In</span>'
                    );
                    if (typeof grecaptcha !== 'undefined') grecaptcha.reset();
                }
            }
        });
    });

    // Allow Enter key on inputs
    $('input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#login-form').submit();
        }
    });
});
</script>

</body>
</html>