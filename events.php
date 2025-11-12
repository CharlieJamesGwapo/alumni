<?php
session_start();
require_once __DIR__ . '/admin/db_connect.php';

// Fetch events ordered by upcoming first
$sql = "SELECT *, 
        CASE WHEN schedule >= CURDATE() THEN 1 ELSE 0 END as is_upcoming 
        FROM events 
        ORDER BY is_upcoming DESC, schedule DESC";
$result = $conn->query($sql);
$events = [];
while($row = $result->fetch_assoc()) {
    $events[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Events - MOIST</title>
    <?php include('header.php'); ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Maroon and White Color Scheme */
        :root {
            --maroon-primary: #800020;
            --maroon-dark: #5c0016;
            --maroon-light: #a6002a;
            --white: #ffffff;
            --black: #000000;
            --gray-light: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--black);
            background-color: var(--gray-light);
        }
        /* Navigation Bar */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--maroon-primary) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--maroon-primary) !important;
        }

        /* Hero Section */
        .events-hero {
            background: linear-gradient(135deg, var(--maroon-primary) 0%, var(--maroon-dark) 100%);
            padding: 100px 0 60px;
            position: relative;
            overflow: hidden;
        }
        
        .events-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)" /></svg>');
            opacity: 0.3;
        }
        
        .events-hero .container {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: white;
            text-shadow: 0 2px 15px rgba(0,0,0,0.3);
            animation: fadeInDown 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.95);
            max-width: 700px;
            margin: 0 auto 2rem;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Event Cards */
        .event-card {
            border: none;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(128,0,32,0.2);
        }
        
        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, var(--maroon-primary), var(--maroon-dark));
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .event-card:hover::before {
            opacity: 1;
        }

        .event-image-wrapper {
            height: 250px;
            overflow: hidden;
            position: relative;
            background: var(--gray-light);
        }
        
        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .event-card:hover .event-image {
            transform: scale(1.1);
        }
        
        .event-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.3));
        }

        .event-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }

        .event-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--black);
            line-height: 1.4;
            min-height: 60px;
        }
        
        .event-description {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .event-date {
            background: linear-gradient(135deg, var(--maroon-primary), var(--maroon-dark));
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            width: fit-content;
        }

        .event-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            z-index: 10;
            backdrop-filter: blur(10px);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .upcoming-badge {
            background: rgba(255, 215, 0, 0.95);
            color: var(--maroon-primary);
            box-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
        }

        .past-badge {
            background: rgba(108, 117, 125, 0.95);
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Filter Section */
        .event-filter {
            margin: 3rem 0;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .filter-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--maroon-primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .filter-btn {
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin: 0.5rem;
            border: 2px solid var(--maroon-primary);
            background: white;
            color: var(--maroon-primary);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .filter-btn.active, .filter-btn:hover {
            background: var(--maroon-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128,0,32,0.3);
        }
        
        .btn-learn-more {
            background: linear-gradient(135deg, var(--maroon-primary), var(--maroon-dark));
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-learn-more:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(128,0,32,0.3);
            color: white;
            text-decoration: none;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--maroon-primary);
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            color: var(--maroon-primary);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: #6c757d;
        }
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
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
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .event-item {
            animation: fadeIn 0.6s ease forwards;
            opacity: 0;
        }
        
        .event-item:nth-child(1) { animation-delay: 0.1s; }
        .event-item:nth-child(2) { animation-delay: 0.2s; }
        .event-item:nth-child(3) { animation-delay: 0.3s; }
        .event-item:nth-child(4) { animation-delay: 0.4s; }
        .event-item:nth-child(5) { animation-delay: 0.5s; }
        .event-item:nth-child(6) { animation-delay: 0.6s; }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-stats {
                gap: 1.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .filter-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
                margin: 0.3rem;
            }
            
            .event-image-wrapper {
                height: 200px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .event-title {
                font-size: 1.1rem;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap mr-2"></i>MOIST Alumni
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link active" href="events.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="directory.php">Directory</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="events-hero text-white text-center">
        <div class="container">
            <h1 class="hero-title">Alumni Events</h1>
            <p class="hero-subtitle">Stay connected with your alma mater through our exciting events and reunions</p>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count($events); ?></span>
                    <span class="stat-label">Total Events</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count(array_filter($events, function($e) { return strtotime($e['schedule']) >= strtotime('today'); })); ?></span>
                    <span class="stat-label">Upcoming</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo count(array_filter($events, function($e) { return strtotime($e['schedule']) < strtotime('today'); })); ?></span>
                    <span class="stat-label">Past Events</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Section -->
    <div class="container py-5">
        <!-- Filter Buttons -->
        <div class="event-filter">
            <h2 class="filter-title"><i class="fas fa-filter mr-2"></i>Filter Events</h2>
            <div class="text-center">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-th mr-2"></i>All Events
                </button>
                <button class="filter-btn" data-filter="upcoming">
                    <i class="fas fa-calendar-plus mr-2"></i>Upcoming Events
                </button>
                <button class="filter-btn" data-filter="past">
                    <i class="fas fa-calendar-check mr-2"></i>Past Events
                </button>
            </div>
        </div>

        <div class="row" id="events-container">
            <?php if(empty($events)): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No Events Found</h3>
                        <p>There are currently no events scheduled. Check back later!</p>
                    </div>
                </div>
            <?php endif; ?>
            <?php foreach($events as $event): 
                $isUpcoming = strtotime($event['schedule']) >= strtotime('today');
                $eventImage = !empty($event['banner']) ? 'uploads/'.htmlspecialchars($event['banner']) : 'assets/img/default-event.jpg';
            ?>
            <div class="col-lg-4 col-md-6 mb-4 event-item <?php echo $isUpcoming ? 'upcoming' : 'past'; ?>">
                <div class="event-card">
                    <div class="event-image-wrapper">
                        <img src="<?php echo $eventImage; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image">
                        <div class="event-image-overlay"></div>
                        <?php if($isUpcoming): ?>
                            <span class="event-badge upcoming-badge">
                                <i class="fas fa-star"></i> Upcoming
                            </span>
                        <?php else: ?>
                            <span class="event-badge past-badge">
                                <i class="fas fa-check"></i> Completed
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="event-content">
                        <h4 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h4>
                        <div class="event-date">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo date('F d, Y', strtotime($event['schedule'])); ?>
                        </div>
                        <p class="event-description"><?php echo substr(strip_tags($event['content']), 0, 120); ?>...</p>
                        <a href="view_event.php?id=<?php echo $event['id']; ?>" class="btn-learn-more">
                            <i class="fas fa-arrow-right mr-2"></i>Learn More
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Filter functionality with smooth animations
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Update active button
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filterValue = button.getAttribute('data-filter');
                const events = document.querySelectorAll('.event-item');
                let visibleCount = 0;

                events.forEach((event, index) => {
                    // Reset animation
                    event.style.animation = 'none';

                    setTimeout(() => {
                        if (filterValue === 'all') {
                            event.style.display = 'block';
                            event.style.animation = `fadeIn 0.6s ease forwards ${index * 0.1}s`;
                            visibleCount++;
                        } else if (filterValue === 'upcoming') {
                            if (event.classList.contains('upcoming')) {
                                event.style.display = 'block';
                                event.style.animation = `fadeIn 0.6s ease forwards ${visibleCount * 0.1}s`;
                                visibleCount++;
                            } else {
                                event.style.display = 'none';
                            }
                        } else if (filterValue === 'past') {
                            if (event.classList.contains('past')) {
                                event.style.display = 'block';
                                event.style.animation = `fadeIn 0.6s ease forwards ${visibleCount * 0.1}s`;
                                visibleCount++;
                            } else {
                                event.style.display = 'none';
                            }
                        }
                    }, 10);
                });

                // Show empty state if no events match filter
                setTimeout(() => {
                    const container = document.getElementById('events-container');
                    const emptyState = container.querySelector('.empty-state');
                    
                    if (visibleCount === 0 && !emptyState) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.className = 'col-12';
                        emptyDiv.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>No Events Found</h3>
                                <p>There are no ${filterValue === 'upcoming' ? 'upcoming' : 'past'} events at this time.</p>
                            </div>
                        `;
                        container.appendChild(emptyDiv);
                    } else if (visibleCount > 0 && emptyState) {
                        emptyState.parentElement.remove();
                    }
                }, 100);
            });
        });

        // Smooth scroll to events section when filter is clicked
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('events-container').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'nearest'
                });
            });
        });

        // Trigger click on "all" filter on page load to show all events
        window.addEventListener('load', () => {
            document.querySelector('.filter-btn[data-filter="all"]').click();
        });
    </script>
</body>
</html>