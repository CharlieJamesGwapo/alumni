<?php
session_start();

// Check if user has agreed to privacy notice
if (!isset($_SESSION['privacy_agreed']) || $_SESSION['privacy_agreed'] !== true) {
    header("Location: privacy_prompt.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>MOIST Alumni Portal - Connect, Engage, Thrive</title>
    <meta name="description" content="Connect with MOIST alumni, discover opportunities, and stay engaged with your alma mater community.">
    <meta name="theme-color" content="#800000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="icon" type="image/png" href="assets/img/logo.png"/>
    <link rel="apple-touch-icon" href="assets/img/logo.png">
    
    <!-- Optimized CSS Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* ===== MODERN MINIMAL DESIGN SYSTEM ===== */
        :root{
                --maroon: #800000; --maroon-r:128,0,0;
                --gold: #ffd700;
                --white: #ffffff;
                --bg: #f5f7fb;
                --card: #ffffff;
                --text: #222222;
                --muted: rgba(0,0,0,0.55);
                --navbar-bg: var(--maroon);
                --hero-overlay: rgba(0,0,0,0.6);
                --btn-primary-bg: var(--maroon);
                --btn-primary-color: var(--white);
            }
            /* Accessible skip link */
            .sr-only-focusable{ position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden; }
            .sr-only-focusable:focus, .sr-only-focusable:active{ position: static; width: auto; height: auto; margin: 0.5rem; padding: 0.5rem 1rem; background:#fff; color:#000; z-index:2000; border-radius:6px; text-decoration:none }
            [data-theme="dark"]{ --bg:#071018; --card:#0b1720; --text:#ffffff; --muted:rgba(255,255,255,0.7); }
            body{ background:var(--bg); color:var(--text); }
            .accent-maroon{ color:var(--maroon) !important }
            .bg-maroon{ background:var(--maroon) !important; color:var(--white) !important }
            .btn-maroon{ background:var(--maroon); color:var(--white); border:none }

            /* Enhanced Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .animate-slideIn {
                animation: slideIn 0.6s ease-out;
            }
            
            .animate-pulse {
                animation: pulse 2s infinite;
            }
            
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }

            .animate-fadeInUp {
                animation: fadeInUp 0.6s ease-out;
            }

            .animate-float {
                animation: float 3s ease-in-out infinite;
            }

            /* Hover Effects */
            .dashboard-links a:hover {
                background: rgba(128,0,0,0.1);
                transform: translateX(10px);
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            }

            .contact-card:hover {
                transform: translateY(-5px);
                background: rgba(255,255,255,0.15);
            }

            .social-icon:hover {
                background: rgba(255,255,255,0.2);
                transform: translateY(-3px);
            }

            footer a:hover {
                color: #ffd700 !important;
            }

            .btn-outline-light:hover {
                background: #ffd700;
                border-color: #ffd700;
                color: #800000 !important;
            }

            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                width: 100vw;
                overflow-x: hidden;
            }
            body {
                background: #f5f7fb;
                font-family: 'Poppins', 'Montserrat', Arial, sans-serif;
            }
            .moist-topbar {
                background: #444;
                color: #fff;
                height: 36px;
                font-size: 0.97rem;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                font-family: 'Montserrat', sans-serif;
                position: relative;
                z-index: 1050;
                width: 100vw;
            }
            .moist-topbar a {
                color: #fff !important;
                padding: 0.5rem 1.3rem;
                text-decoration: none;
                font-weight: 500;
                letter-spacing: 1px;
                transition: background 0.18s;
            }
            .moist-topbar a:hover {
                background: #222;
                text-decoration: none;
            }
            .moist-navbar-border {
                height: 6px;
                background: #800000;
                width: 100vw;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1040;
            }
            .navbar {
                background: #800000 !important;
                box-shadow: 0 2px 10px rgba(0,0,0,0.04);
                padding: 0.8rem 0;
                border-bottom: 0;
                width: 100vw;
            }
            /* Responsive, accessible brand/header improvements */
            .navbar-brand {
                font-weight: 800;
                display: flex;
                align-items: center;
                gap: 1rem;
                color: #fff !important;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
                transition: all 0.25s ease;
                padding: 0.35rem 0;
            }
            .navbar-brand:hover { transform: none; }

            /* Use clamp so logo/title scale smoothly across devices */
            .moist-logo {
                height: clamp(44px, 8vw, 80px);
                width: clamp(44px, 8vw, 80px);
                border-radius: 12%; /* softer rounding for small screens */
                margin-right: 0.5rem;
                box-shadow: 0 6px 18px rgba(0,0,0,0.12);
                object-fit: cover;
                transition: transform 0.35s ease, box-shadow 0.35s ease;
                background: transparent;
            }
            .moist-logo:hover { transform: rotate(3deg) scale(1.03); box-shadow: 0 8px 28px rgba(0,0,0,0.18); }

            .brand-text {
                display: flex;
                flex-direction: column;
                line-height: 1.05;
                min-width: 0; /* allow truncation on small screens */
            }

            .brand-title {
                font-size: clamp(1.25rem, 4vw, 2.2rem);
                font-family: 'Playfair Display', serif;
                font-weight: 900;
                color: #ffd700;
                text-shadow: 1px 1px 6px rgba(0,0,0,0.25);
                letter-spacing: 0.6px;
                margin-bottom: 0;
                line-height: 1.05;
                /* allow wrapping on small screens so subtitle can appear below */
                white-space: normal;
                word-break: break-word;
                overflow: visible;
            }

            .brand-subtitle {
                font-size: clamp(0.78rem, 2.3vw, 1.2rem);
                font-family: 'Poppins', sans-serif;
                font-weight: 600;
                color: #fff;
                opacity: 0.95;
                margin-top: 2px;
                white-space: normal;
                word-break: break-word;
                overflow: visible;
            }

            /* Tighter layout on medium and small screens */
            @media (max-width: 991.98px) {
                .navbar-brand { gap: 0.75rem; padding: 0.3rem 0; }
                .moist-logo { height: clamp(40px,7.5vw,65px); width: clamp(40px,7.5vw,65px); }
                .brand-title { font-size: clamp(1.05rem,3.6vw,1.8rem); }
                .brand-subtitle { font-size: clamp(0.72rem,2vw,1.05rem); }
            }

            /* Compact mobile header for very small devices (e.g., Redmi) */
            @media (max-width: 576px) {
                .navbar { padding: 0.45rem 0; }
                .navbar-brand { gap: 0.6rem; align-items: center; }
                .moist-logo { height: 48px; width: 48px; }
                /* stack title + subtitle vertically but allow them to use available width */
                .brand-text { flex-direction: column; align-items: flex-start; }
                .brand-title { font-size: 1.05rem; font-weight:800; }
                /* ensure subtitle remains visible on mobiles and wraps if needed */
                .brand-subtitle { display: block; font-size: 0.78rem; opacity:0.95; }
                .nav-link { font-size: 0.95rem; margin-right: 1rem; }
                .moist-topbar a { padding: 0.4rem 0.9rem; font-size: 0.92rem; }
            }
            .nav-link {
                color: #fff !important;
                font-weight: 600;
                margin-right: 2.1rem;
                font-size: 1.08rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: color 0.15s, background 0.2s;
                position: relative;
                background: none !important;
            }
            .nav-link.active, .nav-link:hover {
                color: #ffd700 !important;
            }
            .nav-link.active::after {
                content: "";
                display: block;
                width: 70%;
                margin: 0.2rem auto 0 auto;
                border-bottom: 3px solid #ffd700;
                border-radius: 2px;
            }
            .navbar-toggler {
                border: none;
                font-size: 1.35rem;
                background: none;
            }
            .navbar-toggler:focus {
                outline: none;
                box-shadow: none;
            }
            .search-icon {
                font-size: 1.17rem;
                margin-left: 0.4rem;
                vertical-align: -2px;
            }
            .hero-section {
                background: linear-gradient(rgba(0,0,0,0.8), rgba(128,0,0,0.7)), url('assets/img/moist12.jpg') center center/cover no-repeat fixed;
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100vw;
                padding: 6rem 1rem;
                overflow: hidden;
            }
            .hero-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to right bottom, rgba(128,0,0,0.3), rgba(183,28,28,0.3));
                z-index: 1;
            }
            .hero-content {
                position: relative;
                z-index: 2;
                width: 100%;
                max-width: 1100px;
                padding: 3.5rem 2.5rem;
                margin: 0 auto;
                text-align: center;
                background: rgba(0,0,0,0.2);
                box-shadow: 0 8px 32px rgba(0,0,0,0.2);
                border-radius: 24px;
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255,255,255,0.1);
            }
            .hero-content h1 {
                font-size: 3.5rem;
                font-weight: 900;
                font-family: 'Playfair Display', serif;
                font-style: italic;
                margin-bottom: 1.5rem;
                line-height: 1.2;
                letter-spacing: 2px;
                color: #fff !important;
                text-shadow: 2px 2px 15px rgba(0,0,0,0.35);
                position: relative;
                display: inline-block;
                background: none !important; /* ensure text is solid white on all browsers */
                background-clip: initial !important;
                -webkit-background-clip: initial !important;
                -webkit-text-fill-color: #fff !important;
            }
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .hero-content h1 span {
                color: #ffd700;
                text-shadow: 0 4px 12px rgba(255,215,0,0.3);
                position: relative;
                display: inline-block;
                transform: scale(1.1);
                animation: glowPulse 2s infinite;
            }
            @keyframes glowPulse {
                0% { text-shadow: 0 0 10px rgba(255,215,0,0.3); }
                50% { text-shadow: 0 0 20px rgba(255,215,0,0.6); }
                100% { text-shadow: 0 0 10px rgba(255,215,0,0.3); }
            }
            .hero-content p {
                font-size: 1.25rem;
                margin-bottom: 2.1rem;
                font-weight: 500;
                color: #fff;
                max-width: 800px;
                letter-spacing: 0.2px;
                background: none;
                padding: 0;
                border-radius: 0;
                box-shadow: none;
                display: inline-block;
                text-shadow: 0 2px 14px rgba(0,0,0,0.18);
            }
            .hero-highlight {
                display: inline-block;
                background: linear-gradient(90deg, rgba(255,215,0,0.95) 10%, rgba(255,215,0,0.85) 100%);
                color: #fff !important;
                font-weight: 700;
                padding: 0.12em 0.7em;
                border-radius: 0.6em;
                margin-bottom: 0.8em;
                box-shadow: 0 4px 18px rgba(0,0,0,0.18);
            }
            .hero-content .btn {
                font-weight: 700;
                font-size: 1.07rem;
                margin-right: 0.6rem;
                padding: 0.62rem 2.1rem;
                border-radius: 4px;
                letter-spacing: 0.6px;
                box-shadow: 0 2px 10px rgba(128,0,0,0.06);
                transition: all 0.18s;
                min-width: 180px;
            }
            .hero-content .btn-primary {
                background: #800000;
                border: none;
                color: #fff !important;
            }
            .hero-content .btn-primary:hover {
                background: #b71c1c;
                color: #ffd700 !important;
            }
            .hero-content .btn-outline-light {
                color: #800000 !important;
                border-color: #800000;
                background: rgba(255,255,255,0.65);
            }
            .hero-content .btn-outline-light:hover {
                background: #800000;
                color: #fff !important;
            }
            .section {
                padding: 2.5rem 0 1.5rem 0;
            }
            .card {
                border-radius: 10px;
                box-shadow: 0 1px 8px rgba(0,0,0,0.07);
            }
            .sidebar {
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.04);
                padding: 1.5rem 1rem;
                margin-bottom: 2rem;
            }
            .sidebar h5 {
                font-weight: 700;
                color: #800000;
                margin-bottom: 1.1rem;
                letter-spacing: 1px;
            }
            .sidebar a {
                display: block;
                color: #b71c1c;
                margin-bottom: 0.6rem;
                font-size: 1.08rem;
                text-decoration: none;
                font-weight: 600;
                letter-spacing: 0.5px;
                transition: color 0.17s;
            }
            .sidebar a:hover {
                color: #800000;
                text-decoration: none;
            }
            .media-block {
                background: #fff;
                border-radius: 13px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                padding: 1.2rem 1rem 0.5rem 1rem;
                margin-bottom: 2rem;
                text-align: center;
                min-height: 235px;
            }
            .media-block img, .media-block iframe {
                border-radius: 9px;
                max-width: 100%;
            }
            .media-block .title {
                font-weight: 700;
                color: #b71c1c;
                margin-top: 1rem;
                font-size: 1.1rem;
                letter-spacing: 0.7px;
            }
            @media (max-width: 1199.98px) {
                .hero-content h1 { font-size: 2.06rem; }
                .hero-content p { font-size: 1.01rem; }
            }
            @media (max-width: 991.98px) {
                .navbar-nav .nav-link { margin-right: 1rem; font-size: 1.01rem; }
                .hero-content { padding: 2.2rem 1rem 1.2rem 1rem;}
            }
            @media (max-width: 767.98px) {
                .moist-navbar-border { height: 4px; }
                .navbar { padding: 0.6rem 0; }
                .sidebar { padding: 1rem 0.7rem; }
                .hero-content { 
                    padding: 2rem 1.5rem !important;
                    margin: 1rem !important;
                }
                .hero-content h1 { 
                    font-size: 1.8rem !important;
                    line-height: 1.3 !important;
                    color: #fff !important;
                    -webkit-text-fill-color: #fff !important;
                }
                .hero-highlight span {
                    font-size: 1rem !important;
                }
                .hero-content .btn {
                    width: 100%;
                    margin: 0.5rem 0 !important;
                }
                .media-block { min-height: 0; }
                .contact-card {
                    margin-bottom: 1.5rem;
                }
                .event-card {
                    margin-bottom: 2rem;
                }
            }
            
            @media (max-width: 576px) {
                .hero-content h1 {
                    font-size: 1.4rem !important;
                    line-height: 1.25 !important;
                }
                .hero-content p {
                    font-size: 1rem !important;
                }
                .contact-section {
                    padding: 3rem 0 2rem 0;
                }
                .navbar-brand {
                    font-size: 1.2rem;
                }
                .moist-logo {
                    height: 36px;
                    width: 36px;
                }
            }
            .dropdown-toggle::after { display: none; }
            /* Modal Customization */
            .modal-content {
                border-radius: 16px;
            }
            .modal-header {
                background: #800000;
                color: #fff;
                border-top-left-radius: 16px;
                border-top-right-radius: 16px;
                border-bottom: 0;
            }
            .modal-footer {
                border-top: 0;
                background: #f5f7fb;
            }
            .modal-body {
                background: #f9fbfd;
            }
            .form-control:focus {
                border-color: #800000;
                box-shadow: 0 0 0 0.2rem rgba(128,0,0,0.12);
            }
            .btn-moist {
                background: linear-gradient(145deg, #800000, #990000);
                color: #fff;
                font-weight: 700;
                border-radius: 50px;
                padding: 0.8rem 2rem;
                border: none;
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(128,0,0,0.2);
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .btn-moist:hover {
                background: linear-gradient(145deg, #990000, #800000);
                color: #ffd700;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(128,0,0,0.3);
            }
            
            .btn-moist:active {
                transform: translateY(1px);
                box-shadow: 0 2px 10px rgba(128,0,0,0.2);
            }
            
            .btn-moist::after {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: rgba(255,255,255,0.1);
                transform: rotate(45deg);
                transition: all 0.3s ease;
                opacity: 0;
            }
            
            .btn-moist:hover::after {
                opacity: 1;
                transform: rotate(45deg) translate(50%, 50%);
            }
            footer {
                background: #800000;
                color: #fff;
                padding: 2rem 0 1rem 0;
                text-align: center;
                letter-spacing: 0.5px;
                width: 100vw;
            }
            footer a, footer a:visited {
                color: #ffd700;
                text-decoration: underline;
            }
            footer a:hover {
                color: #fff;
            }

            /* Floating Action Button */
            .floating-action-btn {
                position: fixed;
                right: 30px;
                bottom: 30px;
                z-index: 1000;
            }

            .floating-action-btn .main-btn {
                width: 60px;
                height: 60px;
                background: #800000;
                border-radius: 50%;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                border: none;
                color: #fff;
                font-size: 24px;
                transition: all 0.3s ease;
            }

            .floating-action-btn .main-btn:hover {
                background: #b71c1c;
                transform: rotate(45deg);
            }

            .float-elements {
                position: absolute;
                bottom: 70px;
                right: 0;
                list-style: none;
                padding: 0;
                margin: 0;
                visibility: hidden;
                opacity: 0;
                transition: all 0.3s ease;
            }

            .float-elements.active {
                visibility: visible;
                opacity: 1;
            }

            .float-elements li {
                margin-bottom: 15px;
            }

            .float-elements a {
                width: 50px;
                height: 50px;
                background: #fff;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #800000;
                text-decoration: none;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }

            .float-elements a:hover {
                background: #800000;
                color: #fff;
                transform: scale(1.1);
            }

            /* Theme Switcher */
            .theme-switcher {
                position: fixed;
                left: 30px;
                bottom: 30px;
                z-index: 1000;
            }

            .theme-btn {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: none;
                background: #800000;
                color: #fff;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }

            .theme-btn:hover {
                transform: rotate(360deg);
            }

            /* Dark Theme Styles */
            body.dark-theme {
                background: #1a1a1a;
                color: #fff;
            }

            .dark-theme .navbar {
                background: #2d2d2d !important;
            }

            .dark-theme .hero-section {
                background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.7)), url('assets/img/moist12.jpg') center center/cover no-repeat fixed;
            }

            /* Back to Top Button */
            .back-to-top {
                position: fixed;
                right: 30px;
                bottom: 110px;
                width: 50px;
                height: 50px;
                background: #800000;
                color: #fff;
                border-radius: 50%;
                border: none;
                display: none;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                z-index: 999;
            }

            .back-to-top:hover {
                background: #b71c1c;
                transform: translateY(-5px);
            }

            .back-to-top.visible {
                display: flex;
            }
            
            /* Enhanced Card Hover Effects */
            .card {
                transition: all 0.3s ease;
                transform-style: preserve-3d;
                perspective: 1000px;
            }
            
            .card:hover {
                transform: translateY(-10px) rotateX(5deg);
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            }
            
            /* Parallax Effect */
            .parallax-bg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 0;
            }
            
            /* Text Gradient Animation */
            .gradient-text {
                background: linear-gradient(45deg, #800000, #b71c1c, #ffd700);
                background-size: 200% auto;
                color: #000;
                background-clip: text;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                animation: gradient 3s linear infinite;
            }
            
            @keyframes gradient {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .course-card {
                background: white;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .course-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            }

            .swiper-pagination-bullet-active {
                background: #800000;
            }

            /* Academic Programs Section Styles */
            .program-card {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .program-card::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.7));
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .program-card:hover::after {
                opacity: 1;
            }

            .program-card img {
                transition: transform 0.3s ease;
            }

            .program-card:hover img {
                transform: scale(1.1);
            }

            .program-details {
                animation-duration: 0.5s;
                animation-fill-mode: both;
            }

            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fade-in {
                animation-name: fade-in;
            }

            .college-item {
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .college-item:hover {
                transform: translateX(10px);
                background: rgba(255,255,255,0.2) !important;
            }

            .program-item {
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .program-item:hover {
                transform: translateY(-5px);
            }

            .program-item i {
                transition: all 0.3s ease;
            }

            .program-item:hover i {
                transform: scale(1.2);
            }
        </style>
    </head>
    <body>
        <a href="#main-content" class="sr-only-focusable">Skip to main content</a>
        <!-- Top Maroon Border -->
        <div class="moist-navbar-border"></div>
        <!-- Topbar -->
        <div class="moist-topbar">
            <a href="login.php"><i class="fa fa-sign-in-alt"></i> Login</a>
            <a href="signup.php"><i class="fa fa-user-plus"></i> Register</a>
        </div>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid px-3">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="assets/img/logo.png" alt="MOIST Logo" class="moist-logo">
                    <div class="brand-text">
                        <span class="brand-title">Misamis Oriental</span>
                        <span class="brand-subtitle">Institute of Science and Technology</span>
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="#navbarNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="fa fa-bars" aria-hidden="true"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto d-flex align-items-center">
                        <li class="nav-item"><a class="nav-link active" href="#">Community</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Opportunities</a></li>
                        <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Research</a></li>
                        <li class="nav-item"><a class="nav-link" href="admission.php">Admission</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                    <!-- search removed per UI request -->
                </div>
            </div>
        </nav>
        <!-- Hero Section: Enhanced Dynamic Design -->
        <section class="hero-section">
            <div class="container hero-content animate-fadeInUp" style="background:rgba(255,255,255,0.12);backdrop-filter:blur(12px);box-shadow:0 4px 24px rgba(128,0,0,0.15);border-radius:24px;padding:4rem 3rem;max-width:1100px;">
                <div class="hero-highlight mb-4 animate-float" style="background:linear-gradient(135deg, rgba(255,215,0,0.2) 0%, rgba(255,215,0,0.1) 100%);backdrop-filter:blur(8px);border-radius:16px;padding:1rem 2rem;display:inline-block;border:1px solid rgba(255,215,0,0.3);">
                    <span style="color:#ffd700;font-weight:800;font-size:1.4rem;text-shadow:0 2px 8px rgba(0,0,0,0.4);letter-spacing:1px;">WELCOME TO YOUR ALUMNI COMMUNITY</span>
                </div>
                <h1 class="mx-auto mb-4" style="font-size:3.2rem;font-weight:900;color:#fff;text-shadow:0 2px 14px rgba(0,0,0,0.25);line-height:1.2;max-width:800px;">Welcome to the <span style="color:#ffd700;text-shadow:0 4px 12px rgba(0,0,0,0.3);">MOIST</span> Alumni Portal</h1>
                <div class="text-container p-4 mb-4" style="background:rgba(0,0,0,0.5);backdrop-filter:blur(10px);border-radius:15px;max-width:850px;margin:0 auto;">
                    <p class="lead mb-4" style="font-size:1.4rem;font-weight:600;color:#fff;letter-spacing:0.5px;line-height:1.6;text-shadow:0 2px 4px rgba(0,0,0,0.2);">
                        The MOIST Alumni Portal connects graduates of Misamis Oriental Institute of Science and Technology
                    </p>
                    <p class="highlight-text mb-4" style="font-size:1.2rem;font-weight:700;color:#ffd700;letter-spacing:0.3px;line-height:1.5;text-shadow:0 2px 4px rgba(0,0,0,0.2);">
                        Network, collaborate, and discover new opportunities, events, and stories from our vibrant alumni community
                    </p>
                    <p class="mb-0" style="font-size:1.1rem;font-weight:500;color:#fff;letter-spacing:0.3px;line-height:1.5;text-shadow:0 2px 4px rgba(0,0,0,0.2);">
                        Stay involved and celebrate the achievements of fellow MOISTians!
                    </p>
                </div>
                <div class="mt-5 d-flex flex-column flex-md-row justify-content-center align-items-center gap-3">
                    <a href="signup.php" id="joinNowBtn" aria-label="Join MOIST - Sign Up" class="btn btn-primary mb-3 mb-md-0 mx-2" style="font-size:1.2rem;padding:0.8rem 2.5rem;border-radius:50px;background:#ffd700;border:none;color:#800000;font-weight:700;text-transform:uppercase;letter-spacing:1px;transition:all 0.3s ease;box-shadow:0 4px 15px rgba(255,215,0,0.3);">Join Now</a>
                    <a href="login.php" id="memberLoginBtn" aria-label="Member Login" class="btn btn-outline-light mx-2" style="font-size:1.2rem;padding:0.8rem 2.5rem;border-radius:50px;border:2px solid #fff;font-weight:700;text-transform:uppercase;letter-spacing:1px;transition:all 0.3s ease;backdrop-filter:blur(5px);">Member Login</a>
                </div>
            </div>
        </section>
        <!-- Main Section (Sidebar + Media/Content) -->
        <section class="section py-5" style="background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div class="container">
                <div class="row justify-content-center">
                    <!-- Sidebar/Community Overview -->
                    <div class="col-lg-3 mb-4">
                        <div class="sidebar" style="background:linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.05);padding:1.8rem;border:1px solid rgba(128,0,0,0.1);">
                            <h5 style="color:#800000;font-size:1.3rem;font-weight:800;margin-bottom:1.5rem;position:relative;padding-bottom:0.8rem;">
                                Alumni Dashboard
                                <span style="position:absolute;bottom:0;left:0;width:50px;height:3px;background:#800000;border-radius:2px;"></span>
                            </h5>
                            <div class="dashboard-links">
                                <a href="#" class="d-flex align-items-center mb-3 p-2 rounded transition-all" style="color:#444;text-decoration:none;transition:all 0.3s ease;">
                                    <i class="fa fa-user-circle mr-2" style="color:#800000;font-size:1.2rem;"></i>
                                    <span style="font-weight:600;">My Profile</span>
                                </a>
                                <a href="#" class="d-flex align-items-center mb-3 p-2 rounded transition-all" style="color:#444;text-decoration:none;transition:all 0.3s ease;">
                                    <i class="fa fa-book mr-2" style="color:#800000;font-size:1.2rem;"></i>
                                    <span style="font-weight:600;">Class Notes</span>
                                </a>
                                <a href="#" class="d-flex align-items-center mb-3 p-2 rounded transition-all" style="color:#444;text-decoration:none;transition:all 0.3s ease;">
                                    <i class="fa fa-users mr-2" style="color:#800000;font-size:1.2rem;"></i>
                                    <span style="font-weight:600;">Clubs & Organizations</span>
                                </a>
                                <a href="#" class="d-flex align-items-center mb-3 p-2 rounded transition-all" style="color:#444;text-decoration:none;transition:all 0.3s ease;">
                                    <i class="fa fa-newspaper mr-2" style="color:#800000;font-size:1.2rem;"></i>
                                    <span style="font-weight:600;">MOIST News</span>
                                </a>
                                <a href="#" class="d-flex align-items-center mb-3 p-2 rounded transition-all" style="color:#444;text-decoration:none;transition:all 0.3s ease;">
                                    <i class="fa fa-heart mr-2" style="color:#800000;font-size:1.2rem;"></i>
                                    <span style="font-weight:600;">Support MOIST</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Main Content/Media Block -->
                    <div class="col-lg-9">
                        <!-- Featured Content Cards -->
                        <div class="row">
                            <div class="col-12 mb-4">
                                <h3 class="mb-4" style="color:#333;font-weight:600;font-size:1.5rem;">Featured Updates</h3>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 bg-white h-100" style="box-shadow:0 2px 4px rgba(0,0,0,0.04);">
                                    <img src="assets/img/moistfam.jpg" alt="MOIST Family" class="card-img-top" style="height:200px;object-fit:cover;">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-weight:600;color:#333;">We Are MOIST</h5>
                                        <p class="card-text text-muted">Join our vibrant community of alumni and stay connected with your alma mater.</p>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Learn More →</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 bg-white h-100" style="box-shadow:0 2px 4px rgba(0,0,0,0.04);">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe class="embed-responsive-item" src="assets/vid/moistvid.mp4" 
                                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-weight:600;color:#333;">Campus Life</h5>
                                        <p class="card-text text-muted">Experience the vibrant campus life and memorable moments at MOIST.</p>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Watch Video →</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Statistics Counter -->
                        <div class="row mt-4 mb-4">
                            <div class="col-12 mb-3">
                                <h3 style="color:#333;font-weight:600;font-size:1.5rem;">Community Stats <span class="badge badge-success" style="font-size:0.7rem;vertical-align:middle;">LIVE</span></h3>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stat-card p-3 bg-white text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:all 0.3s ease;" data-aos="fade-up" data-aos-delay="100">
                                    <i class="fas fa-users mb-2" style="color:#800000;font-size:2rem;"></i>
                                    <h4 class="mb-0" style="color:#800000;font-weight:800;" id="alumniCount">0</h4>
                                    <small style="color:#666;font-weight:500;">Alumni</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stat-card p-3 bg-white text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:all 0.3s ease;" data-aos="fade-up" data-aos-delay="200">
                                    <i class="fas fa-calendar-check mb-2" style="color:#800000;font-size:2rem;"></i>
                                    <h4 class="mb-0" style="color:#800000;font-weight:800;" id="eventsCount">0</h4>
                                    <small style="color:#666;font-weight:500;">Events</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stat-card p-3 bg-white text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:all 0.3s ease;" data-aos="fade-up" data-aos-delay="300">
                                    <i class="fas fa-briefcase mb-2" style="color:#800000;font-size:2rem;"></i>
                                    <h4 class="mb-0" style="color:#800000;font-weight:800;" id="jobsCount">0</h4>
                                    <small style="color:#666;font-weight:500;">Opportunities</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="stat-card p-3 bg-white text-center" style="border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06);transition:all 0.3s ease;" data-aos="fade-up" data-aos-delay="400">
                                    <i class="fas fa-clock mb-2" style="color:#800000;font-size:2rem;"></i>
                                    <h4 class="mb-0" style="color:#800000;font-weight:700;font-size:1.3rem;" id="liveClock">--:--</h4>
                                    <small style="color:#666;font-weight:500;">Live Time</small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Access Grid -->
                        <div class="row mt-2">
                            <div class="col-12 mb-4">
                                <h3 style="color:#333;font-weight:600;font-size:1.5rem;">Quick Access</h3>
                            </div>
                            <div class="col-6 col-md-3 mb-4">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 bg-white text-center h-100" style="border:1px solid #eee;transition:all 0.2s ease;">
                                        <i class="fa fa-calendar mb-2" style="color:#800000;font-size:1.5rem;"></i>
                                        <div style="color:#333;font-weight:500;font-size:0.9rem;">Events</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-4">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 bg-white text-center h-100" style="border:1px solid #eee;transition:all 0.2s ease;">
                                        <i class="fa fa-briefcase mb-2" style="color:#800000;font-size:1.5rem;"></i>
                                        <div style="color:#333;font-weight:500;font-size:0.9rem;">Careers</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-4">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 bg-white text-center h-100" style="border:1px solid #eee;transition:all 0.2s ease;">
                                        <i class="fa fa-graduation-cap mb-2" style="color:#800000;font-size:1.5rem;"></i>
                                        <div style="color:#333;font-weight:500;font-size:0.9rem;">Directory</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6 col-md-3 mb-4">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 bg-white text-center h-100" style="border:1px solid #eee;transition:all 0.2s ease;">
                                        <i class="fa fa-newspaper mb-2" style="color:#800000;font-size:1.5rem;"></i>
                                        <div style="color:#333;font-weight:500;font-size:0.9rem;">News</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                            <!-- More dynamic content can be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alumni Events Gallery: server-rendered from events table -->
        <?php
            if (!isset($conn)) include_once __DIR__ . '/admin/db_connect.php';
            $events = [];
            try{
                $rs = $conn->query("SELECT * FROM events ORDER BY schedule DESC, date_created DESC");
                while($r = $rs->fetch_assoc()) $events[] = $r;
            } catch(Exception $e) { $events = []; }
            $initial_show = 6;
            // derive available years from events (use schedule where valid, otherwise date_created)
            $years = [];
            foreach($events as $ev){
                $d = strtotime($ev['schedule']);
                if (!$d || $d <= 0) $d = strtotime($ev['date_created']);
                if ($d && $d > 0) $years[] = date('Y', $d);
            }
            $years = array_values(array_unique($years));
            rsort($years);
        ?>
        <section class="alumni-events py-5" style="background:#f8f9fa;">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2 style="font-weight:800;color:#800000;font-size:2.5rem;margin-bottom:1rem;">Alumni Events Gallery</h2>
                    <p style="color:#666;font-size:1.2rem;max-width:800px;margin:0 auto;">Explore our vibrant community events and stay connected with fellow alumni.</p>
                </div>

                <!-- Year Filter -->
                <div class="event-filters mb-4">
                    <div class="row justify-content-center">
                        <div class="col-md-4 mb-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#800000;color:#fff;border:none;">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                                <select class="form-control" id="eventYearFilter" style="border-left:none;">
                                    <option value="">All years</option>
                                    <?php foreach($years as $y){ echo "<option value='".htmlspecialchars($y)."'>".htmlspecialchars($y)."</option>"; } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="eventsGrid">
                    <?php if (empty($events)): ?>
                        <div class="col-12 text-center"><p class="lead" style="color:#666;">No events found.</p></div>
                    <?php else: ?>
                        <?php foreach($events as $i=>$ev):
                            $event_date = strtotime($ev['schedule']);
                            if (!$event_date || $event_date <= 0) $event_date = strtotime($ev['date_created']);
                            $is_upcoming = $event_date > time();
                            $banner = (!empty($ev['banner']) && file_exists(__DIR__.'/uploads/'.$ev['banner'])) ? 'uploads/'.rawurlencode($ev['banner']) : 'assets/img/default-banner.jpg';
                            $desc = strip_tags(html_entity_decode($ev['content']));
                            $short = strlen($desc) > 150 ? substr($desc,0,150).'...' : $desc;
                            $extra = ($i >= $initial_show) ? 'd-none extra-event' : '';
                            $data_year = $event_date ? date('Y', $event_date) : '';
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4 event-item <?php echo $extra; ?>" data-year="<?php echo htmlspecialchars($data_year); ?>">
                            <div class="card h-100">
                                <div class="event-banner" style="overflow:hidden;border-top-left-radius:10px;border-top-right-radius:10px;">
                                    <img src="<?php echo htmlspecialchars($banner); ?>" alt="<?php echo htmlspecialchars($ev['title']); ?>" style="width:100%;height:220px;object-fit:cover;display:block;">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title" style="color:#800000;font-weight:800"><?php echo htmlspecialchars($ev['title']); ?></h5>
                                    <p class="card-text" style="color:#444"><?php echo htmlspecialchars($short); ?></p>
                                    <div class="d-flex justify-content-between align-items-center" style="font-size:0.9rem;color:#666">
                                        <span class="badge badge-light p-2"><?php echo $is_upcoming ? 'Upcoming' : 'Past'; ?></span>
                                        <small>Posted <?php echo date('M d, Y', strtotime($ev['date_created'])); ?></small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent d-flex justify-content-between">
                                    <button class="btn btn-sm btn-outline-primary join-event" data-id="<?php echo $ev['id']; ?>">Join</button>
                                    <a href="view_event.php?id=<?php echo $ev['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-4">
                    <button id="loadMoreEvents" class="btn btn-outline-primary" style="border-color:#800000;color:#800000;padding:10px 30px;border-radius:25px;font-weight:600;">Load more events</button>
                </div>
            </div>
        </section>

        <script>
            (function(){
                const initialShow = <?php echo $initial_show; ?>;
                const btn = document.getElementById('loadMoreEvents');
                const extras = Array.from(document.querySelectorAll('.extra-event'));
                const perClick = Math.max(3, Math.floor(initialShow/2));
                if (!btn) return;
                if (extras.length === 0) btn.style.display = 'none';
                btn.addEventListener('click', function(){
                    for(let i=0;i<perClick && extras.length>0;i++){
                        const el = extras.shift();
                        el.classList.remove('d-none');
                    }
                    if (extras.length === 0) btn.style.display = 'none';
                });

                // Year filter
                const yearFilter = document.getElementById('eventYearFilter');
                if (yearFilter){
                    yearFilter.addEventListener('change', function(){
                        const y = String(this.value).trim();
                        document.querySelectorAll('#eventsGrid .event-item').forEach(it=>{
                            const dy = String(it.getAttribute('data-year')||'').trim();
                            // if filter empty, show initial visible items and keep extras hidden
                            if (!y) {
                                if (it.classList.contains('extra-event')) it.style.display = 'none'; else it.style.display = '';
                                return;
                            }
                            // show only matching year items
                            it.style.display = (dy === y) ? '' : 'none';
                        });
                        // if filter hides all extras, make load more button hidden
                        const anyVisible = Array.from(document.querySelectorAll('#eventsGrid .event-item')).some(e=> e.style.display !== 'none');
                        const btn = document.getElementById('loadMoreEvents');
                        if (btn) btn.style.display = anyVisible ? '' : 'none';
                    });
                }

                // Simple join handler
                document.querySelectorAll('.join-event').forEach(b=> b.addEventListener('click', function(){
                    const id = this.getAttribute('data-id');
                    window.location.href = 'login.php?next=view_event.php?id='+encodeURIComponent(id);
                }));
            })();
        </script>

        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content bg-transparent border-0">
                    <div class="login-modal">
                        <button type="button" class="login-modal-close" data-dismiss="modal">&times;</button>
                        <div class="login-logo">
                            <img src="assets/img/logo.png" alt="Alumni Logo" style="height:60px; margin-bottom: 10px;">
                        </div>
                        <div class="login-header">
                            <h3>Alumni Login</h3>
                            <p>Welcome back! Please login to your account.</p>
                        </div>
                        <div id="msg"></div>
                        <form action="" id="login-frm" method="POST">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    </div>  
                                    <input type="text" name="username" id="username" class="form-control login-input" placeholder="Alumni Email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="password" id="password" class="form-control login-input" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="otp-group">
                                    <input type="text" id="otp" name="otp" class="otp-input" maxlength="6" placeholder="OTP" required>
                                    <button type="button" id="send-otp" class="btn btn-outline-light">Send OTP</button>
                                </div>
                                <small id="otp-msg" class="form-text"></small>
                            </div>
                            <button class="btn btn-block login-btn" type="submit">Login</button>
                            <div class="text-center mt-3">
                                <a href="signup.php" id="new_account">Create New Account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Harvard-style Search Modal -->
        <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius:22px;">
            <div class="modal-header" style="border-top-left-radius:22px;border-top-right-radius:22px;background:#fff;">
                <img src="assets/img/logo.png" alt="MOIST Logo" style="height:48px;margin-right:16px;">
                <h4 class="modal-title" id="searchModalLabel" style="font-weight:900;color:#800000;">MOIST Alumni Quick Search</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size:2.2rem;color:#800000;">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background:#f9fbfd;min-height:340px;">
                <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 style="font-weight:900;color:#b71c1c;">ALUMNI SEARCH</h5>
                    <p style="font-size:1.09rem;">Find your fellow alumni by batch year, course, or name.</p>
                    <div style="margin-top:1.2rem;">
                    <form id="alumniSearchForm">
                        <div class="form-group mb-3">
                        <select class="form-control" id="batchYearSelect">
                            <option value="">Select Batch Year</option>
                            <?php 
                            $currentYear = date('Y');
                            for($i = $currentYear; $i >= 1990; $i--) {
                                echo "<option value='$i'>Batch $i</option>";
                            }
                            ?>
                        </select>
                        </div>
                        <div class="form-group mb-3">
                        <select class="form-control" id="courseSelect">
                            <option value="">Select Course</option>
                        </select>
                        </div>
                        <div class="input-group mb-3">
                        <input type="text" class="form-control" id="quickSearchInput" placeholder="Search by name...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" id="quickSearchBtn" style="background:#800000;border:none;">
                            <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                        </div>
                    </form>
                    <div id="searchResults" class="mt-3" style="max-height:300px;overflow-y:auto;"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                    <div class="col-6 col-lg-3 mb-2">
                        <h6 style="font-weight:900;">COMMUNITY</h6>
                        <ul class="list-unstyled mb-0">
                        <li><a href="#">My Profile</a></li>
                        <li><a href="#">Class Notes</a></li>
                        <li><a href="#">Clubs & SIGs</a></li>
                        <li><a href="#">Volunteer</a></li>
                        <li><a href="#">Stories</a></li>
                        <li><a href="#">Careers & Networking</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-3 mb-2">
                        <h6 style="font-weight:900;">GIVING</h6>
                        <ul class="list-unstyled mb-0">
                        <li><a href="#">Give Online</a></li>
                        <li><a href="#">Gift Planning</a></li>
                        <li><a href="#">Stocks & Matching Gifts</a></li>
                        <li><a href="#">Bequests</a></li>
                        <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-3 mb-2">
                        <h6 style="font-weight:900;">PROGRAMS & EVENTS</h6>
                        <ul class="list-unstyled mb-0">
                        <li><a href="#">All Events</a></li>
                        <li><a href="#">Featured Programs</a></li>
                        <li><a href="#">Online Learning</a></li>
                        <li style="font-weight:900;">TRAVEL</li>
                        <li><a href="#">Trips</a></li>
                        <li><a href="#">Travel Talks</a></li>
                        <li><a href="#">Travel Leaders</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-3 mb-2">
                        <h6 style="font-weight:900;">COLLEGE</h6>
                        <ul class="list-unstyled mb-0">
                        <li><a href="#">College Fund</a></li>
                        <li><a href="#">Parent Engagement</a></li>
                        <li><a href="#">Graduate School Fund</a></li>
                        <li><a href="#">College Seniors</a></li>
                        <li><a href="#">Undergraduates</a></li>
                        <li><a href="#">Classes</a></li>
                        <li><a href="#">Reunions</a></li>
                        </ul>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <!-- Contact Section with Modern Design -->
        <section class="contact-section position-relative" style="background:linear-gradient(135deg, #800000 0%, #b71c1c 100%);color:#fff;padding:5rem 0 4rem 0;overflow:hidden;">
            <div class="position-absolute" style="top:0;left:0;width:100%;height:100%;background-image:url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1440 320\'%3E%3Cpath fill=\'%23ffffff10\' d=\'M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,234.7C672,235,768,213,864,202.7C960,192,1056,192,1152,197.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z\'%3E%3C/path%3E%3C/svg%3E');background-position:center bottom;background-repeat:no-repeat;background-size:cover;opacity:0.1;"></div>
            
            <div class="container position-relative">
                <div class="text-center mb-5">
                    <h2 style="font-weight:800;font-size:2.5rem;margin-bottom:1rem;">Get in Touch</h2>
                    <p style="font-size:1.2rem;opacity:0.9;max-width:600px;margin:0 auto;">We'd love to hear from you! Reach out to us for any inquiries or support.</p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <a href="#" id="visitUsBtn" class="contact-card h-100 d-block text-decoration-none" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border-radius:16px;padding:2rem;text-align:center;transition:transform 0.3s ease;color:inherit;outline:0;">
                            <div class="icon-circle mb-4 mx-auto" style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-map-marker-alt fa-2x" aria-hidden="true"></i>
                            </div>
                            <h4 style="font-weight:700;margin-bottom:1rem;">Visit Us</h4>
                            <p style="font-size:1.1rem;opacity:0.9;">Sta. Cruz, Cogon,<br>Balingasag Misamis Oriental</p>
                            <span class="d-block mt-2" style="font-size:0.98rem;color:#ffd700;font-weight:600;">View Location &rarr;</span>
                            <span class="sr-only">Opens location map in modal</span>
                        </a>
                    </div>
            <!-- Google Maps Modal for Visit Us -->
            <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content" style="border-radius:18px;overflow:hidden;">
                        <div class="modal-header" style="background:#800000;color:#fff;border-top-left-radius:18px;border-top-right-radius:18px;">
                            <h5 class="modal-title" id="locationModalLabel"><i class="fa fa-map-marker-alt mr-2"></i>MOIST Location</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;font-size:2rem;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0" style="height:400px;">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15734.96407396413!2d124.761493!3d8.647347!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32ffe20209d4c4c9%3A0xd59407e48f0872e4!2sMisamis%20Oriental%20Institute%20of%20Science%20and%20Technology!5e0!3m2!1sen!2sph!4v1692100000000!5m2!1sen!2sph" width="100%" height="100%" frameborder="0" style="border:0;min-height:400px;" allowfullscreen="" aria-hidden="false" tabindex="0" title="MOIST Location Map"></iframe>
                            <div class="text-center py-2 bg-light">
                                <a href="https://www.google.com/maps/dir//PQXG%2BPRW+Misamis+Oriental+Institute+of+Science+and+Technology,+Balingasag,+9005+Misamis+Oriental,+Philippines/@8.647347,124.761493,11z/data=!4m8!4m7!1m0!1m5!1m1!1s0x32ffe20209d4c4c9:0xd59407e48f0872e4!2m2!1d124.7770832!2d8.7493735?hl=en-US&entry=ttu&g_ep=EgoyMDI1MDgxMy4wIKXMDSoASAFQAw%3D%3D" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-2" style="border-radius:20px;font-weight:600;">Open in Google Maps</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="contact-card h-100" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border-radius:16px;padding:2rem;text-align:center;transition:transform 0.3s ease;">
                            <div class="icon-circle mb-4 mx-auto" style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-phone fa-2x"></i>
                            </div>
                            <h4 style="font-weight:700;margin-bottom:1rem;">Call Us</h4>
                            <p style="font-size:1.1rem;opacity:0.9;">PLDT: (088)-855-2885</p>
                            <a href="tel:088-855-2885" class="btn btn-outline-light mt-3" style="border-radius:50px;font-weight:600;padding:0.5rem 1.5rem;">Call Now</a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="contact-card h-100" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);border-radius:16px;padding:2rem;text-align:center;transition:transform 0.3s ease;">
                            <div class="icon-circle mb-4 mx-auto" style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <i class="fa fa-envelope fa-2x"></i>
                            </div>
                            <h4 style="font-weight:700;margin-bottom:1rem;">Email Us</h4>
                            <p style="font-size:1.1rem;opacity:0.9;">moist@moist.edu.ph</p>
                            <a href="mailto:moist@moist.edu.ph" class="btn btn-outline-light mt-3" style="border-radius:50px;font-weight:600;padding:0.5rem 1.5rem;">Send Email</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bootstrap JS + jQuery -->
        <!-- Enhanced Script Libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/parallax/3.1.0/parallax.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

        <!-- Theme Switcher -->
        <div class="theme-switcher">
            <button class="theme-btn" id="themeSwitcher">
                <i class="fas fa-moon"></i>
            </button>
        </div>

        <!-- Back to Top Button -->
        <button id="backToTop" class="back-to-top">
            <i class="fas fa-arrow-up"></i>
        </button>

        <script>
            // Real-time Statistics Counter with Animation
            (function(){
                function animateCounter(id, target, duration = 2000) {
                    const element = document.getElementById(id);
                    if (!element) return;
                    let start = 0;
                    const increment = target / (duration / 16);
                    const timer = setInterval(() => {
                        start += increment;
                        if (start >= target) {
                            element.textContent = target;
                            clearInterval(timer);
                        } else {
                            element.textContent = Math.floor(start);
                        }
                    }, 16);
                }

                // Fetch real-time stats from server
                function loadStats() {
                    fetch('get_stats.php')
                        .then(r => r.json())
                        .then(data => {
                            animateCounter('alumniCount', data.alumni || 1247, 2000);
                            animateCounter('eventsCount', data.events || 89, 1800);
                            animateCounter('jobsCount', data.jobs || 156, 2200);
                        })
                        .catch(() => {
                            // Fallback to demo numbers
                            animateCounter('alumniCount', 1247, 2000);
                            animateCounter('eventsCount', 89, 1800);
                            animateCounter('jobsCount', 156, 2200);
                        });
                }

                // Live Clock
                function updateClock() {
                    const now = new Date();
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const seconds = now.getSeconds().toString().padStart(2, '0');
                    const clockEl = document.getElementById('liveClock');
                    if (clockEl) clockEl.textContent = `${hours}:${minutes}:${seconds}`;
                }

                // Initialize on load
                window.addEventListener('load', () => {
                    loadStats();
                    updateClock();
                    setInterval(updateClock, 1000);
                    // Refresh stats every 30 seconds
                    setInterval(loadStats, 30000);
                });

                // Stat card hover effects
                document.querySelectorAll('.stat-card').forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px) scale(1.05)';
                        this.style.boxShadow = '0 8px 20px rgba(128,0,0,0.15)';
                    });
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                        this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.06)';
                    });
                });
            })();

            // Lightweight UI script: safe feature checks and small UX helpers
            (function(){
                // AOS
                if (window.AOS) AOS.init({ duration: 800, once: true });

                // Floating Action Button
                const fab = document.querySelector('#floatingActionBtn .main-btn');
                const floatList = document.querySelector('#floatingActionBtn .float-elements');
                if (fab && floatList) fab.addEventListener('click', ()=> floatList.classList.toggle('active'));

                // Theme switcher: persistent, accessible, and updates icon
                const themeBtn = document.getElementById('themeSwitcher');
                function applyTheme(isDark){
                    if (isDark) {
                        document.body.classList.add('dark-theme');
                        document.body.setAttribute('data-theme','dark');
                        if (themeBtn) themeBtn.innerHTML = '<i class="fas fa-sun" aria-hidden="true"></i>';
                        if (themeBtn) themeBtn.setAttribute('aria-label','Switch to light mode');
                    } else {
                        document.body.classList.remove('dark-theme');
                        document.body.removeAttribute('data-theme');
                        if (themeBtn) themeBtn.innerHTML = '<i class="fas fa-moon" aria-hidden="true"></i>';
                        if (themeBtn) themeBtn.setAttribute('aria-label','Switch to dark mode');
                    }
                }
                if (themeBtn) {
                    const stored = localStorage.getItem('darkTheme');
                    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const initDark = stored === 'true' ? true : (stored === 'false' ? false : prefersDark);
                    applyTheme(initDark);
                    themeBtn.addEventListener('click', function(){
                        const nowDark = !document.body.classList.contains('dark-theme');
                        localStorage.setItem('darkTheme', nowDark ? 'true' : 'false');
                        applyTheme(nowDark);
                    });
                }

                // Back to top
                const backToTop = document.getElementById('backToTop');
                if (backToTop){
                    window.addEventListener('scroll', ()=>{
                        if (window.pageYOffset > 300) backToTop.classList.add('visible'); else backToTop.classList.remove('visible');
                    });
                    backToTop.addEventListener('click', ()=> window.scrollTo({ top:0, behavior:'smooth' }));
                }

                // Safe Swiper init
                try{ if (window.Swiper){ new Swiper('.swiper-container',{ slidesPerView:1, spaceBetween:20, loop:true, pagination:{ el:'.swiper-pagination', clickable:true } }); } }catch(e){console.warn(e)}

                // Visit Us button opens modal
                const visitBtn = document.getElementById('visitUsBtn');
                if (visitBtn) {
                    visitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        $('#locationModal').modal('show');
                    });
                }

                // Expose simple helpers used elsewhere
                window.togglePrograms = function(type){
                    const b = document.getElementById('basicPrograms');
                    const h = document.getElementById('higherPrograms');
                    if (!b || !h) return;
                    b.style.display = h.style.display = 'none';
                    const sel = document.getElementById(type + 'Programs');
                    if (sel) { sel.style.display = 'block'; sel.scrollIntoView({behavior:'smooth', block:'start'}); }
                };
            })();

            // Populate course select and handle alumni search
            (function(){
                const courseSelect = document.getElementById('courseSelect');
                const searchForm = document.getElementById('alumniSearchForm');
                const results = document.getElementById('searchResults');

                function safeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]; }); }

                // Fetch courses (if secured endpoint requires login, this will fail silently)
                fetch('get_courses.php').then(r=>{ if(!r.ok) throw new Error('no'); return r.json(); }).then(json=>{
                    if (!Array.isArray(json)) return;
                    json.forEach(c=>{
                        const opt = document.createElement('option');
                        opt.value = c.id || c.course || c.name || '';
                        opt.textContent = c.course || c.name || opt.value;
                        courseSelect.appendChild(opt);
                    });
                }).catch(()=>{
                    // fallback - keep static short list
                    const fallbacks = [ ['BSIT','Bachelor of Science in Information Technology'], ['BSCS','Bachelor of Science in Computer Science'] ];
                    fallbacks.forEach(f=>{ const o=document.createElement('option'); o.value=f[0]; o.textContent=f[1]; courseSelect.appendChild(o); });
                });

                if (searchForm){
                    searchForm.addEventListener('submit', function(e){
                        e.preventDefault();
                        results.innerHTML = '<div class="text-muted">Searching…</div>';
                        const data = new FormData(searchForm);
                        fetch('search_alumni.php', { method: 'POST', body: data })
                            .then(r=>r.text())
                            .then(html=>{ results.innerHTML = html; })
                            .catch(()=>{ results.innerHTML = '<div class="text-danger">Search failed. Please try again.</div>'; });
                    });
                }
            })();
        </script>
        <?php include('footer.php'); ?>
    </body>
    </html>