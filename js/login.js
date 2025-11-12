// Handle login form submission with OTP verification
$(document).ready(function() {
    let otpSent = false;
    let otpVerified = false;
    let sending = false;
    let resendTimer = null;

    function useSweetAlert(title, text, icon, timer) {
        if (window.Swal) {
            return Swal.fire({
                title: title,
                text: text,
                icon: icon,
                allowOutsideClick: false,
                timer: timer || undefined,
                confirmButtonText: 'OK'
            });
        }
        // fallback
        alert(title + '\n\n' + text);
        return Promise.resolve();
    }

    // Show OTP input after sending OTP
    function showOtpInput() {
        $('#otpSection').removeClass('d-none');
        $('#sendOtpBtn').text('Resend OTP');
        otpSent = true;
    }

    // Hide OTP input
    function hideOtpInput() {
        $('#otpSection').addClass('d-none');
        $('#sendOtpBtn').text('Send OTP');
        otpSent = false;
        otpVerified = false;
    }

    // Reset form state
    function resetForm() {
        hideOtpInput();
        $('#loginForm')[0].reset();
        $('#otpVerifyStatus').text('').removeClass('text-success text-danger');
        $('#loginStatus').text('').removeClass('text-success text-danger');
    }

    function startResendCooldown(seconds) {
        clearInterval(resendTimer);
        let left = Math.max(1, Math.floor(seconds));
        const $btn = $('#sendOtpBtn');
        $btn.prop('disabled', true).addClass('disabled');
        $btn.data('cooldown', left);
        $btn.text(`Wait ${left}s`);
        resendTimer = setInterval(() => {
            left--;
            if (left <= 0) {
                clearInterval(resendTimer);
                $btn.prop('disabled', false).removeClass('disabled').text('Resend OTP');
                $btn.removeData('cooldown');
            } else {
                $btn.text(`Wait ${left}s`);
            }
        }, 1000);
    }

    // Handle Send OTP button click
    $('#sendOtpBtn').on('click touchend', function(e) {
        e.preventDefault();
        const email = $('#username').val().trim();

        if (!email) {
            useSweetAlert('Missing email', 'Please enter your email address before requesting OTP.', 'warning');
            return;
        }

        if (sending || $('#sendOtpBtn').prop('disabled')) return;
        sending = true;

        // Immediate UI feedback
        const $btn = $('#sendOtpBtn');
        $btn.prop('disabled', true).addClass('disabled').text('Sending...');
        $('#otpVerifyStatus').removeClass('text-danger text-success').text('Sending OTP...');

        $.ajax({
            url: 'send_otp.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            timeout: 20000,
            success: function(response) {
                if (response.status === 'success') {
                    // Friendly SweetAlert to explain email may take time
                    useSweetAlert('OTP Sent', 'We sent an OTP to your Gmail. Email delivery can take up to a minute — please wait before requesting another OTP.', 'success', 6000);
                    showOtpInput();
                    $('#otpVerifyStatus').text('OTP sent successfully! Please check your email.').addClass('text-success');
                    const cooldown = response.cooldown ? Number(response.cooldown) : 60;
                    startResendCooldown(cooldown);
                } else {
                    const msg = response.message || response.msg || 'Failed to send OTP. Please try again later.';
                    // If server returned a cooldown, inform user and start countdown
                    if (response.cooldown && Number(response.cooldown) > 0) {
                        useSweetAlert('Please wait', `An OTP was recently sent. Please wait ${response.cooldown} seconds before requesting again.`, 'info');
                        startResendCooldown(Number(response.cooldown));
                        $('#otpVerifyStatus').text(msg).addClass('text-danger');
                    } else {
                        useSweetAlert('Send failed', msg, 'error');
                        $('#otpVerifyStatus').text(msg).addClass('text-danger');
                        $btn.prop('disabled', false).removeClass('disabled').text('Send OTP');
                    }
                }
            },
            error: function() {
                useSweetAlert('Network error', 'Unable to send OTP right now. Please try again later.', 'error');
                $('#otpVerifyStatus').text('Error sending OTP. Please try again.').addClass('text-danger');
                $('#sendOtpBtn').prop('disabled', false).removeClass('disabled').text('Send OTP');
            },
            complete: function() {
                sending = false;
            }
        });
    });
    
    // Handle OTP verification
    $('#verifyOtpBtn').click(function(e) {
        e.preventDefault();
        const email = $('#username').val().trim();
        const otp = $('#otp').val().trim();
        
        if (!otp) {
            alert('Please enter the OTP code.');
            return;
        }
        
        // Verify OTP via AJAX
        $.ajax({
            url: 'verify_otp.php',
            type: 'POST',
            data: { 
                email: email,
                otp: otp 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    otpVerified = true;
                    $('#otpVerifyStatus').text('OTP verified successfully!')
                        .removeClass('text-danger').addClass('text-success');
                    $('#loginBtn').prop('disabled', false);
                } else {
                    $('#otpVerifyStatus').text(response.message)
                        .removeClass('text-success').addClass('text-danger');
                    $('#loginBtn').prop('disabled', true);
                }
            },
            error: function() {
                $('#otpVerifyStatus').text('Error verifying OTP. Please try again.')
                    .removeClass('text-success').addClass('text-danger');
                $('#loginBtn').prop('disabled', true);
            }
        });
    });
    
    // Handle login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        if (!otpVerified) {
            alert('Please verify your email with OTP first.');
            return;
        }
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'login.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#loginStatus').text('Login successful! Redirecting...')
                        .removeClass('text-danger').addClass('text-success');
                    setTimeout(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = 'home.php';
                        }
                    }, 1500);
                } else {
                    $('#loginStatus').text(response.message)
                        .removeClass('text-success').addClass('text-danger');
                    if (response.code === 5 || response.code === 7) {
                        // OTP verification issues - reset the form
                        hideOtpInput();
                        $('#loginBtn').prop('disabled', true);
                    }
                }
            },
            error: function() {
                $('#loginStatus').text('Error during login. Please try again.')
                    .removeClass('text-success').addClass('text-danger');
            }
        });
    });
    
    // Reset form when modal is closed
    $('#loginModal').on('hidden.bs.modal', function () {
        resetForm();
    });
    
    // Disable login button initially
    $('#loginBtn').prop('disabled', true);
    
    // Email validation on change
    $('#username').on('change', function() {
        if (otpSent || otpVerified) {
            hideOtpInput();
            $('#loginBtn').prop('disabled', true);
        }
    });
});
