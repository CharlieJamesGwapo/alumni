
$(document).ready(function(){
    let formChanged = false;
    let alumniVerified = false;
    let verifiedAlumniId = null;
    let verifiedData = null;

    // Track form changes
    $('#create_account :input').on('change input', function() {
        formChanged = true;
    });

    // Function to disable/enable personal info fields
    function togglePersonalInfoFields(disabled) {
        $('[name="lastname"], [name="firstname"], [name="middlename"], [name="suffixname"], [name="birthdate"], [name="gender"], [name="batch"], [name="course_id"]').prop('disabled', disabled);
    }

    // Initially disable the fields
    togglePersonalInfoFields(true);

    // Handle Alumni ID verification and program type detection
    $('#alumni_id').on('change keyup', function() {
        const alumni_id = $(this).val().trim().toUpperCase();
        $(this).val(alumni_id);

        const isSHS = alumni_id.startsWith('SHS-');

        if (isSHS) {
            $('#programLabel').text('Strand Graduated');
            $('#courseSection').hide();
            $('#strandSection').show();
            $('#employmentSection').slideUp();
            $('.program-type-badge').html('<span class="badge badge-info"><i class="fas fa-graduation-cap"></i> Senior High School</span>');
            $('#employmentSection input').val('');
        } else {
            $('#programLabel').text('(College)');
            $('#courseSection').show();
            $('#strandSection').hide();
            $('#employmentSection').slideDown();
            $('.program-type-badge').html('<span class="badge badge-primary"><i class="fas fa-university"></i> College</span>');
        }

        if (!alumni_id) {
            $('#alumni_id_status').html('');
            alumniVerified = false;
            verifiedAlumniId = null;
            verifiedData = null;
            togglePersonalInfoFields(true);
            return;
        }

        $('#alumni_id_status').html('<div class="alert alert-info mt-2"><i class="fas fa-spinner fa-spin"></i> Verifying Alumni ID...</div>');

        $.ajax({
            url: 'verify_alumni.php',
            method: 'POST',
            data: { alumni_id: alumni_id },
            dataType: 'json',
            cache: false
        })
        .done(function(response) {
            if (response.status === 'success') {
                const isSHS = response.data.program_type === 'Senior High';

                if(isSHS) {
                    $('#programLabel').text('Strand Graduated');
                    $('#courseSection').hide();
                    $('#strandSection').show().find('select').prop('disabled', false);
                    $('#employmentSection').slideUp();
                    $('.program-type-badge').html('<span class="badge badge-info"><i class="fas fa-graduation-cap"></i> Senior High School</span>');
                    $('#employmentSection input').val('');
                    if (response.data.strand_id) {
                        $('select[name="strand_id"]').val(response.data.strand_id).trigger('change');
                    }
                } else {
                    $('#programLabel').text('Course Graduated');
                    $('#courseSection').show().find('select').prop('disabled', false);
                    $('#strandSection').hide();
                    $('#employmentSection').slideDown();
                    $('.program-type-badge').html('<span class="badge badge-primary"><i class="fas fa-university"></i> College</span>');
                    if (response.data.course_id) {
                        $('select[name="course_id"]').val(response.data.course_id).trigger('change');
                    }
                    if (response.data.course_name) {
                        $('#courseName').text(response.data.course_name);
                        $('#courseInfo').show();
                    }
                    if (Array.isArray(response.data.majors) && response.data.majors.length > 0) {
                        var $majorSelect = $('#majorSelect');
                        $majorSelect.empty().append('<option value="">Select major</option>');
                        response.data.majors.forEach(function(m){
                            $majorSelect.append($('<option/>').attr('value', m.id).text(m.major));
                        });
                        $('#majorContainer').show();
                        $('#majorInfo').hide();

                        if (response.data.selected_major) {
                            $majorSelect.val(response.data.selected_major.id).trigger('change');
                            $('#majorText').text(response.data.selected_major.major || '');
                            $('#majorAbout').text(response.data.selected_major.about || '');
                            $('#majorSelected').show();
                            $('#majorContainer').hide();
                            if ($('#hidden_major_id').length === 0) {
                                $('<input>').attr({type: 'hidden', id: 'hidden_major_id', name: 'major_id', value: response.data.selected_major.id}).appendTo('#create_account');
                            } else {
                                $('#hidden_major_id').val(response.data.selected_major.id);
                            }
                        } else if (response.data.major_id) {
                            $majorSelect.val(response.data.major_id).trigger('change');
                            var match = response.data.majors.find(function(m){ return m.id == response.data.major_id; });
                            if (match) {
                                $('#majorText').text(match.major || '');
                                $('#majorAbout').text(match.about || '');
                                $('#majorSelected').show();
                                $('#majorContainer').hide();
                                if ($('#hidden_major_id').length === 0) {
                                    $('<input>').attr({type: 'hidden', id: 'hidden_major_id', name: 'major_id', value: match.id}).appendTo('#create_account');
                                } else {
                                    $('#hidden_major_id').val(match.id);
                                }
                            }
                        }
                    } else {
                        $('#majorInfo').html('');
                    }
                }

                handleVerificationSuccess(response.data);
            } else {
                $('#alumni_id_status').html('<div class="alert alert-danger mt-2"><i class="fas fa-times-circle"></i> ' + response.message + '</div>');
                alumniVerified = false;
                verifiedAlumniId = null;
                verifiedData = null;
                togglePersonalInfoFields(true);
            }
        })
        .fail(function(xhr, status, error) {
            $('#alumni_id_status').html('<div class="alert alert-danger mt-2"><i class="fas fa-exclamation-circle"></i> Error verifying Alumni ID. Please try again.</div>');
            alumniVerified = false;
            verifiedAlumniId = null;
            verifiedData = null;
            togglePersonalInfoFields(true);
        });
    });

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({ placeholder: "Please select", width: '100%' });
    }

    // Initialize Year Picker
    if ($.fn.datepicker) {
        $('.datepickerY').datepicker({
            format: "yyyy", viewMode: "years", minViewMode: "years", autoclose: true
        });
    }

    // Real-time email validation
    $('#email').on('input', function() { validateEmail($(this)); });

    // Password strength indicator
    $('#password').on('input', function() { checkPasswordStrength($(this).val()); });

    // Show/Hide password
    $('#togglePassword').click(function() {
        var passwordField = $('#password');
        var icon = $(this).find('i');
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // ========================================
    // Profile Image - File Upload & Camera
    // ========================================

    // Auto-resize uploaded image
    $('#imgInput').on('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;

        // Validate type
        if (!file.type.match(/image\/(jpeg|jpg|png|gif|webp)/)) {
            showAlert('error', 'Invalid File', 'Please select a valid image file (JPG, PNG, GIF, WEBP).');
            $(this).val('');
            return;
        }

        // Validate size (max 10MB raw, will be compressed)
        if (file.size > 10 * 1024 * 1024) {
            showAlert('error', 'File Too Large', 'Please select an image under 10MB.');
            $(this).val('');
            return;
        }

        resizeAndPreviewImage(file);
    });

    function resizeAndPreviewImage(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function() {
                // Resize to max 800x800 while maintaining aspect ratio
                var canvas = document.createElement('canvas');
                var maxSize = 800;
                var width = img.width;
                var height = img.height;

                if (width > height) {
                    if (width > maxSize) {
                        height = Math.round(height * maxSize / width);
                        width = maxSize;
                    }
                } else {
                    if (height > maxSize) {
                        width = Math.round(width * maxSize / height);
                        height = maxSize;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Convert to blob and update the file input
                canvas.toBlob(function(blob) {
                    // Show preview
                    var previewUrl = URL.createObjectURL(blob);
                    $('#imgPreview').attr('src', previewUrl).show();
                    $('#imgPreviewContainer').show();

                    // Create new file from blob to replace the input
                    var resizedFile = new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() });

                    // Store resized file for form submission
                    window._resizedProfileImage = resizedFile;

                    // Show file size info
                    var sizeKB = Math.round(blob.size / 1024);
                    $('#imgSizeInfo').html('<small class="text-success"><i class="fas fa-check"></i> Image ready (' + sizeKB + 'KB, ' + width + 'x' + height + ')</small>').show();
                }, 'image/jpeg', 0.85);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Camera capture
    var cameraStream = null;

    $(document).on('click', '#openCameraBtn', function() {
        openCamera();
    });

    function openCamera() {
        var constraints = {
            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } },
            audio: false
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(function(stream) {
                cameraStream = stream;
                var video = document.getElementById('cameraVideo');
                if (!video) {
                    // Create camera UI dynamically
                    var cameraHtml = '<div id="cameraModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:9999;display:flex;flex-direction:column;align-items:center;justify-content:center;">' +
                        '<video id="cameraVideo" autoplay playsinline style="max-width:90%;max-height:60vh;border-radius:12px;"></video>' +
                        '<div style="margin-top:1rem;display:flex;gap:1rem;">' +
                        '<button type="button" id="captureBtn" class="btn btn-success btn-lg"><i class="fas fa-camera"></i> Capture</button>' +
                        '<button type="button" id="closeCameraBtn" class="btn btn-danger btn-lg"><i class="fas fa-times"></i> Close</button>' +
                        '</div></div>';
                    $('body').append(cameraHtml);
                    video = document.getElementById('cameraVideo');
                }
                video.srcObject = stream;
                $('#cameraModal').show();
            })
            .catch(function(err) {
                showAlert('error', 'Camera Error', 'Could not access camera: ' + err.message);
            });
    }

    $(document).on('click', '#captureBtn', function() {
        var video = document.getElementById('cameraVideo');
        var canvas = document.createElement('canvas');
        // Square crop from center
        var size = Math.min(video.videoWidth, video.videoHeight);
        canvas.width = 640;
        canvas.height = 640;
        var ctx = canvas.getContext('2d');
        var sx = (video.videoWidth - size) / 2;
        var sy = (video.videoHeight - size) / 2;
        ctx.drawImage(video, sx, sy, size, size, 0, 0, 640, 640);

        canvas.toBlob(function(blob) {
            var previewUrl = URL.createObjectURL(blob);
            $('#imgPreview').attr('src', previewUrl).show();
            $('#imgPreviewContainer').show();

            var capturedFile = new File([blob], 'camera_photo.jpg', { type: 'image/jpeg' });
            window._resizedProfileImage = capturedFile;

            var sizeKB = Math.round(blob.size / 1024);
            $('#imgSizeInfo').html('<small class="text-success"><i class="fas fa-check"></i> Photo captured (' + sizeKB + 'KB, 640x640)</small>').show();

            closeCamera();
        }, 'image/jpeg', 0.85);
    });

    $(document).on('click', '#closeCameraBtn', function() {
        closeCamera();
    });

    function closeCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(function(t) { t.stop(); });
            cameraStream = null;
        }
        $('#cameraModal').remove();
    }

    // ========================================
    // Form Submission
    // ========================================

    $('#create_account').submit(function(e){
        e.preventDefault();

        if(!alumniVerified) {
            showAlert('warning', 'Verify Alumni ID', 'Please verify your Alumni ID first before registering.');
            return false;
        }

        // Validate required fields
        var email = $('#email').val().trim();
        var password = $('#password').val();
        var address = $('#address').val().trim();

        if (!email || !password || !address) {
            showAlert('warning', 'Missing Fields', 'Please fill in Email, Password, and Address.');
            return false;
        }

        if (!validateEmail($('#email'))) {
            showAlert('warning', 'Invalid Email', 'Please enter a valid email address.');
            return false;
        }

        if (password.length < 6) {
            showAlert('warning', 'Weak Password', 'Password must be at least 6 characters long.');
            return false;
        }

        // Build FormData
        var formData = new FormData();

        // Add verified alumni data
        if(verifiedData) {
            Object.keys(verifiedData).forEach(function(key) {
                if (key !== 'majors') {
                    formData.append(key, verifiedData[key] || '');
                }
            });
        }

        // Add manual fields
        formData.append('alumni_id', $('#alumni_id').val());
        formData.append('email', email);
        formData.append('password', password);
        formData.append('address', address);
        formData.append('contact_no', $('#contact_no').val() || '');
        formData.append('company_name', $('#company_name').val() || '');
        formData.append('company_address', $('#company_address').val() || '');
        formData.append('company_email', $('#company_email').val() || '');

        // Add major_id if exists
        if ($('#hidden_major_id').length) {
            formData.append('major_id', $('#hidden_major_id').val());
        }

        // Add profile image (use resized version if available)
        if (window._resizedProfileImage) {
            formData.append('img', window._resizedProfileImage);
        } else {
            var imgInput = $('#imgInput')[0];
            if (imgInput && imgInput.files[0]) {
                formData.append('img', imgInput.files[0]);
            }
        }

        // Submit
        var submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating account...');

        $.ajax({
            url: 'admin/ajax.php?action=signup',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                submitBtn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> Create Account');
                try {
                    var response = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if(response.status === 'success') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Registration Successful!',
                                html: '<div class="text-center">' +
                                    '<p class="mt-2 mb-1">Your account has been created successfully!</p>' +
                                    '<p class="text-muted small">Please wait for the registrar to verify your account. You will receive an email notification once approved.</p>' +
                                    '</div>',
                                confirmButtonText: 'Go to Login',
                                confirmButtonColor: '#800000',
                                allowOutsideClick: false
                            }).then(function() {
                                window.location.href = 'login.php';
                            });
                        } else {
                            alert('Registration successful! Please wait for account verification.');
                            window.location.href = 'login.php';
                        }
                    } else {
                        showAlert('error', 'Registration Failed', response.message || 'An error occurred.');
                    }
                } catch(e) {
                    // Legacy response (plain text)
                    resp = (resp + '').trim();
                    if (resp == '1') {
                        alert('Account created successfully! Please wait for verification.');
                        window.location.href = 'login.php';
                    } else {
                        showAlert('error', 'Error', resp || 'Registration failed.');
                    }
                }
            },
            error: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> Create Account');
                showAlert('error', 'Server Error', 'Could not connect to server. Please try again.');
            }
        });
    });
});

