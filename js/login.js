// ========== DOM ELEMENTS ==========
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');
const forgotForm = document.getElementById('forgotForm');
const adminForm = document.getElementById('adminForm');

// ========== PASSWORD TOGGLE ==========
function setupPasswordToggle() {
    const toggles = document.querySelectorAll('.show-password');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

// ========== SHOW/HIDE FORMS ==========
function showForm(formToShow) {
    // Hide all forms
    const forms = [loginForm, signupForm, forgotForm, adminForm];
    forms.forEach(form => {
        if (form) form.classList.add('hidden');
    });
    
    // Show selected form
    if (formToShow) {
        formToShow.classList.remove('hidden');
    }
}

// ========== FORM NAVIGATION ==========
function setupFormNavigation() {
    // Login to Signup
    document.getElementById('goToSignup')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(signupForm);
    });
    
    // Signup to Login
    document.getElementById('goToLogin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginForm);
    });
    
    // Login to Forgot Password
    document.getElementById('forgotPassword')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(forgotForm);
    });
    
    // Forgot to Login
    document.getElementById('backToLogin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginForm);
    });
    
    // Login to Admin
    document.getElementById('goToAdmin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(adminForm);
    });
    
    // Admin to Login
    document.getElementById('backToResidentLogin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginForm);
    });
}

// ========== CTRL + A FOR ADMIN ==========
function setupAdminShortcut() {
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key.toLowerCase() === 'a') {
            e.preventDefault();
            showForm(adminForm);
        }
    });
}

// ========== LOGIN FORM SUBMISSION ==========
function setupLoginForm() {
    if (!loginForm) return;
    
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const loginButton = document.getElementById('loginButton');
        
        if (!email || !password) {
            alert('Please fill in all fields');
            return;
        }
        
        try {
            // Show loading
            if (loginButton) {
                loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> LOGGING IN...';
                loginButton.disabled = true;
            }
            
            const response = await fetch('Backend/resident_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            });
            
            const result = await response.text().then(text => text.trim());
            
            // Reset button
            if (loginButton) {
                loginButton.innerHTML = 'LOGIN';
                loginButton.disabled = false;
            }
            
            switch (result) {
                case 'success':
                    alert('Login successful! Redirecting to Home Page 2...');
                    const redirectUrl = localStorage.getItem('redirectAfterLogin') || 'booking.html';
                    localStorage.removeItem('redirectAfterLogin');
                    window.location.href = redirectUrl;
                    break;
                    
                case 'wrong':
                    alert('Incorrect password. Please try again.');
                    break;
                    
                case 'not_found':
                    alert('User not found. Please sign up first.');
                    break;
                    
                case 'empty':
                    alert('Please fill in all fields.');
                    break;
                    
                default:
                    alert('Login failed. Please try again.');
                    console.error('Login error:', result);
            }
            
        } catch (error) {
            console.error('Network error:', error);
            alert('Network error. Please check your connection.');
            
            if (loginButton) {
                loginButton.innerHTML = 'LOGIN';
                loginButton.disabled = false;
            }
        }
    });
}


// ========== SIGNUP FORM SUBMISSION ==========
function setupSignupForm() {
    if (!signupForm) return;
    
    signupForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const phone = document.getElementById('signupPhone').value;
        const password = document.getElementById('signupPassword').value;
        const signupButton = document.getElementById('signupButton');
        
        if (!name || !email || !phone || !password) {
            alert('Please fill in all fields');
            return;
        }
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters long');
            return;
        }
        
        try {
            // Show loading
            if (signupButton) {
                signupButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> CREATING ACCOUNT...';
                signupButton.disabled = true;
            }
            
            const response = await fetch('Backend/resident_signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(password)}`
            });
            
            const result = await response.text().then(text => text.trim());
            
            // Reset button
            if (signupButton) {
                signupButton.innerHTML = 'CREATE ACCOUNT';
                signupButton.disabled = false;
            }
            
            // IMPORTANT: NO REDIRECTION HERE!
            if (result === 'success') {
                alert('Account created successfully! Please login with your credentials.');
                // Clear the form
                signupForm.reset();
                // Show login form
                showForm(loginForm);
            } 
            else if (result === 'email_exists') {
                alert('Email already exists. Please use a different email or login.');
            }
            else if (result === 'empty') {
                alert('Please fill in all fields.');
            }
            else {
                alert('Signup failed. Please try again.');
            }
            
        } catch (error) {
            console.error('Network error:', error);
            alert('Network error. Please check your connection.');
            
            if (signupButton) {
                signupButton.innerHTML = 'CREATE ACCOUNT';
                signupButton.disabled = false;
            }
        }
    });
}

