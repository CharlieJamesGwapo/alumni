class OTPHandler {
    constructor() {
        this.otpSent = false;
        this.otpVerified = false;
        this.resendTimer = null;
        this.otpTimer = null;
        this.OTP_TIMEOUT = 300; // 5 minutes
        this.RESEND_DELAY = 30; // 30 seconds
        this.isSubmitting = false; // Flag to prevent double submission
        this.lastSubmitTime = 0; // Timestamp of last submission
        this.THROTTLE_MS = 5000; // 5 second minimum between submissions
        this.isSubmitting = false; // Prevent double submission
        this.lastSubmitTime = 0; // Track last submission time
        this.THROTTLE_MS = 5000; // Minimum time between submissions
        
        this.initializeElements();
        this.attachEventListeners();
    }

    initializeElements() {
        this.$otpSection = $('#otpSection');
        this.$otpInputs = $('.otp-input');
        this.$otpField = $('#otp');
        this.$sendOtpBtn = $('#sendOtpBtn');
        this.$resendBtn = $('#resendOtpBtn');
        this.$countdown = $('#otpCountdown');
        this.$emailInput = $('#email');
        this.$spinner = $('.spinner');
    }

    attachEventListeners() {
        this.$sendOtpBtn.on('click', () => this.handleSendOTP());
        this.$resendBtn.on('click', () => this.handleResendOTP());
        this.$otpInputs.on('input', (e) => this.handleOTPInput(e));
        this.$otpInputs.on('keydown', (e) => this.handleKeydown(e));
    }

    async handleSendOTP() {
            if (this.$sendOtpBtn.prop('disabled')) return; // Prevent double tap
            const email = this.$emailInput.val().trim();
            if (!this.validateEmail(email)) {
                this.showMessage('error', 'Please enter a valid email address');
                return;
            }
            this.showLoading(true);
            this.$sendOtpBtn.prop('disabled', true);
            try {
                const response = await $.ajax({
                    url: 'send_otp.php',
                    method: 'POST',
                    data: { email },
                    dataType: 'json'
                });
                if (response.status === 'success') {
                    this.otpSent = true;
                    this.showOTPSection();
                    this.startOTPTimer();
                    this.showMessage('success', 'OTP sent successfully! Check your email.');
                } else {
                    this.showMessage('error', response.message);
                    this.$sendOtpBtn.prop('disabled', false);
                }
            } catch (error) {
                this.showMessage('error', 'Failed to send OTP. Please try again.');
                this.$sendOtpBtn.prop('disabled', false);
            } finally {
                this.showLoading(false);
            }
    }

    showOTPSection() {
        this.$otpSection
            .removeClass('d-none')
            .addClass('animate__animated animate__fadeIn');
        this.$otpInputs.first().focus();
        this.$sendOtpBtn.text('Resend OTP').prop('disabled', true);
        // Responsive: expand OTP section smoothly
        this.$otpSection.css({
            'maxWidth': '400px',
            'margin': '0 auto',
            'padding': '1.5rem',
            'background': '#fff',
            'borderRadius': '16px',
            'boxShadow': '0 4px 24px rgba(128,0,0,0.10)',
            'transition': 'all 0.3s ease'
        });
        // Mobile friendly
        if (window.innerWidth < 576) {
            this.$otpSection.css({
                'maxWidth': '98vw',
                'padding': '1rem',
                'fontSize': '1.1rem'
            });
        }
    }

    startOTPTimer() {
        let timeLeft = this.OTP_TIMEOUT;
        clearInterval(this.otpTimer);
        
        const updateDisplay = () => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            this.$countdown.text(
                minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0')
            );

            if (timeLeft <= 0) {
                clearInterval(this.otpTimer);
                this.$countdown.parent().addClass('expired');
                this.$resendBtn.prop('disabled', false);
                this.otpSent = false;
            }
            timeLeft--;
        };

        updateDisplay();
        this.otpTimer = setInterval(updateDisplay, 1000);
    }

    handleOTPInput(e) {
        const $input = $(e.target);
        const value = $input.val();

        // Allow only numbers
        if (!/^\d*$/.test(value)) {
            $input.val(value.replace(/\D/g, ''));
            return;
        }

        if (value.length === 1) {
            $input.addClass('filled');
            const $next = $input.next('.otp-input');
            if ($next.length) {
                $next.focus();
            }
        } else {
            $input.removeClass('filled');
        }

        this.updateOTPValue();
    }

    handleKeydown(e) {
        const $input = $(e.target);
        if (e.key === 'Backspace' && !$input.val()) {
            const $prev = $input.prev('.otp-input');
            if ($prev.length) {
                $prev.focus().val('');
                this.updateOTPValue();
            }
        }
    }

    updateOTPValue() {
        let otp = '';
        this.$otpInputs.each(function() {
            otp += $(this).val();
        });
        this.$otpField.val(otp);
        
        // Enable submit button when OTP is complete
        const isComplete = otp.length === 6;
        $('#loginBtn').prop('disabled', !isComplete);
    }

    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    showLoading(show) {
        this.$spinner.toggle(show);
        this.$sendOtpBtn.prop('disabled', show);
        if (show) {
            this.$sendOtpBtn.addClass('loading');
        } else {
            this.$sendOtpBtn.removeClass('loading');
        }
    }

    showMessage(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const $alert = $('<div class="alert ' + alertClass + ' animate__animated animate__fadeIn" style="font-size:1rem; border-radius:12px; margin-top:10px;">' + message + '</div>');
        $('#messages').html($alert);
        setTimeout(() => $alert.fadeOut(), 4000);
    }

    reset() {
        this.$otpInputs.val('').removeClass('filled');
        this.$otpField.val('');
        clearInterval(this.otpTimer);
        clearInterval(this.resendTimer);
        this.otpSent = false;
        this.otpVerified = false;
        this.$sendOtpBtn.prop('disabled', false).text('Send OTP');
        this.$otpSection.addClass('d-none').removeAttr('style');
    }
}

// Initialize OTP handler
$(document).ready(() => {
    window.otpHandler = new OTPHandler();
    // Responsive: OTP input boxes
    $('.otp-input').css({
        'width': '2.5em',
        'height': '2.5em',
        'fontSize': '1.5em',
        'textAlign': 'center',
        'margin': '0 0.2em',
        'borderRadius': '8px',
        'border': '1.5px solid #800000',
        'background': '#fff',
        'boxShadow': '0 2px 8px rgba(128,0,0,0.07)'
    });
    if (window.innerWidth < 576) {
        $('.otp-input').css({
            'width': '2em',
            'height': '2em',
            'fontSize': '1.2em',
            'margin': '0 0.12em'
        });
    }
    // Spinner style for button
    $('<style>').text(`
        .loading { position: relative; }
        .loading:after {
            content: '';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            border: 2px solid #fff;
            border-top: 2px solid #800000;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }
    `).appendTo('head');
});