// ========================================
// Helper Functions
// ========================================

function showAlert(icon, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: icon, title: title, text: text, confirmButtonColor: '#800000' });
    } else {
        alert(title + ': ' + text);
    }
}

function validateEmail(input) {
    var email = input.val();
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (emailRegex.test(email)) {
        input.removeClass('is-invalid').addClass('is-valid');
        return true;
    } else {
        input.removeClass('is-valid').addClass('is-invalid');
        return false;
    }
}

function checkPasswordStrength(password) {
    var strength = 0;
    var progressBar = $('#password_strength .progress-bar');

    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[!@#$%^&*]+/)) strength++;

    var percent = (strength / 5) * 100;
    progressBar.width(percent + '%');

    if (strength <= 2) {
        progressBar.removeClass().addClass('progress-bar bg-danger');
    } else if (strength <= 3) {
        progressBar.removeClass().addClass('progress-bar bg-warning');
    } else if (strength === 4) {
        progressBar.removeClass().addClass('progress-bar bg-info');
    } else {
        progressBar.removeClass().addClass('progress-bar bg-success');
    }
    return strength >= 3;
}

function handleVerificationSuccess(data) {
    $('#alumni_id_status').html('<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> Alumni ID verified successfully!</div>');
    window.alumniVerified = true;

    window.verifiedData = {
        lastname: data.lastname,
        firstname: data.firstname,
        middlename: data.middlename,
        suffixname: data.suffixname,
        birthdate: data.birthdate,
        gender: data.gender,
        batch: data.batch,
        course_id: data.course_id,
        course_name: data.course_name || '',
        majors: data.majors || []
    };

    // Auto-fill and lock personal info fields
    Object.keys(window.verifiedData).forEach(function(field) {
        var value = window.verifiedData[field];
        var element = $('[name="' + field + '"]');
        if(element.length && field !== 'majors') {
            element.val(value || '');
            if(element.is('select')) {
                element.prop('disabled', true);
            } else {
                element.prop('readonly', true);
            }
            element.addClass('is-valid');
        }
    });

    $('[name="course_id"]').trigger('change');

    // Scroll to email field
    $('html, body').animate({ scrollTop: $('#email').offset().top - 100 }, 500);
}

// Major select change handler
$(document).on('change', '#majorSelect', function(){
    var mid = $(this).val();
    if(!mid) {
        $('#majorSelected').hide();
        $('#hidden_major_id').remove();
        return;
    }
    var text = $(this).find('option:selected').text();
    $('#majorText').text(text);
    var about = '';
    if(window.verifiedData && Array.isArray(window.verifiedData.majors)){
        var m = window.verifiedData.majors.find(function(x){ return String(x.id) === String(mid); });
        if(m) about = m.about || '';
    }
    $('#majorAbout').text(about);
    $('#majorSelected').show();
    if($('#hidden_major_id').length === 0) {
        $('<input>').attr({type:'hidden', id:'hidden_major_id', name:'major_id', value: mid}).appendTo('#create_account');
    } else {
        $('#hidden_major_id').val(mid);
    }
});