// ========== FORGOT PASSWORD FORM SUBMISSION ==========
function setupForgotForm() {
    if (!forgotForm) return;
    
    forgotForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('forgotEmail').value;
        const newPassword = document.getElementById('newPassword').value;
        const resetButton = document.getElementById('resetButton');
        
        if (!email || !newPassword) {
            alert('Please fill in all fields');
            return;
        }
        
        if (newPassword.length < 6) {
            alert('Password must be at least 6 characters long');
            return;
        }
        
        try {
            // Show loading
            if (resetButton) {
                resetButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> RESETTING...';
                resetButton.disabled = true;
            }
            
            const response = await fetch('Backend/forgot_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(newPassword)}`
            });
            
            const result = await response.text().then(text => text.trim());
            
            // Reset button
            if (resetButton) {
                resetButton.innerHTML = 'RESET PASSWORD';
                resetButton.disabled = false;
            }
            
            switch (result) {
                case 'success':
                    alert('Password reset successfully! You can now login with your new password.');
                    forgotForm.reset();
                    showForm(loginForm);
                    break;
                    
                case 'not_found':
                    alert('Email not found in our database.');
                    break;
                    
                case 'empty':
                    alert('Please fill in all fields.');
                    break;
                    
                default:
                    alert('Password reset failed. Please try again.');
                    console.error('Forgot password error:', result);
            }
            
        } catch (error) {
            console.error('Network error:', error);
            alert('Network error. Please check your connection.');
            
            if (resetButton) {
                resetButton.innerHTML = 'RESET PASSWORD';
                resetButton.disabled = false;
            }
        }
    });
}

// ========== ADMIN LOGIN FORM SUBMISSION ==========
function setupAdminForm() {
    if (!adminForm) return;
    
    adminForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const username = document.getElementById('adminUsername').value;
        const password = document.getElementById('adminPassword').value;
        const adminButton = document.querySelector('.admin-login-btn');
        
        if (!username || !password) {
            alert('Please enter both username and password.');
            return;
        }
        
        try {
            // Show loading
            if (adminButton) {
                adminButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ADMIN LOGIN...';
                adminButton.disabled = true;
            }
            
            const response = await fetch('Backend/admin_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            });
            
            const result = await response.text().then(text => text.trim());
            
            // Reset button
            if (adminButton) {
                adminButton.innerHTML = 'ADMIN LOGIN';
                adminButton.disabled = false;
            }
            
            switch (result) {
                case 'success':
                    alert('Admin login successful! Redirecting...');
                    window.location.href = 'admin/dashboard.html';
                    break;
                    
                case 'wrong':
                    alert('Wrong admin password.');
                    break;
                    
                case 'not_found':
                    alert('Admin account not found.');
                    break;
                    
                default:
                    alert('Admin login failed.');
                    console.error('Admin login error:', result);
            }
            
        } catch (error) {
            console.error('Network error:', error);
            alert('Network error during admin login.');
            
            if (adminButton) {
                adminButton.innerHTML = 'ADMIN LOGIN';
                adminButton.disabled = false;
            }
        }
    });
}

// ========== INITIALIZE EVERYTHING ==========
document.addEventListener('DOMContentLoaded', () => {
    console.log('PGCore Login System Initializing...');
    
    // Setup all functionality
    setupPasswordToggle();
    setupFormNavigation();
    setupAdminShortcut();
    setupLoginForm();
    setupSignupForm();
    setupForgotForm();
    setupAdminForm();
    
    // Make functions available globally (if needed)
    window.showForm = showForm;
    
    console.log('PGCore Login System Ready!');
    
    // Show login form by default
    showForm(loginForm);
});