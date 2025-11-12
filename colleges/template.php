<?php
session_start();

// Check if user has agreed to privacy notice
if (!isset($_SESSION['privacy_agreed']) || $_SESSION['privacy_agreed'] !== true) {
    header("Location: ../privacy_prompt.php");
    exit;
}

// College data - Example structure
$colleges = [
    'education' => [
        'name' => 'College of Teacher Education',
        'icon' => 'fa-chalkboard-teacher',
        'description' => 'Shaping the future educators with comprehensive teaching methodologies and modern pedagogical approaches.',
        'programs' => [
            'Bachelor of Elementary Education',
            'Bachelor of Secondary Education',
            'Bachelor of Physical Education'
        ],
        'features' => [
            'Modern Teaching Laboratories',
            'Experienced Faculty Members',
            'Industry Partnerships',
            'Teaching Practicum',
            'Research Opportunities'
        ]
    ],
    // Add other colleges here
];

// Get college from URL parameter
$collegeId = isset($_GET['id']) ? $_GET['id'] : '';
$college = isset($colleges[$collegeId]) ? $colleges[$collegeId] : null;

if (!$college) {
    header("Location: ../higher_education.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $college['name']; ?> - MOIST Alumni Portal</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Add your custom styles here */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

        .college-header {
            background: linear-gradient(135deg, #800000 0%, #b71c1c 100%);
            color: white;
            padding: 6rem 0;
            position: relative;
            overflow: hidden;
        }

        .college-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/img/pattern.png');
            opacity: 0.1;
        }

        .college-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 3rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .program-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 30px;
        }

        .program-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .feature-list li i {
            color: #800000;
            margin-right: 1rem;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-list li:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .feature-list li:hover i {
            transform: scale(1.2);
        }

        .btn-back {
            background: linear-gradient(145deg, #800000, #990000);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-back:hover {
            background: linear-gradient(145deg, #990000, #800000);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128,0,0,0.3);
            color: #ffd700;
            text-decoration: none;
        }

        .college-icon {
            font-size: 4rem;
            color: #ffd700;
            margin-bottom: 2rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .college-title {
                font-size: 2rem;
            }
            
            .college-header {
                padding: 4rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- College Header -->
    <header class="college-header">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <i class="fas <?php echo $college['icon']; ?> college-icon"></i>
                <h1 class="college-title"><?php echo $college['name']; ?></h1>
                <p class="lead"><?php echo $college['description']; ?></p>
            </div>
        </div>
    </header>

    <!-- College Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Programs -->
                <div class="col-lg-6" data-aos="fade-up">
                    <h2 class="mb-4">Our Programs</h2>
                    <div class="program-list">
                        <?php foreach ($college['programs'] as $program): ?>
                        <div class="program-card p-4">
                            <h4><?php echo $program; ?></h4>
                            <p>Duration: 4 Years</p>
                            <a href="#" class="btn btn-sm btn-outline-danger">Learn More</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Features -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="mb-4">Key Features</h2>
                    <ul class="feature-list">
                        <?php foreach ($college['features'] as $feature): ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <?php echo $feature; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Back Button -->
    <div class="text-center pb-5">
        <a href="../higher_education.php" class="btn-back">
            <i class="fas fa-arrow-left mr-2"></i>Back to Programs
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
