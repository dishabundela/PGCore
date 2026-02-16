// ===== CHECK SESSION =====
function checkSession() {
    fetch('../Backend/check_session.php')
        .then(response => response.text())
        .then(data => {
            if (data.trim() !== "logged_in") {
                window.location.href = "login.html";
            } else {
                loadUserData();
            }
        })
        .catch(error => {
            console.error("Session check failed:", error);
            window.location.href = "login.html";
        });
}

// ===== LOAD USER DATA =====
function loadUserData() {
    fetch('../Backend/get_user_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                document.querySelector('.welcome-text').textContent = `Welcome, ${data.name || 'User'}!`;
            }
        })
        .catch(error => console.error('Error loading user data:', error));
}

// ===== LOGOUT =====
function logout() {
    fetch('../Backend/logout.php')
        .then(() => window.location.href = 'home.html')
        .catch(() => window.location.href = 'home.html');
}

// ===== CHECK IF USER HAS BOOKING (for dashboard access) =====
function checkBookingAccess() {
    fetch('../Backend/has_booking.php')
        .then(response => response.text())
        .then(hasBooking => {
            if (hasBooking.trim() !== "true") {
                alert("Please book a room first to access dashboard.");
                window.location.href = "home.html";
            }
        });
}

// ===== INITIALIZE =====
document.addEventListener('DOMContentLoaded', function() {
    checkSession();
    checkBookingAccess();
    
    // Add logout event listener
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    }
});