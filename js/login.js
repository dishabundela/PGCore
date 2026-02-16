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
    const forms = [loginForm, signupForm, forgotForm, adminForm];
    forms.forEach(form => {
        if (form) form.classList.add('hidden');
    });
    
    if (formToShow) {
        formToShow.classList.remove('hidden');
    }
}

// ========== FORM NAVIGATION ==========
function setupFormNavigation() {
    document.getElementById('goToSignup')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(signupForm);
    });
    
    document.getElementById('goToLogin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginForm);
    });
    
    document.getElementById('forgotPassword')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(forgotForm);
    });
    
    document.getElementById('backToLogin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginForm);
    });
    
    document.getElementById('goToAdmin')?.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(adminForm);
    });
    
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
            if (loginButton) {
                loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> LOGGING IN...';
                loginButton.disabled = true;
            }
            
            const response = await fetch('Backend/user_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            });
            
            const result = await response.text().then(text => text.trim());
            
            if (loginButton) {
                loginButton.innerHTML = 'LOGIN';
                loginButton.disabled = false;
            }
            
            switch (result) {
                case 'success':
                    alert('Login successful!');
                    // ALWAYS GO TO DASHBOARD AFTER LOGIN
                    window.location.href = 'dashboard.html';
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
            if (signupButton) {
                signupButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> CREATING ACCOUNT...';
                signupButton.disabled = true;
            }
            
            const response = await fetch('Backend/user_signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(password)}`
            });
            
            const result = await response.text().then(text => text.trim());
            
            if (signupButton) {
                signupButton.innerHTML = 'CREATE ACCOUNT';
                signupButton.disabled = false;
            }
            
            if (result === 'success') {
                alert('Account created successfully! Please login with your credentials.');
                signupForm.reset();
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
                console.error('Signup error:', result);
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
            
            if (resetButton) {
                resetButton.innerHTML = 'RESET PASSWORD';
                resetButton.disabled = false;
            }
            
            if (result === 'success') {
                alert('Password reset successfully! You can now login with your new password.');
                forgotForm.reset();
                showForm(loginForm);
            } else if (result === 'not_found') {
                alert('Email not found in our database.');
            } else if (result === 'empty') {
                alert('Please fill in all fields.');
            } else {
                alert('Password reset failed. Please try again.');
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
            
            if (adminButton) {
                adminButton.innerHTML = 'ADMIN LOGIN';
                adminButton.disabled = false;
            }
            
            if (result === 'success') {
                alert('Admin login successful! Redirecting...');
                window.location.href = 'admin/dashboard.html';
            } else if (result === 'wrong') {
                alert('Wrong admin password.');
            } else if (result === 'not_found') {
                alert('Admin account not found.');
            } else {
                alert('Admin login failed.');
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
    
    setupPasswordToggle();
    setupFormNavigation();
    setupAdminShortcut();
    setupLoginForm();
    setupSignupForm();
    setupForgotForm();
    setupAdminForm();
    
    window.showForm = showForm;
    
    console.log('PGCore Login System Ready!');
    showForm(loginForm);
});