<?php
session_start();
require_once __DIR__ . '/admin/db_connect.php';

// Security Headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

// Redirect if already logged in
if (isset($_SESSION['login_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MOIST Alumni</title>
    
    <!-- Security & Cache Control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #800000;
            --primary-light: #a00000;
            --secondary: #ffd700;
            --secondary-light: #ffea00;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --success: #28a745;
            --danger: #dc3545;
            --info: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: url('assets/img/moist12.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 26, 46, 0.85);
            z-index: -1;
        }
        .reset-container {
            max-width: 520px;
            margin: 20px auto;
            padding: 2.5rem;
            background: rgba(26, 26, 46, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeInUp 0.5s ease-out;
            color: var(--light);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .logo-wrapper {
            display: inline-block;
            position: relative;
            margin-bottom: 1rem;
        }
        
        .logo-container img {
            width: 100px;
            height: 100px;
            filter: drop-shadow(0 8px 20px rgba(255, 215, 0, 0.4));
            animation: logoFloat 3s ease-in-out infinite;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        
        .logo-container h2 {
            color: var(--secondary);
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
        }
        
        .logo-container p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        
        .step-dot {
            width: 14px;
            height: 14px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .step-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--secondary);
            transform: scale(0);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .step-dot.active {
            background: var(--secondary);
            transform: scale(1.3);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
        }
        
        .step-dot.active::after {
            transform: scale(1);
            animation: pulse-ring 1.5s infinite;
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 2px solid #ddd;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .otp-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
            outline: none;
        }
        .timer {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .password-requirements {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 15px;
        }
        .requirement {
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .requirement.met {
            color: #198754;
        }
        .requirement i {
            margin-right: 8px;
        }
        .btn {
            padding: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .delivery-method {
            padding: 10px;
            background: #e9ecef;
            border-radius: 10px;
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
        }
        .contact-input {
            position: relative;
        }
        .contact-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Form Controls */
        .form-label {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            padding: 0.9rem 1.2rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
            color: white;
            outline: none;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        /* OTP Inputs */
        .otp-input {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: var(--light);
            font-weight: 600;
        }
        
        .otp-input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3);
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        /* Timer */
        .timer {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--secondary);
            font-weight: 500;
        }
        
        /* Password Requirements */
        .password-requirements {
            background: rgba(0, 0, 0, 0.2);
            border-left: 4px solid var(--secondary);
            color: rgba(255, 255, 255, 0.8);
        }
        
        .requirement.met {
            color: var(--success);
        }
        
        .requirement.met i {
            color: var(--success);
        }
        
        /* Buttons */
        .btn {
            border-radius: 10px;
            padding: 0.9rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: var(--dark);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #20c997 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .btn:disabled {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Delivery Method */
        .delivery-method {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid rgba(255, 215, 0, 0.3);
            color: var(--secondary);
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 26, 46, 0.98);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(10px);
        }
        
        .loading-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }
        
        .loading-content {
            text-align: center;
            padding: 2.5rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            max-width: 320px;
            width: 90%;
            border: 1px solid rgba(255, 215, 0, 0.1);
        }
        
        .loading-logo-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
        }
        
        .loading-logo {
            width: 100px;
            height: 100px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            animation: pulse 2s infinite ease-in-out;
        }
        
        .loading-ring {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: var(--secondary);
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .loading-text {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-top: 1rem;
            font-weight: 500;
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(0.95); }
            50% { transform: translate(-50%, -50%) scale(1.05); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Back Link */
        .back-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-link:hover {
            color: var(--secondary-light);
            transform: translateX(-3px);
        }
        
        /* Input Group */
        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-left: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .input-group-text:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--secondary);
        }
        
        /* Step Title */
        .step h5 {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        /* Custom Toastr Styling */
        #toast-container > div {
            opacity: 0.95;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
        }
        
        #toast-container > .toast-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        #toast-container > .toast-error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        #toast-container > .toast-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #1a1a2e;
        }
        
        #toast-container > .toast-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        .toast-progress {
            background: rgba(255, 255, 255, 0.5) !important;
        }
        
        .toast-close-button {
            font-weight: 700;
            text-shadow: none;
            opacity: 0.8;
        }
        
        .toast-close-button:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="logo-container animate__animated animate__fadeIn">
                <div class="logo-wrapper">
                    <img src="assets/img/logo.png" alt="Logo">
                </div>
                <h2>Reset Password</h2>
                <p>Secure password recovery</p>
            </div>
            
            <div class="step-indicator">
                <div class="step-dot active" data-step="1"></div>
                <div class="step-dot" data-step="2"></div>
                <div class="step-dot" data-step="3"></div>
            </div>

            <!-- Step 1: Contact Input -->
            <div id="step1" class="step">
                <form id="contact-form">
                    <div class="mb-4">
                        <label class="form-label">Email or Phone Number</label>
                        <div class="contact-input">
                            <input type="text" class="form-control form-control-lg" id="contact" name="contact" required 
                                   placeholder="Enter email or phone (09xxxxxxxxx)">
                            <span class="contact-icon" id="contactTypeIcon"></span>
                        </div>
                        <div class="delivery-method" id="deliveryMethod"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="sendOtpBtn">
                        Send Verification Code
                    </button>
                </form>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="step2" class="step d-none">
                <h5 class="text-center mb-3">Enter Verification Code</h5>
                <form id="otp-form">
                    <div class="otp-inputs">
                        <input type="text" class="otp-input" maxlength="1" required>
                        <input type="text" class="otp-input" maxlength="1" required>
                        <input type="text" class="otp-input" maxlength="1" required>
                        <input type="text" class="otp-input" maxlength="1" required>
                        <input type="text" class="otp-input" maxlength="1" required>
                        <input type="text" class="otp-input" maxlength="1" required>
                    </div>
                    <div class="timer text-center mb-3">
                        Resend available in <span id="countdown">60</span>s
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                </form>
            </div>

            <!-- Step 3: New Password -->
            <div id="step3" class="step d-none">
                <form id="password-form">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-requirements mt-2">
                            <div class="requirement" id="req-length">
                                <i class="fas fa-circle"></i> At least 8 characters
                            </div>
                            <div class="requirement" id="req-uppercase">
                                <i class="fas fa-circle"></i> One uppercase letter
                            </div>
                            <div class="requirement" id="req-lowercase">
                                <i class="fas fa-circle"></i> One lowercase letter
                            </div>
                            <div class="requirement" id="req-number">
                                <i class="fas fa-circle"></i> One number
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Update Password</button>
                </form>
            </div>

            <div class="text-center mt-3">
                <a href="login.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-logo-container">
                <img src="assets/img/logo.png" alt="MOIST Logo" class="loading-logo">
                <div class="loading-ring"></div>
            </div>
            <div class="loading-text" id="loadingText">
                Processing...
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        let countdownInterval;
        const COOLDOWN_TIME = 60;

        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Enhanced Notification Functions
        function showSuccess(message, title = 'Success!') {
            toastr.success(message, title);
        }

        function showError(message, title = 'Error!') {
            toastr.error(message, title);
        }

        function showInfo(message, title = 'Info') {
            toastr.info(message, title);
        }

        function showWarning(message, title = 'Warning!') {
            toastr.warning(message, title);
        }

        // Loading Overlay Functions
        function showLoading(message = 'Processing...') {
            $('#loadingText').text(message);
            $('#loadingOverlay').addClass('show');
        }

        function hideLoading() {
            $('#loadingOverlay').removeClass('show');
        }

        function showStep(step) {
            $('.step').addClass('d-none');
            $(`#step${step}`).removeClass('d-none');
            updateStepIndicators(step);
        }

        function startCountdown() {
            let timeLeft = COOLDOWN_TIME;
            $('#sendOtpBtn').prop('disabled', true);
            
            countdownInterval = setInterval(() => {
                timeLeft--;
                $('#countdown').text(timeLeft);
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    $('#sendOtpBtn').prop('disabled', false);
                }
            }, 1000);
        }

        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function validatePassword(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };

            $('#req-length').toggleClass('met', requirements.length)
                .find('i').toggleClass('fa-circle fa-check-circle', requirements.length);
            $('#req-uppercase').toggleClass('met', requirements.uppercase)
                .find('i').toggleClass('fa-circle fa-check-circle', requirements.uppercase);
            $('#req-lowercase').toggleClass('met', requirements.lowercase)
                .find('i').toggleClass('fa-circle fa-check-circle', requirements.lowercase);
            $('#req-number').toggleClass('met', requirements.number)
                .find('i').toggleClass('fa-circle fa-check-circle', requirements.number);

            return Object.values(requirements).every(req => req);
        }

        // Update step indicators
        function updateStepIndicators(currentStep) {
            $('.step-dot').removeClass('active');
            $(`.step-dot[data-step="${currentStep}"]`).addClass('active');
        }

        // Detect contact type and update UI
        $('#contact').on('input', function() {
            const value = $(this).val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^09\d{9}$/;
            
            if (emailRegex.test(value)) {
                $('#contactTypeIcon').html('<i class="fas fa-envelope"></i>');
                $('#deliveryMethod').html('<i class="fas fa-info-circle"></i> OTP will be sent to your email');
                $('#deliveryMethod').show();
            } else if (phoneRegex.test(value)) {
                $('#contactTypeIcon').html('<i class="fas fa-mobile-alt"></i>');
                $('#deliveryMethod').html('<i class="fas fa-info-circle"></i> OTP will be sent via SMS');
                $('#deliveryMethod').show();
            } else {
                $('#contactTypeIcon').html('');
                $('#deliveryMethod').hide();
            }
        });

        // Add animation to OTP inputs
        $('.otp-input').on('focus', function() {
            $(this).addClass('scale-105').css('border-color', '#0d6efd');
        }).on('blur', function() {
            $(this).removeClass('scale-105').css('border-color', '');
        });

        // Handle OTP input
        $('.otp-input').on('input', function() {
            if (this.value.length === 1) {
                $(this).next('.otp-input').focus();
            }
        });

        $('.otp-input').on('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value) {
                $(this).prev('.otp-input').focus();
            }
        });

        // Form submissions
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();
            const contact = $('#contact').val().trim();
            
            // Validate input
            if (!contact) {
                showWarning('Please enter your email or phone number');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^09\d{9}$/;
            
            if (!emailRegex.test(contact) && !phoneRegex.test(contact)) {
                showError('Please enter a valid email address or phone number (09xxxxxxxxx)', 'Invalid Format');
                return;
            }
            
            // Show loading
            showLoading('Sending verification code...');
            $('#sendOtpBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...');

            $.ajax({
                url: 'send_reset_otp.php',
                method: 'POST',
                data: { contact: contact },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    hideLoading();
                    $('#sendOtpBtn').prop('disabled', false).html('Send Verification Code');
                    
                    if (response.status === 'success') {
                        showSuccess('Verification code sent successfully! Check your ' + (emailRegex.test(contact) ? 'email' : 'phone'), 'OTP Sent!');
                        setTimeout(() => {
                            showStep(2);
                            startCountdown();
                        }, 1500);
                    } else {
                        showError(response.message || 'Failed to send OTP. Please try again.', 'Send Failed');
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    $('#sendOtpBtn').prop('disabled', false).html('Send Verification Code');
                    
                    if (status === 'timeout') {
                        showError('Request timed out. Please check your connection and try again.', 'Timeout Error');
                    } else if (xhr.status === 404) {
                        showError('Service not found. Please contact support.', 'Service Error');
                    } else if (xhr.status === 500) {
                        showError('Server error occurred. Please try again later.', 'Server Error');
                    } else {
                        showError('Failed to send OTP. Please check your connection.', 'Connection Error');
                    }
                }
            });
        });

        $('#otp-form').on('submit', function(e) {
            e.preventDefault();
            const otp = Array.from($('.otp-input')).map(input => input.value).join('');
            const contact = $('#contact').val().trim();
            
            if (otp.length !== 6) {
                showWarning('Please enter all 6 digits of the verification code', 'Incomplete Code');
                $('.otp-input').first().focus();
                return;
            }
            
            // Show loading
            showLoading('Verifying OTP...');

            $.ajax({
                url: 'verify_reset_otp.php',
                method: 'POST',
                data: { otp: otp, contact: contact },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    hideLoading();
                    
                    if (response.status === 'success') {
                        showSuccess('Verification successful! You can now reset your password.', 'Verified!');
                        setTimeout(() => {
                            showStep(3);
                        }, 1500);
                    } else {
                        showError(response.message || 'The code you entered is incorrect. Please try again.', 'Invalid OTP');
                        // Clear OTP inputs
                        $('.otp-input').val('').first().focus();
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    
                    if (status === 'timeout') {
                        showError('Request timed out. Please try again.', 'Timeout Error');
                    } else if (xhr.status === 404) {
                        showError('Verification service not found.', 'Service Error');
                    } else if (xhr.status === 500) {
                        showError('Server error occurred. Please try again.', 'Server Error');
                    } else {
                        showError('Failed to verify OTP. Please check your connection.', 'Connection Error');
                    }
                    $('.otp-input').val('').first().focus();
                }
            });
        });

        $('#password').on('input', function() {
            validatePassword(this.value);
        });

        $('#password-form').on('submit', function(e) {
            e.preventDefault();
            const password = $('#password').val();
            const confirm_password = $('#confirm_password').val();

            if (!password || !confirm_password) {
                showWarning('Please fill in all password fields', 'Missing Information');
                return;
            }

            if (!validatePassword(password)) {
                showError('Please meet all password requirements before continuing', 'Weak Password');
                $('#password').focus();
                return;
            }

            if (password !== confirm_password) {
                showError('Both passwords must match exactly', 'Passwords Don\'t Match');
                $('#confirm_password').val('').focus();
                return;
            }
            
            // Show loading
            showLoading('Updating your password...');

            $.ajax({
                url: 'update_password.php',
                method: 'POST',
                data: {
                    password: password,
                    confirm_password: confirm_password,
                    contact: $('#contact').val().trim()
                },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    hideLoading();
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Updated!',
                            html: '<div style="font-size:1.1rem;color:#28a745;"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Your password has been updated successfully!</p><p class="text-muted mt-2">Redirecting to login page...</p></div>',
                            timer: 3000,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            background: 'var(--dark)',
                            color: 'white'
                        }).then(() => {
                            window.location.href = 'login.php';
                        });
                    } else {
                        showError(response.message || 'Failed to update password. Please try again.', 'Update Failed');
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    
                    if (status === 'timeout') {
                        showError('Request timed out. Please try again.', 'Timeout Error');
                    } else if (xhr.status === 404) {
                        showError('Update service not found. Please contact support.', 'Service Error');
                    } else if (xhr.status === 500) {
                        showError('Server error occurred. Please try again later.', 'Server Error');
                    } else {
                        showError('Failed to update password. Please check your connection.', 'Connection Error');
                    }
                }
            });
        });
    </script>
</body>
</html>
