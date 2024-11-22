document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.querySelector('.password-form');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');

    // Create strength indicator
    const strengthIndicator = document.createElement('div');
    strengthIndicator.className = 'password-strength';
    newPassword.parentNode.insertBefore(strengthIndicator, passwordError);

    function calculatePasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 20;
        if (password.length >= 12) strength += 10;
        
        // Character variety checks
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[a-z]/.test(password)) strength += 10;
        if (/[0-9]{3,}/.test(password)) strength += 20;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 20;
        
        return {
            score: strength,
            text: strength < 40 ? 'Weak' : strength < 70 ? 'Moderate' : 'Strong',
            color: strength < 40 ? '#ff4d4d' : strength < 70 ? '#ffd700' : '#00cc00'
        };
    }

    function updateStrengthIndicator(password) {
        const strength = calculatePasswordStrength(password);
        
        strengthIndicator.innerHTML = `
            <div class="strength-bar">
                <div class="strength-fill" style="width: ${strength.score}%; background-color: ${strength.color}"></div>
            </div>
            <span class="strength-text" style="color: ${strength.color}">${strength.text}</span>
        `;
    }

    function validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasThreeDigits = (password.match(/\d/g) || []).length >= 3;
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        return password.length >= minLength && hasUpperCase && hasThreeDigits && hasSpecialChar;
    }

    newPassword.addEventListener('input', function() {
        updateStrengthIndicator(this.value);
        if (!validatePassword(this.value)) {
            passwordError.textContent = "Password must be at least 8 characters long, include one uppercase letter, at least three digits, and one special character.";
        } else {
            passwordError.textContent = "";
        }
    });

    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            confirmPasswordError.textContent = "Passwords do not match.";
        } else {
            confirmPasswordError.textContent = "";
        }
    });

    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let valid = true;

        if (!validatePassword(newPassword.value)) {
            passwordError.textContent = "Password must be at least 8 characters long, include one uppercase letter, at least three digits, and one special character.";
            valid = false;
        }

        if (newPassword.value !== confirmPassword.value) {
            confirmPasswordError.textContent = "Passwords do not match.";
            valid = false;
        }

        if (valid) {
            this.submit();
        }
    });
}); 