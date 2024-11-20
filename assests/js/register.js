document.getElementById("signupForm")?.addEventListener("submit", async function(event) {
    event.preventDefault();
    
    // Clear error messages
    document.getElementById("firstNameError").textContent = "";
    document.getElementById("lastNameError").textContent = "";
    document.getElementById("emailError").textContent = "";
    document.getElementById("passwordError").textContent = "";
    document.getElementById("confirmPasswordError").textContent = "";
    document.getElementById("roleError").textContent = "";

    // Get input values
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const role = document.getElementById("role").value;

    // Validation flags
    let valid = true;

    // Validate fields
    if (!firstName) {
        document.getElementById("firstNameError").textContent = "First name is required.";
        valid = false;
    }

    if (!lastName) {
        document.getElementById("lastNameError").textContent = "Last name is required.";
        valid = false;
    }

    if (!email) {
        document.getElementById("emailError").textContent = "Email is required.";
        valid = false;
    } else if (!validateEmail(email)) {
        document.getElementById("emailError").textContent = "Please enter a valid email address.";
        valid = false;
    }

    if (!password) {
        document.getElementById("passwordError").textContent = "Password is required.";
        valid = false;
    } else if (!validatePassword(password)) {
        document.getElementById("passwordError").textContent = 
            "Password must be at least 8 characters long, include one uppercase letter, at least three digits, and one special character.";
        valid = false;
    }

    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").textContent = "Passwords do not match.";
        valid = false;
    }

    if (!role) {
        document.getElementById("roleError").textContent = "Role is required.";
        valid = false;
    } else if (!['student', 'teacher'].includes(role.toLowerCase())) {
        document.getElementById("roleError").textContent = "Role must be either 'student' or 'teacher'.";
        valid = false;
    }

    // If valid, submit form
    if (valid) {
        try {
            console.log('Sending data:', {
                firstName,
                lastName,
                email,
                password,
                role
            });

            const response = await fetch('../../actions/register_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    firstName,
                    lastName,
                    email,
                    password,
                    role
                })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                window.location.href = 'login.php';
            } else {
                // Handle array of errors
                if (data.errors && Array.isArray(data.errors)) {
                    data.errors.forEach(error => {
                        // Map error messages to appropriate error elements
                        if (error.includes('First name')) {
                            document.getElementById("firstNameError").textContent = error;
                        } else if (error.includes('Last name')) {
                            document.getElementById("lastNameError").textContent = error;
                        } else if (error.includes('email') || error.includes('Email')) {
                            document.getElementById("emailError").textContent = error;
                        } else if (error.includes('Password')) {
                            document.getElementById("passwordError").textContent = error;
                        } else if (error.includes('Role')) {
                            document.getElementById("roleError").textContent = error;
                        } else {
                            // For any unmatched errors, show in alert
                            alert(error);
                        }
                    });
                } else {
                    // Fallback for general error message
                    alert(data.message || 'Registration failed. Please try again.');
                }
            }
        } catch (error) {
            console.error('Detailed error:', error);
            console.error('Error stack:', error.stack);
            alert('An error occurred during registration. Please try again.');
        }
    }
});

// Email validation function
function validateEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

// Password validation function
function validatePassword(password) {
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d{3,})(?=.*[!@#\$%\^\&\)\(+=._-]).{8,}$/;
    return passwordPattern.test(password);
}
