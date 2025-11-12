<?php
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

// Get user ID from session or URL parameter
$userId = isset($_GET['id']) ? intval($_GET['id']) : intval($_SESSION['login_id']);

// If viewing someone else's CV, check if they exist
if ($userId !== intval($_SESSION['login_id'])) {
    $stmt = $conn->prepare("SELECT id FROM alumnus_bio WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        echo "User not found";
        exit;
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT a.*, c.course as course_name, s.name as strand_name FROM alumnus_bio a LEFT JOIN courses c ON a.course_id = c.id LEFT JOIN strands s ON a.strand_id = s.id WHERE a.id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "User not found";
    exit;
}

// Helper function to format date
function formatDate($date) {
    if (empty($date) || $date === '0000-00-00') return 'Present';
    return date('F Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?> - Curriculum Vitae</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .cv-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            min-height: 100vh;
        }

        .cv-header {
            background: linear-gradient(135deg, #800000, #600000);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .cv-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .cv-header .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .cv-content {
            padding: 30px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #800000;
            border-bottom: 3px solid #800000;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .contact-item i {
            width: 20px;
            color: #800000;
        }

        .profile-photo {
            width: 160px;
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: bold;
            color: #800000;
        }

        .info-value {
            color: #333;
        }

        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .skill-item {
            background: #f8f9fa;
            padding: 5px 15px;
            border-radius: 20px;
            border: 1px solid #800000;
            color: #800000;
            font-size: 0.9rem;
        }

        .experience-item, .education-item {
            margin-bottom: 20px;
            padding: 15px;
            border-left: 4px solid #800000;
            background: #f8f9fa;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .item-title {
            font-weight: bold;
            color: #800000;
            font-size: 1.1rem;
        }

        .item-date {
            color: #666;
            font-style: italic;
        }

        .item-subtitle {
            color: #666;
            margin-bottom: 10px;
        }

        .item-description {
            color: #333;
            line-height: 1.5;
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #800000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            z-index: 1000;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            z-index: 1000;
            text-decoration: none;
        }

        .reference-item {
            margin-bottom: 20px;
            padding: 20px;
            border-left: 4px solid #800000;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .reference-item:hover {
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .reference-name {
            font-weight: bold;
            color: #800000;
            font-size: 1.2rem;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .reference-name::before {
            content: "👤";
            font-size: 1.1rem;
        }
        
        .reference-position {
            color: #555;
            font-style: italic;
            margin-bottom: 12px;
            padding-left: 28px;
            font-size: 0.95rem;
        }
        
        .reference-contact {
            color: #333;
            font-size: 0.9rem;
            padding-left: 28px;
            display: grid;
            gap: 6px;
        }
        
        .reference-contact div {
            display: flex;
            align-items: center;
            padding: 4px 0;
        }
        
        .reference-contact i {
            color: #800000;
            width: 24px;
            margin-right: 8px;
            font-size: 0.9rem;
        }
        
        .reference-contact a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .reference-contact a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        /* Hide empty sections */
        .empty-section {
            display: none !important;
        }

        @media print {
            .print-btn, .back-btn, .edit-btn, .save-btn, .cancel-btn, .home-btn,
            .action-buttons-container, .add-item-btn, .remove-btn, .remove-item-btn,
            .section-edit-btn {
                display: none !important;
            }

            .cv-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }

            body {
                background: white;
            }
            
            /* Hide sections with only "No ... found" messages */
            .section:has(.text-muted:only-child) {
                display: none !important;
            }
            
            /* Hide empty sections */
            .empty-section {
                display: none !important;
            }
            
            /* Optimize for print */
            .cv-header {
                padding: 30px 20px;
                page-break-after: avoid;
            }
            
            .cv-content {
                padding: 20px;
            }
            
            .section {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
            
            .section h2 {
                page-break-after: avoid;
            }
            
            .experience-item, .education-item, .work-experience-item, .reference-item {
                page-break-inside: avoid;
            }
        }

        .edit-btn, .save-btn, .cancel-btn, .home-btn, .print-btn {
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .edit-btn:hover, .save-btn:hover, .cancel-btn:hover, .home-btn:hover, .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .home-btn:hover {
            background: #5a359a;
        }

        .print-btn:hover {
            background: #138496;
        }

        .cv-editing {
            border: 2px dashed #007bff;
            background: #f8f9ff;
        }

        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #800000;
            box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.25);
        }

        .editable-section {
            position: relative;
        }

        .editable-section:hover {
            background: rgba(0, 123, 255, 0.05);
            border-radius: 5px;
        }

        .section-edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .editable-section:hover .section-edit-btn {
            opacity: 1;
        }

        .add-item-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            margin: 10px 0;
        }

        .add-item-btn:hover {
            background: #218838;
        }

        .remove-item-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 3px 8px;
            border-radius: 50%;
            cursor: pointer;
            margin-left: 10px;
        }

        .remove-item-btn:hover {
            background: #c82333;
        }

        .edit-mode .cv-container {
            background: #f8f9ff;
        }

        .edit-mode .section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .edit-mode .cv-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .edit-mode .section h2 {
            color: #007bff;
            border-color: #007bff;
        }

        .edit-mode .info-label {
            color: #007bff;
        }

        .edit-mode .contact-item i,
        .edit-mode .item-title {
            color: #007bff;
        }

        .edit-mode .experience-item,
        .edit-mode .education-item {
            border-left-color: #007bff;
        }

        .edit-mode .skill-item {
            border-color: #007bff;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .skill-input-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .skill-input-container input {
            flex: 1;
        }

        .work-experience-item,
        .education-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .work-experience-item .remove-btn,
        .education-item .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
        }

        .work-experience-item .remove-btn:hover,
        .education-item .remove-btn:hover {
            background: #c82333;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .cv-container {
                margin: 10px;
            }
            
            .cv-header {
                padding: 30px 20px;
            }
            
            .cv-header h1 {
                font-size: 2rem;
            }
            
            .cv-content {
                padding: 20px;
            }
            
            .contact-info {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .info-label {
                font-weight: bold;
                margin-bottom: 5px;
            }
            
            .item-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .item-date {
                margin-top: 5px;
                font-size: 0.9rem;
            }
            
            .action-buttons-container {
                right: 10px !important;
                top: 10px !important;
            }
            
            .action-buttons-container button {
                padding: 10px 15px !important;
                font-size: 12px !important;
            }
            
            .profile-photo {
                width: 140px;
                height: 180px;
            }
            
            .section h2 {
                font-size: 1.3rem;
            }
            
            .otp-inputs {
                gap: 0.3rem;
            }
            
            .skill-item {
                font-size: 0.85rem;
                padding: 4px 12px;
            }
        }
        
        @media (max-width: 480px) {
            .cv-header h1 {
                font-size: 1.5rem;
            }
            
            .cv-header .subtitle {
                font-size: 1rem;
            }
            
            .profile-photo {
                width: 120px;
                height: 160px;
            }
            
            .section h2 {
                font-size: 1.2rem;
            }
            
            .action-buttons-container {
                position: static !important;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="cv-container">
        <!-- Header Section -->
        <div class="cv-header">
            <?php if (!empty($user['img']) && file_exists('uploads/' . $user['img'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['img']); ?>" alt="Profile Photo" class="profile-photo">
            <?php endif; ?>

            <h1><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h1>
            <div class="subtitle">
                <?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? 'Alumni'); ?> •
                Batch <?php echo htmlspecialchars($user['batch']); ?>
            </div>

            <div class="contact-info">
                <?php if (!empty($user['email'])): ?>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['contact_no'])): ?>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo htmlspecialchars($user['contact_no']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['address'])): ?>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($user['address']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['birthdate']) && $user['birthdate'] !== '0000-00-00'): ?>
                    <div class="contact-item">
                        <i class="fas fa-birthday-cake"></i>
                        <span><?php echo date('F j, Y', strtotime($user['birthdate'])); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content Section -->
        <div class="cv-content">
            <!-- Personal Information -->
            <div class="section">
                <h2><i class="fas fa-user"></i> Personal Information</h2>
                <div class="info-grid">
                    <div class="info-label">Full Name:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname'] . ' ' . $user['suffixname']); ?></div>

                    <div class="info-label">Gender:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['gender']); ?></div>

                    <div class="info-label">Academic Program:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? 'N/A'); ?></div>

                    <?php if (!empty($user['academic_honor']) && $user['academic_honor'] !== 'None' && $user['academic_honor'] !== 'None specified'): ?>
                    <div class="info-label">Academic Honors:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['academic_honor']); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Professional Experience with Add Button -->
            <div class="section">
                <h2><i class="fas fa-briefcase"></i> Professional Experience</h2>

                <?php if (!empty($user['employment_status']) && $user['employment_status'] !== 'Unemployed'): ?>
                    <div class="experience-item">
                        <div class="item-header">
                            <div class="item-title">
                                <?php
                                switch($user['employment_status']) {
                                    case 'Employed':
                                        echo 'Currently Employed';
                                        break;
                                    case 'Self-employed':
                                        echo 'Self-Employed';
                                        break;
                                    case 'Business Owner':
                                        echo 'Business Owner';
                                        break;
                                    default:
                                        echo htmlspecialchars($user['employment_status']);
                                }
                                ?>
                            </div>
                            <div class="item-date">Current</div>
                        </div>

                        <?php if (!empty($user['connected_to'])): ?>
                            <div class="item-subtitle">
                                <i class="fas fa-building"></i> <?php echo htmlspecialchars($user['connected_to']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="item-description">
                            <?php if (!empty($user['company_address'])): ?>
                                <strong>Company Address:</strong> <?php echo htmlspecialchars($user['company_address']); ?><br>
                            <?php endif; ?>

                            <?php if (!empty($user['company_email'])): ?>
                                <strong>Company Email:</strong> <?php echo htmlspecialchars($user['company_email']); ?><br>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No current professional experience information available.</p>
                <?php endif; ?>

                <!-- Add Work Experience Button -->
                <button type="button" class="add-item-btn" onclick="addWorkExperience()" style="display: none;" id="addWorkExpBtn">
                    <i class="fas fa-plus"></i> Add Work Experience
                </button>
            </div>

            <!-- Detailed Work Experience from Database -->
            <div class="section">
                <h2><i class="fas fa-history"></i> Work Experience History</h2>

                <?php
                // Fetch work experience from employment_history table
                $work_stmt = $conn->prepare("SELECT * FROM employment_history WHERE user_id = ? ORDER BY date_start DESC");
                $work_stmt->bind_param("i", $userId);
                $work_stmt->execute();
                $work_result = $work_stmt->get_result();

                if ($work_result->num_rows > 0):
                    while ($work = $work_result->fetch_assoc()):
                ?>
                    <div class="work-experience-item">
                        <button type="button" class="remove-btn" onclick="removeWorkExperience(<?php echo $work['id']; ?>)">×</button>
                        <div class="item-header">
                            <div class="item-title"><?php echo htmlspecialchars($work['position']); ?></div>
                            <div class="item-date">
                                <?php echo date('M Y', strtotime($work['date_start'])); ?> -
                                <?php echo $work['date_end'] ? date('M Y', strtotime($work['date_end'])) : 'Present'; ?>
                            </div>
                        </div>
                        <div class="item-subtitle">
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($work['company_name']); ?>
                            <?php if ($work['location']): ?>
                                <span style="color: #666;">• <?php echo htmlspecialchars($work['location']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($work['description']): ?>
                            <div class="item-description">
                                <?php echo nl2br(htmlspecialchars($work['description'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p class="text-muted">No detailed work experience records found.</p>
                <?php endif; ?>

                <!-- Add Work Experience Button -->
                <button type="button" class="add-item-btn" onclick="addWorkExperience()" id="addWorkExpHistBtn">
                    <i class="fas fa-plus"></i> Add Work Experience
                </button>
            </div>

            <!-- Education -->
            <div class="section">
                <h2><i class="fas fa-graduation-cap"></i> Education</h2>

                <!-- Primary Education from Profile -->
                <div class="education-item" id="primaryEducation">
                    <div class="item-header">
                        <div class="item-title">
                            <?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? 'Academic Program'); ?>
                        </div>
                        <div class="item-date">Batch <?php echo htmlspecialchars($user['batch']); ?></div>
                    </div>

                    <div class="item-subtitle">
                        <i class="fas fa-university"></i> Misamis Oriental Institute of Science and Technology (MOIST)
                    </div>

                    <div class="item-description">
                        Completed academic program with specialization in <?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? 'the chosen field'); ?>.
                        <?php if (!empty($user['academic_honor']) && $user['academic_honor'] !== 'None' && $user['academic_honor'] !== 'None specified'): ?>
                            Graduated with <?php echo htmlspecialchars($user['academic_honor']); ?> honors.
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Additional Education from Database -->
                <?php
                // Fetch additional education from employment_history table (type = 'education')
                $edu_stmt = $conn->prepare("SELECT * FROM employment_history WHERE user_id = ? AND description LIKE '%\"type\":\"education\"%' ORDER BY date_start DESC");
                $edu_stmt->bind_param("i", $userId);
                $edu_stmt->execute();
                $edu_result = $edu_stmt->get_result();

                if ($edu_result->num_rows > 0):
                    while ($edu = $edu_result->fetch_assoc()):
                        $edu_data = json_decode($edu['description'], true);
                ?>
                    <div class="education-item" data-id="<?php echo $edu['id']; ?>">
                        <button type="button" class="remove-btn" onclick="removeEducation(<?php echo $edu['id']; ?>)">×</button>
                        <div class="item-header">
                            <div class="item-title"><?php echo htmlspecialchars($edu_data['degree'] ?? $edu['position']); ?></div>
                            <div class="item-date">
                                <?php echo date('Y', strtotime($edu['date_start'])); ?>
                                <?php if ($edu['date_end'] && $edu['date_end'] !== '0000-00-00'): ?>
                                    - <?php echo date('Y', strtotime($edu['date_end'])); ?>
                                <?php else: ?>
                                    - Present
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="item-subtitle">
                            <i class="fas fa-university"></i> <?php echo htmlspecialchars($edu_data['school'] ?? $edu['company_name']); ?>
                            <?php if (!empty($edu_data['location'])): ?>
                                <span style="color: #666;">• <?php echo htmlspecialchars($edu_data['location']); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($edu_data['description'])): ?>
                            <div class="item-description">
                                <?php echo nl2br(htmlspecialchars($edu_data['description'])); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($edu_data['gpa'])): ?>
                            <div class="item-description">
                                <strong>GPA:</strong> <?php echo htmlspecialchars($edu_data['gpa']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php
                    endwhile;
                endif;
                ?>

                <!-- Add Education Button -->
                <button type="button" class="add-item-btn" onclick="addEducation()" id="addEducationBtn">
                    <i class="fas fa-plus"></i> Add Education
                </button>
            </div>

            <!-- Skills & Competencies with Database Skills -->
            <div class="section">
                <h2><i class="fas fa-star"></i> Skills & Competencies</h2>

                <?php
                // Fetch skills from employment_history table (type = 'skills')
                $skills_stmt = $conn->prepare("SELECT * FROM employment_history WHERE user_id = ? AND description LIKE '%\"type\":\"skills\"%' ORDER BY date_start DESC LIMIT 1");
                $skills_stmt->bind_param("i", $userId);
                $skills_stmt->execute();
                $skills_result = $skills_stmt->get_result();

                if ($skills_result->num_rows > 0):
                    $skills_data = $skills_result->fetch_assoc();
                    $skills = json_decode($skills_data['description'], true);
                    if (is_array($skills)):
                ?>
                    <div class="skills-list">
                        <?php foreach ($skills as $skill): ?>
                            <div class="skill-item"><?php echo htmlspecialchars($skill); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; else: ?>
                    <div class="skills-list">
                        <div class="skill-item">Academic Excellence</div>
                        <div class="skill-item">Professional Communication</div>
                        <div class="skill-item">Leadership</div>
                        <div class="skill-item">Problem Solving</div>
                        <div class="skill-item">Team Collaboration</div>
                        <div class="skill-item">Project Management</div>
                        <div class="skill-item">Research & Analysis</div>
                        <div class="skill-item">Digital Literacy</div>
                    </div>
                <?php endif; ?>

                <!-- Add Skills Button -->
                <button type="button" class="add-item-btn" onclick="addSkills()" id="addSkillsBtn">
                    <i class="fas fa-plus"></i> Add Skills
                </button>
            </div>

            <!-- Certifications and Achievements -->
            <div class="section">
                <h2><i class="fas fa-certificate"></i> Certifications & Achievements</h2>

                <?php
                // Fetch certifications from employment_history table (type = 'certification')
                $cert_stmt = $conn->prepare("SELECT * FROM employment_history WHERE user_id = ? AND description LIKE '%\"type\":\"certification\"%' ORDER BY date_start DESC");
                $cert_stmt->bind_param("i", $userId);
                $cert_stmt->execute();
                $cert_result = $cert_stmt->get_result();

                if ($cert_result->num_rows > 0):
                    while ($cert = $cert_result->fetch_assoc()):
                        $cert_data = json_decode($cert['description'], true);
                ?>
                    <div class="work-experience-item">
                        <button type="button" class="remove-btn" onclick="removeCertification(<?php echo $cert['id']; ?>)">×</button>
                        <div class="item-header">
                            <div class="item-title"><?php echo htmlspecialchars($cert_data['name'] ?? $cert['position']); ?></div>
                            <div class="item-date">
                                <?php echo $cert_data['issue_date'] ?? $cert['date_start']; ?>
                                <?php if (!empty($cert_data['expiry_date']) && $cert_data['expiry_date'] !== '0000-00-00'): ?>
                                    - <?php echo $cert_data['expiry_date']; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="item-subtitle">
                            <i class="fas fa-award"></i> <?php echo htmlspecialchars($cert_data['issuer'] ?? $cert['company_name']); ?>
                        </div>
                        <?php if (!empty($cert_data['description'])): ?>
                            <div class="item-description">
                                <?php echo nl2br(htmlspecialchars($cert_data['description'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p class="text-muted">No certifications or achievements found.</p>
                <?php endif; ?>

                <!-- Add Certification Button -->
                <button type="button" class="add-item-btn" onclick="addCertification()" id="addCertBtn">
                    <i class="fas fa-plus"></i> Add Certification
                </button>
            </div>

            <!-- Projects and Portfolio -->
            <div class="section">
                <h2><i class="fas fa-project-diagram"></i> Projects & Portfolio</h2>

                <?php
                // Fetch projects from employment_history table (type = 'project')
                $project_stmt = $conn->prepare("SELECT * FROM employment_history WHERE user_id = ? AND description LIKE '%\"type\":\"project\"%' ORDER BY date_start DESC");
                $project_stmt->bind_param("i", $userId);
                $project_stmt->execute();
                $project_result = $project_stmt->get_result();

                if ($project_result->num_rows > 0):
                    while ($project = $project_result->fetch_assoc()):
                        $project_data = json_decode($project['description'], true);
                ?>
                    <div class="work-experience-item">
                        <button type="button" class="remove-btn" onclick="removeProject(<?php echo $project['id']; ?>)">×</button>
                        <div class="item-header">
                            <div class="item-title">
                                <?php echo htmlspecialchars($project_data['name'] ?? $project['company_name']); ?>
                                <?php if (!empty($project_data['url'])): ?>
                                    <a href="<?php echo htmlspecialchars($project_data['url']); ?>" target="_blank" style="font-size: 0.8em; color: #800000;">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="item-date">
                                <?php echo $project_data['start_date'] ?? $project['date_start']; ?> -
                                <?php echo $project_data['end_date'] ?? $project['date_end'] ?? 'Present'; ?>
                            </div>
                        </div>
                        <?php if (!empty($project_data['technologies'])): ?>
                            <div class="item-subtitle">
                                <i class="fas fa-code"></i>
                                <span class="skill-item" style="display: inline-block; margin: 0 5px;">
                                    <?php echo htmlspecialchars($project_data['technologies']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($project_data['description'])): ?>
                            <div class="item-description">
                                <?php echo nl2br(htmlspecialchars($project_data['description'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p class="text-muted">No projects or portfolio items found.</p>
                <?php endif; ?>

                <!-- Add Project Button -->
                <button type="button" class="add-item-btn" onclick="addProject()" id="addProjectBtn">
                    <i class="fas fa-plus"></i> Add Project
                </button>
            </div>

            <!-- References -->
            <div class="section" id="referencesSection">
                <h2><i class="fas fa-user-friends"></i> References</h2>

                <?php
                // Fetch references from employment_history table (type = 'reference')
                $ref_stmt = $conn->prepare("SELECT * FROM employment_history WHERE user_id = ? AND description LIKE '%\"type\":\"reference\"%' ORDER BY date_start DESC");
                $ref_stmt->bind_param("i", $userId);
                $ref_stmt->execute();
                $ref_result = $ref_stmt->get_result();

                if ($ref_result->num_rows > 0):
                    while ($ref = $ref_result->fetch_assoc()):
                        $ref_data = json_decode($ref['description'], true);
                ?>
                    <div class="reference-item">
                        <button type="button" class="remove-btn" onclick="removeReference(<?php echo $ref['id']; ?>)">×</button>
                        <div class="reference-name"><?php echo htmlspecialchars($ref_data['name'] ?? $ref['position']); ?></div>
                        <div class="reference-position">
                            <?php echo htmlspecialchars($ref_data['position'] ?? ''); ?>
                            <?php if (!empty($ref_data['company'])): ?>
                                at <strong><?php echo htmlspecialchars($ref_data['company']); ?></strong>
                            <?php endif; ?>
                        </div>
                        <div class="reference-contact">
                            <?php if (!empty($ref_data['email'])): ?>
                                <div>
                                    <i class="fas fa-envelope"></i> 
                                    <a href="mailto:<?php echo htmlspecialchars($ref_data['email']); ?>">
                                        <?php echo htmlspecialchars($ref_data['email']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($ref_data['phone'])): ?>
                                <div>
                                    <i class="fas fa-phone"></i> 
                                    <a href="tel:<?php echo htmlspecialchars($ref_data['phone']); ?>">
                                        <?php echo htmlspecialchars($ref_data['phone']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($ref_data['relationship'])): ?>
                                <div>
                                    <i class="fas fa-user-tie"></i> 
                                    <span style="color: #666; font-weight: 500;">
                                        <?php echo htmlspecialchars($ref_data['relationship']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p class="text-muted">Available upon request.</p>
                <?php endif; ?>

                <!-- Add Reference Button -->
                <button type="button" class="add-item-btn" onclick="addReference()" id="addReferenceBtn">
                    <i class="fas fa-plus"></i> Add Reference
                </button>
            </div>

            <!-- Contact Information Summary -->
            <div class="section">
                <h2><i class="fas fa-address-book"></i> Contact Information</h2>

                <div class="info-grid">
                    <div class="info-label">Email Address:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>

                    <div class="info-label">Phone Number:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['contact_no'] ?? 'Not provided'); ?></div>

                    <div class="info-label">Home Address:</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></div>

                    <div class="info-label">Date of Birth:</div>
                    <div class="info-value">
                        <?php
                        if (!empty($user['birthdate']) && $user['birthdate'] !== '0000-00-00') {
                            echo date('F j, Y', strtotime($user['birthdate']));
                        } else {
                            echo 'Not provided';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px; flex-direction: column; align-items: flex-end;">

        <button onclick="toggleEditMode()" class="edit-btn" id="editBtn" style="background: #28a745; color: white; border: none; padding: 12px 20px; border-radius: 25px; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s ease; font-size: 14px;">
            <i class="fas fa-edit"></i> Edit CV
        </button>

        <button onclick="saveCV()" class="save-btn" id="saveBtn" style="background: #007bff; color: white; border: none; padding: 12px 20px; border-radius: 25px; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s ease; font-size: 14px; display: none;">
            <i class="fas fa-save"></i> Save Changes
        </button>

        <button onclick="cancelEdit()" class="cancel-btn" id="cancelBtn" style="background: #dc3545; color: white; border: none; padding: 12px 20px; border-radius: 25px; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s ease; font-size: 14px; display: none;">
            <i class="fas fa-times"></i> Cancel
        </button>

        <button onclick="goHome()" class="home-btn" style="background: #6f42c1; color: white; border: none; padding: 12px 20px; border-radius: 25px; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s ease; font-size: 14px;">
            <i class="fas fa-home"></i> Back to Home
        </button>

        <button onclick="window.print()" class="print-btn" style="background: #17a2b8; color: white; border: none; padding: 12px 20px; border-radius: 25px; cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2); transition: all 0.3s ease; font-size: 14px;">
            <i class="fas fa-print"></i> Print CV
        </button>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let originalData = {};
        let editMode = false;

        // Real-time: Hide empty sections before printing
        window.addEventListener('beforeprint', function() {
            hideEmptySections();
        });

        // Real-time: Show sections after printing
        window.addEventListener('afterprint', function() {
            showAllSections();
        });

        // Function to hide empty sections
        function hideEmptySections() {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => {
                const textMuted = section.querySelector('.text-muted');
                const hasContent = section.querySelector('.experience-item, .education-item, .work-experience-item, .reference-item, .info-grid, .skills-list');
                
                // Check if section only has "No ... found" or "Available upon request" message
                if (textMuted && !hasContent) {
                    const mutedText = textMuted.textContent.toLowerCase();
                    if (mutedText.includes('no ') || mutedText.includes('not found') || mutedText.includes('available upon request')) {
                        section.classList.add('empty-section');
                    }
                }
            });
        }

        // Function to show all sections again
        function showAllSections() {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => {
                section.classList.remove('empty-section');
            });
        }

        // Go back to home page
        function goHome() {
            if (editMode && confirm('You have unsaved changes. Are you sure you want to leave?')) {
                window.location.href = 'home.php';
            } else if (!editMode) {
                window.location.href = 'home.php';
            }
        }

        // Toggle edit mode
        function toggleEditMode() {
            editMode = !editMode;
            const body = document.body;
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');

            if (editMode) {
                body.classList.add('edit-mode');
                editBtn.style.display = 'none';
                saveBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';
                makeSectionsEditable();
                showAddButtons();
            } else {
                body.classList.remove('edit-mode');
                editBtn.style.display = 'inline-block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                hideAddButtons();
                // Note: We don't remove editable content here as it will be handled by cancelEdit()
            }
        }

        // Cancel edit mode
        function cancelEdit() {
            if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
                editMode = false;
                document.body.classList.remove('edit-mode');
                document.getElementById('editBtn').style.display = 'inline-block';
                document.getElementById('saveBtn').style.display = 'none';
                document.getElementById('cancelBtn').style.display = 'none';

                // Restore original content
                const cvContainer = document.querySelector('.cv-container');
                cvContainer.innerHTML = originalData.html;
            }
        }

        // Show add buttons in edit mode
        function showAddButtons() {
            const addButtons = document.querySelectorAll('#addWorkExpBtn, #addWorkExpHistBtn, #addCertBtn, #addProjectBtn, #addSkillsBtn');
            addButtons.forEach(btn => {
                btn.style.display = 'inline-block';
            });
        }

        // Hide add buttons in view mode
        function hideAddButtons() {
            const addButtons = document.querySelectorAll('#addWorkExpBtn, #addWorkExpHistBtn, #addCertBtn, #addProjectBtn, #addSkillsBtn');
            addButtons.forEach(btn => {
                btn.style.display = 'none';
            });
        }

        // Make personal information editable
        function makePersonalInfoEditable() {
            const personalSection = document.querySelector('.section:nth-child(1)');
            const infoGrid = personalSection.querySelector('.info-grid');

            // Add edit button to section
            const editBtn = document.createElement('button');
            editBtn.className = 'section-edit-btn';
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editBtn.onclick = () => editPersonalInfo();
            personalSection.appendChild(editBtn);
        }

        // Edit personal information
        function editPersonalInfo() {
            const section = document.querySelector('.section:nth-child(1)');
            const infoGrid = section.querySelector('.info-grid');

            // Create form
            const form = document.createElement('form');
            form.innerHTML = `
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname'] . ' ' . $user['suffixname']); ?>" id="fullName">
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select class="form-select" id="gender">
                        <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Academic Program:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? 'N/A'); ?>" id="academicProgram">
                </div>
                <div class="form-group">
                    <label>Academic Honors:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['academic_honor'] ?? ''); ?>" id="academicHonor">
                </div>
                <div class="form-group">
                    <button type="button" class="add-item-btn" onclick="savePersonalInfo()">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="cancelPersonalInfoEdit()">Cancel</button>
                </div>
            `;

            infoGrid.innerHTML = '';
            infoGrid.appendChild(form);
        }

        // Save personal information
        function savePersonalInfo() {
            const formData = new FormData();
            formData.append('action', 'update_personal_info');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('full_name', document.getElementById('fullName').value);
            formData.append('gender', document.getElementById('gender').value);
            formData.append('academic_program', document.getElementById('academicProgram').value);
            formData.append('academic_honor', document.getElementById('academicHonor').value);

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Personal information updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating personal information: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating personal information');
            });
        }

        // Cancel personal info edit
        function cancelPersonalInfoEdit() {
            location.reload();
        }

        // Make work experience editable
        function makeWorkExperienceEditable() {
            const workSection = document.querySelector('.section:nth-child(2)');
            const editBtn = document.createElement('button');
            editBtn.className = 'section-edit-btn';
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editBtn.onclick = () => editWorkExperience();
            workSection.appendChild(editBtn);
        }

        // Edit work experience
        function editWorkExperience() {
            const section = document.querySelector('.section:nth-child(2)');
            const content = section.querySelector('.experience-item, p');

            // Create form for work experience
            const form = document.createElement('div');
            form.innerHTML = `
                <div class="form-group">
                    <label>Employment Status:</label>
                    <select class="form-select" id="employmentStatus">
                        <option value="Employed" <?php echo $user['employment_status'] === 'Employed' ? 'selected' : ''; ?>>Employed</option>
                        <option value="Self-employed" <?php echo $user['employment_status'] === 'Self-employed' ? 'selected' : ''; ?>>Self-employed</option>
                        <option value="Business Owner" <?php echo $user['employment_status'] === 'Business Owner' ? 'selected' : ''; ?>>Business Owner</option>
                        <option value="Unemployed" <?php echo $user['employment_status'] === 'Unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                    </select>
                </div>
                <div class="form-group" id="companyFields" style="display: <?php echo !empty($user['employment_status']) && $user['employment_status'] !== 'Unemployed' ? 'block' : 'none'; ?>;">
                    <label>Company/Organization:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['connected_to'] ?? ''); ?>" id="companyName">
                </div>
                <div class="form-group" id="companyAddressFields" style="display: <?php echo !empty($user['employment_status']) && $user['employment_status'] !== 'Unemployed' ? 'block' : 'none'; ?>;">
                    <label>Company Address:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['company_address'] ?? ''); ?>" id="companyAddress">
                </div>
                <div class="form-group" id="companyEmailFields" style="display: <?php echo !empty($user['employment_status']) && $user['employment_status'] !== 'Unemployed' ? 'block' : 'none'; ?>;">
                    <label>Company Email:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['company_email'] ?? ''); ?>" id="companyEmail">
                </div>
                <div class="form-group">
                    <button type="button" class="add-item-btn" onclick="saveWorkExperience()">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="cancelWorkExperienceEdit()">Cancel</button>
                </div>
            `;

            content.innerHTML = '';
            content.appendChild(form);

            // Add event listener for employment status change
            document.getElementById('employmentStatus').addEventListener('change', function() {
                const companyFields = document.getElementById('companyFields');
                const companyAddressFields = document.getElementById('companyAddressFields');
                const companyEmailFields = document.getElementById('companyEmailFields');

                if (this.value === 'Unemployed') {
                    companyFields.style.display = 'none';
                    companyAddressFields.style.display = 'none';
                    companyEmailFields.style.display = 'none';
                } else {
                    companyFields.style.display = 'block';
                    companyAddressFields.style.display = 'block';
                    companyEmailFields.style.display = 'block';
                }
            });
        }

        // Save work experience
        function saveWorkExperience() {
            const formData = new FormData();
            formData.append('action', 'update_work_experience');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('employment_status', document.getElementById('employmentStatus').value);
            formData.append('company_name', document.getElementById('companyName')?.value || '');
            formData.append('company_address', document.getElementById('companyAddress')?.value || '');
            formData.append('company_email', document.getElementById('companyEmail')?.value || '');

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Work experience updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating work experience: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating work experience');
            });
        }

        // Cancel work experience edit
        function cancelWorkExperienceEdit() {
            location.reload();
        }

        // Make education editable
        function makeEducationEditable() {
            const educationSection = document.querySelector('.section:nth-child(3)');
            const editBtn = document.createElement('button');
            editBtn.className = 'section-edit-btn';
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editBtn.onclick = () => editEducation();
            educationSection.appendChild(editBtn);
        }

        // Edit education
        function editEducation() {
            const section = document.querySelector('.section:nth-child(3)');
            const content = section.querySelector('.education-item');

            // Create form for education
            const form = document.createElement('div');
            form.innerHTML = `
                <div class="form-group">
                    <label>Degree/Program:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? ''); ?>" id="degree">
                </div>
                <div class="form-group">
                    <label>Institution:</label>
                    <input type="text" class="form-control" value="University/School" id="institution">
                </div>
                <div class="form-group">
                    <label>Graduation Year:</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['batch']); ?>" id="graduationYear">
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea class="form-control" rows="3" id="educationDescription">Completed academic program with specialization in <?php echo htmlspecialchars($user['course_name'] ?? $user['strand_name'] ?? 'the chosen field'); ?>. <?php echo !empty($user['academic_honor']) ? 'Graduated with ' . htmlspecialchars($user['academic_honor']) . ' honors.' : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <button type="button" class="add-item-btn" onclick="saveEducation()">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="cancelEducationEdit()">Cancel</button>
                </div>
            `;

            content.innerHTML = '';
            content.appendChild(form);
        }

        // Save education
        function saveEducation() {
            const formData = new FormData();
            formData.append('action', 'update_education');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('degree', document.getElementById('degree').value);
            formData.append('institution', document.getElementById('institution').value);
            formData.append('graduation_year', document.getElementById('graduationYear').value);
            formData.append('description', document.getElementById('educationDescription').value);

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Education updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating education: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating education');
            });
        }

        // Cancel education edit
        function cancelEducationEdit() {
            location.reload();
        }

        // Make skills editable
        function makeSkillsEditable() {
            const skillsSection = document.querySelector('.section:nth-child(4)');
            const editBtn = document.createElement('button');
            editBtn.className = 'section-edit-btn';
            editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit';
            editBtn.onclick = () => editSkills();
            skillsSection.appendChild(editBtn);
        }

        // Edit skills
        function editSkills() {
            const section = document.querySelector('.section:nth-child(4)');
            const skillsList = section.querySelector('.skills-list');

            // Create form for skills
            const form = document.createElement('div');
            form.innerHTML = `
                <div class="form-group">
                    <label>Skills (one per line):</label>
                    <textarea class="form-control" rows="5" id="skillsInput" placeholder="Academic Excellence
Professional Communication
Leadership
Problem Solving
Team Collaboration
Project Management
Research & Analysis
Digital Literacy"></textarea>
                </div>
                <div class="form-group">
                    <button type="button" class="add-item-btn" onclick="saveSkills()">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="cancelSkillsEdit()">Cancel</button>
                </div>
            `;

            skillsList.innerHTML = '';
            skillsList.appendChild(form);
        }

        // Save skills
        function saveSkills() {
            const skillsText = document.getElementById('skillsInput').value;
            const skills = skillsText.split('\n').filter(skill => skill.trim() !== '');

            const formData = new FormData();
            formData.append('action', 'update_skills');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('skills', JSON.stringify(skills));

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Skills updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating skills: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating skills');
            });
        }

        // Add work experience
        function addWorkExperience() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Work Experience</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="workExperienceForm">
                                <div class="form-group">
                                    <label>Company Name:</label>
                                    <input type="text" class="form-control" id="workCompany" required>
                                </div>
                                <div class="form-group">
                                    <label>Position:</label>
                                    <input type="text" class="form-control" id="workPosition" required>
                                </div>
                                <div class="form-group">
                                    <label>Location:</label>
                                    <input type="text" class="form-control" id="workLocation">
                                </div>
                                <div class="form-group">
                                    <label>Start Date:</label>
                                    <input type="date" class="form-control" id="workStartDate" required>
                                </div>
                                <div class="form-group">
                                    <label>End Date:</label>
                                    <input type="date" class="form-control" id="workEndDate">
                                    <small class="form-text text-muted">Leave empty if currently working here</small>
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea class="form-control" rows="3" id="workDescription"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveWorkExperience()">Save</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        // Save work experience
        function saveWorkExperience() {
            const formData = new FormData();
            formData.append('action', 'add_work_experience');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('company_name', document.getElementById('workCompany').value);
            formData.append('position', document.getElementById('workPosition').value);
            formData.append('location', document.getElementById('workLocation').value);
            formData.append('start_date', document.getElementById('workStartDate').value);
            formData.append('end_date', document.getElementById('workEndDate').value);
            formData.append('description', document.getElementById('workDescription').value);

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Work experience added successfully!');
                    location.reload();
                } else {
                    alert('Error adding work experience: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding work experience');
            });
        }

        // Remove work experience
        function removeWorkExperience(id) {
            if (confirm('Are you sure you want to remove this work experience?')) {
                const formData = new FormData();
                formData.append('action', 'remove_work_experience');
                formData.append('id', id);

                fetch('update_cv.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Work experience removed successfully!');
                        location.reload();
                    } else {
                        alert('Error removing work experience: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing work experience');
                });
            }
        }

        // ============================================
        // EDUCATION CRUD OPERATIONS
        // ============================================

        // Add education
        function addEducation() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Education</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="educationForm">
                                <div class="form-group">
                                    <label>Degree/Program:</label>
                                    <input type="text" class="form-control" id="eduDegree" placeholder="e.g., Bachelor of Science in Computer Science" required>
                                </div>
                                <div class="form-group">
                                    <label>School/University:</label>
                                    <input type="text" class="form-control" id="eduSchool" placeholder="e.g., Misamis Oriental Institute of Science and Technology" required>
                                </div>
                                <div class="form-group">
                                    <label>Location:</label>
                                    <input type="text" class="form-control" id="eduLocation" placeholder="e.g., Cagayan de Oro City">
                                </div>
                                <div class="form-group">
                                    <label>Start Year:</label>
                                    <input type="date" class="form-control" id="eduStartDate" required>
                                </div>
                                <div class="form-group">
                                    <label>End Year:</label>
                                    <input type="date" class="form-control" id="eduEndDate">
                                    <small class="form-text text-muted">Leave empty if currently studying</small>
                                </div>
                                <div class="form-group">
                                    <label>GPA (Optional):</label>
                                    <input type="text" class="form-control" id="eduGPA" placeholder="e.g., 3.8/4.0">
                                </div>
                                <div class="form-group">
                                    <label>Description/Achievements:</label>
                                    <textarea class="form-control" rows="3" id="eduDescription" placeholder="e.g., Dean's List, Honors, Activities"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveEducation()">Save</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        // Save education
        function saveEducation() {
            const degree = document.getElementById('eduDegree').value;
            const school = document.getElementById('eduSchool').value;
            const location = document.getElementById('eduLocation').value;
            const startDate = document.getElementById('eduStartDate').value;
            const endDate = document.getElementById('eduEndDate').value;
            const gpa = document.getElementById('eduGPA').value;
            const description = document.getElementById('eduDescription').value;

            if (!degree || !school || !startDate) {
                alert('Please fill in all required fields');
                return;
            }

            const eduData = {
                type: 'education',
                degree: degree,
                school: school,
                location: location,
                gpa: gpa,
                description: description
            };

            const formData = new FormData();
            formData.append('action', 'add_education');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('position', degree);
            formData.append('company_name', school);
            formData.append('location', location);
            formData.append('date_start', startDate);
            formData.append('date_end', endDate || '0000-00-00');
            formData.append('description', JSON.stringify(eduData));

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Education added successfully!');
                    location.reload();
                } else {
                    alert('Error adding education: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding education');
            });
        }

        // Remove education
        function removeEducation(id) {
            if (confirm('Are you sure you want to remove this education entry?')) {
                const formData = new FormData();
                formData.append('action', 'remove_education');
                formData.append('id', id);

                fetch('update_cv.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Education removed successfully!');
                        location.reload();
                    } else {
                        alert('Error removing education: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing education');
                });
            }
        }

        // Add certification
        function addCertification() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Certification</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="certificationForm">
                                <div class="form-group">
                                    <label>Certification Name:</label>
                                    <input type="text" class="form-control" id="certName" required>
                                </div>
                                <div class="form-group">
                                    <label>Issuing Organization:</label>
                                    <input type="text" class="form-control" id="certIssuer" required>
                                </div>
                                <div class="form-group">
                                    <label>Issue Date:</label>
                                    <input type="date" class="form-control" id="certIssueDate" required>
                                </div>
                                <div class="form-group">
                                    <label>Expiry Date:</label>
                                    <input type="date" class="form-control" id="certExpiryDate">
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea class="form-control" rows="3" id="certDescription"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveCertification()">Save</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        // Save certification
        function saveCertification() {
            const formData = new FormData();
            formData.append('action', 'add_certification');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('certification_name', document.getElementById('certName').value);
            formData.append('issuer', document.getElementById('certIssuer').value);
            formData.append('issue_date', document.getElementById('certIssueDate').value);
            formData.append('expiry_date', document.getElementById('certExpiryDate').value);
            formData.append('description', document.getElementById('certDescription').value);

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Certification added successfully!');
                    location.reload();
                } else {
                    alert('Error adding certification: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding certification');
            });
        }

        // Remove certification
        function removeCertification(id) {
            if (confirm('Are you sure you want to remove this certification?')) {
                const formData = new FormData();
                formData.append('action', 'remove_certification');
                formData.append('id', id);

                fetch('update_cv.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Certification removed successfully!');
                        location.reload();
                    } else {
                        alert('Error removing certification: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing certification');
                });
            }
        }

        // Add project
        function addProject() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="projectForm">
                                <div class="form-group">
                                    <label>Project Name:</label>
                                    <input type="text" class="form-control" id="projectName" required>
                                </div>
                                <div class="form-group">
                                    <label>Technologies Used:</label>
                                    <input type="text" class="form-control" id="projectTech" placeholder="HTML, CSS, JavaScript, PHP">
                                </div>
                                <div class="form-group">
                                    <label>Start Date:</label>
                                    <input type="date" class="form-control" id="projectStartDate" required>
                                </div>
                                <div class="form-group">
                                    <label>End Date:</label>
                                    <input type="date" class="form-control" id="projectEndDate">
                                    <small class="form-text text-muted">Leave empty if ongoing project</small>
                                </div>
                                <div class="form-group">
                                    <label>Project URL:</label>
                                    <input type="url" class="form-control" id="projectUrl" placeholder="https://github.com/username/project">
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea class="form-control" rows="4" id="projectDescription" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveProject()">Save</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        // Save project
        function saveProject() {
            const formData = new FormData();
            formData.append('action', 'add_project');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('project_name', document.getElementById('projectName').value);
            formData.append('technologies', document.getElementById('projectTech').value);
            formData.append('start_date', document.getElementById('projectStartDate').value);
            formData.append('end_date', document.getElementById('projectEndDate').value);
            formData.append('url', document.getElementById('projectUrl').value);
            formData.append('description', document.getElementById('projectDescription').value);

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Project added successfully!');
                    location.reload();
                } else {
                    alert('Error adding project: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding project');
            });
        }

        // Remove project
        function removeProject(id) {
            if (confirm('Are you sure you want to remove this project?')) {
                const formData = new FormData();
                formData.append('action', 'remove_project');
                formData.append('id', id);

                fetch('update_cv.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Project removed successfully!');
                        location.reload();
                    } else {
                        alert('Error removing project: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing project');
                });
            }
        }

        // Add skills
        function addSkills() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Skills</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="skillsForm">
                                <div class="form-group">
                                    <label>Skills (one per line):</label>
                                    <textarea class="form-control" rows="8" id="skillsText" placeholder="Academic Excellence
