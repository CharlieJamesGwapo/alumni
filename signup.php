<?php
    session_start();
    include 'admin/db_connect.php';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Create Account | MOIST Alumni Portal</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="assets/img/logo.png"/>
        <!-- Meta tags for SEO and mobile -->
        <meta name="description" content="MOIST Alumni Portal - Register and stay connected with your alma mater">
        <meta name="keywords" content="MOIST, alumni, registration, portal, education">
        <meta name="author" content="MOIST">
        <meta name="theme-color" content="#800000">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700|Quicksand:400,500,600,700&display=swap" rel="stylesheet">
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <!-- Animate.css -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <!-- Loading Bar -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.css">
    <!-- Toastify for unobtrusive toasts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css">
    <style>
        /* Responsive tweaks for OTP input group */
        @media (max-width: 576px) {
            .input-group .custom-select {
                width: 100% !important;
                margin-top: 8px;
                border-top-left-radius: 8px;
                border-bottom-left-radius: 8px;
            }
            .input-group .input-group-append {
                width: 100%;
                margin-top: 8px;
            }
            .input-group { display: block; }
            #login_contact { width: 100% !important; }
        }
        /* Make the contact input expand and keep select/button compact */
        .input-group .form-control { flex: 1 1 auto; min-width: 0; }
        .input-group .custom-select { flex: 0 0 120px; }
        .input-group .input-group-append > .btn { white-space: nowrap; }
        /* Visual badge showing the contact used for OTP; responsive and copy-friendly */
        #sent_contact_badge {
            display: inline-block;
            margin-top: 8px;
            padding: 8px 12px;
            background: rgba(40,167,69,0.08);
            border: 1px solid rgba(40,167,69,0.16);
            color: #155724;
            border-radius: 999px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        /* On small screens make the badge span full width below the control */
        @media(max-width:576px){
            #sent_contact_badge{ display:block; width:100%; }
        }

        /* Live preview of the entered contact (email or phone) */
        #contact_preview{
            display:block;
            margin-top:8px;
            font-size:0.94rem;
            color: rgba(0,0,0,0.6);
            word-break:break-word;
        }
        /* Additional mobile-friendly improvements */
        .input-group { display:flex; align-items:stretch; gap:0.5rem; }
        .input-group .form-control { min-width:0; flex:1 1 auto; }
        .input-group .input-group-append { display:flex; align-items:center; }
        .input-group .input-group-append .custom-select,
        .input-group .input-group-append .btn { margin-left:0.5rem; }

        /* OTP row and verify button styling */
        #otp_row .input-group { margin-top:0.5rem; }
        #otp_input { font-size:1rem; padding:0.6rem 0.9rem; }
        #verifyOtpBtn { white-space:nowrap; }

        /* Small screens: stack controls and make touch targets larger */
        @media (max-width: 576px) {
            .input-group { display:block; }
            .input-group .form-control { width:100% !important; margin-bottom:8px; }
            .input-group .input-group-append { width:100%; display:flex; gap:8px; }
            .input-group .input-group-append .custom-select,
            .input-group .input-group-append .btn { flex:1 1 0%; width:100%; margin:0; }
            #sendOtpBtn { padding:10px 12px; }
            #verifyOtpBtn { display:block; width:100%; padding:12px 14px; font-size:1rem; border-radius:8px; }
            #otp_row .input-group { display:block; }
            #otp_input { width:100%; margin-bottom:8px; }
            #sent_contact_badge { font-size:0.95rem; padding:10px 14px; }
            #contact_preview { font-size:0.95rem; }
            .form-group { margin-bottom:1.25rem; }
        }
    </style>
        <!-- Select2 & Datepicker (optional, for better UX) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
    <!-- moved signup scripts to load after libraries (so jQuery/select2 are available) -->
        <style>
            :root {
                /* College Theme */
                --primary: #800000;
                --primary-dark: #600000;
                --primary-light: #b71c1c;
                
                /* SHS Theme */
                --shs-primary: #1a5f7a;
                --shs-secondary: #2d8bba;
                --shs-gradient-start: #1a5f7a;
                --shs-gradient-end: #3498db;
                
                /* Common Colors */
                --white: #ffffff;
                --gray-100: #f8f9fa;
                --gray-200: #e9ecef;
                --gray-300: #dee2e6;
                --success: #28a745;
                --danger: #dc3545;
                --warning: #ffc107;
            }
                
            
            body {
                font-family: 'Quicksand', 'Montserrat', Arial, sans-serif;
                background-attachment: fixed;
                min-height: 100vh;
                margin: 0;
                padding: 0;
                transition: all 0.3s ease;
            }

            /* Default College Theme */
            body {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            }

            /* SHS Theme */
            body.shs-theme {
                background: linear-gradient(135deg, var(--shs-gradient-start) 0%, var(--shs-gradient-end) 100%);
            }

            body.shs-theme .btn-primary {
                background-color: var(--shs-primary);
                border-color: var(--shs-primary);
            }

            body.shs-theme .btn-primary:hover {
                background-color: var(--shs-secondary);
                border-color: var(--shs-secondary);
            }

            body.shs-theme .form-section-title {
                color: var(--shs-primary);
                border-color: var(--shs-primary);
            }

            /* Program Type Badge */
            .program-badge {
                display: inline-block;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: 600;
                margin-bottom: 1rem;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .program-badge.college {
                background-color: var(--primary);
                color: var(--white);
            }

            .program-badge.shs {
                background-color: var(--shs-primary);
                color: var(--white);
            }

            /* Strand Section Styles */
            #strandSection {
                display: none;
            }

            .strand-info {
                background: rgba(255,255,255,0.9);
                border-radius: 8px;
                padding: 1rem;
                margin-top: 1rem;
                border-left: 4px solid var(--shs-secondary);
            }

            /* Course info (presentable read-only block for verified college users) */
            .course-info {
                background: rgba(255,255,255,0.95);
                border-radius: 8px;
                padding: 0.85rem 1rem;
                margin-top: 0.75rem;
                border-left: 4px solid var(--primary);
                color: #333;
                font-size: 0.95rem;
            }

            /* Employment Section */
            #employmentSection {
                transition: all 0.3s ease;
            }
            
            .text-maroon {
                color: var(--primary);
            }
            
            .logo {
                filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
                transition: transform 0.3s ease;
            }
            
            .logo:hover {
                transform: scale(1.05);
            }
            
            .btn-back-home {
                position: fixed;
                top: 16px;
                left: 12px;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 16px;
                color: var(--white);
                background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
                border: 2px solid rgba(0,0,0,0.06);
                border-radius: 999px;
                font-weight: 700;
                font-size: 0.92rem;
                transition: all 0.18s ease;
                box-shadow: 0 6px 18px rgba(128,0,0,0.12);
                text-decoration: none;
                z-index: 1100;
            }
            .btn-back-home i {
                margin-right: 6px;
                font-size: 1rem;
                color: rgba(255,255,255,0.95);
                transition: transform 0.18s ease;
            }
            .btn-back-home:hover {
                transform: translateX(-4px) scale(1.01);
                box-shadow: 0 10px 30px rgba(128,0,0,0.18);
                text-decoration: none;
            }
            .btn-back-home:active { transform: translateX(-2px); }

            /* Make Back button more compact on very small screens */
            @media (max-width: 420px) {
                .btn-back-home { top: 10px; left: 8px; padding: 8px 12px; font-size: 0.88rem; }
                .btn-back-home span { display: none; }
                .btn-back-home i { margin-right: 0; }
            }
            .btn-back {
                transition: all 0.3s ease;
                border: 2px solid rgba(255,255,255,0.7);
                font-weight: 600;
                padding: 0.5rem 1.2rem;
                font-size: 0.9rem;
            }
            .btn-back:hover {
                background: rgba(255,255,255,0.1);
                transform: translateX(-3px);
            }
            .btn-back i {
                margin-right: 5px;
                font-size: 0.85rem;
            }
            .signup-bg-box {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border-radius: 20px;
                box-shadow: 0 15px 50px 0 rgba(128,0,0,0.18);
                max-width: 900px;
                margin: 20px auto;
                padding: 1.5rem 1.25rem;
                position: relative;
                z-index: 5;
                transform: translateY(0);
                transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                border: 1px solid rgba(255,255,255,0.3);
                animation: fadeInUp 0.6s ease-out;
            }
            
            .signup-bg-box:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 60px 0 rgba(128,0,0,0.22);
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

            /* Modern form styling */
            .form-control {
                height: 48px;
                border-radius: 12px;
                border: 2px solid rgba(128,0,0,0.1);
                padding: 10px 16px;
                font-size: 1rem;
                transition: all 0.3s ease;
                background: rgba(255,255,255,0.9);
            }

            .form-control:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(128,0,0,0.1);
                transform: translateY(-1px);
            }

            .input-group .form-control {
                border-top-right-radius: 12px !important;
                border-bottom-right-radius: 12px !important;
            }

            .input-group-text {
                border-top-left-radius: 12px !important;
                border-bottom-left-radius: 12px !important;
                padding: 0 16px;
                background: var(--primary);
                border: none;
                color: white;
            }

            /* Enhanced responsive design */
            @media (max-width: 768px) {
                .signup-bg-box {
                    margin: 15px;
                    padding: 1.5rem;
                    border-radius: 16px;
                }

                .form-group {
                    margin-bottom: 1rem;
                }

                .form-control {
                    height: 44px;
                    font-size: 0.95rem;
                }
            }

            @media (max-width: 576px) {
                .signup-bg-box {
                    margin: 10px;
                    padding: 1.25rem;
                }

                .form-section-title {
                    font-size: 1.1rem;
                }

                .btn {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
            }
            .signup-bg-box:hover {
                transform: translateY(-5px);
                box-shadow: 0 16px 48px 0 rgba(128,0,0,0.2);
            }
            .masthead {
                min-height: 18vh;
                background: #800000 url('assets/img/moist12.jpg') center center/cover no-repeat;
                position: relative;
                box-shadow: 0 2px 12px 0 rgba(128,0,0,0.09);
            }
            .masthead:before {
                content: "";
                display: block;
                position: absolute;
                left: 0; top: 0; right: 0; bottom: 0;
                background: linear-gradient(90deg, #800000 70%, #b71c1c 100%);
                opacity: 0.47;
                z-index: 1;
            }
            .masthead h3 {
                color: #fff;
                font-weight: 800;
                letter-spacing: 1.2px;
                text-shadow: 0 2px 10px #4b0000;
                z-index: 2;
                position: relative;
            }
            .divider {
                border-top: 2px solid #fff;
                max-width: 120px;
                margin: 8px auto 0 auto;
                opacity: 0.21;
            }
            .form-section-title {
                font-size: 1.15rem;
                font-weight: 700;
                color: var(--primary);
                margin-top: 2rem;
                margin-bottom: 1.5rem;
                padding: 1rem;
                border-radius: 8px;
                background: linear-gradient(135deg, rgba(128,0,0,0.05) 0%, rgba(183,28,28,0.05) 100%);
                position: relative;
                display: flex;
                align-items: center;
                transition: all 0.3s ease;
            }
            
            .form-section-title i {
                margin-right: 10px;
                font-size: 1.2rem;
                color: var(--primary);
                opacity: 0.8;
            }
            
            .form-section-title:hover {
                background: rgba(128,0,0,0.08);
                transform: translateY(-1px);
            }
            label, .control-label {
                color: #222;
                font-weight: 600;
                font-size: 0.98rem;
                margin-bottom: 6px;
                display: flex;
                align-items: center;
            }
            label .text-danger {
                margin-left: 4px;
            }
            .form-group {
                margin-bottom: 1.5rem;
                position: relative;
                transition: all 0.3s ease;
            }
            .form-group:focus-within {
                transform: translateY(-2px);
            }
            .form-group label {
                font-size: 0.9rem;
                font-weight: 600;
                color: #495057;
                margin-bottom: 0.5rem;
            }
            .form-control {
                height: calc(2.5rem + 2px);
                padding: 0.5rem 1rem;
                font-size: 0.95rem;
                border: 1px solid var(--gray-300);
                border-radius: 0.375rem;
                transition: all 0.2s ease-in-out;
            }
            .form-control:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.15);
            }
            .input-group-text {
                padding: 0.5rem 0.75rem;
                background-color: var(--primary);
                border-color: var(--primary);
                color: white;
            }
            .alumni-id-container .form-control {
                max-width: 200px;
                text-transform: uppercase;
            }
            .btn-verify {
                padding: 0.5rem 1rem;
                font-weight: 500;
                background: var(--primary);
                border-color: var(--primary);
                color: white;
                transition: all 0.2s ease;
            }
            .btn-verify:hover {
                background: var(--primary-light);
                transform: translateX(2px);
            }
            .custom-file-label {
                height: calc(2.5rem + 2px);
                padding: 0.5rem 1rem;
                font-size: 0.95rem;
                background-color: white;
                border: 1px solid var(--gray-300);
            }
            .section-card {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 1rem;
                padding: 2rem;
                margin-bottom: 1.5rem;
                box-shadow: 0 8px 24px rgba(128,0,0,0.1);
                border: 1px solid rgba(128,0,0,0.1);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .section-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .section-card:hover::before {
                opacity: 1;
            }
            .form-section-title {
                font-size: 1.1rem;
                font-weight: 700;
                color: var(--primary);
                margin-bottom: 1.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid var(--primary);
            }
            .form-section-title i {
                margin-right: 0.5rem;
            }
            
            /* Program Type Specific Styles */
            #employmentSection {
                transition: all 0.3s ease-in-out;
            }
            
            .program-type-badge {
                text-align: center;
                margin-bottom: 1rem;
            }
            
            .program-type-badge .badge {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
                border-radius: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .badge-info {
                background-color: var(--shs-primary);
                color: white;
            }
            
            #strandSection {
                display: none;
                transition: all 0.3s ease-in-out;
            }
            
            #courseSection {
                transition: all 0.3s ease-in-out;
            }
            
            .strand-description {
                margin-top: 0.5rem;
                padding: 0.5rem;
                background-color: rgba(26, 95, 122, 0.1);
                border-left: 3px solid var(--shs-primary);
                border-radius: 4px;
            }
            input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="file"], select {
                color: #222;
                font-size: 0.98rem;
                border-radius: 6px !important;
                border: 1px solid #ccc;
                box-shadow: none;
                transition: all 0.3s ease;
                height: calc(2em + 0.75rem + 2px);
            }
            .form-control:focus, .custom-select:focus {
                border-color: #800000;
                box-shadow: 0 0 0 0.13rem rgba(128,0,0,0.09);
                transform: translateY(-1px);
            }
            .input-group-text {
                background-color: #800000;
                color: #fff;
                border: 1px solid #800000;
                border-radius: 6px 0 0 6px !important;
                transition: background-color 0.3s ease;
            }
            .input-group .form-control {
                border-left: 0;
                border-radius: 0 6px 6px 0 !important;
                padding-left: 0.5rem;
            }
            #alumni_id_status .alert {
                margin-bottom: 0;
                padding: 0.5rem 1rem;
                border-radius: 6px;
                animation: fadeIn 0.3s ease;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .btn-primary {
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
                border: none;
                color: #fff;
                font-weight: 600;
                font-size: 0.95rem;
                border-radius: 12px;
                padding: 12px 28px;
                min-width: 160px;
                transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
                box-shadow: 0 4px 15px rgba(128,0,0,0.15);
                letter-spacing: 0.8px;
                position: relative;
                overflow: hidden;
                text-transform: uppercase;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-primary:hover, .btn-primary:focus {
                background-color: #b71c1c;
                border-color: #b71c1c;
                color: #fff;
            }
            .btn:active, .btn-primary:active {
                background-color: #600000 !important;
                border-color: #600000 !important;
            }
            #cimg {
                max-height: 88px;
                max-width: 88px;
                margin-top: 7px;
                border-radius: 8px;
                border: 2px solid #eee;
                background: #f2f2f2;
                display: block;
            }
            .alert {
                margin-top: 12px;
                margin-bottom: 0;
            }
            .form-control::placeholder {
                color: #999;
                opacity: 0.7;
                font-size: 0.9rem;
            }
            .form-row {
                margin-left: -12px;
                margin-right: -12px;
            }
            .form-row > [class*="col-"] {
                padding-left: 12px;
                padding-right: 12px;
            }
            .btn-primary {
                position: relative;
                overflow: hidden;
            }
            .btn-primary::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                background: rgba(255,255,255,0.2);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                transition: width 0.6s ease, height 0.6s ease;
            }
            .btn-primary:hover::after {
                width: 300%;
                height: 300%;
            }

            /* Additions from code block */
            .input-group-text {
                transition: background-color 0.3s ease;
            }

            .form-control.is-valid + .input-group-text {
                background-color: #28a745;
                border-color: #28a745;
            }

            .form-control.is-invalid + .input-group-text {
                background-color: #dc3545;
                border-color: #dc3545;
            }

            #password_strength .progress {
                margin-top: 0.5rem;
                height: 4px;
                border-radius: 2px;
            }

            #password_strength .progress-bar {
                transition: width 0.3s ease;
            }

            /* New styles from suggested change */
            .form-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 1rem 1rem;
                background: #fff;
                border-radius: 15px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            }

            .section-card {
                background: #fff;
                border: 1px solid rgba(128,0,0,0.1);
                border-radius: 10px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                transition: all 0.3s ease;
            }

            .section-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(128,0,0,0.1);
            }

            .form-section-title {
                font-size: 1.2rem;
                color: var(--primary);
                border-left: 4px solid var(--primary);
                padding-left: 1rem;
                margin-bottom: 1.5rem;
            }

            .input-group {
                margin-bottom: 1rem;
            }

            .input-group-text {
                min-width: 40px;
                justify-content: center;
            }

            .form-control.is-valid {
                border-color: #28a745;
                background-image: none;
            }

            .form-control.is-invalid {
                border-color: #dc3545;
                background-image: none;
            }

            .validation-feedback {
                display: none;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }

            .is-valid ~ .validation-feedback.valid-feedback {
                display: block;
                color: #28a745;
            }

            .is-invalid ~ .validation-feedback.invalid-feedback {
                display: block;
                color: #dc3545;
            }

            .btn-verify {
                background: var(--primary);
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 0 5px 5px 0;
                transition: all 0.3s ease;
            }

            .btn-verify:hover {
                background: var(--primary-light);
            }

            #password_strength {
                height: 4px;
                background: #e9ecef;
                margin-top: 0.5rem;
                border-radius: 2px;
                overflow: hidden;
            }

            #password_strength .progress-bar {
                height: 100%;
                border-radius: 2px;
                transition: width 0.3s ease;
            }

            /* Responsive improvements */
            @media (max-width: 768px) {
                .form-container {
                    padding: 1rem;
                }
                
                .section-card {
                    padding: 1rem;
                }
                
                .form-row {
                    margin-left: -0.5rem;
                    margin-right: -0.5rem;
                }
                
                .form-row > [class*="col-"] {
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
            }
            @media (max-width: 991.98px) {
                .signup-bg-box {
                    max-width: 95vw;
                    padding: 1.5rem 1rem;
                    margin: 20px auto;
                }
                .form-section-title {
                    margin-bottom: 15px;
                }
            }
            @media (max-width: 767.98px) {
                .masthead { 
                    min-height: 12vh; 
                }
                .signup-bg_box { 
                    margin-top: 0;
                    transform: none !important;
                }
                .form-section-title { 
                    font-size: 1.1rem;
                    padding-bottom: 8px;
                }
                .btn-back-home {
                    top: 15px;
                    left: 15px;
                    padding: 8px 16px;
                    font-size: 0.9rem;
                }
                .btn-back-home i {
                    font-size: 0.9rem;
                }
            }
            @media (max-width: 576px) {
                .signup-bg-box { 
                    padding: 1rem 0.8rem;
                    border-radius: 12px;
                }
                .masthead h3 { 
                    font-size: 1.2rem; 
                }
                .form-row {
                    margin-left: -8px;
                    margin-right: -8px;
                }
                .form-row > [class*="col-"] {
                    padding-left: 8px;
                    padding-right: 8px;
                }
                .btn-primary {
                    width: 100%;
                    margin-top: 10px;
                }
            }

            .btn-verify {
                background: var(--primary);
                color: white;
                border: 1px solid var(--primary);
                transition: all 0.3s ease;
                font-weight: 600;
                padding: 0.375rem 1rem;
            }

            .btn-verify:hover {
                background: var(--primary-light);
                color: white;
                transform: translateX(2px);
            }

            .btn-verify:active {
                background: var(--primary-dark) !important;
            }

            .btn-verify i {
                margin-right: 5px;
            }

            #alumni_id_status .alert {
                border-radius: 8px;
                margin-top: 10px;
                padding: 0.75rem 1.25rem;
                border: none;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                animation: slideIn 0.3s ease;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .form-control.is-valid {
                border-color: #28a745;
                padding-right: calc(1.5em + 0.75rem);
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(0.375em + 0.1875rem) center;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            }

            .form-control.is-invalid {
                border-color: #dc3545;
                padding-right: calc(1.5em + 0.75rem);
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23dc3545' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E");
                background-repeat: no-repeat;
                background-position: right calc(0.375em + 0.1875rem) center;
                background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            }

            .alumni-verification {
                background: linear-gradient(135deg, rgba(128,0,0,0.03) 0%, rgba(183,28,28,0.03) 100%);
                padding: 1.5rem;
                border-radius: 12px;
                margin-bottom: 2rem;
                border: 1px solid rgba(128,0,0,0.1);
                transition: all 0.3s ease;
            }

            .alumni-verification:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(128,0,0,0.05);
            }

            .form-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 2rem;
                background: #fff;
                border-radius: 15px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            }

            .section-card {
                background: #fff;
                border: 1px solid rgba(128,0,0,0.1);
                border-radius: 10px;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                transition: all 0.3s ease;
            }

            .section-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(128,0,0,0.1);
            }

            .form-section-title {
                font-size: 1.2rem;
                color: var(--primary);
                border-left: 4px solid var(--primary);
                padding-left: 1rem;
                margin-bottom: 1.5rem;
            }

            .input-group {
                margin-bottom: 1rem;
            }

            .input-group-text {
                min-width: 40px;
                justify-content: center;
            }

            .form-control.is-valid {
                border-color: #28a745;
                background-image: none;
            }

            .form-control.is-invalid {
                border-color: #dc3545;
                background-image: none;
            }

            .validation-feedback {
                display: none;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }

            .is-valid ~ .validation-feedback.valid-feedback {
                display: block;
                color: #28a745;
            }

            .is-invalid ~ .validation-feedback.invalid-feedback {
                display: block;
                color: #dc3545;
            }

            .btn-verify {
                background: var(--primary);
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 0 5px 5px 0;
                transition: all 0.3s ease;
            }

            .btn-verify:hover {
                background: var(--primary-light);
            }

            #password_strength {
                height: 4px;
                background: #e9ecef;
                margin-top: 0.5rem;
                border-radius: 2px;
                overflow: hidden;
            }

            #password_strength .progress-bar {
                height: 100%;
                border-radius: 2px;
                transition: width 0.3s ease;
            }

            /* Responsive improvements */
            @media (max-width: 768px) {
                .form-container {
                    padding: 1rem;
                }
                
                .section-card {
                    padding: 1rem;
                }
                
                .form-row {
                    margin-left: -0.5rem;
                    margin-right: -0.5rem;
                }
                
                .form-row > [class*="col-"] {
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
            }
            @media (max-width: 991.98px) {
                .signup-bg-box {
                    max-width: 95vw;
                    padding: 1.5rem 1rem;
                    margin: 20px auto;
                }
                .form-section-title {
                    margin-bottom: 15px;
                }
            }
            @media (max-width: 767.98px) {
                .masthead { 
                    min-height: 12vh; 
                }
                .signup-bg_box { 
                    margin-top: 0;
                    transform: none !important;
                }
                .form-section-title { 
                    font-size: 1.1rem;
                    padding-bottom: 8px;
                }
                .btn-back-home {
                    top: 15px;
                    left: 15px;
                    padding: 8px 16px;
                    font-size: 0.9rem;
                }
                .btn-back-home i {
                    font-size: 0.9rem;
                }
            }
            @media (max-width: 576px) {
                .signup-bg-box { 
                    padding: 1rem 0.8rem;
                    border-radius: 12px;
                }
                .masthead h3 { 
                    font-size: 1.2rem; 
                }
                .form-row {
                    margin-left: -8px;
                    margin-right: -8px;
                }
                .form-row > [class*="col-"] {
                    padding-left: 8px;
                    padding-right: 8px;
                }
                .btn-primary {
                    width: 100%;
                    margin-top: 10px;
                }
            }

            /* Password toggle wrapper and button */
.password-wrapper{position:relative;display:block;}
.password-toggle-btn{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:transparent;border:none;color:var(--primary);cursor:pointer;font-size:1.05rem;padding:6px;line-height:1}
.password-toggle-btn:focus{outline:none}

/* Ensure course/graduated long text wraps and remains visible */
.course-graduated, #courseGraduated, .course-text {white-space:normal;word-break:break-word;max-width:100%;overflow-wrap:break-word}

/* Small responsive tweak */
@media(max-width:576px){
  .password-toggle-btn{right:6px;font-size:0.95rem}
}
        </style>
        <style>
            /* Camera modal improvements */
            #cameraContainer { max-width: 100%; }
            @media(min-width: 576px){
                #cameraContainer { max-width: 420px; border-radius:12px; }
                #cameraStream { width: 320px; height:320px; }
                #canvasPreview, #cimg { width:320px; height:auto; }
            }
            @media(max-width:575px){
                #cameraContainer { position: fixed; left:0; right:0; top:0; bottom:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.6); padding:20px; }
                #cameraStream { width: 92vw; height:92vw; border-radius:8px; }
                #canvasPreview, #cimg { width:92vw; height:auto; }
                #cameraContainer .btn { width:48%; }
            }
            /* larger capture button for touch devices */
            #captureBtn { padding:10px 14px; font-size:1rem; }
            #closeCameraBtn { padding:10px 12px; }
        </style>
    </head>
    <body>
        <!-- Back Button -->
        <a href="index.php" class="btn-back-home" id="backBtn" title="Back to Home Page">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Home</span>
        </a>

        <header class="masthead">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end mb-3 page-title">
                        <h3 class="text-white mb-0 animate__animated animate__fadeInDown">Welcome to MOIST Alumni Portal</h3>
                        <p class="text-white-50 mb-0 animate__animated animate__fadeInUp">Create your alumni account to stay connected</p>
                        <div class="divider-container animate__animated animate__fadeIn animate__delay-1s">
                            <hr class="divider my-4" />
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="signup-bg-box">
                <div class="text-center mb-4">
                    <img src="assets/img/logo.png" alt="MOIST Logo" class="logo mb-3" style="height: 80px;">
                    <h4 class="text-maroon mb-3">Alumni Registration</h4>
                    <p class="text-muted">Please fill in the required information to create your account</p>
                </div>
                
                <div class="form-container">
                    <form action="" id="create_account" enctype="multipart/form-data">
                        <!-- Account Info Section (moved to top) -->
                        <div class="section-card">
                            <div class="form-section-title">
                                <i class="fas fa-user-lock mr-2"></i>Account Login Details
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Alumni Email / Phone for verification <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <!-- This field is used only for sending/verifying OTP. It may be an email or phone. -->
                                        <input type="text" class="form-control" name="login_contact" id="login_contact" 
                                            autocomplete="off" placeholder="Enter your email address or phone number for verification">
                                        <div class="input-group-append">
                                            <select id="otp_method" class="custom-select" style="width:120px">
                                                <option value="email">Email</option>
                                                <option value="sms">SMS</option>
                                            </select>
                                            <button class="btn btn-outline-primary" type="button" id="sendOtpBtn">
                                                <i class="fas fa-paper-plane"></i> Send OTP
                                            </button>
                                        </div>
                                    </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> This contact will be used to verify your identity (OTP). For account login, provide your account email below.
                                        </small>
                                        <!-- Live contact preview (updates as user types) -->
                                        <div id="contact_preview" aria-live="polite"></div>
                                    <div id="email_status" class="mt-2"></div>
                                    <!-- OTP input row (shown after sending) -->
                                    <div id="otp_row" class="mt-2" style="display:none">
                                        <div class="input-group">
                                            <input type="text" id="otp_input" class="form-control" placeholder="Enter 6-digit OTP">
                                            <div class="input-group-append">
                                                <button class="btn btn-success" type="button" id="verifyOtpBtn"><i class="fas fa-check"></i> Verify OTP</button>
                                            </div>
                                        </div>
                                        <div id="otp_status" class="mt-2" style="display:none"></div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Account Email <span class="text-danger">*</span></label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email" id="account_email" required 
                                            autocomplete="email" placeholder="Enter account email for login and communications">
                                    </div>
                                    <label>Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" name="password" id="password" required 
                                            autocomplete="new-password" placeholder="Create a strong password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="password_strength" class="mt-2"></div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Minimum 8 characters with letters, numbers, and symbols
                                    </small>
                                </div>
                            </div>

                        </div>

                        <!-- Alumni Verification Section (moved below contact) -->
                        <div class="section-card mt-3">
                            <div class="form-section-title">
                                <i class="fas fa-id-badge mr-2"></i>Alumni Verification
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label>Alumni ID <span class="text-danger">*</span></label>
                                    <div class="input-group alumni-id-container">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="alumni_id" id="alumni_id" required 
                                            placeholder="Alumni ID will be auto-filled after OTP verification" pattern="[A-Za-z0-9-]+"
                                            title="Please enter a valid Alumni ID">
                                        <div class="input-group-append">
                                            <button class="btn btn-verify" type="button" id="verifyBtn">
                                                <i class="fas fa-check-circle"></i> Verify ID
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Alumni ID will be auto-filled when your contact is verified
                                    </small>
                                    <div id="alumni_id_status" class="mt-2"></div>
                                </div>
                            </div>
                            
                            
                            <!-- Personal Info Section -->
                            <div class="form-section-title">
                                <i class="fas fa-user mr-2"></i>Personal Information
                                <span class="badge badge-success ml-2">Auto-filled from verification</span>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Last Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="lastname" id="lastname" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>First Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="firstname" id="firstname" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Middle Name</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="middlename" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Suffix Name</label>
                                    <input type="text" class="form-control" name="suffixname">
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Birthdate <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="birthdate" readonly>
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Gender <span class="text-danger">*</span></label>
                                    <select class="custom-select" name="gender" disabled>
                                        <option value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-lg-4">
                                    <label>Batch <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepickerY" name="batch" readonly>
                                </div>
                                <div id="courseSection" class="form-group col-md-6 col-lg-4">
                                    <label>Course Graduated <span id="programLabel" class="text-muted" style="font-weight:600;"></span> <span class="text-danger">*</span></label>
                                    <select class="custom-select select2" name="course_id" disabled>
                                        <option value="">Select</option>
                                        <?php 
                                        $course = $conn->query("SELECT * FROM courses ORDER BY course ASC");
                                        while($row = $course->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $row['id'] ?>"><?php echo htmlspecialchars($row['course']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <!-- Course / Major read-only info (shown after verification) -->
                                    <div id="courseInfo" class="course-info" style="display:none;">
                                        <strong id="courseName" class="d-block"></strong>
                                        <!-- Dynamic majors: select (if multiple) or selected read-only block -->
                                        <div id="majorContainer" class="mt-2" style="display:none;">
                                            <label for="majorSelect" class="mb-1"><small class="text-muted">Major</small></label>
                                            <select id="majorSelect" class="custom-select">
                                                <option value="">Select major</option>
                                            </select>
                                            <small id="majorSelectHelp" class="form-text text-muted">Choose your major, if applicable.</small>
                                        </div>

                                        <div id="majorSelected" class="mt-2" style="display:none;">
                                            <small class="text-muted">Major</small>
                                            <div id="majorText" class="font-weight-bold"></div>
                                            <div id="majorAbout" class="small text-muted"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Strand (for SHS) -->
                                <div id="strandSection" class="form-group col-md-6 col-lg-4" style="display:none;">
                                    <label>Strand Graduated <span class="text-danger">*</span></label>
                                    <select id="strand_id" name="strand_id" class="custom-select select2" disabled>
                                        <option value="">Select Strand</option>
                                        <option value="1">STEM</option>
                                        <option value="2">HUMSS</option>
                                        <option value="3">ABM</option>
                                        <option value="4">GAS</option>
                                        <option value="5">TVL</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Enhanced Profile Image Section -->
                            <div class="form-section-title mt-4">Profile Image (Optional)</div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Profile Photo</label>
                                    <div class="d-flex flex-column flex-md-row align-items-md-center">
                                        <!-- Use native camera on mobile via capture attribute; desktop will open file picker -->
                                        <input type="file" class="form-control mb-2 mb-md-0" name="img" id="imgInput" accept="image/*" capture="user" onchange="displayImg(this)">
                                        <button type="button" class="btn btn-outline-secondary ml-md-2" id="takePicBtn"><i class="fa fa-camera"></i> Take a Picture</button>
                                    </div>
                                    <div id="cameraContainer" style="display:none; margin-top:10px;">
                                        <video id="cameraStream" width="220" height="220" autoplay style="border-radius:8px; border:2px solid #eee;"></video>
                                        <br>
                                        <button type="button" class="btn btn-sm btn-success mt-2" id="captureBtn"><i class="fa fa-check"></i> Capture</button>
                                        <button type="button" class="btn btn-sm btn-danger mt-2" id="closeCameraBtn"><i class="fa fa-times"></i> Cancel</button>
                                    </div>
                                    <canvas id="canvasPreview" width="220" height="220" style="display:none;"></canvas>
                                    <img src="" alt="" id="cimg" style="display:none; margin-top:7px; max-width:220px; border-radius:8px;">
                                    <div id="photoActions" style="display:none; margin-top:8px; gap:8px;">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="retakeBtn"><i class="fas fa-redo"></i> Retake</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removePhotoBtn"><i class="fas fa-trash"></i> Remove</button>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Recommended: Square image (2x2 inch, approx. 220x220px)<br>
                                        <i class="fas fa-check-circle"></i> Max file size: 5MB | Formats: JPG, PNG
                                    </small>
                                </div>
                            </div>

                            <!-- Employment Info Section - Hidden for SHS -->
                            <div id="employmentSection">
                                <div class="form-section-title mt-4">Employment Information</div>
                                <!-- Employment History display (dynamic) -->
                                <div class="form-row">
                                    <div class="form-group col-12">
                                        <label>Employment History</label>
                                        <div id="employmentHistoryList" class="list-group mb-3" aria-live="polite">
                                            <!-- Dynamic entries will appear here -->
                                        </div>
                                        <small class="form-text text-muted">Add your current or past employments. Duration is calculated automatically from the start date.</small>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Employment Status</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                            </div>
                                            <select class="custom-select" name="employment_status" id="employment_status">
                                                <option value="">Select</option>
                                                <option value="employed">Employed</option>
                                                <option value="not employed">Not Employed</option>
                                                <option value="student">Student / Continue Schooling</option>
                                                <option value="self-employed">Self-employed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Type of Industry</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            </div>
                                            <input type="text" class="form-control" name="connected_to" placeholder="Enter company name">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Company Contact No</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="text" class="form-control" name="contact_no" id="company_contact" placeholder="Enter contact number">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Date Started</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" class="form-control" name="date_started" id="date_started" placeholder="YYYY-MM-DD">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Company Address</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <input type="text" class="form-control" name="company_address" placeholder="Enter company address">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Company Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" class="form-control" name="company_email" placeholder="Enter company email">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12 text-right">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="addEmploymentBtn"><i class="fas fa-plus"></i> Add to History</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="msg"></div>
                        <hr class="divider">
                        <div class="form-row">
                            <div class="col-md-12 text-center">
                                <button class="btn btn-primary" type="submit" style="min-width:160px;">Create Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <?php include('footer.php'); ?>

        <!-- JS Libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
        
        <!-- Program Type Handling -->
        <script>
            $(document).ready(function() {
                // Function to handle program type switching
                function handleProgramType(alumniId) {
                    const isSHS = alumniId.startsWith('SHS-');
                    const isCollege = alumniId.startsWith('CC-');
                    
                    if (isSHS) {
                        // Switch to SHS mode
                        $('#employmentSection').slideUp();
                        $('#courseSection').hide();
                        $('#strandSection').show();
                        $('#programLabel').text('Strand Graduated');
                        $('.program-type-badge').html(
                            '<span class="badge badge-info">' +
                            '<i class="fas fa-graduation-cap"></i> Senior High School' +
                            '</span>'
                        );
                        // Clear employment fields
                        $('#employmentSection input').val('');
                    } else if (isCollege) {
                        // Switch to College mode
                        $('#employmentSection').slideDown();
                        $('#courseSection').show();
                        $('#strandSection').hide();
                        $('#programLabel').text('(College)');
                        $('.program-type-badge').html(
                            '<span class="badge badge-primary">' +
                            '<i class="fas fa-university"></i> College' +
                            '</span>'
                        );
                    }
                }

                // Watch for alumni ID changes
                $('#alumni_id').on('change keyup', function() {
                    const alumniId = $(this).val().trim().toUpperCase();
                    $(this).val(alumniId);
                    handleProgramType(alumniId);
                });

                // Handle verification response
                $(document).on('alumniVerified', function(e, data) {
                    var program = (data.program_type || '').toString().toLowerCase();
                    var isSHS = program.indexOf('senior') !== -1 || program.indexOf('high') !== -1 || program.indexOf('shs') !== -1;

                    if (isSHS) {
                        // Senior High - do not alter college fields. Enable and set strand only.
                        $('#strandSection').show();
                        $('#courseSection').hide();
                        $('select[name="strand_id"]').prop('disabled', false);
                        if (typeof data.strand_id !== 'undefined' && data.strand_id !== null && data.strand_id !== '') {
                            $('select[name="strand_id"]').val(String(data.strand_id)).trigger('change');
                        }
                        // Small visual cue
                        $('#programLabel').text('(Senior High)');
                    } else {
                        // College / other programs: show course selector and display a presentable course label
                        $('#courseSection').show();
                        $('#strandSection').hide();
                        $('select[name="course_id"]').prop('disabled', false);
                        if (typeof data.course_id !== 'undefined' && data.course_id !== null && data.course_id !== '') {
                            var $course = $('select[name="course_id"]');
                            $course.val(String(data.course_id)).trigger('change');
                            var courseText = $course.find('option:selected').text() || '';
                            showCourseInfo(data.course_id, courseText);
                        } else {
                            hideCourseInfo();
                        }
                        $('#programLabel').text('(College)');
                    }
                });

                // Handle form submission
                $('#create_account').on('submit', function(e) {
                    const alumniId = $('#alumni_id').val().trim().toUpperCase();
                    const isSHS = alumniId.startsWith('SHS');
                    
                    if (isSHS) {
                        // Clear employment data for SHS
                        $('#employmentSection input').val('');
                        // reset employment status select as well
                        $('#employment_status').val('');
                        // clear history
                        $('#employmentHistoryList').empty();
                        $('#hidden_employment_history').remove();
                    }
                });
            });
        </script>
        <script>
        $(document).ready(function(){
            // Initialize Select2 for industry type
            $('#industry_type').select2({
                placeholder: "Select Industry Type",
                allowClear: true,
                theme: "bootstrap"
            });

            // Initialize custom file input
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });

            // Company contact number validation
            $('#company_contact').on('input', function() {
                this.value = this.value.replace(/[^0-9+\-\s]/g, '');
            });

            // Company email validation
            $('#company_email').on('blur', function() {
                let email = $(this).val();
                if(email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Camera functionality for profile picture
            // Use the existing #cameraContainer from the markup (avoid duplicating elements)
            let stream = null;
            const $cameraContainer = $('#cameraContainer');
            const videoEl = document.getElementById('cameraStream');
            const canvasEl = document.getElementById('canvasPreview');

            // Ensure camera container is styled responsively (mobile full-screen, centered on desktop)
            $cameraContainer.addClass('p-3');
            // Buttons inside the existing container are used: #captureBtn and #closeCameraBtn
            // Back button confirmation if form has changes
            let formChanged = false;
            
            // Track form changes
            $('#create_account :input').on('change input', function() {
                formChanged = true;
            });

            // Handle back button click
            $('#backBtn').on('click', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to go back? Any unsaved changes will be lost.')) {
                        window.location.href = 'index.php';
                    }
                }
            });
            // Alumni ID verification
            let alumniVerified = false;
            let verifiedAlumniId = null;

            // Function to disable/enable personal info fields
            function togglePersonalInfoFields(disabled) {
                $('[name="lastname"], [name="firstname"], [name="middlename"], [name="suffixname"], [name="birthdate"], [name="gender"], [name="batch"], [name="course_id"], [name="strand_id"]').prop('disabled', disabled);
            }

            // Helper: show the course as a disabled select (matching the provided screenshot)
            function showCourseInfo(courseId, courseText){
                courseId = courseId ? String(courseId) : '';
                courseText = courseText || '';
                // ensure section visible
                $('#courseSection').show();
                var $course = $('select[name="course_id"]');
                // set value if provided and refresh Select2 if used
                if(courseId) {
                    $course.val(String(courseId)).trigger('change');
                    // If select2 is initialized, refresh its display
                    try{
                        if($course.hasClass('select2-hidden-accessible')){
                            $course.trigger('change.select2');
                        }
                    }catch(e){/* ignore if select2 not present */}
                }
                // disable the select so it cannot be changed and keep it visible
                $course.prop('disabled', true).show();

                // ensure hidden input exists because disabled fields are not submitted
                if($('#hidden_course_id').length === 0){
                    $('<input>').attr({type:'hidden', id:'hidden_course_id', name:'course_id', value: courseId}).appendTo('#create_account');
                } else {
                    $('#hidden_course_id').val(courseId);
                }
            }

            function hideCourseInfo(){
                // remove hidden input
                $('#hidden_course_id').remove();
                // re-enable the select so user can change it manually
                var $course = $('select[name="course_id"]');
                $course.prop('disabled', false).show();
                $('#courseSection').show();
            }

            // Initially disable the fields
            togglePersonalInfoFields(true);

            $('#alumni_id').on('change', function() {
                const alumni_id = $(this).val();
                if(alumni_id) {
                    // Clear previous status
                    $('#alumni_id_status').html('');
                    alumniVerified = false;
                    
                    // Show loading indicator
                    $('#alumni_id_status').html('<div class="alert alert-info mt-2">Verifying Alumni ID...</div>');
                    
                    $.ajax({
                        url: 'verify_alumni.php',
                        method: 'POST',
                        data: { alumni_id: alumni_id },
                        dataType: 'json',
                        success: function(response) {
                            if(response.status === 'success') {
                                $('#alumni_id_status').html('<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> Alumni ID verified successfully!</div>');
                                alumniVerified = true;
                                verifiedAlumniId = alumni_id;

                                // Auto-fill the form fields with response data
                                const data = response.data;
                                $('[name="lastname"]').val(data.lastname);
                                $('[name="firstname"]').val(data.firstname);
                                $('[name="middlename"]').val(data.middlename);
                                $('[name="suffixname"]').val(data.suffixname);
                                $('[name="birthdate"]').val(data.birthdate);
                                $('[name="gender"]').val(data.gender);
                                $('[name="batch"]').val(data.batch);
                                // If Senior High, set strand (numeric). Otherwise set course. Do not overwrite college if user selected it.
                                if((data.program_type||'').toLowerCase().indexOf('high') !== -1 || (data.program_type||'').toString().toLowerCase().indexOf('senior') !== -1) {
                                    // Senior High
                                    $('#strandSection').show();
                                    $('#courseSection').hide();
                                    $('#programLabel').text('Strand Graduated');
                                    var $strand = $('select[name="strand_id"]');
                                    $strand.prop('disabled', false);
                                    if(data.strand_id) $strand.val(String(data.strand_id)).trigger('change');
                                } else {
                                    // College / other
                                    $('#courseSection').show();
                                    $('#strandSection').hide();
                                    $('#programLabel').text('(College)');
                                    var $course = $('select[name="course_id"]');
                                    $course.prop('disabled', false);
                                    // ensure select is visible before setting value
                                    $course.show();
                                        if(data.course_id) {
                                            $course.val(String(data.course_id)).trigger('change');
                                            var courseText = $course.find('option:selected').text() || '';
                                            // use helper to show a single presentable block and handle hidden input
                                            showCourseInfo(data.course_id, courseText);
                                        } else {
                                            hideCourseInfo();
                                        }
                                }
                                // only set college if empty
                                if(data.college && !$('#college').val()) $('#college').val(data.college).trigger('change');

                                // Keep fields disabled to prevent editing
                                togglePersonalInfoFields(true);

                                // Add a hidden input for alumni_id
                                if($('#hidden_alumni_id').length === 0) {
                                    $('<input>').attr({
                                        type: 'hidden',
                                        id: 'hidden_alumni_id',
                                        name: 'verified_alumni_id',
                                        value: alumni_id
                                    }).appendTo('#create_account');
                                }
                            } else {
                                $('#alumni_id_status').html('<div class="alert alert-danger mt-2"><i class="fas fa-times-circle"></i> ' + response.message + '</div>');
                                alumniVerified = false;
                                verifiedAlumniId = null;
                                
                                // Clear and enable fields
                                $('#create_account')[0].reset();
                                togglePersonalInfoFields(true);
                            }
                        },
                        error: function() {
                            $('#alumni_id_status').html('<div class="alert alert-danger mt-2"><i class="fas fa-exclamation-circle"></i> Error verifying Alumni ID. Please try again.</div>');
                            alumniVerified = false;
                            verifiedAlumniId = null;
                            togglePersonalInfoFields(true);
                        }
                    });
                } else {
                    $('#alumni_id_status').html('');
                    alumniVerified = false;
                    verifiedAlumniId = null;
                    togglePersonalInfoFields(true);
                }
            });

            // SEND OTP: prevent double-send and add cooldown + feedback
            (function(){
                const $sendBtn = $('#sendOtpBtn');
                const $email = $('#login_contact');
                const $status = $('#email_status');
                let sending = false;
                let cooldown = 0; // seconds
                let cooldownTimer = null;

                // add aria-live region for screen readers
                if ($('#otpAriaLive').length === 0) {
                    $('<div id="otpAriaLive" aria-live="polite" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;"></div>').appendTo('body');
                }

                function setAria(msg) {
                    $('#otpAriaLive').text(msg);
                }

                function startCooldown(seconds) {
                    cooldown = seconds;
                    $sendBtn.prop('disabled', true).addClass('disabled').attr('aria-disabled', 'true');
                    updateSendText();
                    setAria('You can resend OTP in ' + cooldown + ' seconds.');
                    cooldownTimer = setInterval(function(){
                        cooldown--;
                        updateSendText();
                        if(cooldown <= 0) {
                            clearInterval(cooldownTimer);
                            $sendBtn.prop('disabled', false).removeClass('disabled').attr('aria-disabled', 'false').html('<i class="fas fa-paper-plane"></i> Send OTP');
                            setAria('You can request an OTP now.');
                        }
                    }, 1000);
                }

                function updateSendText() {
                    if(cooldown > 0) {
                        $sendBtn.html('<i class="fas fa-clock"></i> Resend (' + cooldown + 's)');
                    }
                }

                // On page load, no centralized cooldown for new endpoint; user can still be prevented server-side

                    $sendBtn.on('click touchend', function(e){
                        e.preventDefault();
                        if(sending || $sendBtn.prop('disabled')) return;
                        const contactVal = $email.val().trim();
                        const method = $('#otp_method').val() || 'email';
                        if(!contactVal) {
                                Toastify({text: 'Please enter an email or phone before requesting OTP.', duration:4000, gravity:'top', position:'right', backgroundColor:'linear-gradient(to right,#e74c3c,#c0392b)'}).showToast();
                                setAria('Please enter an email or phone before requesting OTP.');
                            return;
                        }
                        // basic validation for email or phone
                        if(method === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contactVal)){
                                Toastify({text: 'Please enter a valid email address.', duration:4000, gravity:'top', position:'right', backgroundColor:'linear-gradient(to right,#e74c3c,#c0392b)'}).showToast();
                                setAria('Please enter a valid email address.');
                            return;
                        }
                        if(method === 'sms' && !/^[0-9+\-\s]{7,20}$/.test(contactVal)){
                                Toastify({text: 'Please enter a valid phone number for SMS.', duration:4000, gravity:'top', position:'right', backgroundColor:'linear-gradient(to right,#e74c3c,#c0392b)'}).showToast();
                                setAria('Please enter a valid phone number for SMS.');
                            return;
                        }
                        sending = true;
                            $sendBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...').attr('aria-disabled', 'true');
                            setAria('Sending OTP to ' + contactVal + '.');
                            Toastify({text: 'Sending OTP to ' + contactVal, duration:2000, gravity:'top', position:'right', backgroundColor:'linear-gradient(to right,#3498db,#2ecc71)'}).showToast();

                            $.ajax({
                                url: 'send_signup_otp.php',
                                method: 'POST',
                                data: { contact: contactVal, method: method },
                                dataType: 'json',
                                timeout: 20000,
                                success: function(resp){
                                    if(resp && resp.status === 'success') {
                                        // show the sent contact as a badge for clarity
                                        // show the sent contact as a badge for clarity (insert after the group)
                                        if($('#sent_contact_badge').length === 0){
                                            $('<div id="sent_contact_badge">OTP sent to: <strong>'+contactVal+'</strong></div>').insertAfter($('#sendOtpBtn').closest('.input-group'));
                                        } else {
                                            $('#sent_contact_badge').html('OTP sent to: <strong>'+contactVal+'</strong>');
                                        }
                                        // show OTP entry controls
                                        $('#otp_row').show();
                                        $('#otp_status').hide();
                                        // Toast notification
                                        Toastify({
                                            text: 'OTP sent successfully to ' + contactVal,
                                            duration: 4000,
                                            close: true,
                                            gravity: 'top',
                                            position: 'right',
                                            backgroundColor: 'linear-gradient(to right, #28a745, #2ecc71)'
                                        }).showToast();

                                        // Client-side resend cooldown (30s visible countdown). Server still enforces its cooldown.
                                        let clientCooldown = 30;
                                        let remain = clientCooldown;
                                        $sendBtn.prop('disabled', true).attr('aria-disabled','true');
                                        $sendBtn.html('Resend OTP in '+remain+'s');
                                        const cdInterval = setInterval(function(){
                                            remain -= 1;
                                            if(remain <= 0){
                                                clearInterval(cdInterval);
                                                $sendBtn.prop('disabled', false).removeAttr('aria-disabled').html('<i class="fas fa-paper-plane"></i> Send OTP');
                                                Toastify({text: 'You can resend OTP now', duration: 3000, gravity: 'top', position: 'right', backgroundColor: 'linear-gradient(to right, #3498db, #2ecc71)'}).showToast();
                                            } else {
                                                $sendBtn.html('Resend OTP in '+remain+'s');
                                            }
                                        }, 1000);

                                    } else {
                                        const message = resp && resp.message ? resp.message : 'Unable to send OTP. Please try again later.';
                                        setAria(message);
                                        Toastify({
                                            text: message,
                                            duration: 4000,
                                            close: true,
                                            gravity: 'top',
                                            position: 'right',
                                            backgroundColor: 'linear-gradient(to right, #e74c3c, #c0392b)'
                                        }).showToast();
                                        $sendBtn.prop('disabled', false).removeClass('disabled').attr('aria-disabled', 'false').html('<i class="fas fa-paper-plane"></i> Send OTP');
                                    }
                                },
                                error: function(xhr, status, err){
                                    const msg = 'Network error while sending OTP. Please try again.';
                                    setAria(msg);
                                    Toastify({text: msg, duration: 4000, gravity: 'top', position: 'right', backgroundColor: 'linear-gradient(to right,#e74c3c,#c0392b)'}).showToast();
                                    $sendBtn.prop('disabled', false).removeClass('disabled').attr('aria-disabled', 'false').html('<i class="fas fa-paper-plane"></i> Send OTP');
                                },
                                complete: function(){
                                    sending = false;
                                }
                            });
                    });
                })();

                // Verify OTP handler
                (function(){
                    const $verifyBtn = $('#verifyOtpBtn');
                    const $otpInput = $('#otp_input');
                    const $otpStatus = $('#otp_status');
                    let verifying = false;

                    $verifyBtn.on('click touchend', function(e){
                        e.preventDefault();
                        if(verifying) return;
                        const otp = $otpInput.val().trim();
                        const contactVal = $('#login_contact').val().trim();
                        const method = $('#otp_method').val() || 'email';
                        if(!otp){
                            Toastify({text: 'Please enter the 6-digit OTP.', duration:4000, gravity:'top', position:'right', backgroundColor:'linear-gradient(to right,#e74c3c,#c0392b)'}).showToast();
                            return;
                        }
                        verifying = true;
                        $verifyBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verifying...');
                        $('#otp_status').hide();

                        $.ajax({
                            url: 'verify_signup_otp.php',
                            method: 'POST',
                            data: { contact: contactVal, otp: otp },
                            dataType: 'json',
                            timeout: 10000
                        }).done(function(resp){
                            end_load();
                            $verifyBtn.prop('disabled', false).removeClass('disabled');
                            if(resp && resp.status === 'success'){
                                // Show SweetAlert2 success modal
                                Swal.fire({
                                    icon: 'success',
                                    title: 'OTP Verified Successfully!',
                                    text: 'The form has been pre-filled if a matching alumni record was found.',
                                    timer: 2200,
                                    showConfirmButton: false
                                });
                                // hide the inline status area; SweetAlert + toast used instead
                                $('#otp_status').hide();
                                // prefill if data returned
                                if(resp.data){
                                    const d = resp.data;
                                    // If the backend returned an alumni_id, populate the visible Alumni ID field,
                                    // make it readonly and trigger change so the existing verify_alumni.php flow auto-fills the rest.
                                    if(d.alumni_id){
                                        $('#alumni_id').val(d.alumni_id).prop('readonly', true).trigger('change');
                                        // ensure hidden verified id exists for form submission
                                        if($('#hidden_alumni_id').length === 0) {
                                            $('<input>').attr({type:'hidden', id:'hidden_alumni_id', name:'verified_alumni_id', value: d.alumni_id}).appendTo('#create_account');
                                        } else {
                                            $('#hidden_alumni_id').val(d.alumni_id);
                                        }
                                        alumniVerified = true;
                                        verifiedAlumniId = d.alumni_id;
                                    }
                                    // Also directly set any remaining personal fields to reduce perceived latency
                                    $('[name="lastname"]').val(d.lastname || '');
                                    $('[name="firstname"]').val(d.firstname || '');
                                    $('[name="middlename"]').val(d.middlename || '');
                                    $('[name="suffixname"]').val(d.suffixname || '');
                                    $('[name="birthdate"]').val(d.birthdate || '');
                                    $('[name="batch"]').val(d.batch || '');
                                    if(d.program_type && d.program_type.toLowerCase().indexOf('high') !== -1){
                                        $('#strandSection').show(); $('#courseSection').hide();
                                        $('select[name="strand_id"]').prop('disabled', false);
                                        if(d.strand_id) $('select[name="strand_id"]').val(String(d.strand_id)).trigger('change');
                                    } else {
                                        $('#courseSection').show(); $('#strandSection').hide();
                                        if(d.course_id) showCourseInfo(d.course_id, '');
                                    }
                                }
                            } else {
                                const message = resp && resp.message ? resp.message : 'OTP verification failed.';
                                $('#otp_status').show().html('<div class="alert alert-danger">'+message+'</div>');
                                Swal.fire({icon: 'error', title: 'Verification Failed', text: message});
                            }
                        }).fail(function(){
                            const nm = 'Network error while verifying OTP. Please try again.';
                            $('#otp_status').show().html('<div class="alert alert-danger">'+nm+'</div>');
                            Swal.fire({icon:'error', title:'Verification Failed', text: nm});
                        }).always(function(){
                            verifying = false;
                            $verifyBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Verify OTP');
                        });
                    });

                    // allow Enter key to trigger verify
                    $otpInput.on('keypress', function(e){ if(e.which === 13){ $verifyBtn.trigger('click'); } });
                })();

            // Year picker for batch
            if($.fn.datepicker) {
                $('.datepickerY').datepicker({
                    format: " yyyy",
                    viewMode: "years",
                    minViewMode: "years",
                    autoclose: true
                });
            }
            if($.fn.select2) {
                $('.select2').select2({
                    placeholder: "Please Select Here",
                    width: "100%"
                });
            }

            // Employment history logic
            var employmentHistory = [];
            // expose the same array on window so other handlers (rebinding helpers) operate on the same instance
            window.employmentHistory = employmentHistory;

            function computeDuration(startDate) {
                if(!startDate) return '';
                var start = new Date(startDate);
                var now = new Date();
                if(isNaN(start.getTime())) return '';
                var years = now.getFullYear() - start.getFullYear();
                var months = now.getMonth() - start.getMonth();
                if(now.getDate() < start.getDate()) months--;
                if(months < 0) { years--; months += 12; }
                var parts = [];
                if(years > 0) parts.push(years + ' year' + (years > 1 ? 's' : ''));
                if(months > 0) parts.push(months + ' month' + (months > 1 ? 's' : ''));
                if(parts.length === 0) return 'Less than a month';
                return parts.join(' ');
            }

            function renderEmploymentHistory() {
                var $list = $('#employmentHistoryList');
                $list.empty();
                // Render only date started and duration for each history entry
                employmentHistory.forEach(function(item, idx){
                    var started = $('<div>').text(item.date_started || 'N/A').html();
                    var duration = $('<div>').text(item.duration || '').html();
                    var $el = $('<div class="list-group-item d-flex justify-content-between align-items-center">')
                        .append($('<div>').html('<strong>' + started + '</strong><div class="text-muted small">' + duration + '</div>'))
                        .append($('<div>').append('<button type="button" class="btn btn-sm btn-danger remove-employment" data-idx="' + idx + '"><i class="fas fa-trash"></i></button>'));
                    $list.append($el);
                });
                // update hidden input
                // Only store date_started and duration in the hidden JSON so server receives minimal data
                var minimal = employmentHistory.map(function(it){
                    return {date_started: it.date_started || '', duration: it.duration || ''};
                });
                if($('#hidden_employment_history').length === 0) {
                    $('<input>').attr({type:'hidden', id:'hidden_employment_history', name:'employment_history', value: JSON.stringify(minimal)}).appendTo('#create_account');
                } else {
                    $('#hidden_employment_history').val(JSON.stringify(minimal));
                }
            }

            // Add employment entry
            $('#addEmploymentBtn').on('click', function(){
                var status = $('#employment_status').val() || '';
                var dateStarted = $('#date_started').val() || '';

                if(!dateStarted) {
                    alert('Please provide Date Started (mm/dd/yyyy) to add to history.');
                    return;
                }

                // Create minimal entry (date + computed duration). Do NOT clear any inputs — preserve all employment information
                var entry = {
                    date_started: dateStarted,
                    duration: computeDuration(dateStarted)
                };
                employmentHistory.unshift(entry); // newest at top
                renderEmploymentHistory();

                // preserve all fields (including employment_status and company inputs)
                // show a SweetAlert2 toast for nicer feedback
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Added to employment history',
                    showConfirmButton: false,
                    timer: 2200,
                    background: '#f6ffed',
                    customClass: {
                        popup: 'border-success'
                    }
                });
            });

            // Remove entry
            $(document).on('click', '.remove-employment', function(){
                var idx = $(this).data('idx');
                employmentHistory.splice(idx, 1);
                renderEmploymentHistory();
            });

            // Update hidden input when user changes course selection manually
            $(document).on('change', 'select[name="course_id"]', function(){
                var courseId = $(this).val() || '';
                if($('#hidden_course_id').length) {
                    $('#hidden_course_id').val(courseId);
                }
            });

            // Edit button: show the select for manual change
            $(document).on('click', '.edit-course', function(){
                hideCourseInfo();
                var $course = $('select[name="course_id"]');
                $course.focus();
                // ensure programLabel and section visibility
                $('#courseSection').show();
            });


            // Camera capture logic
            const videoStream = {
                current: null
            };
            
            $('#takePicBtn').click(function(){
                // On phones, prefer the native file input camera (better compatibility and permissions handling)
                const fileInput = document.getElementById('imgInput');
                const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
                if(isMobile && fileInput) {
                    // Trigger file input which will open the camera app if allowed by the browser
                    fileInput.click();
                    return;
                }

                // Desktop / fallback: try getUserMedia with a friendly facingMode preference
                $('#imgInput').prop('disabled', true);
                $cameraContainer.show();
                const constraints = { video: { width: { ideal: 640 }, height: { ideal: 640 }, facingMode: { ideal: 'user' } } };
                if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia(constraints)
                        .then(function(s) {
                            stream = s;
                            try {
                                videoEl.srcObject = stream;
                                videoEl.play();
                            } catch (e) {
                                videoEl.src = window.URL.createObjectURL(stream);
                            }
                        })
                        .catch(function(err){
                            // Friendly fallback: inform user and re-enable file input
                            $cameraContainer.hide();
                            $('#imgInput').prop('disabled', false);
                            Swal.fire({
                                icon: 'warning',
                                title: 'Camera unavailable',
                                text: 'Unable to access the camera. You can still upload a photo from your device.',
                                confirmButtonText: 'OK'
                            });
                        });
                } else {
                    Swal.fire({icon:'info', title:'No camera API', text:'Your browser does not support camera capture. Please upload an image manually.'});
                    $('#imgInput').prop('disabled', false);
                }
            });
            $('#closeCameraBtn').click(function(){
                $cameraContainer.hide();
                $('#imgInput').prop('disabled', false);
                if(stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            });

            $('#captureBtn').click(function(){
                const video = document.getElementById('cameraStream');
                const canvas = document.getElementById('canvasPreview');
                // Enforce final size 220x220 (2x2 inch approx)
                const FINAL_SIZE = 220;
                canvas.width = FINAL_SIZE;
                canvas.height = FINAL_SIZE;
                const ctx = canvas.getContext('2d');
                const vw = video.videoWidth || FINAL_SIZE;
                const vh = video.videoHeight || FINAL_SIZE;
                const s = Math.min(vw, vh);
                const sx = Math.max(0, (vw - s) / 2);
                const sy = Math.max(0, (vh - s) / 2);
                // draw centered square and scale to FINAL_SIZE
                ctx.drawImage(video, sx, sy, s, s, 0, 0, FINAL_SIZE, FINAL_SIZE);
                // compress to JPEG for smaller payloads
                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                $('#cimg').attr('src', dataUrl).show().css({'max-width':FINAL_SIZE+'px','height':'auto','margin-top':'8px'});
                $('#canvasPreview').hide();
                $cameraContainer.hide();
                $('#imgInput').prop('disabled', false);
                if(stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                // Store image in hidden input for submission
                if($('#profileCapture').length === 0) {
                    $('<input>').attr({type:'hidden',name:'profileCapture',id:'profileCapture'}).appendTo('#create_account');
                }
                $('#profileCapture').val(dataUrl);
                $('#photoActions').show();
            });

            // Image validation for 2x2 inch (approx. 220x220px)
            $('#imgInput').change(function(){
                let file = this.files[0];
                if(file) {
                    let img = new Image();
                    img.onload = function(){
                        if((img.width < 200 || img.height < 200) || (img.width > 260 || img.height > 260)) {
                            $('#msg').html('<div class="alert alert-danger">Profile image must be close to 2x2 inch (220x220px). Your image is '+img.width+'x'+img.height+'px.</div>');
                            $('#cimg').hide();
                            $('#imgInput').val('');
                        } else {
                            $('#msg').html('');
                            $('#cimg').attr('src', img.src).show();
                        }
                    };
                    img.src = URL.createObjectURL(file);
                }
            });

            // Duplicate prevention: add helper and rebind add button to enforce unique date_started entries
            (function($){
                // return true if dateStarted is already present in employmentHistory
                function isDuplicateEntry(dateStarted){
                    if(!dateStarted) return false;
                    var ds = String(dateStarted).trim();
                    // employmentHistory is defined in the main script above
                    if(!window.employmentHistory || !Array.isArray(window.employmentHistory)) return false;
                    return window.employmentHistory.some(function(it){
                        return String(it.date_started || '').trim() === ds;
                    });
                }

                // Rebind the add button safely (remove any previous handlers)
                $(document).ready(function(){
                    $('#addEmploymentBtn').off('click').on('click', function(){
                        var dateStarted = $('#date_started').val() || '';

                        if(!dateStarted){
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Please enter a start date before adding to history',
                                showConfirmButton: false,
                                timer: 2200
                            });
                            return;
                        }

                        if(isDuplicateEntry(dateStarted)){
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: 'This start date is already added to your employment history',
                                showConfirmButton: false,
                                timer: 2200
                            });
                            return;
                        }

                        // Build minimal entry and reuse existing render function
                        var entry = {
                            date_started: dateStarted,
                            duration: (typeof computeDuration === 'function') ? computeDuration(dateStarted) : ''
                        };

                        // Ensure global array exists
                        if(!window.employmentHistory || !Array.isArray(window.employmentHistory)) window.employmentHistory = [];
                        window.employmentHistory.unshift(entry);

                        // call existing render function if available
                        if(typeof renderEmploymentHistory === 'function'){
                            renderEmploymentHistory();
                        }

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Added to employment history',
                            showConfirmButton: false,
                            timer: 1800
                        });
                    });
                });
            })(jQuery);

            // Form submission
            $('#create_account').submit(function(e){
                e.preventDefault();
                
                // Clear previous messages
                $('#msg').html('');
                
                // Get form data
                let formData = new FormData(this);
                
                // Check if alumni ID is verified
                if(!alumniVerified) {
                    $('#msg').html('<div class="alert alert-danger">Please verify your Alumni ID first</div>');
                    return false;
                }
                
                // Check required fields
                let requiredFields = {
                    'alumni_id': 'Alumni ID',
                    'email': 'Email', 
                    'password': 'Password',
                    'address': 'Address'
                };
                
                let missingFields = [];
                for(let field in requiredFields) {
                    if(!formData.get(field)) {
                        missingFields.push(requiredFields[field]);
                        $(`[name="${field}"]`).addClass('is-invalid');
                    } else {
                        $(`[name="${field}"]`).removeClass('is-invalid');
                    }
                }
                
                // Profile image is optional
                if($('#imgInput')[0].files[0]) {
                    // Validate image if one is selected
                    const file = $('#imgInput')[0].files[0];
                    if(file.size > 5 * 1024 * 1024) {
                        $('#msg').html('<div class="alert alert-danger">Profile image must be under 5MB</div>');
                        return false;
                    }
                }
                
                if(missingFields.length > 0) {
                    $('#msg').html(`
                        <div class="alert alert-danger">
                            Please fill in the following required fields:
                            <ul class="mb-0 mt-2">
                                ${missingFields.map(field => `<li>${field}</li>`).join('')}
                            </ul>
                        </div>
                    `);
                    return false;
                }
                
                // Ensure course value is present in form (if select hidden, copy to hidden input)
                var $courseSel = $('select[name="course_id"]');
                if($courseSel.length) {
                    var cval = $courseSel.val() || '';
                    if($('#hidden_course_id').length) {
                        $('#hidden_course_id').val(cval);
                    } else {
                        $('<input>').attr({type:'hidden', id:'hidden_course_id', name:'course_id', value: cval}).appendTo('#create_account');
                    }
                }

                // Ensure employment_status is present in form (copy to hidden input if needed)
                var $empSel = $('select[name="employment_status"]');
                if($empSel.length) {
                    var evalv = $empSel.val() || '';
                    if($('#hidden_employment_status').length) {
                        $('#hidden_employment_status').val(evalv);
                    } else {
                        $('<input>').attr({type:'hidden', id:'hidden_employment_status', name:'employment_status', value: evalv}).appendTo('#create_account');
                    }
                }

                // If all validation passes, submit the form
                start_load();
                $.ajax({
                    url: 'admin/ajax.php?action=signup',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function(response){
                        try {
                            // Parse the response if it's a string
                            const resp = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if(resp.status === 'success'){
                                end_load();
                                // Show success modal and inform user about confirmation email and validation
                                const accountEmail = $('#account_email').val() || resp.email || '';
                                Swal.fire({
                                    title: 'Registration Successful!',
                                    html: `
                                        <div class="text-center">
                                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                                            <p class="mt-3">${resp.message}</p>
                                            <p class="mt-2">A confirmation message has been sent to <strong>${accountEmail}</strong>.</p>
                                            <p class="text-muted">Please wait for validation from the Registrar. You will receive an email once your account is validated.</p>
                                            <p class="text-muted">You will be redirected to the home page in 4 seconds...</p>
                                        </div>
                                    `,
                                    icon: 'success',
                                    timer: 4000,
                                    timerProgressBar: true,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                }).then(() => {
                                    window.location.href = 'index.php';
                                });
                            } else {
                                end_load();
                                Swal.fire({
                                    title: 'Error!',
                                    text: resp.message || 'Something went wrong!',
                                    icon: 'error',
                                    confirmButtonText: 'Ok'
                                });
                            }
                        } catch (error) {
                            end_load();
                            $('#msg').html('<div class="alert alert-danger">'+response+'</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#msg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                        end_load();
                    }
                });
            });
        });

        function displayImg(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var reader = new FileReader();
            reader.onload = function(e){
                var img = new Image();
                img.onload = function(){
                    // create canvas to enforce 220x220 square
                    var FINAL = 220;
                    var canvas = document.createElement('canvas');
                    canvas.width = FINAL; canvas.height = FINAL;
                    var ctx = canvas.getContext('2d');
                    // compute centered square crop source
                    var s = Math.min(img.width, img.height);
                    var sx = Math.max(0, (img.width - s) / 2);
                    var sy = Math.max(0, (img.height - s) / 2);
                    try{
                        ctx.drawImage(img, sx, sy, s, s, 0, 0, FINAL, FINAL);
                        // compress to JPEG for smaller size
                        var dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                        $('#cimg').attr('src', dataUrl).show().css({'max-width':FINAL+'px','height':'auto','margin-top':'8px'});
                        $('#msg').html('');
                        if($('#profileCapture').length === 0) {
                            $('<input>').attr({type:'hidden',name:'profileCapture',id:'profileCapture'}).appendTo('#create_account');
                        }
                        $('#profileCapture').val(dataUrl);
                        $('#photoActions').show();
                    }catch(err){
                        $('#msg').html('<div class="alert alert-danger">Unable to process image. Please try a different file.</div>');
                        $('#cimg').hide();
                        $('#imgInput').val('');
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
        }

        // Retake and remove handlers
        $(document).on('click', '#retakeBtn', function(){
            // clear current capture and re-open camera/file picker
            $('#profileCapture').remove();
            $('#cimg').attr('src','').hide();
            $('#photoActions').hide();
            // trigger the file input on mobile or open camera modal for desktop
            const fileInput = document.getElementById('imgInput');
            const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            if(isMobile && fileInput) { fileInput.click(); return; }
            // otherwise re-open camera modal
            $('#takePicBtn').trigger('click');
        });

        $(document).on('click', '#removePhotoBtn', function(){
            $('#profileCapture').remove();
            $('#cimg').attr('src','').hide();
            $('#photoActions').hide();
            $('#imgInput').val('');
        });

        function start_load(){
            if($('#preloader2').length===0) {
                $('body').prepend('<div id="preloader2" style="position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7) url(\'assets/img/logo.png\') center center no-repeat;"></div>');
            }
        }

        function end_load(){
            $('#preloader2').fadeOut('fast', function() {
                $(this).remove();
            });
        }

        function alert_toast($msg = 'TEST',$bg = 'success'){
            $('#alert_toast').remove();
            var alert_toast = $('<div id="alert_toast" class="alert alert-'+$bg+' py-2 px-4" style="position: fixed; top: 20px; right: 20px; z-index: 99999;">')
                .text($msg);
            $('body').append(alert_toast);
            alert_toast.show();
            setTimeout(function(){
                alert_toast.hide('slow', function(){
                    $(this).remove();
                });
            }, 2000);
        }
                </script>

                <!-- UI Enhancements: course full-view modal and clean JS helpers -->
                <div class="modal fade" id="courseFullModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="background:linear-gradient(90deg,#800000,#600000);color:#fff">
                                <h5 class="modal-title">Full Course / Graduated</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="word-break:break-word;white-space:pre-wrap"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                (function($){
                    // Safe, non-destructive UI helpers for signup
                    $(function(){
                        // Password show/hide: attach to any password inputs if not already wrapped
                        $('input[type="password"]').each(function(){
                            var $inp = $(this);
                            if($inp.closest('.password-wrapper').length) return;
                            $inp.wrap('<div class="password-wrapper" style="position:relative"></div>');
                            var $btn = $('<button type="button" class="password-toggle-btn" aria-label="Toggle password"><i class="fas fa-eye"></i></button>');
                            $inp.after($btn);
                            $btn.on('click', function(e){
                                e.preventDefault();
                                if($inp.attr('type') === 'password'){
                                    $inp.attr('type','text'); $btn.html('<i class="fas fa-eye-slash"></i>');
                                } else {
                                    $inp.attr('type','password'); $btn.html('<i class="fas fa-eye"></i>');
                                }
                                $inp.focus();
                            });
                        });

                        // Password strength meter for #password -> updates #password_strength .progress-bar
                        $(document).on('input', '#password', function(){
                            var val = $(this).val() || '';
                            var score = 0;
                            if(val.length >= 8) score++;
                            if(/[a-z]/.test(val)) score++;
                            if(/[A-Z]/.test(val)) score++;
                            if(/[0-9]/.test(val)) score++;
                            if(/[^A-Za-z0-9]/.test(val)) score++;
                            var pct = Math.min(100, Math.round(score * 20));
                            var $bar = $('#password_strength .progress-bar');
                            if($bar.length){
                                $bar.css('width', pct + '%').removeClass('bg-danger bg-warning bg-success');
                                if(pct < 40) $bar.addClass('bg-danger').css('background-color','#dc3545');
                                else if(pct < 80) $bar.addClass('bg-warning').css('background-color','#ffc107');
                                else $bar.addClass('bg-success').css('background-color','#28a745');
                            }
                        });

                        // Add small 'View full' buttons next to course inputs/texts
                        function addCourseViewButtons(){
                            var selectors = '.course-graduated, .course-text, #courseGraduated, #course_id';
                            $(selectors).each(function(){
                                var $el = $(this);
                                if(!$el.length) return;
                                if($el.data('hasView')) return;
                                var $btn = $('<button type="button" class="btn btn-sm btn-outline-secondary ms-2 view-full-course" title="View full text">View full</button>');
                                $el.after($btn);
                                $el.data('hasView', true);
                            });
                        }
                        addCourseViewButtons();

                        $(document).on('click', '.view-full-course', function(){
                            var $el = $(this).prev();
                            var text = '';
                            if($el.is('input') || $el.is('textarea')) text = $el.val(); else text = $el.text();
                            $('#courseFullModal .modal-body').text(text || 'No value');
                            $('#courseFullModal').modal('show');
                        });

                        // Image preview for #imgInput -> #cimg
                        $(document).on('change', '#imgInput', function(){
                            var file = this.files && this.files[0]; if(!file) return;
                            var reader = new FileReader();
                            reader.onload = function(e){ $('#cimg').attr('src', e.target.result).show(); };
                            reader.readAsDataURL(file);
                        });

                        // Small UX: animate signup card and focus first input
                        if($('.signup-bg-box').length) $('.signup-bg-box').addClass('animate__animated animate__fadeInUp');
                        setTimeout(function(){ var $first = $('#create_account :input:visible:first'); if($first.length) $first.focus(); }, 250);
                    });
                })(jQuery);
                                </script>

                                <script>
                                (function(){
                                    // Minimal client-side normalization and resilient send/verify UX
                                    function normalizeContact(c){
                                        c = (c||'').toString().trim();
                                        if(!c) return '';
                                        if(c.indexOf('@') !== -1 || /[A-Za-z]/.test(c)) return c.toLowerCase();
                                        return c.replace(/[^0-9]/g, '');
                                    }
                                    function normalizeOtp(o){ return (o||'').toString().replace(/[^0-9]/g,'').trim(); }

                                    document.addEventListener('DOMContentLoaded', function(){
                                        var send = document.getElementById('sendOtpBtn');
                                        var verify = document.getElementById('verifyOtpBtn');
                                        var loginContact = document.getElementById('login_contact');
                                        var otpInput = document.getElementById('otp_input');

                                        if(send && loginContact){
                                            send.addEventListener('click', function(e){
                                                e.preventDefault();
                                                var raw = loginContact.value || '';
                                                var contact = normalizeContact(raw);
                                                if(!contact){ alert('Please enter email or phone'); return; }
                                                // write back normalized value so existing code reads same
                                                loginContact.value = contact;
                                            });
                                        }

                                        if(verify && loginContact && otpInput){
                                            verify.addEventListener('click', function(e){
                                                e.preventDefault();
                                                var raw = loginContact.value || '';
                                                var contact = normalizeContact(raw);
                                                var otp = normalizeOtp(otpInput.value || '');
                                                if(!contact || !otp){ alert('Enter contact and 6-digit OTP'); return; }
                                                loginContact.value = contact;
                                                otpInput.value = otp;
                                            });
                                        }
                                    });
                                })();
                                </script>

                </body>
        </html>