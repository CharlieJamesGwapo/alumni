<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <?php include 'header.php'; ?>
    <!-- Bootstrap (core) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RQ+5Q5Ck5Qb1z5i6yZg1q7G1fQp5L5eZq5Y5K5Q5" crossorigin="anonymous">
    <!-- Disable Tailwind preflight to avoid resetting Bootstrap styles -->
    <script>window.tailwind = { config: { corePlugins: { preflight: false } } }</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      /* Maroon and White Color Scheme */
      :root {
        --maroon-primary: #800020;
        --maroon-dark: #5c0016;
        --maroon-light: #a6002a;
        --white: #ffffff;
        --black: #000000;
        --gray-light: #f8f9fa;
        --gray-medium: #6c757d;
      }
      
      /* Small hybrid adjustments to make Bootstrap components work nicely with Tailwind utilities */
      .hero-gradient{ background: linear-gradient(135deg, #800020 0%, #5c0016 100%); }
      .timeline-item { position: relative; padding-left: 3rem; }
      .timeline-item .badge { position: absolute; left: -2.1rem; top: 0; }
      /* Ensure footer from footer.php sits nicely */
      main { padding-top: 2.5rem; padding-bottom: 2.5rem; }
      
      /* Text Colors */
      body {
        color: #000000 !important;
      }
      
      .text-indigo-700 {
        color: #800020 !important;
      }
      
      .text-primary {
        color: #800020 !important;
      }
      
      .bg-primary {
        background-color: #800020 !important;
      }
      
      .btn-primary {
        background-color: #800020 !important;
        border-color: #800020 !important;
      }
      
      .btn-primary:hover {
        background-color: #5c0016 !important;
        border-color: #5c0016 !important;
      }
      
      .btn-outline-primary {
        color: #800020 !important;
        border-color: #800020 !important;
      }
      
      .btn-outline-primary:hover {
        background-color: #800020 !important;
        border-color: #800020 !important;
        color: white !important;
      }
      
      .badge-primary {
        background-color: #800020 !important;
      }
      
      .border-primary {
        border-color: #800020 !important;
      }
      
      /* Team Section Styles */
      .team-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        overflow: hidden;
      }
      
      .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(128, 0, 32, 0.25) !important;
      }
      
      .team-img-wrapper {
        position: relative;
        height: 320px;
        overflow: hidden;
      }
      
      .team-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
      }
      
      .team-card:hover .team-img {
        transform: scale(1.1);
      }
      
      .team-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(128, 0, 32, 0.95), rgba(92, 0, 22, 0.95));
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s ease;
      }
      
      .team-card:hover .team-overlay {
        opacity: 1;
      }
      
      .team-social {
        display: flex;
        gap: 15px;
      }
      
      .social-icon {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        text-decoration: none;
        transition: all 0.3s ease;
        transform: translateY(20px);
        opacity: 0;
      }
      
      .team-card:hover .social-icon {
        transform: translateY(0);
        opacity: 1;
      }
      
      .team-card:hover .social-icon:nth-child(1) {
        transition-delay: 0.1s;
      }
      
      .team-card:hover .social-icon:nth-child(2) {
        transition-delay: 0.2s;
      }
      
      .team-card:hover .social-icon:nth-child(3) {
        transition-delay: 0.3s;
      }
      
      .social-icon:hover {
        background: white;
        color: #800020;
        transform: translateY(-5px) scale(1.1);
      }
      
      .text-indigo-700 {
        color: #4338ca;
      }
      
      /* Responsive adjustments */
      @media (max-width: 768px) {
        .team-img-wrapper {
          height: 280px;
        }
        
        .team-card {
          margin-bottom: 20px;
        }
      }
      
      @media (max-width: 576px) {
        .team-img-wrapper {
          height: 250px;
        }
      }
      
      /* Animation for team section on load */
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
      
      .team-card {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
      }
      
      .team-card:nth-child(1) { animation-delay: 0.1s; }
      .team-card:nth-child(2) { animation-delay: 0.2s; }
      .team-card:nth-child(3) { animation-delay: 0.3s; }
      .team-card:nth-child(4) { animation-delay: 0.4s; }
      .team-card:nth-child(5) { animation-delay: 0.5s; }
      .team-card:nth-child(6) { animation-delay: 0.6s; }
      
      /* Stats Counter Section */
      .stats-section {
        background: linear-gradient(135deg, #800020 0%, #5c0016 100%);
        position: relative;
        overflow: hidden;
      }
      
      .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)" /></svg>');
        opacity: 0.3;
      }
      
      .stat-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 2rem;
        transition: all 0.3s ease;
      }
      
      .stat-card:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
      }
      
      .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        line-height: 1;
      }
      
      .stat-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        margin-top: 0.5rem;
      }
      
      /* Value Cards */
      .value-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
      }
      
      .value-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #800020, #5c0016);
      }
      
      .value-card:hover {
        transform: translateX(10px);
        box-shadow: 0 10px 30px rgba(128, 0, 32, 0.25) !important;
      }
      
      .value-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #800020, #5c0016);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-bottom: 1rem;
      }
      
      /* Timeline Enhancements */
      .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 30px;
        bottom: -30px;
        width: 2px;
        background: linear-gradient(180deg, #800020, transparent);
      }
      
      .timeline-item:last-child::before {
        display: none;
      }
      
      /* Section Dividers */
      .section-divider {
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #800020, #5c0016);
        margin: 1rem auto 2rem;
      }
      
      /* Parallax Effect */
      .parallax-section {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
      }
      
      /* Feature Cards */
      .feature-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        height: 100%;
      }
      
      .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
        border-color: #800020;
      }
      
      .feature-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #fff5f7, #ffe8ec);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: #800020;
        font-size: 28px;
      }
      
      /* Animated Background */
      @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
      }
      
      .floating {
        animation: float 6s ease-in-out infinite;
      }
      
      /* Quote Section */
      .quote-section {
        background: linear-gradient(135deg, #fff5f7 0%, #ffe8ec 100%);
        border-left: 5px solid #800020;
        padding: 2rem;
        border-radius: 10px;
        font-style: italic;
        position: relative;
      }
      
      .quote-section::before {
        content: '"';
        font-size: 4rem;
        color: #800020;
        opacity: 0.2;
        position: absolute;
        top: -10px;
        left: 20px;
        font-family: Georgia, serif;
      }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans leading-relaxed">

  <!-- Top navbar (Bootstrap) -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <?php if(isset($_SESSION['system']['name'])): ?>
          <span class="font-weight-bold mr-2"><?php echo htmlspecialchars($_SESSION['system']['name']); ?></span>
        <?php else: ?>
          MOIST Alumni
        <?php endif; ?>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item active"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="directory.php">Directory</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section with Enhanced Design -->
  <header class="hero-gradient text-white py-5 position-relative overflow-hidden">
    <div class="position-absolute" style="top: 20%; right: 10%; opacity: 0.1; font-size: 300px;">
      <i class="fas fa-graduation-cap"></i>
    </div>
    <div class="container position-relative" style="z-index: 1;">
      <div class="row align-items-center py-4">
        <div class="col-md-8">
          <div class="mb-3">
            <span class="badge badge-light px-3 py-2">
              <i class="fas fa-star text-warning"></i> Excellence Since 2002
            </span>
          </div>
          <h1 class="display-3 font-weight-bold mb-3" style="line-height: 1.2;">Misamis Oriental Institute of Science and Technology</h1>
          <p class="lead mb-4" style="font-size: 1.3rem;">A career-oriented institution that provides holistic education for personal and professional growth.</p>
          <div class="d-flex flex-wrap gap-3">
            <a href="register.php" class="btn btn-light btn-lg px-4 py-3 mr-3 mb-2">
              <i class="fas fa-user-plus mr-2"></i>Join the Alumni
            </a>
            <a href="#vision" class="btn btn-outline-light btn-lg px-4 py-3 mb-2">
              <i class="fas fa-arrow-down mr-2"></i>Learn More
            </a>
          </div>
        </div>
        <div class="col-md-4 text-center mt-4 mt-md-0">
          <div class="floating">
            <div class="bg-white rounded-circle p-4 shadow-lg d-inline-block">
              <i class="fas fa-university" style="font-size: 80px; color: #800020;"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Statistics Section -->
  <section class="stats-section py-5 position-relative">
    <div class="container position-relative" style="z-index: 1;">
      <div class="row text-center text-white">
        <div class="col-md-3 col-6 mb-4 mb-md-0">
          <div class="stat-card">
            <div class="stat-number" data-count="23">0</div>
            <div class="stat-label">Years of Excellence</div>
          </div>
        </div>
        <div class="col-md-3 col-6 mb-4 mb-md-0">
          <div class="stat-card">
            <div class="stat-number" data-count="1000">0</div>
            <div class="stat-label">+ Students Enrolled</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-count="15">0</div>
            <div class="stat-label">+ Programs Offered</div>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="stat-card">
            <div class="stat-number" data-count="100">0</div>
            <div class="stat-label">% Commitment</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <main class="container">
    <div class="row">
      <div class="col-lg-8">
        <!-- Vision & Mission Cards -->
        <section id="vision" class="mb-5">
          <div class="text-center mb-4">
            <h2 class="display-4 font-weight-bold" style="color: #800020;">Our Vision & Mission</h2>
            <div class="section-divider"></div>
          </div>
          
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="feature-card">
                <div class="feature-icon">
                  <i class="fas fa-eye"></i>
                </div>
                <h3 class="h4 font-weight-bold text-center mb-3" style="color: #800020;">Vision</h3>
                <p class="text-center" style="color: #000000;">A career-oriented institution that provides holistic education to learners leading to personal and professional growth responsive to the needs of the community for sustainable development.</p>
              </div>
            </div>
            
            <div class="col-md-6 mb-4">
              <div class="feature-card">
                <div class="feature-icon">
                  <i class="fas fa-bullseye"></i>
                </div>
                <h3 class="h4 font-weight-bold text-center mb-3" style="color: #800020;">Mission</h3>
                <p class="text-center mb-3" style="color: #000000;">Committed to provide accessible quality education geared towards total development:</p>
                <ul class="list-unstyled text-left">
                  <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color: #800020;"></i>Create a learner-centered environment</li>
                  <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color: #800020;"></i>Produce graduates with MOISTian values</li>
                  <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color: #800020;"></i>Develop catalysts for nation-building</li>
                  <li class="mb-2"><i class="fas fa-check-circle mr-2" style="color: #800020;"></i>Strengthen community linkages</li>
                </ul>
              </div>
            </div>
          </div>
        </section>

        <!-- Core Values -->
        <section id="core-values" class="mb-5">
          <div class="text-center mb-4">
            <h2 class="display-4 font-weight-bold" style="color: #800020;">MOISTian Core Values</h2>
            <div class="section-divider"></div>
            <p class="lead" style="color: #000000;">The principles that guide our institution</p>
          </div>
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="value-card card h-100 shadow-sm">
                <div class="card-body pl-4">
                  <div class="value-icon">
                    <i class="fas fa-brain"></i>
                  </div>
                  <h5 class="card-title font-weight-bold" style="color: #800020;">M - Mindfulness</h5>
                  <p class="card-text" style="color: #000000;">Exercised sensitivity to the needs of the community, demonstrating awareness and compassion in all our actions.</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="value-card card h-100 shadow-sm">
                <div class="card-body pl-4">
                  <div class="value-icon">
                    <i class="fas fa-smile-beam"></i>
                  </div>
                  <h5 class="card-title font-weight-bold" style="color: #800020;">O - Optimism</h5>
                  <p class="card-text" style="color: #000000;">Embodied a positive perspective in personal, emotional, spiritual, and social interactions.</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="value-card card h-100 shadow-sm">
                <div class="card-body pl-4">
                  <div class="value-icon">
                    <i class="fas fa-lightbulb"></i>
                  </div>
                  <h5 class="card-title font-weight-bold" style="color: #800020;">I - Innovativeness</h5>
                  <p class="card-text" style="color: #000000;">Internalized importance of research and displays analytical, critical, and creative thinking.</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-4">
              <div class="value-card card h-100 shadow-sm">
                <div class="card-body pl-4">
                  <div class="value-icon">
                    <i class="fas fa-hands-helping"></i>
                  </div>
                  <h5 class="card-title font-weight-bold" style="color: #800020;">S - Service</h5>
                  <p class="card-text" style="color: #000000;">Practiced servant-leadership and showed concern for others, putting community needs first.</p>
                </div>
              </div>
            </div>
            <div class="col-12 mb-4">
              <div class="value-card card h-100 shadow-sm">
                <div class="card-body pl-4">
                  <div class="value-icon">
                    <i class="fas fa-shield-alt"></i>
                  </div>
                  <h5 class="card-title font-weight-bold" style="color: #800020;">T - Trustworthiness</h5>
                  <p class="card-text" style="color: #000000;">Demonstrated reliability, dependability, and responsibility in all endeavors and commitments.</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Academic Growth Timeline -->
        <section id="academic-growth" class="mb-5">
          <div class="text-center mb-4">
            <h2 class="display-4 font-weight-bold" style="color: #800020;">Our Journey of Growth</h2>
            <div class="section-divider"></div>
            <p class="lead" style="color: #000000;">From humble beginnings to academic excellence</p>
          </div>
          <div class="timeline pl-3">
            <?php
            $timeline = [
              ['date'=>'2002','title'=>'Founding Year','text'=>'Misamis Oriental Institute of Science and Technology was founded.'],
              ['date'=>'2002-2003','title'=>'Initial Courses Offered','text'=>'Nursery, Kindergarten I & II, and three TESDA Technical courses: Electronics Technology, Electrical Technology, and Food Preparation and Servicing Technology were established.'],
              ['date'=>'2003-2004','title'=>'Elementary & Secondary Expansion','text'=>'Grades I, II, and first year high school were offered.'],
              ['date'=>'2004-2005','title'=>'More Grades Opened','text'=>'Grades 3 and 4 and second and third year high school were opened.'],
              ['date'=>'2005-2006','title'=>'Final Grade Levels and Additional TESDA Courses','text'=>'Grades 5 and 6 and fourth year high school were opened. Added Computer Hardware Servicing NC II and Programming NC IV.'],
              ['date'=>'2010','title'=>'CHED Government Recognition','text'=>'Granted recognition for BEED, BSED (English), and BSBA (Marketing Management).'],
              ['date'=>'2011','title'=>'Criminology Program','text'=>'Government Recognition granted for Bachelor of Science in Criminology.'],
              ['date'=>'2012','title'=>'Diploma in Midwifery','text'=>'CHED granted Government Recognition for Diploma in Midwifery.'],
              ['date'=>'2014','title'=>'Hotel & Restaurant Management','text'=>'Granted Government Recognition for Bachelor of Science in Hotel & Restaurant Management.'],
              ['date'=>'2017','title'=>'Information Technology','text'=>'Government Recognition granted for Bachelor of Science in Information Technology.'],
              ['date'=>'2021-2022','title'=>'New Majors & Programs','text'=>'CHED approved additional majors in BSBA and BSED; TESDA approved Diploma in Hotel Services Technology and new assessment center qualifications. CHED approved BSTM and BSOA programs.']
            ];
            foreach($timeline as $item): ?>
              <div class="mb-4 timeline-item">
                <span class="badge badge-pill badge-primary badge-lg text-white p-2 font-weight-bold" style="background-color: #800020 !important;"><?php echo $item['date']; ?></span>
                <h5 class="mt-2 font-weight-bold" style="color: #800020;"><?php echo $item['title']; ?></h5>
                <p style="color: #000000;"><?php echo $item['text']; ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <!-- History -->
        <section id="history" class="mb-5">
          <div class="text-center mb-4">
            <h2 class="display-4 font-weight-bold" style="color: #800020;">Our Story</h2>
            <div class="section-divider"></div>
            <p class="lead" style="color: #000000;">A legacy of educational excellence and community service</p>
          </div>
          
          <div class="quote-section mb-4">
            <p class="mb-0 text-dark font-weight-normal">It started with a simple conversation over coca cola at the Valmores Garden and Restaurant—a vision to address the employment challenges faced by graduates in Balingasag.</p>
          </div>
          
          <div class="prose text-muted">
            <h5 class="font-weight-bold mb-3" style="color: #800020;"><i class="fas fa-users mr-2"></i>The Founding Vision</h5>
            <p>The Valmores TRIUMVIRATE—Enrico, Reynaldo, and Atty. Romulo R. Valmores—dared to push a worthy project for the people of Balingasag. Their vision was clear: create an institution that would bridge the gap between education and employment.</p>
            
            <h5 class="font-weight-bold mb-3 mt-4" style="color: #800020;"><i class="fas fa-seedling mr-2"></i>Humble Beginnings</h5>
            <p>The first technical courses under TESDA were Electronics Technology, Electrical Technology, and Food Preparation and Servicing Technology. The Nursery and Kindergarten I-II comprised the pre-school under the Department of Education, staffed by graduates from renowned universities and colleges.</p>
            
            <h5 class="font-weight-bold mb-3 mt-4" style="color: #800020;"><i class="fas fa-chart-line mr-2"></i>Steady Growth</h5>
            <p>From barely a hundred enrollees, the school continued its step towards completing the offering of elementary and secondary levels. Additional technical courses like Computer Programming and Computer Technician were introduced, marking the institution's commitment to technological advancement.</p>
            
            <h5 class="font-weight-bold mb-3 mt-4" style="color: #800020;"><i class="fas fa-graduation-cap mr-2"></i>Current Programs</h5>
            <p class="mb-3">Presently, MOIST has grown steadily to serve over a thousand students. Our CHED-accredited programs include:</p>
            <div class="row">
              <div class="col-md-6">
                <ul class="list-unstyled">
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>Bachelor of Elementary Education</li>
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>Bachelor of Secondary Education</li>
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>BS in Business Administration</li>
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>BS in Criminology</li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul class="list-unstyled">
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>BS in Hospitality Management</li>
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>BS in Tourism Management</li>
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>BS in Information Technology</li>
                  <li class="mb-2"><i class="fas fa-check mr-2" style="color: #800020;"></i>BS in Midwifery</li>
                </ul>
              </div>
            </div>
            
            <div class="alert mt-4 border-0" style="background: linear-gradient(135deg, #fff5f7, #ffe8ec);">
              <h6 class="font-weight-bold mb-2" style="color: #800020;"><i class="fas fa-info-circle mr-2"></i>Looking Ahead</h6>
              <p class="mb-0 small">MOIST is awaiting CHED's approval for additional courses including Marine Transportation, Office Administration, and Nursing—continuing our commitment to expand educational opportunities.</p>
            </div>
            
            <p class="mt-4 font-weight-bold" style="color: #800020;">MOIST is strongly committed to becoming a better school of learning for technical and formal education, especially for low-income earners with the potential to become professionals and technically skilled achievers.</p>
          </div>
        </section>
      </div>


      <aside class="col-lg-4">
        <!-- Quick Facts Card -->
        <div class="card mb-4 shadow-sm border-0" style="border-left: 4px solid #800020 !important;">
          <div class="card-body">
            <h5 class="card-title font-weight-bold mb-3" style="color: #800020;">
              <i class="fas fa-info-circle mr-2"></i>Quick Facts
            </h5>
            <div class="mb-3">
              <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #800020;">
                  <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                  <small class="d-block" style="color: #6c757d;">Established</small>
                  <strong style="color: #000000;">2002</strong>
                </div>
              </div>
              <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #800020;">
                  <i class="fas fa-map-marker-alt text-white"></i>
                </div>
                <div>
                  <small class="d-block" style="color: #6c757d;">Location</small>
                  <strong style="color: #000000;">Balingasag</strong>
                </div>
              </div>
              <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background-color: #800020;">
                  <i class="fas fa-certificate text-white"></i>
                </div>
                <div>
                  <small class="d-block" style="color: #6c757d;">Accreditation</small>
                  <strong style="color: #000000;">CHED & TESDA</strong>
                </div>
              </div>
            </div>
            <a href="register.php" class="btn btn-primary btn-block">
              <i class="fas fa-user-plus mr-2"></i>Join Alumni Network
            </a>
          </div>
        </div>
        
        <!-- Contact Card -->
        <div class="card mb-4 shadow-sm border-0 d-none d-lg-block" style="background: linear-gradient(135deg, #800020 0%, #5c0016 100%);">
          <div class="card-body text-white">
            <h5 class="card-title font-weight-bold mb-3">
              <i class="fas fa-phone-alt mr-2"></i>Get In Touch
            </h5>
            <div class="mb-3">
              <p class="mb-2">
                <i class="fas fa-phone mr-2"></i>
                <a href="tel:<?php echo isset($_SESSION['system']['contact']) ? htmlspecialchars($_SESSION['system']['contact']) : '0912-345-6789'; ?>" class="text-white">
                  <?php echo isset($_SESSION['system']['contact']) ? htmlspecialchars($_SESSION['system']['contact']) : '0912-345-6789'; ?>
                </a>
              </p>
              <p class="mb-0">
                <i class="fas fa-envelope mr-2"></i>
                <a href="mailto:<?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'info@moist.edu.ph'; ?>" class="text-white">
                  <?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'info@moist.edu.ph'; ?>
                </a>
              </p>
            </div>
            <a href="contact.php" class="btn btn-light btn-block">
              <i class="fas fa-paper-plane mr-2"></i>Contact Us
            </a>
          </div>
        </div>
        
        <!-- Why Choose MOIST -->
        <div class="card mb-4 shadow-sm border-0">
          <div class="card-body">
            <h5 class="card-title font-weight-bold mb-3" style="color: #800020;">
              <i class="fas fa-star mr-2"></i>Why Choose MOIST?
            </h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="fas fa-check-circle mr-2" style="color: #800020;"></i>
                <small style="color: #000000;">Affordable Quality Education</small>
              </li>
              <li class="mb-2">
                <i class="fas fa-check-circle mr-2" style="color: #800020;"></i>
                <small style="color: #000000;">Industry-Ready Programs</small>
              </li>
              <li class="mb-2">
                <i class="fas fa-check-circle mr-2" style="color: #800020;"></i>
                <small style="color: #000000;">Experienced Faculty</small>
              </li>
              <li class="mb-2">
                <i class="fas fa-check-circle mr-2" style="color: #800020;"></i>
                <small style="color: #000000;">Modern Facilities</small>
              </li>
              <li class="mb-2">
                <i class="fas fa-check-circle mr-2" style="color: #800020;"></i>
                <small style="color: #000000;">Strong Alumni Network</small>
              </li>
            </ul>
          </div>
        </div>
      </aside>
    </div>

    <!-- Meet Our Team Section - Full Width -->
    <div class="row">
      <div class="col-12">
        <section id="team" class="mb-5 py-5">
          <div class="text-center mb-5">
            <h2 class="display-4 font-weight-bold" style="color: #800020;">Meet Our Team</h2>
            <p class="lead" style="color: #000000;">The passionate individuals behind MOIST Alumni Management System</p>
            <div class="mx-auto" style="width: 80px; height: 4px; background: linear-gradient(90deg,#800020 0%,#5c0016 100%);"></div>
          </div>

          <div class="row justify-content-center">
            <?php
            // Team members data - You can easily modify this array to add/remove team members
            $team_members = [
              [
                'name' => 'Mary Anne Ampo',
                'role' => 'Project Leader & System Architect',
                'image' => 'assets/team/member1.jpg',
                'bio' => 'Leading the MOIST Alumni Management System with strategic vision and technical expertise. Coordinating team efforts to deliver excellence.',
                'social' => [
                  'facebook' => '#',
                  'linkedin' => '#',
                  'github' => '#'
                ]
              ],
              [
                'name' => 'Charlie James Abejo',
                'role' => 'Lead Developer & Backend Specialist',
                'image' => 'assets/team/member2.jpg',
                'bio' => 'Building robust backend systems with PHP and MySQL. Developing core features and ensuring system reliability.',
                'social' => [
                  'facebook' => '#',
                  'linkedin' => '#',
                  'github' => '#'
                ]
              ],
              [
                'name' => 'Kenneth G. Ladra',
                'role' => 'Frontend Developer & UI Designer',
                'image' => 'assets/team/member3.jpg',
                'bio' => 'Crafting beautiful and responsive user interfaces. Implementing modern design principles for optimal user experience.',
                'social' => [
                  'facebook' => '#',
                  'linkedin' => '#',
                  'github' => '#'
                ]
              ],
              [
                'name' => 'Geraldine Hapayayon',
                'role' => 'Quality Assurance & Documentation Specialist',
                'image' => 'assets/team/member4.jpg',
                'bio' => 'Ensuring system quality through comprehensive testing and maintaining detailed documentation for the alumni management system.',
                'social' => [
                  'facebook' => '#',
                  'linkedin' => '#',
                  'github' => '#'
                ]
              ]
            ];

            foreach($team_members as $member): 
            ?>
              <div class="col-lg-4 col-md-6 mb-4">
                <div class="team-card card h-100 shadow-sm border-0">
                  <div class="team-img-wrapper position-relative overflow-hidden">
                    <img src="<?php echo htmlspecialchars($member['image']); ?>" 
                         class="card-img-top team-img" 
                         alt="<?php echo htmlspecialchars($member['name']); ?>"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($member['name']); ?>&size=400&background=2563eb&color=fff&bold=true'">
                    <div class="team-overlay">
                      <div class="team-social">
                        <?php if(!empty($member['social']['facebook'])): ?>
                          <a href="<?php echo htmlspecialchars($member['social']['facebook']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                          </a>
                        <?php endif; ?>
                        <?php if(!empty($member['social']['linkedin'])): ?>
                          <a href="<?php echo htmlspecialchars($member['social']['linkedin']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                          </a>
                        <?php endif; ?>
                        <?php if(!empty($member['social']['github'])): ?>
                          <a href="<?php echo htmlspecialchars($member['social']['github']); ?>" class="social-icon" target="_blank">
                            <i class="fab fa-github"></i>
                          </a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <div class="card-body text-center">
                    <h5 class="card-title font-weight-bold mb-1" style="color: #800020;"><?php echo htmlspecialchars($member['name']); ?></h5>
                    <p class="small font-weight-semibold mb-3" style="color: #800020;"><?php echo htmlspecialchars($member['role']); ?></p>
                    <p class="card-text small" style="color: #000000;"><?php echo htmlspecialchars($member['bio']); ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      </div>
    </div>
  </main>

  <?php include 'footer.php'; ?>

  <!-- Bootstrap JS bundle (includes Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc7ZV7h5QZ6f3r9g4Y5l5J5G5" crossorigin="anonymous"></script>
  
  <!-- Custom JavaScript for Animations -->
  <script>
    // Animated Counter for Statistics
    function animateCounter(element) {
      const target = parseInt(element.getAttribute('data-count'));
      const duration = 2000; // 2 seconds
      const increment = target / (duration / 16); // 60 FPS
      let current = 0;
      
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          element.textContent = target;
          clearInterval(timer);
        } else {
          element.textContent = Math.floor(current);
        }
      }, 16);
    }
    
    // Intersection Observer for triggering animations when in view
    const observerOptions = {
      threshold: 0.5,
      rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const counters = entry.target.querySelectorAll('[data-count]');
          counters.forEach(counter => {
            if (counter.textContent === '0') {
              animateCounter(counter);
            }
          });
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);
    
    // Observe the stats section
    document.addEventListener('DOMContentLoaded', () => {
      const statsSection = document.querySelector('.stats-section');
      if (statsSection) {
        observer.observe(statsSection);
      }
      
      // Smooth scroll for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          const href = this.getAttribute('href');
          if (href !== '#' && href !== '') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
              target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
              });
            }
          }
        });
      });
      
      // Add scroll reveal animation for sections
      const revealElements = document.querySelectorAll('.feature-card, .value-card, .timeline-item');
      const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(20px)';
            entry.target.style.transition = 'all 0.6s ease-out';
            
            setTimeout(() => {
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            revealObserver.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });
      
      revealElements.forEach(el => revealObserver.observe(el));
    });
    
    // Add parallax effect to hero section
    window.addEventListener('scroll', () => {
      const scrolled = window.pageYOffset;
      const parallaxElements = document.querySelectorAll('.floating');
      parallaxElements.forEach(el => {
        const speed = 0.5;
        el.style.transform = `translateY(${scrolled * speed}px)`;
      });
    });
  </script>
</body>
</html>
