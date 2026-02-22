// ===== LOAD ROOM DETAILS ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    loadRoomDetails();
});

// ===== LOAD ROOM DETAILS =====
function loadRoomDetails() {
    fetch('Backend/get_user_profile.php')
    .then(response => response.json())
    .then(profile => {
        if(profile.error) {
            console.error('Error loading profile:', profile.error);
            if(profile.error === 'not_logged_in') {
                window.location.href = 'login.html';
            }
            return;
        }
        
        const roomContent = document.getElementById('roomContent');
        
        if(!profile.has_booking || profile.booking_status !== 'confirmed') {
            // No confirmed booking
            roomContent.innerHTML = `
                <div class="no-booking-card">
                    <i class="fas fa-bed"></i>
                    <h2>No Room Assigned</h2>
                    <p>You don't have a confirmed booking yet. Book a room to see your room details here.</p>
                    <a href="home.html?open=booking" class="book-now-btn">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </a>
                </div>
            `;
            return;
        }
        
        // Has confirmed booking - show room details
        roomContent.innerHTML = `
            <div class="room-title"><i class="fas fa-bed"></i> My Room Details</div>
            <div class="room-desc">
                This section provides complete information about your assigned PG room, facilities, and occupancy status.
                Please report any maintenance issues through the Complaint section.
            </div>
            
            <div class="box">
                <h3><i class="fas fa-info-circle"></i> Room Information</h3>
                <div class="info-grid">
                    <div class="info-card">
                        <b>Room Number</b>
                        <span>${profile.room_number || 'N/A'}</span>
                    </div>
                    <div class="info-card">
                        <b>Room Type</b>
                        <span>${profile.room_type || 'N/A'}</span>
                    </div>
                    <div class="info-card">
                        <b>Monthly Rent</b>
                        <span>₹${profile.room_rent || '0'}</span>
                    </div>
                    <div class="info-card">
                        <b>Joining Date</b>
                        <span>${profile.move_in_date || 'N/A'}</span>
                    </div>
                    <div class="info-card">
                        <b>Booking Status</b>
                        <span class="badge-confirmed" style="background:#d4edda;color:#155724;padding:5px 15px;border-radius:25px;">Confirmed</span>
                    </div>
                </div>
            </div>
            
            <div class="box">
                <h3><i class="fas fa-utensils"></i> Room Facilities</h3>
                <ul>
                    <li><i class="fas fa-bed"></i> Bed with Mattress</li>
                    <li><i class="fas fa-chair"></i> Study Table & Chair</li>
                    <li><i class="fas fa-tshirt"></i> Wardrobe</li>
                    <li><i class="fas fa-fan"></i> Fan & Lighting</li>
                    <li><i class="fas fa-shower"></i> Attached Bathroom</li>
                    <li><i class="fas fa-water"></i> 24/7 Water Supply</li>
                    <li><i class="fas fa-wifi"></i> WiFi Internet</li>
                    <li><i class="fas fa-broom"></i> Daily Cleaning</li>
                    <li><i class="fas fa-tshirt"></i> Laundry Facility</li>
                    <li><i class="fas fa-bolt"></i> Power Backup</li>
                </ul>
            </div>
        `;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('roomContent').innerHTML = `
            <div class="no-booking-card">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                <h2>Error Loading Room Details</h2>
                <p>Please try refreshing the page.</p>
                <button onclick="window.location.reload()" class="book-now-btn" style="background: #dc3545;">
                    <i class="fas fa-redo"></i> Refresh
                </button>
            </div>
        `;
    });
}