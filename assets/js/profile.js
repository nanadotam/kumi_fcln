document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.querySelector('.profile-form');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    profileForm.addEventListener('submit', function(e) {
        if (newPassword.value || confirmPassword.value) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }
            
            if (newPassword.value.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return;
            }
        }
    });
}); 