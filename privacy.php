<?php
// privacy.php - MOIST Alumni Portal Privacy Policy
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | MOIST Alumni Portal</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .back-to-home {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #007bff;
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 1000;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        .back-to-home:hover {
            background: #0056b3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        .back-to-home i {
            margin-right: 8px;
        }
        @media (max-width: 768px) {
            .back-to-home {
                bottom: 20px;
                right: 20px;
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }
        .privacy-section {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2.5rem 2rem;
            margin: 2rem auto;
            max-width: 900px;
        }
        .privacy-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.2rem;
        }
        .privacy-subtitle {
            font-size: 1.2rem;
            font-weight: 600;
            color: #222;
            margin-top: 1.5rem;
        }
        .privacy-list {
            margin-left: 1.2rem;
        }
        .privacy-section p, .privacy-section li {
            color: #444;
            font-size: 1.07rem;
        }
        @media (max-width: 600px) {
            .privacy-section { padding: 1.2rem 0.5rem; }
            .privacy-title { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php
// Use the shared hero for consistent header across site
include 'includes/hero.php';
render_hero([
    'title' => 'Privacy Policy',
    'subtitle' => 'How we collect, use, and protect your information.',
    'bg' => 'assets/img/moist12.jpg',
    'cta_url' => 'contact.php',
    'cta_text' => 'Contact Us',
]);
?>

<a href="index.php" class="back-to-home">
    <i class="fas fa-home"></i> Back to Home
</a>
<div class="container">
    <div class="privacy-section">
        <div class="privacy-title"><i class="fas fa-user-shield mr-2"></i>Privacy Policy</div>
        <p>At MOIST Alumni Portal, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your data when you use our website and services.</p>
        <div class="privacy-subtitle">1. Information We Collect</div>
        <ul class="privacy-list">
            <li><b>Personal Information:</b> Name, email address, contact number, graduation details, and other information you provide during registration or profile updates.</li>
            <li><b>Usage Data:</b> Pages visited, login times, device/browser information, and IP address for security and analytics.</li>
        </ul>
        <div class="privacy-subtitle">2. How We Use Your Information</div>
        <ul class="privacy-list">
            <li>To manage your alumni account and provide access to portal features.</li>
            <li>To communicate important updates, events, and opportunities.</li>
            <li>To improve our services and personalize your experience.</li>
            <li>To ensure the security and integrity of our platform.</li>
        </ul>
        <div class="privacy-subtitle">3. Data Sharing & Disclosure</div>
        <ul class="privacy-list">
            <li>We do <b>not</b> sell or rent your personal data to third parties.</li>
            <li>Information may be shared with authorized MOIST staff for legitimate purposes (e.g., alumni events, verification).</li>
            <li>We may disclose information if required by law or to protect the rights and safety of users.</li>
        </ul>
        <div class="privacy-subtitle">4. Data Security</div>
        <ul class="privacy-list">
            <li>We implement industry-standard security measures to protect your data from unauthorized access, alteration, or disclosure.</li>
            <li>Access to your data is restricted to authorized personnel only.</li>
        </ul>
        <div class="privacy-subtitle">5. Your Rights & Choices</div>
        <ul class="privacy-list">
            <li>You may review, update, or delete your personal information at any time via your account settings.</li>
            <li>Contact us if you wish to deactivate your account or have privacy concerns.</li>
        </ul>
        <div class="privacy-subtitle">6. Cookies & Tracking</div>
        <ul class="privacy-list">
            <li>We use cookies to enhance your browsing experience and analyze site traffic. You can manage cookie preferences in your browser settings.</li>
        </ul>
        <div class="privacy-subtitle">7. Changes to This Policy</div>
        <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with the updated date.</p>
        <div class="privacy-subtitle">8. Contact Us</div>
        <p>If you have any questions or concerns about this Privacy Policy, please contact us at <a href="mailto:<?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'info@moist.edu.ph'; ?>"><?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'info@moist.edu.ph'; ?></a>.</p>
        <p class="text-muted small mt-4">Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
