<?php
session_start();
include 'admin/db_connect.php';

// Always show the privacy notice on index.php
if (basename($_SERVER['PHP_SELF']) !== 'privacy_prompt.php') {
    header("Location: privacy_prompt.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agree'])) {
    $_SESSION['privacy_agreed'] = true;
    $_SESSION['agree_timestamp'] = time();
    
    // If user is logged in, store their agreement in database
    if (isset($_SESSION['login_id'])) {
        $user_id = (int) $_SESSION['login_id'];
        $date = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];

        // Ensure user_id is a positive integer
        if ($user_id > 0) {
            // Verify that the referenced alumnus_bio row exists to satisfy FK constraint
            $chk = $conn->prepare("SELECT id FROM alumnus_bio WHERE id = ? LIMIT 1");
            if ($chk) {
                $chk->bind_param("i", $user_id);
                $chk->execute();
                $chk->store_result();
                if ($chk->num_rows > 0) {
                    // Safe to insert into privacy_agreements
                    $stmt = $conn->prepare("INSERT INTO privacy_agreements (user_id, agree_date, ip_address) VALUES (?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param("iss", $user_id, $date, $ip);
                        if (!$stmt->execute()) {
                            error_log("privacy_agreements insert failed for user_id {$user_id}: " . $stmt->error);
                        }
                        $stmt->close();
                    } else {
                        error_log("prepare failed for privacy_agreements insert: " . $conn->error);
                    }
                } else {
                    // Referenced alumnus_bio row missing — log and skip DB insert to avoid FK error
                    error_log("privacy_agreements skipped: alumnus_bio record not found for user_id {$user_id}");
                    // Optionally: redirect user to complete profile instead of silently skipping
                    // header('Location: profile.php'); exit;
                }
                $chk->close();
            } else {
                error_log("prepare failed for alumnus_bio check: " . $conn->error);
            }
        } else {
            error_log("privacy_agreements skipped: invalid login_id ({$user_id})");
        }
    }
    
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Notice | MOIST Alumni Portal</title>
    <meta name="theme-color" content="#800000">
    <meta name="description" content="MOIST Alumni Portal Privacy Notice - Protecting your personal information">
    <link rel="icon" type="image/png" href="assets/img/logo.png"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary: #800000;
            --secondary: #b71c1c;
            --accent: #ffd700;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --bg-light: #f5f6fa;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-light) 0%, #e9ecef 100%);
            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            padding: 0 1rem;
        }

        .privacy-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .privacy-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            transition: var(--transition);
        }

        .privacy-header {
            background: var(--primary);
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            position: relative;
        }

        .privacy-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,215,0,0.1) 0%, rgba(128,0,0,0.1) 100%);
        }

        .privacy-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: block;
            border: 3px solid rgba(255,255,255,0.2);
            object-fit: contain;
        }

        .privacy-title {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
        }

        .privacy-subtitle {
            color: rgba(255,255,255,0.9);
            margin-top: 0.5rem;
            font-size: 1.2rem;
        }

        .privacy-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .privacy-text {
            color: var(--text-light);
            line-height: 1.6;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .privacy-list {
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .privacy-list li {
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .privacy-footer {
            padding: 1.5rem;
            background: var(--bg-light);
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .custom-control-label {
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-agree {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition);
            width: 100%;
        }

        .btn-agree:hover:not(:disabled) {
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128,0,0,0.2);
        }

        .btn-agree:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .contact-email {
            color: var(--primary);
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .privacy-header {
                padding: 1rem;
                text-align: center;
            }
            .privacy-title {
                font-size: 1.8rem;
            }
            .privacy-subtitle {
                font-size: 1rem;
            }
            .privacy-logo {
                width: 80px;
                height: 80px;
            }
        }

        @media (max-width: 767px) {
            .privacy-container {
                padding: 1rem;
            }
            .privacy-card {
                max-height: 80vh;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="privacy-container">
        <div class="privacy-card fade-in">
            <div class="privacy-header">
                <img src="assets/img/logo.png" alt="MOIST Logo" class="privacy-logo">
                <h1 class="privacy-title">MOIST Alumni Portal</h1>
                <p class="privacy-subtitle">Privacy Notice</p>
            </div>
            
            <div class="privacy-content">
                <div class="privacy-body">
                    <p class="privacy-text">
                        In MOIST (Misamis Oriental Institute of Science and Technology), we value your privacy and aim to uphold the same when processing your personal data.
                    </p>
                    
                    <p class="privacy-text">
                        For purposes of processing your Alumni eCard, we may collect basic information about you such as:
                    </p>
                    
                    <ul class="privacy-list">
                        <li>Name</li>
                        <li>Student Number</li>
                        <li>Program</li>
                        <li>E-mail</li>
                        <li>ID Picture</li>
                    </ul>
                    
                    <p class="privacy-text">
                        We are committed to protecting your personal data from loss, misuse, and any unauthorized processing activities, and will take all reasonable precautions to safeguard its security and confidentiality. Neither will we disclose, share, or transfer the same to any third party without your consent.
                    </p>
                    
                    <p class="privacy-text">
                        Unless you agree to have us retain your personal data for the purposes stated above, your data will only be kept for a limited period as soon as the purpose for their use has been achieved after which, they will be disposed of in a safe and secure manner.
                    </p>
                    
                    <p class="privacy-text">
                        We recognize your rights with respect to your personal data. Should you wish to exercise any of them or if you have any concerns regarding our processing activities, you may contact us at 
                        <a href="mailto:privacy@moist.edu.ph" class="contact-email">privacy@moist.edu.ph</a>
                    </p>
                </div>
                
                <div class="privacy-footer">
                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="privacyCheck" required>
                                <label class="custom-control-label" for="privacyCheck">
                                    I have read and agree to the privacy notice
                                </label>
                            </div>
                        </div>
                        <button type="submit" name="agree" class="btn btn-agree" id="agreeBtn" disabled>
                            Proceed to Alumni Portal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your existing footer -->
    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable/disable submit button based on checkbox
        $(document).ready(function() {
            $('#privacyCheck').change(function() {
                $('#agreeBtn').prop('disabled', !this.checked);
            });
            
            // Adjust height dynamically
            function adjustHeight() {
                const windowHeight = $(window).height();
                const headerHeight = $('.privacy-header').outerHeight();
                const footerHeight = $('.privacy-footer').outerHeight();
                const maxBodyHeight = windowHeight - headerHeight - footerHeight - 100;
                $('.privacy-body').css('max-height', maxBodyHeight + 'px');
            }

            adjustHeight();
            $(window).resize(adjustHeight);
        });
    </script>
</body>
</html>
