<?php
// help.php - MOIST Alumni Portal Help Center
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center | MOIST Alumni Portal</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .help-section {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.07);
            padding: 2.5rem 2rem;
            margin: 2rem auto;
            max-width: 1000px;
        }
        .help-search {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .help-category {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        .help-category:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .help-icon {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 1rem;
        }
        .help-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.2rem;
        }
        .category-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 0.8rem;
        }
        .faq-item {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1.5rem;
        }
        .faq-question {
            font-weight: 600;
            color: #007bff;
            margin-bottom: 0.5rem;
            cursor: pointer;
        }
        .faq-answer {
            color: #555;
            display: none;
            padding: 0.5rem 0;
        }
        .contact-support {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
            text-align: center;
        }
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
            .help-section { padding: 1.5rem 1rem; }
            .help-search { padding: 1.5rem; }
            .help-category { padding: 1.2rem; }
            .help-title { font-size: 1.5rem; }
            .back-to-home {
                bottom: 20px;
                right: 20px;
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php
include 'includes/hero.php';
render_hero([
    'title' => 'Help Center',
    'subtitle' => 'Find answers, guides, and support for the MOIST Alumni Portal.',
    'bg' => 'assets/img/moist12.jpg',
    'cta_url' => 'help.php',
    'cta_text' => 'Browse Help',
]);
?>

<a href="index.php" class="back-to-home">
    <i class="fas fa-home"></i> Back to Home
</a>
<div class="container">
    <div class="help-section">
        <div class="help-search">
            <h1 class="help-title text-center"><i class="fas fa-question-circle mr-2"></i>How can we help you?</h1>
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" id="helpSearch" placeholder="Search for help topics...">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button">Search</button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Account & Profile -->
            <div class="col-md-6">
                <div class="help-category">
                    <div class="help-icon"><i class="fas fa-user-circle"></i></div>
                    <h3 class="category-title">Account & Profile</h3>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>How do I update my profile?</div>
                        <div class="faq-answer">
                            Navigate to "My Profile" from the top menu, click "Edit Profile", update your information, and click "Save Changes".
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>I forgot my password</div>
                        <div class="faq-answer">
                            Click "Forgot Password" on the login page, enter your email address, and follow the reset instructions sent to your email.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Events & Activities -->
            <div class="col-md-6">
                <div class="help-category">
                    <div class="help-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h3 class="category-title">Events & Activities</h3>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>How do I register for events?</div>
                        <div class="faq-answer">
                            Browse the Events page, select an event you're interested in, and click the "Register" button. Follow the instructions to complete your registration.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>Can I propose an alumni event?</div>
                        <div class="faq-answer">
                            Yes! Contact the alumni office through the feedback form or email us directly with your event proposal.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Networking -->
            <div class="col-md-6">
                <div class="help-category">
                    <div class="help-icon"><i class="fas fa-network-wired"></i></div>
                    <h3 class="category-title">Networking</h3>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>How do I find my batchmates?</div>
                        <div class="faq-answer">
                            Use the Alumni Directory to search by batch year, course, or name. You can also use the advanced search filters to narrow down results.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>How do I join discussion forums?</div>
                        <div class="faq-answer">
                            Navigate to the Forums section, browse available topics, and click "Join Discussion" to participate. You can also create new topics.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Career Services -->
            <div class="col-md-6">
                <div class="help-category">
                    <div class="help-icon"><i class="fas fa-briefcase"></i></div>
                    <h3 class="category-title">Career Services</h3>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>How do I post a job opportunity?</div>
                        <div class="faq-answer">
                            Go to the Careers section, click "Post a Job", fill in the job details form, and submit for review.
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question"><i class="fas fa-chevron-right mr-2"></i>How can I find mentorship opportunities?</div>
                        <div class="faq-answer">
                            Check the Career Services section for mentorship programs, or contact the alumni office for guidance.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-support">
            <h3><i class="fas fa-headset mr-2"></i>Still Need Help?</h3>
            <p class="mb-3">Our support team is here to assist you</p>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-3">
                    <a href="mailto:<?php echo isset($_SESSION['system']['email']) ? htmlspecialchars($_SESSION['system']['email']) : 'support@moist.edu.ph'; ?>" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-envelope mr-2"></i>Email Support
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="#" data-toggle="modal" data-target="#feedbackModal" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-comment-alt mr-2"></i>Send Feedback
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle FAQ answers
    $('.faq-question').click(function() {
        $(this).find('i').toggleClass('fa-chevron-right fa-chevron-down');
        $(this).next('.faq-answer').slideToggle();
    });

    // Help search functionality
    $('#helpSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.faq-item').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>
