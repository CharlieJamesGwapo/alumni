
$(document).ready(function(){
    // Back button confirmation if form has changes
    let formChanged = false;
    let alumniVerified = false;
    let verifiedAlumniId = null;

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
        $(this).val(alumni_id); // Convert input to uppercase
        
        // Detect program type from ID prefix
        const isSHS = alumni_id.startsWith('SHS-');
        
        // Update UI based on program type
        if (isSHS) {
            $('#programLabel').text('Strand Graduated');
            $('#courseSection').hide();
            $('#strandSection').show();
            $('#employmentSection').slideUp();
            $('.program-type-badge').html('<span class="badge badge-info"><i class="fas fa-graduation-cap"></i> Senior High School</span>');
            // Clear employment fields
            $('#employmentSection input').val('');
        } else {
            $('#programLabel').text('(College)');
            $('#courseSection').show();
            $('#strandSection').hide();
            $('#employmentSection').slideDown();
            $('.program-type-badge').html('<span class="badge badge-primary"><i class="fas fa-university"></i> College</span>');
        }        if (!alumni_id) {
            $('#alumni_id_status').html('');
            alumniVerified = false;
            verifiedAlumniId = null;
            togglePersonalInfoFields(true);
            return;
        }

        // Show loading indicator
        $('#alumni_id_status').html('<div class="alert alert-info mt-2"><i class="fas fa-spinner fa-spin"></i> Verifying Alumni ID...</div>');
        
        // Make the AJAX call to verify alumni ID
        $.ajax({
            url: 'verify_alumni.php',
            method: 'POST',
            data: { alumni_id: alumni_id },
            dataType: 'json',
            cache: false
        })
        .done(function(response) {
            console.log('Server response:', response);
            
            if (response.status === 'success') {
                const isSHS = response.data.program_type === 'Senior High';
                
                // Update UI based on program type
                if(isSHS) {
                    $('#programLabel').text('Strand Graduated');
                    $('#courseSection').hide();
                    $('#strandSection').show().find('select').prop('disabled', false);
                    $('#employmentSection').slideUp();
                    $('.program-type-badge').html('<span class="badge badge-info"><i class="fas fa-graduation-cap"></i> Senior High School</span>');
                    // Clear employment fields
                    $('#employmentSection input').val('');
                    
                    // Set strand if available
                    if (response.data.strand_id) {
                        $('select[name="strand_id"]').val(response.data.strand_id).trigger('change');
                    }
                } else {
                    $('#programLabel').text('Course Graduated');
                    $('#courseSection').show().find('select').prop('disabled', false);
                    $('#strandSection').hide();
                    $('#employmentSection').slideDown();
                    $('.program-type-badge').html('<span class="badge badge-primary"><i class="fas fa-university"></i> College</span>');
                    
                    // Set course if available
                    if (response.data.course_id) {
                        $('select[name="course_id"]').val(response.data.course_id).trigger('change');
                    }
                    // Show course name and majors (if any)
                    if (response.data.course_name) {
                        $('#courseName').text(response.data.course_name);
                        $('#courseInfo').show();
                    }
                    // If majors returned, try to match/auto-select major by text (e.g., "BSED - English")
                    if (Array.isArray(response.data.majors) && response.data.majors.length > 0) {
                        // Populate majors select for better UX
                        const $majorSelect = $('#majorSelect');
                        $majorSelect.empty().append('<option value="">Select major</option>');
                        response.data.majors.forEach(function(m){
                            $majorSelect.append($('<option/>').attr('value', m.id).text(m.major));
                        });
                        $('#majorContainer').show();
                        $('#majorInfo').hide();

                        // If the server returned a selected_major or major_id, pre-select it in the select
                        if (response.data.selected_major) {
                            $majorSelect.val(response.data.selected_major.id).trigger('change');
                        } else if (response.data.major_id) {
                            $majorSelect.val(response.data.major_id).trigger('change');
                        }

                        // If the server returned a selected_major (e.g., alumni_ids.major_id), show it and set hidden input
                        if (response.data.selected_major) {
                            // show read-only selected block and create hidden input
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
                            // Fallback: server returned major_id only
                            // find it in the majors list
                            const match = response.data.majors.find(m => m.id == response.data.major_id);
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
                
                // Clear form and keep fields disabled
                $('#create_account')[0].reset();
                togglePersonalInfoFields(true);
            }
        })
        .fail(function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.log('Response:', xhr.responseText);
            $('#alumni_id_status').html('<div class="alert alert-danger mt-2"><i class="fas fa-exclamation-circle"></i> Error verifying Alumni ID. Please try again.</div>');
            alumniVerified = false;
            verifiedAlumniId = null;
            togglePersonalInfoFields(true);
        });
    });

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            placeholder: "Please select",
            width: '100%'
        });
    }

    // Initialize Year Picker
    if ($.fn.datepicker) {
        $('.datepickerY').datepicker({
            format: "yyyy",
            viewMode: "years",
            minViewMode: "years",
            autoclose: true
        });
    }

    // Real-time email validation
    $('#email').on('input', function() {
        validateEmail($(this));
    });

    // Password strength indicator
    $('#password').on('input', function() {
        checkPasswordStrength($(this).val());
    });

    // Show/Hide password
    $('#togglePassword').click(function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Form validation
    $('#create_account').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });

    // Form submission handler
    $('#create_account').submit(function(e){
        e.preventDefault();
        
        if(!alumniVerified) {
            $('#msg').html('<div class="alert alert-danger">Please verify your Alumni ID first</div>');
            return false;
        }
        
        // Create FormData object
        let formData = new FormData();
        
        // Add verified alumni data first
        if(verifiedData) {
            Object.entries(verifiedData).forEach(([key, value]) => {
                formData.append(key, value || '');
            });
        }
        
        // Add manually entered fields
        const manualFields = {
            'alumni_id': $('#alumni_id').val(),
            'email': $('#email').val(),
            'password': $('#password').val(),
            'address': $('#address').val(),
            'contact_no': $('#contact_no').val() || '',
            'company_name': $('#company_name').val() || '',
            'company_address': $('#company_address').val() || '',
            'company_email': $('#company_email').val() || ''
        };
        
        Object.entries(manualFields).forEach(([key, value]) => {
            formData.append(key, value);
        });
        
        // Add profile image (optional)
        const imgInput = $('#imgInput')[0];
        if(imgInput.files[0]) {
            // Validate image size if one is selected
            if(imgInput.files[0].size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Profile image must be under 5MB.'
                });
                return false;
            }
            formData.append('img', imgInput.files[0]);
        }
        
        // Add status and date
        formData.append('status', '1');
        formData.append('date_created', new Date().toISOString().slice(0, 19).replace('T', ' '));
        
        // Validate required fields
        let missingFields = [];
        ['alumni_id', 'email', 'password', 'address'].forEach(field => {
            if(!formData.get(field)) {
                missingFields.push(field);
                $(`[name="${field}"]`).addClass('is-invalid');
            }
        });
        
        if(missingFields.length > 0) {
            $('#msg').html(`
                <div class="alert alert-danger">
                    <strong>Please complete the following:</strong>
                    <ul class="mb-0 mt-2">
                        ${missingFields.map(field => field.replace('_', ' ').toUpperCase()).map(field => `<li>${field}</li>`).join('')}
                    </ul>
                </div>
            `);
            return false;
        }
        
        // Debug log
        console.log('Form data before submission:');
        for(let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Submit form
        start_load();
        $.ajax({
            url: 'admin/ajax.php?action=signup',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                end_load();
                try {
                    var response = typeof resp === 'string' ? JSON.parse(resp) : resp;
                    if(response.status === 'success') {
                        // Show beautiful success message with SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: '<span class="text-success">Registration Successful!</span>',
                            html: `
                                <div class="mt-3">
                                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                                    <p class="mt-3 mb-1">Your account has been created successfully!</p>
                                    <p class="text-muted small">Please wait for account verification.</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'Continue to Login',
                            confirmButtonColor: '#800000',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'animated fadeInDown faster',
                                title: 'swal2-title-custom',
                                content: 'text-center'
                            },
                            backdrop: `
                                rgba(128, 0, 0, 0.1)
                                url("assets/img/logo.png")
                                right top
                                no-repeat
                            `
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php';
                            }
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: response.message || 'An error occurred during registration.',
                            confirmButtonColor: '#800000'
                        });
                    }
                } catch(e) {
                    console.error(e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                        confirmButtonColor: '#800000'
                    });
                }
            },
            error: function(xhr, status, error) {
                end_load();
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Failed to connect to the server. Please try again later.',
                    confirmButtonColor: '#800000'
                });
            },
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp){
                if(resp == 1){
                    alert_toast("Account created successfully!", 'success');
                    setTimeout(function(){
                        location.href = 'login.php';
                    }, 1500);
                } else {
                    $('#msg').html('<div class="alert alert-danger">' + resp + '</div>');
                    end_load();
                }
            },
            error: function(xhr, status, error) {
                console.error('Submission error:', error);
                $('#msg').html('<div class="alert alert-danger">Server error occurred. Please try again.</div>');
                end_load();
            }
        });
    });
});

function validateEmail(input) {
    const email = input.val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (emailRegex.test(email)) {
        input.removeClass('is-invalid').addClass('is-valid');
        return true;
    } else {
        input.removeClass('is-valid').addClass('is-invalid');
        return false;
    }
}

function checkPasswordStrength(password) {
    let strength = 0;
    const progressBar = $('#password_strength .progress-bar');
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[!@#$%^&*]+/)) strength++;
    
    const percent = (strength / 5) * 100;
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

    return strength >= 3; // Consider password strong enough if strength is 3 or more
}

// Update the handleVerificationSuccess function
function handleVerificationSuccess(data) {
    $('#alumni_id_status').html('<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> Alumni ID verified successfully!</div>');
    alumniVerified = true;
    
    // Store verified data globally
    verifiedData = {
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
    
    // Auto-fill and disable personal information fields
    Object.entries(verifiedData).forEach(([field, value]) => {
        const element = $(`[name="${field}"]`);
        if(element.length) {
            element.val(value || '');
            
            // Use disabled for select elements, readonly for others
            if(element.is('select')) {
                element.prop('disabled', true);
            } else {
                element.prop('readonly', true);
            }
            
            // Add visual feedback
            element.addClass('is-valid bg-light');
        }
    });
    
    // Trigger change for select2 if used
    $('[name="course_id"]').trigger('change');
}

// Major select change: update read-only panel and hidden input
$(document).on('change', '#majorSelect', function(){
    const mid = $(this).val();
    if(!mid) {
        $('#majorSelected').hide();
        $('#hidden_major_id').remove();
        return;
    }
    // Find details from the majors list returned earlier (we rely on server to return majors on verify)
    // Try to find matching option text and lookup its about by reusing DOM option text
    const text = $(this).find('option:selected').text();
    $('#majorText').text(text);
    // Try to get about from majors list in response stored in verifiedData if present
    let about = '';
    if(window.verifiedData && Array.isArray(window.verifiedData.majors)){
        const m = window.verifiedData.majors.find(x => String(x.id) === String(mid));
        if(m) about = m.about || '';
    }
    $('#majorAbout').text(about);
    $('#majorSelected').show();
    // ensure hidden input exists
    if($('#hidden_major_id').length === 0) {
        $('<input>').attr({type:'hidden', id:'hidden_major_id', name:'major_id', value: mid}).appendTo('#create_account');
    } else {
        $('#hidden_major_id').val(mid);
    }
});
