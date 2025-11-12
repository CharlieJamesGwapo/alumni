<?php
session_start();
include '../admin/db_connect.php';
include '../admin/recaptcha_config.php';

$error = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify reCAPTCHA first
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    if (!verify_recaptcha($recaptcha_response)) {
        $error = "Please complete the reCAPTCHA verification.";
    } else {
        $username = $_POST['username'];
        $password = md5($_POST['password']); // Convert input password to MD5

        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['type'] != 4) {
            $error = "Access denied! Only registrars can log in.";
        } elseif ($user['password'] === $password) { // Check MD5 password
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['type'];
            
            header("Location: alumni.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "User not found.";
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Login</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="../admin/assets/uploads/logo.png"/>
    <link rel="icon" type="image/jpeg" href="../admin/assets/img/logo.png"/>
    <link rel="icon" type="image/jpeg" href="../assets/img/logo.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background-image: url('../assets/img/moist12.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        
        .login-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
            margin: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        @media (max-width: 576px) {
            .login-logo {
                max-width: 120px;
            }
            .login-card {
                margin: 10px;
                background: rgba(255, 255, 255, 0.98);
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="card shadow p-4 login-card">
            <div class="text-center">
              <h4 class="mb-4">Registrar Login</h4>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <!-- reCAPTCHA v2 -->
                <div class="mb-3 d-flex justify-content-center">
                    <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" id="loginBtn">Login</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation with reCAPTCHA
        document.querySelector('form').addEventListener('submit', function(e) {
            const recaptchaResponse = grecaptcha.getResponse();
            
            if (recaptchaResponse.length === 0) {
                e.preventDefault();
                alert('Please complete the reCAPTCHA verification.');
                return false;
            }
            
            // Disable submit button to prevent double submission
            document.getElementById('loginBtn').disabled = true;
            document.getElementById('loginBtn').innerHTML = 'Logging in...';
        });
        
        // Reset button state if there's an error
        <?php if (!empty($error)): ?>
        document.getElementById('loginBtn').disabled = false;
        document.getElementById('loginBtn').innerHTML = 'Login';
        <?php endif; ?>
    </script>
</body>
</html>
