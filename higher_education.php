<?php
session_start();

// Check if user has agreed to privacy notice
if (!isset($_SESSION['privacy_agreed']) || $_SESSION['privacy_agreed'] !== true) {
    header("Location: privacy_prompt.php");
    exit;
}

// Define college data
$colleges = [
    'education' => [
        'name' => 'College of Teacher Education',
        'icon' => 'fa-chalkboard-teacher',
        'image' => 'assets/img/colleges/education.jpg',
        'description' => 'Shaping the future educators with comprehensive teaching methodologies.',
        'color' => '#1e88e5'
    ],
    'it' => [
        'name' => 'College of Information Technology',
        'icon' => 'fa-laptop-code',
        'image' => 'assets/img/colleges/it.jpg',
        'description' => 'Empowering students with cutting-edge technology skills.',
        'color' => '#00acc1'
    ],
    'criminology' => [
        'name' => 'College of Criminal Justice Education',
        'icon' => 'fa-balance-scale',
        'image' => 'assets/img/colleges/criminology.jpg',
        'description' => 'Preparing future law enforcement professionals.',
        'color' => '#43a047'
    ],
    'hospitality' => [
        'name' => 'College of Hospitality Management',
        'icon' => 'fa-concierge-bell',
        'image' => 'assets/img/colleges/hospitality.jpg',
        'description' => 'Training world-class hospitality professionals.',
        'color' => '#fb8c00'
    ],
    'midwifery' => [
        'name' => 'College of Midwifery',
        'icon' => 'fa-heartbeat',
        'image' => 'assets/img/colleges/midwifery.jpg',
        'description' => 'Developing skilled healthcare professionals.',
        'color' => '#e53935'
    ],
    'tourism' => [
        'name' => 'College of Tourism',
        'icon' => 'fa-plane',
        'image' => 'assets/img/colleges/tourism.jpg',
        'description' => 'Creating tourism professionals for the global industry.',
        'color' => '#8e24aa'
    ],
    'business' => [
        'name' => 'College of Business Administration',
        'icon' => 'fa-chart-line',
        'image' => 'assets/img/colleges/business.jpg',
        'description' => 'Developing future business leaders.',
        'color' => '#3949ab'
    ],
    'office' => [
        'name' => 'BS in Office Administration',
        'icon' => 'fa-tasks',
        'image' => 'assets/img/colleges/office.jpg',
        'description' => 'Training skilled office professionals.',
        'color' => '#546e7a'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Higher Education Programs - MOIST Alumni Portal</title>
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
            padding: 4rem 0;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23f5f7fb" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,234.7C672,235,768,213,864,202.7C960,192,1056,192,1152,197.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center/cover no-repeat;
        }

        .header-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .program-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
            position: relative;
            height: 100%;
        }

        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .program-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .program-card:hover .program-image {
            transform: scale(1.1);
        }
        
        .image-container {
            position: relative;
            overflow: hidden;
            height: 200px;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(128,0,0,0.2), rgba(128,0,0,0.8));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .program-card:hover .image-overlay {
            opacity: 1;
        }

        .program-icon {
            width: 80px;
            height: 80px;
            background: #800000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -40px auto 20px;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(128,0,0,0.2);
            transition: all 0.3s ease;
        }

        .program-card:hover .program-icon {
            transform: rotate(360deg) scale(1.1);
            background: #b71c1c;
        }

        .program-icon i {
            font-size: 30px;
            color: white;
        }

        .program-content {
            padding: 20px;
            text-align: center;
        }

        .program-title {
            color: #800000;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.25rem;
        }

        .program-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .btn-learn-more {
            background: linear-gradient(145deg, #800000, #990000);
            color: white;
            border: none;
            padding: 8px 25px;
            border-radius: 25px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .btn-learn-more:hover {
            background: linear-gradient(145deg, #990000, #800000);
            transform: translateY(-2px);
            color: #ffd700;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 2rem;
            }
            
            .page-header {
                padding: 3rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h1 class="header-title">Higher Education Programs</h1>
                <p class="lead">CHED Accredited Programs at MOIST</p>
            </div>
        </div>
    </header>

    <!-- Programs Section -->
    <section class="programs-section py-5">
        <div class="container">
            <div class="row">
                <!-- College of Teacher Education -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="program-card">
                        <div class="image-container">
                            <img src="assets/img/colleges/education.jpg" alt="College of Teacher Education" class="program-image">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="program-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Teacher Education</h3>
                            <p class="program-description">Shaping the future educators with comprehensive teaching methodologies and modern pedagogical approaches.</p>
                            <a href="#" class="btn btn-learn-more" data-program="education">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- College of Information Technology -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Information Technology</h3>
                            <p class="program-description">Empowering students with cutting-edge technology skills and innovative computing solutions.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- College of Criminal Justice Education -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Criminal Justice Education</h3>
                            <p class="program-description">Preparing future law enforcement professionals with comprehensive criminal justice education.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- College of Hospitality Management -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Hospitality Management</h3>
                            <p class="program-description">Training world-class hospitality professionals for the global service industry.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- College of Midwifery -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Midwifery</h3>
                            <p class="program-description">Developing skilled healthcare professionals specialized in maternal and infant care.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- College of Tourism -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-plane"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Tourism</h3>
                            <p class="program-description">Creating tourism professionals ready to excel in the global travel and hospitality industry.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- College of Business Administration -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="700">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">College of Business Administration</h3>
                            <p class="program-description">Developing future business leaders with strong management and entrepreneurial skills.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>

                <!-- Bachelor of Science in Office Administration -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="800">
                    <div class="program-card">
                        <div class="program-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="program-content">
                            <h3 class="program-title">BS in Office Administration</h3>
                            <p class="program-description">Training skilled office professionals with modern administrative and management expertise.</p>
                            <a href="#" class="btn btn-learn-more">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Home -->
    <div class="text-center pb-5">
        <a href="index.php" class="btn btn-learn-more">
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