Professional Communication
Leadership
Problem Solving
Team Collaboration
Project Management
Research & Analysis
Digital Literacy
Web Development
Database Management
UI/UX Design
Data Analysis
Content Writing
Public Speaking
Time Management
Critical Thinking
Creative Problem Solving
Technical Writing
Customer Service
Marketing Strategy
Social Media Management"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveSkills()">Save</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        // Save skills
        function saveSkills() {
            const skillsText = document.getElementById('skillsText').value;
            const skills = skillsText.split('\n').filter(skill => skill.trim() !== '');

            const formData = new FormData();
            formData.append('action', 'update_skills');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('skills', JSON.stringify(skills));

            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Skills updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating skills: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating skills');
            });
        }

        // Add Reference Function
        function addReference() {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="fas fa-user-friends"></i> Add Reference</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="referenceForm">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="refName" placeholder="e.g., Dr. John Smith" required>
                                        <small class="text-muted">Enter the full name of your reference</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Position/Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="refPosition" placeholder="e.g., Senior Manager" required>
                                        <small class="text-muted">Their job title or position</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Company/Organization</label>
                                        <input type="text" class="form-control" id="refCompany" placeholder="e.g., ABC Corporation">
                                        <small class="text-muted">Where they work</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="refEmail" placeholder="e.g., john.smith@company.com" required>
                                        <small class="text-muted">Professional email address</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="refPhone" placeholder="e.g., +63 912 345 6789" required>
                                        <small class="text-muted">Contact number</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Relationship <span class="text-danger">*</span></label>
                                        <select class="form-select" id="refRelationship" required>
                                            <option value="">Select relationship...</option>
                                            <option value="Former Supervisor">Former Supervisor</option>
                                            <option value="Current Supervisor">Current Supervisor</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Colleague">Colleague</option>
                                            <option value="Professor">Professor</option>
                                            <option value="Academic Advisor">Academic Advisor</option>
                                            <option value="Mentor">Mentor</option>
                                            <option value="Client">Client</option>
                                            <option value="Business Partner">Business Partner</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <small class="text-muted">Your professional relationship with this person</small>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> <strong>Tip:</strong> Choose references who can speak positively about your work ethic, skills, and professional character. Always ask for permission before listing someone as a reference.
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-primary" onclick="saveReference()">
                                <i class="fas fa-save"></i> Save Reference
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        // Save Reference Function
        function saveReference() {
            const name = document.getElementById('refName').value.trim();
            const position = document.getElementById('refPosition').value.trim();
            const company = document.getElementById('refCompany').value.trim();
            const email = document.getElementById('refEmail').value.trim();
            const phone = document.getElementById('refPhone').value.trim();
            const relationship = document.getElementById('refRelationship').value;

            // Validation
            if (!name || !position || !email || !phone || !relationship) {
                alert('Please fill in all required fields (marked with *)');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }

            // Phone validation (basic)
            if (phone.length < 10) {
                alert('Please enter a valid phone number');
                return;
            }

            // Create reference data object
            const referenceData = {
                type: 'reference',
                name: name,
                position: position,
                company: company,
                email: email,
                phone: phone,
                relationship: relationship
            };

            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'add_reference');
            formData.append('user_id', '<?php echo $userId; ?>');
            formData.append('reference_data', JSON.stringify(referenceData));

            // Show loading state
            const saveBtn = event.target;
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            // Send to server
            fetch('update_cv.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reference added successfully!');
                    location.reload();
                } else {
                    alert('Error adding reference: ' + data.message);
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding reference. Please try again.');
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        }

        // Remove Reference Function
        function removeReference(id) {
            if (confirm('Are you sure you want to remove this reference?')) {
                const formData = new FormData();
                formData.append('action', 'remove_reference');
                formData.append('id', id);

                fetch('update_cv.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reference removed successfully!');
                        location.reload();
                    } else {
                        alert('Error removing reference: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing reference');
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            storeOriginalData();
        });
    </script>
</body>
</html>
