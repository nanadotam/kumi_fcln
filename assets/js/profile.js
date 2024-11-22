document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.querySelector('.password-form');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');

    function validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasThreeDigits = (password.match(/\d/g) || []).length >= 3;
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        return password.length >= minLength && hasUpperCase && hasThreeDigits && hasSpecialChar;
    }

    newPassword.addEventListener('input', function() {
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