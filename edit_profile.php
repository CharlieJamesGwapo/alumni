<?php
// edit_profile.php - standalone profile edit page (no modal)
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['login_id'];

// Get user data with employment info
$stmt = $conn->prepare("SELECT a.*, c.course as course_name 
                       FROM alumnus_bio a 
                       LEFT JOIN courses c ON a.course_id = c.id 
                       WHERE a.id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get employment history from JSON field
$employment_history = [];
if (!empty($user['employment_history'])) {
    $employment_history = json_decode($user['employment_history'], true) ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    :root {
        --primary: #800000;
        --secondary: #6c757d;
    }
    .profile-section {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }
    .profile-header {
        display: flex;
        align-items: flex-start;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 12px;
        object-fit: cover;
    }
    .no-avatar {
        background: #f8f9fa;
        color: #adb5bd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }
    .timeline-item {
        padding: 1.5rem;
        border-left: 3px solid var(--primary);
        margin-bottom: 1rem;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
    }
    .image-upload-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.5);
        padding: 8px;
        text-align: center;
        border-radius: 0 0 12px 12px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .profile-header .position-relative:hover .image-upload-overlay {
        opacity: 1;
    }

    .image-upload-overlay label {
        margin: 0;
        cursor: pointer;
    }
    
    /* Loading overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .loading-overlay.active {
        display: flex;
    }
    
    .loading-spinner {
        text-align: center;
        color: white;
    }
    
    .loading-spinner .spinner-border {
        width: 3rem;
        height: 3rem;
    }
    </style>
</head>
<body class="bg-light">

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner-border text-light" role="status"></div>
        <p class="mt-3">Updating profile...</p>
    </div>
</div>

<div class="container py-5">
    <div class="profile-section">
        <h3 class="mb-4">Profile Information</h3>
        
        <form id="profileForm" enctype="multipart/form-data">
            <!-- Profile Image Section -->
            <div class="profile-header">
                <div class="position-relative">
                    <img src="<?php echo !empty($user['img']) ? 'uploads/'.$user['img'] : 'assets/img/default_avatar.jpg' ?>" 
                         id="profilePreview" 
                         class="profile-avatar <?php echo empty($user['img']) ? 'no-avatar' : '' ?>"
                         alt="Profile Image">
                    <div class="image-upload-overlay">
                        <label for="profileImage" class="btn btn-sm btn-light">
                            <i class="fas fa-camera"></i> Change Photo
                        </label>
                        <input type="file" 
                               id="profileImage" 
                               name="profileImage" 
                               class="d-none" 
                               accept="image/*">
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h4><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h4>
                    <p class="text-muted">
                        <?php echo htmlspecialchars($user['course_name']) ?> - Batch <?php echo htmlspecialchars($user['batch']) ?>
                    </p>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="firstname" value="<?php echo htmlspecialchars($user['firstname']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lastname" value="<?php echo htmlspecialchars($user['lastname']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="middlename" value="<?php echo htmlspecialchars($user['middlename']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact</label>
                    <input type="text" class="form-control" name="contact_no" value="<?php echo htmlspecialchars($user['contact_no']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender">
                        <option value="Male" <?php echo $user['gender']=='Male'?'selected':''; ?>>Male</option>
                        <option value="Female" <?php echo $user['gender']=='Female'?'selected':''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Birthdate</label>
                    <input type="date" class="form-control" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']) ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($user['address']) ?>">
                </div>
            </div>

            <!-- Employment Status -->
            <div class="mb-4">
                <h4 class="mb-3">Employment Status</h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Current Status</label>
                        <select class="form-select" name="employment_status" id="employmentStatus">
                            <option value="employed" <?php echo $user['employment_status']=='employed'?'selected':''; ?>>Employed</option>
                            <option value="not employed" <?php echo $user['employment_status']=='not employed'?'selected':''; ?>>Not Employed</option>
                            <option value="self-employed" <?php echo $user['employment_status']=='self-employed'?'selected':''; ?>>Self-Employed</option>
                            <option value="student" <?php echo $user['employment_status']=='student'?'selected':''; ?>>Student</option>
                        </select>
                    </div>
                    <div class="col-md-6 employment-field">
                        <label class="form-label">Industry Type</label>
                        <input type="text" class="form-control" name="connected_to" value="<?php echo htmlspecialchars($user['connected_to']) ?>">
                    </div>
                    <div class="col-md-6 employment-field">
                        <label class="form-label">Company Address</label>
                        <input type="text" class="form-control" name="company_address" value="<?php echo htmlspecialchars($user['company_address']) ?>">
                    </div>
                    <div class="col-md-6 employment-field">
                        <label class="form-label">Company Email</label>
                        <input type="email" class="form-control" name="company_email" value="<?php echo htmlspecialchars($user['company_email']) ?>">
                    </div>
                </div>
            </div>

            <!-- Employment History -->
            <div class="mb-4"></div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Employment History</h4>
                    <button type="button" class="btn btn-primary btn-sm" id="addHistoryBtn">
                        <i class="fas fa-plus"></i> Add Entry
                    </button>
                </div>

                <div id="employmentHistory">
                    <?php foreach ($employment_history as $idx => $history): ?>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Started: <?php echo htmlspecialchars($history['date_started']); ?></strong>
                                <span>Duration: <?php echo htmlspecialchars($history['duration']); ?></span>
                            </div>
                            <?php if (!empty($history['connected_to'])): ?>
                                <div>Industry: <?php echo htmlspecialchars($history['connected_to']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($history['company_address'])): ?>
                                <div>Company Address: <?php echo htmlspecialchars($history['company_address']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($history['company_email'])): ?>
                                <div>Company Email: <?php echo htmlspecialchars($history['company_email']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employment History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="historyForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="date_started" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Industry Type</label>
                        <input type="text" class="form-control" name="connected_to">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company Address</label>
                        <input type="text" class="form-control" name="company_address">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company Email</label>
                        <input type="email" class="form-control" name="company_email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    const historyModal = new bootstrap.Modal('#historyModal');
    
    // Toggle employment fields based on status
    $('#employmentStatus').change(function() {
        const isEmployed = ['employed', 'self-employed'].includes($(this).val());
        $('.employment-field').toggle(isEmployed);
    }).trigger('change');

    // Handle adding new history entry
    $('#addHistoryBtn').click(() => historyModal.show());

    $('#historyForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Calculate duration
        const startDate = new Date(formData.get('date_started'));
        const now = new Date();
        const months = (now.getFullYear() - startDate.getFullYear()) * 12 + 
                      (now.getMonth() - startDate.getMonth());
        
        let duration;
        if (months < 1) {
            duration = 'Less than a month';
        } else if (months < 12) {
            duration = `${months} months`;
        } else {
            const years = Math.floor(months / 12);
            const remainingMonths = months % 12;
            duration = `${years} year${years > 1 ? 's' : ''}${remainingMonths ? ' ' + remainingMonths + ' months' : ''}`;
        }

        // Add duration to form data
        formData.append('duration', duration);

        $.ajax({
            url: 'ajax/add_employment_history.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Employment history added successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to add entry',
                        confirmButtonColor: '#800000'
                    });
                }
            }
        });
    });

    // Image preview functionality
    $('#profileImage').change(function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePreview')
                    .attr('src', e.target.result)
                    .removeClass('no-avatar');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Enhanced form submission with image upload and real-time updates
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        
        // Disable submit button
        const $saveBtn = $('#saveBtn');
        $saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Show loading overlay
        $('#loadingOverlay').addClass('active');
        
        const formData = new FormData(this);

        $.ajax({
            url: 'update_profile.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                // Hide loading overlay
                $('#loadingOverlay').removeClass('active');
                $saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
                
                if (response.success) {
                    // Real-time update: Update profile display immediately
                    if (response.userData) {
                        const fullName = response.userData.firstname + ' ' + response.userData.lastname;
                        $('h4').first().text(fullName);
                        
                        // Update image if changed
                        if (response.imageUpdated && response.userData.img) {
                            $('#profilePreview').attr('src', 'uploads/' + response.userData.img);
                        }
                    }
                    
                    // Notify parent window if in iframe
                    if (window.parent && window.parent !== window) {
                        window.parent.postMessage({
                            type: 'profileUpdated',
                            success: true,
                            userData: response.userData
                        }, '*');
                    }
                    
                    // Show success notification with countdown and redirect
                    let timerInterval;
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated Successfully!',
                        html: '<p>' + response.message + '</p>' +
                              '<p class="text-muted">Your changes have been saved.</p>' +
                              '<p class="mt-3"><strong>Redirecting to home in <b></b> seconds...</strong></p>',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'Go Now',
                        confirmButtonColor: '#800000',
                        allowOutsideClick: false,
                        didOpen: () => {
                            const b = Swal.getHtmlContainer().querySelector('b');
                            timerInterval = setInterval(() => {
                                const timeLeft = Math.ceil(Swal.getTimerLeft() / 1000);
                                b.textContent = timeLeft;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    }).then((result) => {
                        // Redirect to home page
                        window.location.href = 'home.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: response.message || 'Failed to update profile',
                        confirmButtonColor: '#800000'
                    });
                }
            },
            error: function(xhr, status, error) {
                // Hide loading overlay
                $('#loadingOverlay').removeClass('active');
                $saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
                
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'An error occurred while updating profile. Please check your connection and try again.',
                    confirmButtonColor: '#800000'
                });
            }
        });
    });
});
</script>

</body>
</html>
