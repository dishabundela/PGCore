// ===== HANDLE BOOK NOW CLICK =====
function handleBookNow() {
    fetch('Backend/check_session.php')
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "logged_in") {
                // User is logged in - go to DASHBOARD
                window.location.href = "dashboard.html";
            } else {
                // User not logged in - go to login
                localStorage.setItem("redirectAfterLogin", "dashboard.html");
                window.location.href = "login.html";
            }
        })
        .catch(error => {
            console.error("Session check failed:", error);
            window.location.href = "login.html";
        });
}

// ===== LOGOUT FUNCTION =====
function logout() {
    fetch('Backend/logout.php')
        .then(() => {
            window.location.href = 'home.html';
        })
        .catch(() => {
            window.location.href = 'home.html';
        });
}

// ===== CHECK LOGIN STATUS AND UPDATE UI =====
function checkLoginStatus() {
    fetch('Backend/check_session.php')
        .then(response => response.text())
        .then(data => {
            const isLoggedIn = data.trim() === "logged_in";
            const loginBtn = document.getElementById('loginBtn');
            const logoutBtn = document.getElementById('logoutBtn');
            const welcomeUser = document.getElementById('welcomeUser');
            
            if (isLoggedIn) {
                if (loginBtn) loginBtn.style.display = 'none';
                if (logoutBtn) logoutBtn.style.display = 'inline-block';
                if (welcomeUser) {
                    welcomeUser.style.display = 'inline-block';
                    loadUserData();
                }
            } else {
                if (loginBtn) loginBtn.style.display = 'inline-block';
                if (logoutBtn) logoutBtn.style.display = 'none';
                if (welcomeUser) welcomeUser.style.display = 'none';
            }
        });
}

// ===== LOAD USER DATA =====
function loadUserData() {
    fetch('Backend/get_user_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                document.getElementById('welcomeUser').textContent = `Welcome, ${data.name || 'User'}!`;
            }
        })
        .catch(error => console.error('Error loading user data:', error));
}

// ===== BOOKING MODAL FUNCTIONS =====
function openBookingModal() {
    loadUserDataForPopup();
    document.getElementById('bookingModal').style.display = 'block';
    document.body.classList.add('modal-open');
    
    // Force scroll to top when modal opens
    setTimeout(() => {
        const modal = document.querySelector('.modal');
        if (modal) {
            modal.scrollTop = 0;
        }
    }, 100);
}

function closeBookingModal() {
    document.getElementById('bookingModal').style.display = 'none';
    document.body.classList.remove('modal-open');
}

// Load user data for popup
function loadUserDataForPopup() {
    fetch('Backend/get_user_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                document.getElementById('popupFullName').value = data.name || '';
                document.getElementById('popupEmail').value = data.email || '';
                document.getElementById('popupPhone').value = data.phone || '';
            }
        })
        .catch(error => console.error('Error loading user data:', error));
}

// Room selection for popup
function setupPopupRoomSelection() {
    const roomOptions = document.querySelectorAll('.room-option');
    const selectedRoomDisplay = document.getElementById('popupSelectedRoom');
    const totalAmount = document.getElementById('popupTotalAmount');

    roomOptions.forEach(option => {
        option.addEventListener('click', function() {
            roomOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            
            const room = this.dataset.room;
            const type = this.dataset.type;
            const price = this.dataset.price;
            
            selectedRoomDisplay.textContent = `${room} - ${type} (₹${price})`;
            totalAmount.textContent = `₹${price}`;
        });
    });
}

// Set min dates for popup
function setPopupMinDates() {
    const today = new Date().toISOString().split('T')[0];
    const bookingDate = document.getElementById('popupBookingDate');
    const moveInDate = document.getElementById('popupMoveInDate');
    
    if (bookingDate) {
        bookingDate.min = today;
        bookingDate.value = today;
    }
    if (moveInDate) {
        moveInDate.min = today;
        moveInDate.value = today;
    }
}

// ===== POPUP FORM SUBMISSION - FIXED WITH ROOM ID =====
function setupPopupBookingForm() {
    const form = document.getElementById('popupBookingForm');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const activeRoom = document.querySelector('.room-option.active');
        if (!activeRoom) {
            alert('Please select a room');
            return;
        }
        
        const btn = document.querySelector('.confirm-btn-popup');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> PROCESSING...';
        btn.disabled = true;
        
        // Get ALL form data including room_id
        const room_id = activeRoom.dataset.room;
        const price = activeRoom.dataset.price;
        const booking_date = document.getElementById('popupBookingDate').value;
        const move_in_date = document.getElementById('popupMoveInDate').value;
        const fullname = document.getElementById('popupFullName').value;
        const email = document.getElementById('popupEmail').value;
        const phone = document.getElementById('popupPhone').value;
        
        // Debug log
        console.log("Sending booking data:", { room_id, price, booking_date, move_in_date });
        
        try {
            const response = await fetch('Backend/create_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `room_id=${encodeURIComponent(room_id)}&price=${encodeURIComponent(price)}&booking_date=${encodeURIComponent(booking_date)}&move_in_date=${encodeURIComponent(move_in_date)}`
            });
            
            const result = await response.text();
            const trimmed = result.trim();
            
            console.log("Server response:", trimmed);
            
            if (trimmed === 'success') {
                alert('Booking request submitted successfully! Your booking is pending admin approval.');
                closeBookingModal();
                form.reset();
                setPopupMinDates();
                
                // Reset to default room
                document.querySelector('.room-option[data-room="101"]').classList.add('active');
                document.getElementById('popupSelectedRoom').textContent = '101 - Single Sharing (₹8000)';
                document.getElementById('popupTotalAmount').textContent = '₹8000';
                
                // Redirect to dashboard
                window.location.href = 'dashboard.html';
            } 
            else if (trimmed === 'empty') {
                alert('Please fill all fields including room selection');
            } 
            else if (trimmed === 'not_logged_in') {
                alert('Session expired. Please login again.');
                window.location.href = 'login.html';
            }
            else if (trimmed === 'invalid_room') {
                alert('Selected room is not available. Please choose another room.');
            }
            else if (trimmed === 'already_booked') {
                alert('You already have a pending or confirmed booking. You cannot book another room.');
                window.location.href = 'dashboard.html';
            }
            else {
                alert('Booking failed. Please try again. Error: ' + trimmed);
            }
            
        } catch (error) {
            console.error('Network error:', error);
            alert('Network error. Please check your connection.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}
// IMAGE SLIDER
let slides = document.querySelectorAll(".slides");
let index = 0;

if (slides.length > 0) {
    setInterval(() => {
        slides[index].classList.remove("active");
        index = (index + 1) % slides.length;
        slides[index].classList.add("active");
    }, 4000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('bookingModal');
    if (event.target == modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    setupPopupRoomSelection();
    setPopupMinDates();
    setupPopupBookingForm();
    checkLoginStatus();
    
    // Set default active room
    setTimeout(() => {
        const defaultRoom = document.querySelector('.room-option[data-room="101"]');
        if (defaultRoom) defaultRoom.classList.add('active');
    }, 100);
});