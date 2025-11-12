class OTPManager {
    constructor() {
        this.isProcessing = false;
        this.cooldownTimer = null;
        this.cooldownDuration = 60; // 60 seconds cooldown
        this.setupEventListeners();
    }

    setupEventListeners() {
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        if (sendOtpBtn) {
            sendOtpBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleSendOTP();
            });
        }
    }

    async handleSendOTP() {
        // Prevent double submission
        if (this.isProcessing) {
            console.log('Request already in progress');
            return;
        }

        const emailInput = document.getElementById('email');
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const otpSection = document.getElementById('otpSection');

        if (!emailInput || !this.validateEmail(emailInput.value)) {
            this.showMessage('error', 'Please enter a valid email address');
            return;
        }

        try {
            this.isProcessing = true;
            sendOtpBtn.disabled = true;
            this.showLoadingState(true);

            const response = await fetch('send_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(emailInput.value)}&timestamp=${Date.now()}`
            });

            const data = await response.json();

            if (data.status === 'success') {
                this.showMessage('success', 'OTP sent successfully! Please check your email.');
                this.startCooldown();
                if (otpSection) {
                    otpSection.classList.remove('d-none');
                    this.setupOTPInputs();
                }
            } else {
                this.showMessage('error', data.message || 'Failed to send OTP');
                sendOtpBtn.disabled = false;
            }
        } catch (error) {
            console.error('OTP send error:', error);
            this.showMessage('error', 'Failed to send OTP. Please try again later.');
            sendOtpBtn.disabled = false;
        } finally {
            this.isProcessing = false;
            this.showLoadingState(false);
        }
    }

    startCooldown() {
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        let timeLeft = this.cooldownDuration;

        if (this.cooldownTimer) {
            clearInterval(this.cooldownTimer);
        }

        sendOtpBtn.disabled = true;
        this.updateButtonText(timeLeft);

        this.cooldownTimer = setInterval(() => {
            timeLeft--;
            this.updateButtonText(timeLeft);

            if (timeLeft <= 0) {
                clearInterval(this.cooldownTimer);
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send OTP';
            }
        }, 1000);
    }

    updateButtonText(timeLeft) {
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        if (timeLeft > 0) {
            sendOtpBtn.innerHTML = `<i class="fas fa-clock"></i> Resend in ${timeLeft}s`;
        }
    }

    showLoadingState(show) {
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        if (show) {
            sendOtpBtn.classList.add('loading');
            sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        } else {
            sendOtpBtn.classList.remove('loading');
        }
    }

    setupOTPInputs() {
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.value = '';
            input.addEventListener('input', (e) => {
                if (e.target.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    }

    validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    showMessage(type, message) {
        const messageDiv = document.getElementById('otpMessages');
        if (!messageDiv) return;

        const alert = document.createElement('div');
        alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} fade show`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
        `;

        messageDiv.innerHTML = '';
        messageDiv.appendChild(alert);

        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => messageDiv.innerHTML = '', 300);
        }, 5000);
    }
}

// Initialize OTP manager when the document is ready
document.addEventListener('DOMContentLoaded', () => {
    window.otpManager = new OTPManager();
});
