<?php
session_start();
include '../admin/db_connect.php';
include '../admin/recaptcha_config.php';
include_once '../admin/rate_limit.php';

// Redirect if already logged in
if (isset($_SESSION['login_id']) && isset($_SESSION['login_type']) && $_SESSION['login_type'] == 4) {
    header("Location: alumni.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    if (!verify_recaptcha($recaptcha_response)) {
        $error = "Please complete the reCAPTCHA verification.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

            // Rate limit check
            $rl = check_rate_limit($conn, $ip, 'registrar', 5, 15);
            if ($rl['blocked']) {
                $mins = ceil($rl['retry_after'] / 60);
                $error = "Too many failed attempts. Please try again in {$mins} minute(s).";
            } else {
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND type = 4 LIMIT 1");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // Check account lockout
                    if (is_account_locked($conn, 'users', $user['id'])) {
                        $error = "Account temporarily locked due to too many failed attempts. Try again later.";
                        record_login_attempt($conn, $ip, $username, 'registrar', false);
                    } else {
                        $password_valid = false;
                        if (password_verify($password, $user['password'])) {
                            $password_valid = true;
                        } elseif (md5($password) === $user['password']) {
                            $password_valid = true;
                            $new_hash = password_hash($password, PASSWORD_DEFAULT);
                            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                            $upd->bind_param("si", $new_hash, $user['id']);
                            $upd->execute();
                            $upd->close();
                        }

                        if ($password_valid) {
                            session_regenerate_id(true);
                            $_SESSION['login_id'] = $user['id'];
                            $_SESSION['login_name'] = $user['name'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['login_type'] = $user['type'];

                            reset_failed_attempts($conn, 'users', $user['id']);
                            record_login_attempt($conn, $ip, $username, 'registrar', true);

                            header("Location: alumni.php");
                            exit();
                        } else {
                            increment_failed_attempts($conn, 'users', $user['id']);
                            record_login_attempt($conn, $ip, $username, 'registrar', false);
                            $error = "Invalid password. " . $rl['remaining'] . " attempt(s) remaining.";
                        }
                    }
                } else {
                    record_login_attempt($conn, $ip, $username, 'registrar', false);
                    $error = "Account not found or access denied.";
                }
                $stmt->close();
            }
        }
    }
}

// Get system name
$sys_name = 'MOIST Alumni';
$sys_q = $conn->query("SELECT name FROM system_settings LIMIT 1");
if ($sys_q && $row = $sys_q->fetch_assoc()) {
    $sys_name = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Login | <?php echo htmlspecialchars($sys_name); ?></title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png"/>
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
        }

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
            background: linear-gradient(135deg, rgba(128,0,0,0.85), rgba(80,0,0,0.7));
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 480px;
        }

        .login-left-content h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .login-left-content p {
            font-size: 1rem;
            opacity: 0.85;
            line-height: 1.6;
        }

        .login-right {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8f9fc;
            min-width: 420px;
        }

        .login-form-box {
            width: 100%;
            max-width: 380px;
        }

        .login-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            border-radius: 50%;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            margin-bottom: 1.25rem;
            background: white;
            padding: 4px;
        }

        .login-form-box h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .login-form-box .subtitle {
            color: #64748b;
            font-size: 0.88rem;
            margin-bottom: 1.75rem;
        }

        .form-group { margin-bottom: 1.15rem; }

        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i.icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            background: white;
            transition: all 0.2s;
            outline: none;
        }

        .input-wrap input:focus {
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128,0,0,0.08);
        }

        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
        }

        .toggle-pw:hover { color: #374151; }

        .g-recaptcha {
            margin: 1rem 0;
            transform: scale(0.92);
            transform-origin: left;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: white;
            background: #800000;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: #600000;
            box-shadow: 0 4px 16px rgba(128,0,0,0.3);
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert-box {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-footer {
            text-align: center;
            margin-top: 1.75rem;
            font-size: 0.8rem;
            color: #9ca3af;
        }

        .login-footer a { color: #800000; text-decoration: none; font-weight: 500; }
        .login-footer a:hover { text-decoration: underline; }

        .badge-registrar {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        @media (max-width: 991px) {
            .login-left { display: none; }
            .login-right {
                flex: 1;
                min-width: unset;
                background: #800000;
                position: relative;
            }
            .login-right::before {
                content: '';
                position: absolute;
                inset: 0;
                background: url('../assets/img/moist12.jpg') center/cover no-repeat;
                opacity: 0.12;
            }
            .login-form-box {
                position: relative;
                z-index: 2;
                background: rgba(255,255,255,0.97);
                padding: 2rem;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            }
        }

        @media (max-width: 480px) {
            .login-right { padding: 1rem; }
            .login-form-box { padding: 1.5rem; max-width: 100%; }
            .login-logo { width: 56px; height: 56px; }
            .login-form-box h2 { font-size: 1.3rem; }
            .g-recaptcha { transform: scale(0.82); }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-left">
        <div class="login-left-content">
            <h1><?php echo htmlspecialchars($sys_name); ?></h1>
            <p>Registrar portal for managing alumni records, verification, ID cards, events, and communications.</p>
        </div>
    </div>

    <div class="login-right">
        <div class="login-form-box">
            <img src="../assets/img/logo.png" alt="Logo" class="login-logo">
            <div class="badge-registrar"><i class="fa-solid fa-shield-halved"></i> Registrar Access</div>
            <h2>Registrar Login</h2>
            <p class="subtitle">Sign in to manage alumni records</p>

            <?php if (!empty($error)): ?>
            <div class="alert-box">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <input type="text" name="username" placeholder="Enter your username" required autofocus value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        <i class="fa-regular fa-user icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="password" placeholder="Enter your password" required>
                        <i class="fa-solid fa-lock icon"></i>
                        <button type="button" class="toggle-pw" onclick="togglePassword()">
                            <i class="fa-regular fa-eye" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>

                <button type="submit" class="btn-login" id="loginBtn">
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

<script>
function togglePassword() {
    var pw = document.getElementById('password');
    var icon = document.getElementById('pwIcon');
    if (pw.type === 'password') {
        pw.type = 'text';
        icon.className = 'fa-regular fa-eye-slash';
    } else {
        pw.type = 'password';
        icon.className = 'fa-regular fa-eye';
    }
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    if (typeof grecaptcha !== 'undefined') {
        var resp = grecaptcha.getResponse();
        if (resp.length === 0) {
            e.preventDefault();
            alert('Please complete the reCAPTCHA verification.');
            return false;
        }
    }
    var btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Signing in...';
});

<?php if (!empty($error)): ?>
var btn = document.getElementById('loginBtn');
btn.disabled = false;
btn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> <span>Sign In</span>';
<?php endif; ?>
</script>

</body>
</html>
