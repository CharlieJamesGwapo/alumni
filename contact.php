<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <?php include 'header.php'; ?>
  <title>Contact | MOIST Alumni Portal</title>
  <!-- Core styles to match site privacy/help pages -->
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* privacy-like shared styles */
    .back-to-home {
      position: fixed; bottom: 30px; right: 30px; background: #007bff; color: white; padding: 12px 20px; border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); transition: all 0.3s ease; z-index: 1000; text-decoration: none; display: flex; align-items: center; font-weight: 500;
    }
    .back-to-home:hover { background: #0056b3; color: white; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); text-decoration: none; }
    .back-to-home i { margin-right: 8px; }

    .contact-section { background: #fff; border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); padding: 2.5rem 2rem; margin: 2rem auto; max-width: 900px; }
    .contact-title { font-size: 2rem; font-weight: 700; color: #333; margin-bottom: 1rem; }
    .contact-subtitle { font-size: 1.08rem; color: #444; margin-bottom: 1rem; }

    /* Small page-specific styles to keep contact page clean and professional */
    .hero-contact{ background: linear-gradient(90deg,#800000 0%,#b71c1c 100%); }
    .contact-card { border-radius: 12px; box-shadow: 0 6px 30px rgba(0,0,0,0.08); }
    .map-frame{ width:100%; height:420px; border:0; border-radius:12px; overflow:hidden; }
    @media (max-width:767.98px){ .map-frame{ height:260px } .back-to-home{ bottom:20px; right:20px; padding:10px 16px; font-size:0.95rem } .contact-section{ padding:1.2rem } .contact-title{ font-size:1.3rem } }
  </style>
</head>
<body class="bg-light text-dark">

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
          <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="directory.php">Directory</a></li>
          <li class="nav-item active"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Shared Hero -->
  <?php include 'includes/hero.php';
    render_hero([
        'title' => 'Get in Touch',
        'subtitle' => "We'd love to hear from you! Reach out to us for any inquiries or support.",
        'bg' => 'assets/img/moist12.jpg',
        'cta_url' => 'signup.php',
        'cta_text' => 'Join the Alumni'
    ]);
  ?>

  <style>
    /* privacy-like styles for contact page */
    .back-to-home {
        position: fixed; bottom: 30px; right: 30px; background: #007bff; color: #fff; padding: 12px 20px; border-radius:50px; box-shadow:0 2px 10px rgba(0,0,0,0.1); transition:all .25s; z-index:1000; display:flex; align-items:center; text-decoration:none; font-weight:500;
    }
    .back-to-home:hover{ background:#0056b3; transform:translateY(-3px); }
    .contact-section{ background:#fff; border-radius:10px; box-shadow:0 2px 16px rgba(0,0,0,0.07); padding:2.5rem 2rem; margin:2rem auto; max-width:900px; }
    .contact-title{ font-size:2rem; font-weight:700; color:#333; margin-bottom:1rem; }
    .contact-subtitle{ font-size:1.08rem; color:#444; margin-bottom:1rem; }
    @media (max-width:768px){ .back-to-home{ bottom:20px; right:20px; padding:10px 16px; font-size:0.95rem } .contact-section{ padding:1.2rem } .contact-title{ font-size:1.3rem } }
  </style>

  <a href="index.php" class="back-to-home"><i class="fas fa-home" style="margin-right:8px"></i> Back to Home</a>

  <main class="container">
    <div class="contact-section">
      <div class="contact-title"><i class="fas fa-phone-alt mr-2"></i>Contact Admissions</div>
      <div class="contact-subtitle">We'd love to hear from you — general inquiries, admissions, or campus visits.</div>

      <?php include 'includes/contact.php'; render_contact(); ?>

    </div>
  </main>

  <?php include 'footer.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Make the "View Location" button open the large modal already provided by includes/contact.php
    document.addEventListener('DOMContentLoaded', function(){
      var btn = document.getElementById('viewLocationBtn');
      if (btn){ btn.addEventListener('click', function(e){ e.preventDefault(); $('#admissionMapModal').modal('show'); }); }
    });
  </script>
</body>
</html>
