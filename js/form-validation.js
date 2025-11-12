document.addEventListener('DOMContentLoaded', function() {
    // Validation patterns
    const patterns = {
        nameOnly: /^[A-Za-z\s\-'.]+$/,
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
        phone: /^[0-9\-+()]{7,15}$/,
        address: /^[A-Za-z0-9\s,.\-'#\/]+$/,
        alumni_id: /^[A-Z0-9\-]{5,10}$/,
        companyName: /^[A-Za-z\s&.,'-]+$/, // No numbers allowed in company name
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    };

    // Function to validate input against pattern
    function validateInput(input, pattern, errorMsg) {
        const value = input.value;
        const isValid = pattern.test(value);
        input.classList.toggle('is-valid', isValid);
        input.classList.toggle('is-invalid', !isValid);
        
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.appendChild(feedback);
        }
        feedback.textContent = errorMsg;
        return isValid;
    }

    // Validate text fields (no numbers allowed)
    ['lastname', 'firstname', 'middlename', 'suffixname'].forEach(fieldName => {
        const input = document.querySelector(`input[name="${fieldName}"]`);
        if (input) {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[0-9]/g, '');
                validateInput(this, patterns.nameOnly, 'Only letters, spaces, and basic punctuation allowed');
            });
        }
    });

    // Company name validation (no numbers)
    const companyInput = document.querySelector('input[name="company"]');
    if (companyInput) {
        companyInput.addEventListener('input', function() {
            this.value = this.value.replace(/[0-9]/g, '');
            validateInput(this, patterns.companyName, 'Company name cannot contain numbers');
        });
    }

    // Alumni ID validation
    const alumniIdInput = document.querySelector('input[name="alumni_id"]');
    if (alumniIdInput) {
        alumniIdInput.addEventListener('input', function() {
            validateInput(this, patterns.alumni_id, 'Please enter a valid Alumni ID (e.g., CC-2025)');
        });
    }

    // Phone number validation
    const phoneInput = document.querySelector('input[name="contact_no"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            validateInput(this, patterns.phone, 'Please enter a valid phone number');
        });
    }

    // Email validation
    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            validateInput(this, patterns.email, 'Please enter a valid email address');
        });
    }

    // Address validation (allows numbers)
    const addressInput = document.querySelector('textarea[name="address"]');
    if (addressInput) {
        addressInput.addEventListener('input', function() {
            validateInput(this, patterns.address, 'Please enter a valid address');
        });
    }

    // Password strength checker
    document.addEventListener('DOMContentLoaded', function() {
    // Validation patterns
    const patterns = {
        nameOnly: /^[A-Za-z\s\-'.]+$/,
        email: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
        phone: /^[0-9\-+()]{7,15}$/,
        address: /^[A-Za-z0-9\s,.\-'#\/]+$/,
        alumni_id: /^[A-Z0-9\-]{5,10}$/,
        companyName: /^[A-Za-z\s&.,'-]+$/ // No numbers allowed in company name
    };

    // Function to validate input against pattern
    function validateInput(input, pattern, errorMsg) {
        const value = input.value;
        const isValid = pattern.test(value);
        input.classList.toggle('is-valid', isValid);
        input.classList.toggle('is-invalid', !isValid);
        
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            input.parentNode.appendChild(feedback);
        }
        feedback.textContent = errorMsg;
        return isValid;
    }

    // Setup field validations
    function setupValidation() {
        // Name fields (no numbers allowed)
        ['lastname', 'firstname', 'middlename', 'suffixname'].forEach(fieldName => {
            const input = document.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[0-9]/g, '');
                    validateInput(this, patterns.nameOnly, 'Only letters, spaces, and basic punctuation allowed');
                });
            }
        });

        // Company name validation (no numbers)
        const companyInput = document.querySelector('input[name="company"]');
        if (companyInput) {
            companyInput.addEventListener('input', function() {
                this.value = this.value.replace(/[0-9]/g, '');
                validateInput(this, patterns.companyName, 'Company name cannot contain numbers');
            });
        }

        // Alumni ID validation
        const alumniIdInput = document.querySelector('input[name="alumni_id"]');
        if (alumniIdInput) {
            alumniIdInput.addEventListener('input', function() {
                validateInput(this, patterns.alumni_id, 'Please enter a valid Alumni ID (e.g., CC-2025)');
            });
        }

        // Contact number validation
        const contactInput = document.querySelector('input[name="contact_no"]');
        if (contactInput) {
            contactInput.addEventListener('input', function() {
                validateInput(this, patterns.phone, 'Please enter a valid phone number');
            });
        }

        // Email validation for both personal and company email
        ['email', 'company_email'].forEach(fieldName => {
            const input = document.querySelector(`input[name="${fieldName}"]`);
            if (input) {
                input.addEventListener('input', function() {
                    validateInput(this, patterns.email, 'Please enter a valid email address');
                });
            }
        });

        // Address validation (allows numbers)
        const addressInput = document.querySelector('textarea[name="address"]');
        if (addressInput) {
            addressInput.addEventListener('input', function() {
                validateInput(this, patterns.address, 'Please enter a valid address');
            });
        }
    }

    // Initialize validations
    setupValidation();

    // Password strength checker
    const passwordInput = document.querySelector('input[name="password"]');
    const strengthMeter = document.getElementById('password_strength');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check length
            if (password.length >= 8) strength++;
            // Check lowercase letters
            if (password.match(/[a-z]/g)) strength++;
            // Check uppercase letters
            if (password.match(/[A-Z]/g)) strength++;
            // Check numbers
            if (password.match(/[0-9]/g)) strength++;
            // Check special characters
            if (password.match(/[^a-zA-Z0-9]/g)) strength++;
            
            // Update strength meter
            const strengthBar = strengthMeter.querySelector('.progress-bar');
            strengthBar.style.width = (strength * 20) + '%';
            
            // Update color and text
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.className = 'progress-bar bg-danger';
                    strengthBar.textContent = 'Very Weak';
                    break;
                case 2:
                    strengthBar.className = 'progress-bar bg-warning';
                    strengthBar.textContent = 'Weak';
                    break;
                case 3:
                    strengthBar.className = 'progress-bar bg-info';
                    strengthBar.textContent = 'Medium';
                    break;
                case 4:
                    strengthBar.className = 'progress-bar bg-primary';
                    strengthBar.textContent = 'Strong';
                    break;
                case 5:
                    strengthBar.className = 'progress-bar bg-success';
                    strengthBar.textContent = 'Very Strong';
                    break;
            }
        });
    }

    // Name fields validation (no numbers allowed)
    const nameFields = document.querySelectorAll('input[name="firstname"], input[name="lastname"], input[name="middlename"]');
    nameFields.forEach(field => {
        field.addEventListener('input', function() {
            // Remove any numbers
            this.value = this.value.replace(/[0-9]/g, '');
            // Remove special characters except hyphen and apostrophe
            this.value = this.value.replace(/[^a-zA-Z\s'-]/g, '');
            
            // Validate the field
            if (this.value.match(/^[a-zA-Z\s'-]*$/)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });

    // Email validation
    const emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            
            if (emailRegex.test(this.value)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }

    // Employment fields validation (no numbers allowed)
    const employmentFields = document.querySelectorAll('input[name="occupation"], input[name="company"]');
    employmentFields.forEach(field => {
        field.addEventListener('input', function() {
            // Remove numbers
            this.value = this.value.replace(/[0-9]/g, '');
            
            if (this.value.match(/^[a-zA-Z\s&.-]*$/)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    });

    // Address validation (allows numbers)
    const addressInput = document.querySelector('textarea[name="address"]');
    if (addressInput) {
        addressInput.addEventListener('input', function() {
            // Allow letters, numbers, spaces, and common address characters
            if (this.value.match(/^[a-zA-Z0-9\s,.-/#]*$/)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
    }

    // Form submission validation
    const form = document.getElementById('create_account');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            // Check for any invalid fields
            const invalidFields = form.querySelectorAll('.is-invalid');
            if (invalidFields.length > 0) {
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields correctly.');
            }
        });
    }

    // XSS Prevention
    function sanitizeInput(input) {
        return input.replace(/[<>]/g, '').trim();
    }

    // Apply sanitization to all inputs
    const allInputs = document.querySelectorAll('input, textarea');
    allInputs.forEach(input => {
        input.addEventListener('blur', function() {
            this.value = sanitizeInput(this.value);
        });
    });
});
