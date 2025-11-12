<?php
session_start();
require_once 'auth_functions.php';

// Comprehensive Security Headers - Enhanced
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0");
header("Pragma: no-cache");
header("Expires: Mon, 01 Jan 1990 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://cdn.jsdelivr.net https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data:; connect-src 'self';");

// Prevent browser back button after logout
if (isset($_SESSION['alumni_id'])) {
    header("Location: alumni/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOIST Alumni Login</title>
    
    <!-- Enhanced Security & Cache Control Meta Tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0, post-check=0, pre-check=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="no-referrer">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #800000;
            --primary-light: #a00000;
            --secondary: #ffd700;
            --secondary-light: #ffea00;
            --dark: #1a1a2e;
            --darker: #16213e;
            --light: #f8f9fa;
            --light-gray: #e9ecef;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #fd7e14;
            --danger: #dc3545;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: var(--light);
            position: relative;
            overflow-x: hidden;
            background: url('assets/img/moist12.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Background overlay */
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

        /* Animated background elements */
        .bg-circle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255, 215, 0, 0.05);
            z-index: -1;
            animation: float 15s infinite ease-in-out;
        }

        .bg-circle:nth-child(1) {
            width: 80vw;
            height: 80vw;
            top: -30vw;
            right: -30vw;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 60vw;
            height: 60vw;
            bottom: -20vw;
            left: -20vw;
            animation-delay: 2s;
        }

        .bg-circle:nth-child(3) {
            width: 40vw;
            height: 40vw;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(0, 20px); }
        }

        /* Main container */
        .login-container {
            background: rgba(26, 26, 46, 0.95);
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
            padding: 1.5rem;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            animation: fadeInUp 0.5s ease-out;
            margin: 1rem auto;
            max-height: 95vh;
            overflow-y: auto;
        }

        .login-container:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            transform: translateY(-5px);
        }

        /* Header section */
        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .back-to-home {
            position: absolute;
            top: -10px;
            left: -10px;
            color: var(--light);
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.1);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .back-to-home:hover {
            color: var(--secondary);
            background: rgba(255, 215, 0, 0.2);
            transform: translateX(-3px);
        }

        /* Professional Logo Container */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 8px 20px rgba(255, 215, 0, 0.4));
            animation: logoFloat 3s ease-in-out infinite;
            position: relative;
        }

        /* Logo background glow */
        .logo-container::before {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 2s ease-in-out infinite;
            z-index: 1;
        }

        @keyframes logoFloat {
            0%, 100% { 
                transform: translateY(0px);
            }
            50% { 
                transform: translateY(-8px);
            }
        }

        @keyframes pulse {
            0%, 100% { 
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1);
            }
            50% { 
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        .login-title {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            background: linear-gradient(to right, var(--secondary), var(--secondary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            font-weight: 400;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: var(--light);
            padding: 0.75rem 1rem;
            transition: all 0.3s;
            font-size: 0.95rem;
            height: auto;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

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

        /* OTP Section */
        .otp-section {
            margin: 1rem 0;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            border-left: 3px solid var(--secondary);
            animation: fadeIn 0.5s ease-out;
            display: none;
        }

        .otp-title {
            font-size: 0.9rem;
            color: var(--secondary);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .otp-inputs {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .otp-input {
            width: 40px;
            height: 48px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--light);
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.3);
            transform: translateY(-2px);
        }

        .otp-input.filled {
            border-color: var(--success);
            background: rgba(40, 167, 69, 0.1);
        }

        .otp-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .otp-timer {
            font-size: 0.85rem;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .otp-timer.expired {
            color: var(--danger);
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-send-otp {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-light) 100%);
            color: var(--dark);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.75rem 1.5rem;
        }

        .btn-send-otp:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
            background: linear-gradient(135deg, var(--secondary-light) 0%, var(--secondary) 100%);
        }

        .btn-send-otp:disabled {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            width: 100%;
            padding: 0.85rem;
            font-size: 1rem;
            margin-top: 0.75rem;
            box-shadow: 0 4px 15px rgba(128, 0, 0, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(128, 0, 0, 0.4);
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
        }

        .btn-login:disabled {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            box-shadow: none;
            cursor: not-allowed;
        }

        /* Checkbox */
        .form-check {
            margin: 1rem 0;
        }

        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.1em;
            background-color: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Footer links */
        .login-footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 0.85rem;
        }

        .login-footer p {
            margin-bottom: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .login-footer a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .login-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--secondary);
            transition: width 0.3s;
        }

        .login-footer a:hover {
            color: white;
        }

        .login-footer a:hover::after {
            width: 100%;
        }

        /* Spinner */
        .spinner {
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--secondary);
            animation: spin 1s linear infinite;
            display: none;
        }

        /* Updated Loading Overlay Styles */
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
            -webkit-backdrop-filter: blur(10px);
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

        .loading-ring::before,
        .loading-ring::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .loading-ring::before {
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border-top-color: var(--primary);
            animation: spin 2s linear infinite;
        }

        .loading-ring::after {
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border-top-color: var(--secondary);
            animation: spin 1.5s linear infinite;
        }

        .loading-text {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-top: 1.5rem;
            font-weight: 500;
        }

        .loading-dots span {
            position: relative;
        }

        .loading-dots span::after {
            content: '...';
            position: absolute;
            animation: dots 2s infinite;
            margin-left: 2px;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(0.95); }
            50% { transform: translate(-50%, -50%) scale(1.05); }
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .loading-content {
                padding: 2rem;
                max-width: 280px;
            }
            
            .loading-logo-container {
                width: 100px;
                height: 100px;
            }
            
            .loading-logo {
                width: 80px;
                height: 80px;
            }
            
            .loading-ring {
                width: 100px;
                height: 100px;
            }
            
            .loading-text {
                font-size: 1rem;
            }
        }

        /* Additional animation for showing/hiding overlay */
        .loading-overlay.show {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .loading-overlay.hide {
            animation: fadeOut 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* Status messages */
        .status-message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.3s ease-out;
        }

        .status-message.success {
            background: rgba(40, 167, 69, 0.15);
            border-left: 4px solid var(--success);
            color: #b8f5c2;
        }

        .status-message.error {
            background: rgba(220, 53, 69, 0.15);
            border-left: 4px solid var(--danger);
            color: #ffb8c1;
        }

        .status-message.info {
            background: rgba(23, 162, 184, 0.15);
            border-left: 4px solid var(--info);
            color: #b8e6f5;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            -webkit-backdrop-filter: blur(10px);
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

        .loading-ring::before,
        .loading-ring::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 3px solid transparent;
        }

        .loading-ring::before {
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border-top-color: var(--primary);
            animation: spin 2s linear infinite;
        }

        .loading-ring::after {
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border-top-color: var(--secondary);
            animation: spin 1.5s linear infinite;
        }

        .loading-text {
            color: var(--secondary);
            font-size: 1.1rem;
            margin-top: 1.5rem;
            font-weight: 500;
        }

        .loading-dots span {
            position: relative;
        }

        .loading-dots span::after {
            content: '...';
            position: absolute;
            animation: dots 2s infinite;
            margin-left: 2px;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(0.95); }
            50% { transform: translate(-50%, -50%) scale(1.05); }
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .loading-content {
                padding: 2rem;
                max-width: 280px;
            }
            
            .loading-logo-container {
                width: 100px;
                height: 100px;
            }
            
            .loading-logo {
                width: 80px;
                height: 80px;
            }
            
            .loading-ring {
                width: 100px;
                height: 100px;
            }
            
            .loading-text {
                font-size: 1rem;
            }
        }

        /* Additional animation for showing/hiding overlay */
        .loading-overlay.show {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .loading-overlay.hide {
            animation: fadeOut 0.3s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        /* Enhanced Responsive Design */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }
            
            .login-container {
                padding: 1.25rem;
                max-width: 100%;
                margin: 0.5rem auto;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .logo-container::before {
                width: 100px;
                height: 100px;
            }
            
            .login-title {
                font-size: 1.35rem;
            }
            
            .login-subtitle {
                font-size: 0.8rem;
            }
            
            .form-control {
                padding: 0.7rem 0.9rem;
                font-size: 0.9rem;
            }
            
            .otp-input {
                width: 36px;
                height: 44px;
                font-size: 1rem;
            }
            
            .otp-inputs {
                gap: 0.4rem;
            }
            
            .btn {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .login-footer {
                font-size: 0.8rem;
            }
        }
        
        @media (max-height: 700px) {
            .login-container {
                padding: 1rem;
                margin: 0.5rem auto;
            }
            
            .login-header {
                margin-bottom: 1rem;
            }
            
            .logo-container {
                margin-bottom: 0.75rem;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .otp-section {
                padding: 0.85rem;
                margin: 0.75rem 0;
            }
            
            .form-check {
                margin: 0.75rem 0;
            }
            
            .login-footer {
                margin-top: 1rem;
            }
        }
        
        /* Scrollbar Styling */
        .login-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .login-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        
        .login-container::-webkit-scrollbar-thumb {
            background: rgba(255, 215, 0, 0.3);
            border-radius: 10px;
        }
        
        .login-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 215, 0, 0.5);
        }
    </style>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
</head>
<body>
    <!-- Background elements -->
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="login-container">
        <a href="index.php" class="back-to-home">
            <i class="fas fa-arrow-left"></i>
        </a>
        
        <div class="login-header animate__animated animate__fadeIn">
            <div class="logo-container">
                <img src="assets/img/logo.png" alt="MOIST Logo" class="login-logo">
            </div>
            <h1 class="login-title">Alumni Login</h1>
            <p class="login-subtitle">Welcome back! Please login to continue</p>
        </div>

        <div id="login-message"></div>

        <form id="login-form" method="POST" action="verify_login.php">
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                    placeholder="Enter your registered email" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" 
                        placeholder="Enter your password" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <!-- Send OTP Button -->
            <button type="button" class="btn btn-send-otp" id="send-otp" disabled>
                <span id="send-text">Send OTP</span>
                <span class="spinner" id="send-spinner"></span>
            </button>

            <!-- OTP Section -->
            <div class="otp-section" id="otp-section">
                <div class="otp-title">
                    <i class="fas fa-shield-alt"></i>
                    <span>Two-Factor Authentication</span>
                </div>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 1.25rem;">
                    For your security, we've sent a 6-digit code to your email. Please enter it below.
                </p>
                
                <div class="otp-inputs">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                </div>
                
                <input type="hidden" id="otp" name="otp">
                
                <div class="otp-actions">
                    <div class="otp-timer" id="otp-timer">
                        <i class="fas fa-clock"></i>
                        <span id="otp-countdown">05:00</span>
                    </div>
                    <button type="button" class="btn btn-send-otp" id="resend-otp" disabled>
                        <span id="resend-text">Resend OTP</span>
                        <span class="spinner" id="otp-spinner"></span>
                    </button>
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me on this device</label>
            </div>

            <!-- Open-edit-after-login removed: edit profile will open automatically after login -->

            <button type="submit" class="btn btn-login" id="login-btn" disabled>
                <span class="spinner" id="login-spinner"></span>
                <span id="login-text">Login</span>
            </button>

            <div class="login-footer animate__animated animate__fadeIn animate__delay-1s">
                <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                <p><a href="reset_password.php">Forgot your password?</a></p>
            </div>
        </form>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-logo-container">
                <img src="assets/img/logo.png" alt="MOIST Logo" class="loading-logo">
                <div class="loading-ring"></div>
            </div>
            <div class="loading-text" id="loadingText">
                <span class="loading-dots">Verifying your credentials</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ============================================
        // ENHANCED SECURITY MEASURES
        // ============================================
        
        // Comprehensive DevTools Detection and Prevention
        (function() {
            'use strict';
            
            // Detect DevTools opening
            const devtools = {
                isOpen: false,
                orientation: null
            };
            
            const threshold = 160;
            const emitEvent = (isOpen, orientation) => {
                window.dispatchEvent(new CustomEvent('devtoolschange', {
                    detail: { isOpen, orientation }
                }));
            };
            
            const main = ({ emitEvents = true } = {}) => {
                const widthThreshold = window.outerWidth - window.innerWidth > threshold;
                const heightThreshold = window.outerHeight - window.innerHeight > threshold;
                const orientation = widthThreshold ? 'vertical' : 'horizontal';
                
                if (!(heightThreshold && widthThreshold) && ((window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized) || widthThreshold || heightThreshold)) {
                    if ((!devtools.isOpen || devtools.orientation !== orientation) && emitEvents) {
                        emitEvent(true, orientation);
                    }
                    devtools.isOpen = true;
                    devtools.orientation = orientation;
                } else {
                    if (devtools.isOpen && emitEvents) {
                        emitEvent(false, null);
                    }
                    devtools.isOpen = false;
                    devtools.orientation = null;
                }
            };
            
            main({ emitEvents: false });
            setInterval(main, 500);
            
            // Block page when DevTools detected
            window.addEventListener('devtoolschange', event => {
                if (event.detail.isOpen) {
                    document.body.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#1a1a2e;color:#fff;font-family:Arial;text-align:center;flex-direction:column;"><i class="fas fa-shield-alt" style="font-size:4rem;color:#dc3545;margin-bottom:1rem;"></i><h1>Access Restricted</h1><p style="font-size:1.2rem;max-width:600px;margin:1rem auto;">For security reasons, developer tools are not allowed on this page.</p><button onclick="location.reload()" style="margin-top:1rem;padding:0.75rem 2rem;background:#800000;color:#fff;border:none;border-radius:8px;font-size:1rem;cursor:pointer;">Reload Page</button></div>';
                }
            });
        })();
        
        // Disable All Inspection Methods
        document.addEventListener('keydown', function(e) {
            // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C, Ctrl+U, F5 (with Ctrl)
            if (e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.shiftKey && e.keyCode === 67) || // Ctrl+Shift+C
                (e.ctrlKey && e.keyCode === 85) || // Ctrl+U
                (e.ctrlKey && e.keyCode === 83)) { // Ctrl+S
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
        
        // Disable right-click completely
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable text selection on sensitive elements
        document.addEventListener('selectstart', function(e) {
            if (e.target.tagName === 'INPUT' && (e.target.type === 'password' || e.target.type === 'email')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable copy/paste on password fields
        document.addEventListener('copy', function(e) {
            if (document.activeElement.type === 'password') {
                e.preventDefault();
                return false;
            }
        });
        
        // Comprehensive Cache Prevention
        window.onload = function() {
            if (performance.navigation.type === 2 || performance.navigation.type === 1) {
                window.location.reload(true);
            }
        };
        
        // Prevent back button navigation
        (function() {
            if (window.history && window.history.pushState) {
                window.history.pushState('forward', null, window.location.href);
                window.addEventListener('popstate', function() {
                    window.history.pushState('forward', null, window.location.href);
                });
            }
        })();
        
        // Clear form data from cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload(true);
            }
        });
        
        // Clear all form fields on page load
        window.addEventListener('load', function() {
            document.querySelectorAll('input').forEach(input => {
                if (input.type !== 'checkbox' && input.id !== 'email') {
                    input.value = '';
                }
            });
        });
        
        // Clear sensitive data on page unload
        window.addEventListener('beforeunload', function() {
            document.querySelectorAll('input[type="password"], .otp-input').forEach(input => {
                input.value = '';
            });
            sessionStorage.clear();
        });
        
        // Detect and prevent console usage
        (function() {
            const noop = function() {};
            const methods = ['log', 'debug', 'info', 'warn', 'error', 'table', 'trace', 'dir', 'dirxml', 'group', 'groupCollapsed', 'groupEnd', 'clear', 'count', 'countReset', 'assert', 'profile', 'profileEnd', 'time', 'timeLog', 'timeEnd', 'timeStamp'];
            const consoleMock = {};
            methods.forEach(method => {
                consoleMock[method] = noop;
            });
            window.console = consoleMock;
        })();
        
        // ============================================
        // APPLICATION CODE
        // ============================================
        
        // Global variables
        let otpTimer;
        let otpResendInterval;
        const OTP_TIMEOUT = 300; // 5 minutes in seconds
        const OTP_RESEND_DELAY = 30; // 30 seconds delay for resend
        
        $(document).ready(function() {
            const $form = $('#login-form');
            const $loginBtn = $('#login-btn');
            const $loginSpinner = $('#login-spinner');
            const $loginText = $('#login-text');
            const $otpSection = $('#otp-section');
            const $otpInputs = $('.otp-input');
            const $otpField = $('#otp');
            const $sendOtpBtn = $('#send-otp');
            const $sendText = $('#send-text');
            const $sendSpinner = $('#send-spinner');
            const $resendBtn = $('#resend-otp');
            const $resendText = $('#resend-text');
            const $otpSpinner = $('#otp-spinner');
            const $emailInput = $('#email');
            const $passwordInput = $('#password');

            // Initialize - hide OTP section
            $otpSection.hide();
            $loginBtn.prop('disabled', true);

            // Function to enable/disable send OTP button
            function updateSendOtpButton() {
                const email = $emailInput.val().trim();
                const password = $passwordInput.val();
                $sendOtpBtn.prop('disabled', !email || !password);
            }

            // Check for Remember Me cookie
            const rememberedEmail = getCookie('moist_remember_email');
            if (rememberedEmail) {
                $emailInput.val(atob(rememberedEmail)); // Decode base64
                $('#remember').prop('checked', true);
                updateSendOtpButton();
                showMessage('info', 'Welcome back! Your email is pre-filled.');
            }

            // Update OTP button state on input changes
            $emailInput.on('input', updateSendOtpButton);
            $passwordInput.on('input', updateSendOtpButton);
            
            // Send OTP button click handler
            $sendOtpBtn.on('click', function() {
                const email = $emailInput.val().trim();
                const password = $passwordInput.val();
                
                if (!email || !password) {
                    showMessage('error', 'Please enter your email and password first');
                    return;
                }
                
                // Show loading overlay
                showLoading('Verifying credentials...');
                
                // Disable form inputs
                $emailInput.prop('disabled', true);
                $passwordInput.prop('disabled', true);
                $sendOtpBtn.prop('disabled', true);
                
                // First verify credentials
                $.ajax({
                    url: 'verify_login_first.php',
                    method: 'POST',
                    data: { 
                        email: email, 
                        password: password 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update loading text
                            $('#loadingText').text('Sending OTP to your email...');
                            
                            // Then send OTP
                            $.ajax({
                                url: 'send_otp.php',
                                method: 'POST',
                                data: { email: email },
                                dataType: 'json',
                                success: function(otpResponse) {
                                    if (otpResponse.status === 'success') {
                                        // Hide loading overlay with success animation
                                        $('#loadingText').text('OTP Sent Successfully!');
                                        setTimeout(() => {
                                            hideLoading(() => {
                                                showMessage('success', 'OTP sent successfully! Please check your email.');
                                                $otpSection.slideDown();
                                                startOtpCountdown();
                                                $otpInputs.first().focus();
                                                $sendOtpBtn.hide();
                                            });
                                        }, 1000);
                                    } else {
                                        showMessage('error', otpResponse.message || 'Failed to send OTP');
                                        hideLoading();
                                    }
                                },
                                error: function() {
                                    showMessage('error', 'Failed to send OTP. Please try again.');
                                    hideLoading();
                                },
                                complete: function() {
                                    // Re-enable form inputs
                                    $emailInput.prop('disabled', false);
                                    $passwordInput.prop('disabled', false);
                                    $sendOtpBtn.prop('disabled', false);
                                }
                            });
                        } else {
                            hideLoading();
                            showMessage('error', response.message || 'Invalid credentials');
                            // Re-enable form inputs
                            $emailInput.prop('disabled', false);
                            $passwordInput.prop('disabled', false);
                            $sendOtpBtn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        hideLoading();
                        showMessage('error', 'Failed to verify credentials. Please try again.');
                        // Re-enable form inputs
                        $emailInput.prop('disabled', false);
                        $passwordInput.prop('disabled', false);
                        $sendOtpBtn.prop('disabled', false);
                    }
                });
            });

            // Initialize OTP input behavior
            function initOtpInputs() {
                $otpInputs.on('input', function() {
                    const value = $(this).val();
                    if (value.length === 1) {
                        $(this).addClass('filled');
                        const nextInput = $(this).next('.otp-input');
                        if (nextInput.length) {
                            nextInput.focus();
                        } else {
                            $(this).blur();
                        }
                    } else {
                        $(this).removeClass('filled');
                    }
                    
                    updateOtpValue();
                });
                
                $otpInputs.on('keydown', function(e) {
                    if (e.key === 'Backspace' && $(this).val().length === 0) {
                        const prevInput = $(this).prev('.otp-input');
                        if (prevInput.length) {
                            prevInput.focus();
                        }
                    }
                });
            }
            
            function updateOtpValue() {
                let otp = '';
                $otpInputs.each(function() {
                    otp += $(this).val();
                });
                
                $otpField.val(otp);
                $loginBtn.prop('disabled', otp.length !== 6);
            }
            
            // Initialize OTP inputs
            initOtpInputs();
            
            // Resend OTP button handler
            $resendBtn.on('click', function() {
                const email = $emailInput.val().trim();
                
                if (!email) {
                    showMessage('error', 'Please enter your email first');
                    return;
                }
                
                // Show loading overlay
                $('#loadingOverlay').css('display', 'flex').hide().fadeIn(300);
                $('#loadingText').text('Resending OTP...');
                
                // Disable buttons
                $resendBtn.prop('disabled', true);
                
                // Send OTP request
                $.ajax({
                    url: 'send_otp.php',
                    method: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Hide loading overlay with success animation
                            $('#loadingText').text('New OTP Sent Successfully!');
                            setTimeout(() => {
                                $('#loadingOverlay').fadeOut(300);
                                showMessage('success', 'New OTP sent successfully!');
                                resetOtpInputs();
                                startOtpCountdown();
                                $otpInputs.first().focus();
                            }, 1000);
                        } else {
                            $('#loadingOverlay').fadeOut(300);
                            showMessage('error', response.message || 'Failed to resend OTP');
                        }
                    },
                    error: function() {
                        $('#loadingOverlay').fadeOut(300);
                        showMessage('error', 'Failed to resend OTP. Please try again.');
                    },
                    complete: function() {
                        $resendBtn.prop('disabled', true);
                        $resendText.text('Resend OTP');
                        $otpSpinner.hide();
                    }
                });
            });
            
            function resetOtpInputs() {
                $otpInputs.val('').removeClass('filled');
                $otpField.val('');
                $loginBtn.prop('disabled', true);
            }
            
            function startOtpCountdown() {
                clearInterval(otpTimer);
                clearInterval(otpResendInterval);
                
                let timeLeft = OTP_TIMEOUT;
                let resendDelay = OTP_RESEND_DELAY;
                
                // Update countdown display
                const updateDisplay = () => {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    $('#otp-countdown').text(`${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
                    
                    if (timeLeft <= 0) {
                        clearInterval(otpTimer);
                        $('#otp-timer').addClass('expired').find('span').text('OTP expired');
                        startResendCountdown();
                    }
                    timeLeft--;
                };
                
                updateDisplay();
                otpTimer = setInterval(updateDisplay, 1000);
            }
            
            function startResendCountdown() {
                let delayLeft = OTP_RESEND_DELAY;
                
                $resendBtn.prop('disabled', true);
                $resendText.text(`Resend in ${delayLeft}s`);
                
                otpResendInterval = setInterval(() => {
                    delayLeft--;
                    $resendText.text(`Resend in ${delayLeft}s`);
                    
                    if (delayLeft <= 0) {
                        clearInterval(otpResendInterval);
                        $resendBtn.prop('disabled', false);
                        $resendText.text('Resend OTP');
                    }
                }, 1000);
            }
            
            // Form submission handler
            $form.on('submit', function(e) {
                e.preventDefault();
                
                const email = $emailInput.val().trim();
                const password = $passwordInput.val();
                const otp = $otpField.val();
                const remember = $('#remember').prop('checked');
                
                if (!isValidEmail(email)) {
                    showMessage('error', 'Please enter a valid email address');
                    return;
                }
                
                if (!password || password.length < 6) {
                    showMessage('error', 'Password must be at least 6 characters');
                    return;
                }
                
                if (!otp || otp.length !== 6) {
                    showMessage('error', 'Please enter a valid 6-digit OTP');
                    return;
                }
                
                // Show loading state
                $loginBtn.prop('disabled', true);
                $loginText.text('Authenticating...');
                $loginSpinner.show();
                
                // Handle Remember Me before submitting
                if (remember) {
                    // Store email in secure cookie for 30 days
                    setCookie('moist_remember_email', btoa(email), 30);
                } else {
                    // Remove cookie if unchecked
                    deleteCookie('moist_remember_email');
                }
                
                // Submit login request
                $.ajax({
                    url: 'verify_login.php',
                    method: 'POST',
                    data: { 
                        email: email,
                        password: password,
                        otp: otp,
                        remember: remember ? 'true' : 'false'
                    },
                    dataType: 'json',
                    success: function(response) {
                        try {
                            // Parse response if it's a string
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                            
                            if (response.status === 'success') {
                                // Clear any previous errors
                                $('#login-message').empty();
                                
                                // Show success message and redirect
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Welcome Back!',
                                    html: '<div style="font-size:1.1rem">Login successful! Redirecting to your profile...</div>',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    background: 'var(--dark)',
                                    color: 'white',
                                    didClose: () => {
                                        window.location.href = response.redirect || 'home.php';
                                    }
                                });
                            } else {
                                throw new Error(response.message || 'Login failed');
                            }
                        } catch (e) {
                            showMessage('error', e.message);
                            $loginBtn.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Login failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showMessage('error', errorMessage);
                        $loginBtn.prop('disabled', false);
                    },
                    complete: function() {
                        $loginText.text('Login');
                        $loginSpinner.hide();
                    }
                });
            });
            
            function showMessage(type, text) {
                const $message = $('#login-message');
                const icon = type === 'error' ? 'fa-exclamation-circle' : 
                            type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
                
                $message.html(`
                    <div class="status-message ${type} animate__animated animate__fadeIn">
                        <i class="fas ${icon}"></i>
                        <span>${text}</span>
                    </div>
                `);
                
                // Auto-hide success messages after 5 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        $message.find('.status-message').addClass('animate__fadeOut');
                        setTimeout(() => $message.empty(), 300);
                    }, 5000);
                }
            }
            
            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }
        });
        
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Add these functions to your existing JavaScript
        function showLoading(message) {
            const $overlay = $('#loadingOverlay');
            const $text = $('#loadingText span');
            
            $text.text(message || 'Processing');
            $overlay.removeClass('hide').addClass('show').css('display', 'flex');
        }

        function hideLoading(callback) {
            const $overlay = $('#loadingOverlay');
            
            $overlay.removeClass('show').addClass('hide');
            setTimeout(() => {
                $overlay.css('display', 'none');
                if (callback) callback();
            }, 300);
        }
        
        // Cookie Management Functions for Remember Me
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/;SameSite=Strict;Secure';
        }
        
        function getCookie(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
        
        function deleteCookie(name) {
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;';
        }
    </script>
</body>
</html>