<?php
// terms.php - MOIST Alumni Portal Terms of Use
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Use | MOIST Alumni Portal</title>
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
        .terms-section {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2.5rem 2rem;
            margin: 2rem auto;
            max-width: 900px;
        }
        .terms-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.2rem;
        }
        .terms-subtitle {
            font-size: 1.2rem;
            font-weight: 600;
            color: #222;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .terms-list {
            margin-left: 1.2rem;
            margin-bottom: 1.5rem;
        }
        .terms-section p, .terms-section li {
            color: #444;
            font-size: 1.07rem;
            line-height: 1.6;
        }
        .terms-highlight {
            background: #f8f9fa;
            padding: 1rem;
            border-left: 4px solid #007bff;
            margin: 1rem 0;
        }
        @media (max-width: 600px) {
            .terms-section { padding: 1.2rem 1rem; }
            .terms-title { font-size: 1.3rem; }
            .terms-subtitle { font-size: 1.1rem; }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<a href="index.php" class="back-to-home">
    <i class="fas fa-home"></i> Back to Home
</a>
<div class="container">
    <div class="terms-section">
        <div class="terms-title"><i class="fas fa-gavel mr-2"></i>Terms of Use</div>
        <p>Welcome to the MOIST Alumni Portal. By accessing and using this website, you agree to comply with and be bound by the following terms and conditions.</p>

        <div class="terms-subtitle">1. Acceptance of Terms</div>
        <p>By accessing or using the MOIST Alumni Portal, you agree to be bound by these Terms of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws.</p>

        <div class="terms-subtitle">2. User Account and Registration</div>
        <ul class="terms-list">
            <li>You must be a verified MOIST alumnus/alumna to register for an account.</li>
            <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
            <li>You agree to provide accurate, current, and complete information during registration.</li>
            <li>Any misuse or unauthorized access should be reported immediately.</li>
        </ul>

        <div class="terms-subtitle">3. Acceptable Use</div>
        <ul class="terms-list">
            <li>Content posted must be professional and respectful.</li>
            <li>No unauthorized commercial solicitation.</li>
            <li>No posting of confidential or proprietary information.</li>
            <li>No harmful, offensive, or discriminatory content.</li>
        </ul>

        <div class="terms-highlight">
            <strong>Important:</strong> Violation of these terms may result in account suspension or termination.
        </div>

        <div class="terms-subtitle">4. Intellectual Property</div>
        <p>All content on this portal, including but not limited to text, graphics, logos, and software, is the property of MOIST and is protected by intellectual property laws.</p>

        <div class="terms-subtitle">5. User Content</div>
        <ul class="terms-list">
            <li>You retain ownership of content you post.</li>
            <li>You grant MOIST a license to use, display, and distribute posted content.</li>
            <li>Content must not infringe on third-party rights.</li>
        </ul>

        <div class="terms-subtitle">6. Privacy and Data Protection</div>
        <p>Your use of the portal is also governed by our <a href="privacy.php">Privacy Policy</a>. Please review it to understand how we collect and use your information.</p>

        <div class="terms-subtitle">7. Disclaimer and Limitation of Liability</div>
        <ul class="terms-list">
            <li>The portal is provided "as is" without warranties of any kind.</li>
            <li>MOIST is not liable for any damages arising from portal use.</li>
            <li>MOIST does not guarantee continuous, uninterrupted access.</li>
        </ul>

        <div class="terms-subtitle">8. Modifications</div>
        <p>MOIST reserves the right to modify these terms at any time. Changes will be effective immediately upon posting to the portal.</p>

        <div class="terms-subtitle">9. Governing Law</div>
        <p>These terms are governed by the laws of the Philippines, without regard to conflicts of law principles.</p>

        <div class="terms-subtitle">10. Contact Information</div>
        <p>For questions about these Terms of Use, please contact us at <a href="mailto:<?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'info@moist.edu.ph'; ?>"><?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'info@moist.edu.ph'; ?></a>.</p>

        <p class="text-muted small mt-4">Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
