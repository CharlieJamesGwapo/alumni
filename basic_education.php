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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Education Programs - MOIST Alumni Portal</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

        .page-header {
            background: linear-gradient(135deg, #800000 0%, #b71c1c 100%);
            color: white;
            padding: 6rem 0 8rem;
            margin-bottom: -4rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/img/pattern.png');
            opacity: 0.1;
        }

        .header-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .education-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .education-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(128,0,0,0.15);
        }

        .education-header {
            background: linear-gradient(45deg, #800000, #b71c1c);
            color: white;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .education-header::after {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
            top: -50%;
            left: -50%;
            transform: rotate(30deg);
            opacity: 0;
            transition: all 0.5s ease;
        }

        .education-card:hover .education-header::after {
            opacity: 1;
            transform: rotate(0deg);
        }

        .education-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .education-card:hover .education-icon {
            transform: rotate(360deg) scale(1.1);
        }

        .education-icon i {
            font-size: 35px;
            color: #800000;
        }

        .education-title {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin: 0;
        }

        .education-content {
            padding: 2rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        .feature-list li i {
            color: #800000;
            margin-right: 1rem;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .feature-list li:hover {
            transform: translateX(10px);
            color: #800000;
        }

        .feature-list li:hover i {
            transform: scale(1.2);
        }

        .btn-learn-more {
            background: linear-gradient(145deg, #800000, #990000);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-learn-more:hover {
            background: linear-gradient(145deg, #990000, #800000);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128,0,0,0.3);
            color: #ffd700;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 2.5rem;
            }
            
            .page-header {
                padding: 4rem 0 6rem;
            }

            .education-header {
                padding: 1.5rem;
            }

            .education-title {
                font-size: 1.25rem;
            }
        }

        .wave-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23f5f7fb" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,234.7C672,235,768,213,864,202.7C960,192,1056,192,1152,197.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center/cover no-repeat;
        }
    </style>
</head>
<body>
    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h1 class="header-title">Basic Education Programs</h1>
                <p class="lead">Building Strong Foundations for Future Success</p>
            </div>
        </div>
        <div class="wave-bottom"></div>
    </header>

    <!-- Education Programs Section -->
    <section class="programs-section py-5">
        <div class="container">
            <div class="row">
                <!-- Grade School -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="education-card">
                        <div class="education-header">
                            <div class="education-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <h3 class="education-title">Grade School</h3>
                        </div>
                        <div class="education-content">
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i> Strong Academic Foundation</li>
                                <li><i class="fas fa-book-reader"></i> Comprehensive Curriculum</li>
                                <li><i class="fas fa-palette"></i> Arts and Music Programs</li>
                                <li><i class="fas fa-running"></i> Physical Education</li>
                                <li><i class="fas fa-users"></i> Character Development</li>
                            </ul>
                            <div class="text-center mt-4">
                                <a href="#" class="btn-learn-more">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Junior High School -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="education-card">
                        <div class="education-header">
                            <div class="education-icon">
                                <i class="fas fa-book-reader"></i>
                            </div>
                            <h3 class="education-title">Junior High School</h3>
                        </div>
                        <div class="education-content">
                            <ul class="feature-list">
                                <li><i class="fas fa-microscope"></i> Advanced Sciences</li>
                                <li><i class="fas fa-calculator"></i> Mathematics Excellence</li>
                                <li><i class="fas fa-language"></i> Language Arts</li>
                                <li><i class="fas fa-globe-americas"></i> Social Studies</li>
                                <li><i class="fas fa-laptop-code"></i> Technology Integration</li>
                            </ul>
                            <div class="text-center mt-4">
                                <a href="#" class="btn-learn-more">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Senior High School -->
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="education-card">
                        <div class="education-header">
                            <div class="education-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h3 class="education-title">Senior High School</h3>
                        </div>
                        <div class="education-content">
                            <ul class="feature-list">
                                <li><i class="fas fa-stream"></i> Academic Track</li>
                                <li><i class="fas fa-tools"></i> Technical-Vocational Track</li>
                                <li><i class="fas fa-chart-line"></i> Business Track</li>
                                <li><i class="fas fa-palette"></i> Arts and Design Track</li>
                                <li><i class="fas fa-medal"></i> Sports Track</li>
                            </ul>
                            <div class="text-center mt-4">
                                <a href="#" class="btn-learn-more">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Home -->
    <div class="text-center pb-5">
        <a href="index.php" class="btn-learn-more">
            <i class="fas fa-arrow-left mr-2"></i>Back to Home
        </a>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>
</html>
