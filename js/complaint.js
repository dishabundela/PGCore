// ===== LOAD COMPLAINTS ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    loadUserComplaints();
    
    // Set today's date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').value = today;
    
    // Add event listener to button
    const btn = document.getElementById('submitBtn');
    if(btn) {
        btn.addEventListener('click', submitComplaint);
    }
});

// ===== SUBMIT COMPLAINT =====
function submitComplaint() {
    const title = document.getElementById('title').value;
    const category = document.getElementById('category').value;
    const room = document.getElementById('room').value;
    const date = document.getElementById('date').value;
    const description = document.getElementById('desc').value;
    
    if(!title || !category || !room || !date || !description) {
        alert('Please fill all fields');
        return;
    }
    
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SUBMITTING...';
    btn.disabled = true;
    
    // Send separate fields
    const formData = new URLSearchParams();
    formData.append('title', title);
    formData.append('category', category);
    formData.append('room_no', room);
    formData.append('description', description);
    
    fetch('Backend/create_complaint.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => response.text())
    .then(data => {
        data = data.trim();
        if(data === 'success') {
            // Show success message
            const msg = document.getElementById('successMsg');
            msg.style.display = 'block';
            msg.innerHTML = '✔ Complaint submitted successfully!';
            setTimeout(() => {
                msg.style.display = 'none';
            }, 3000);
            
            // Clear form
            document.getElementById('title').value = '';
            document.getElementById('desc').value = '';
            
            // Reload complaints list
            loadUserComplaints();
        } else if(data === 'not_logged_in') {
            alert('Please login first');
            window.location.href = 'login.html';
        } else {
            alert('Error submitting complaint. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// ===== LOAD USER COMPLAINTS =====
function loadUserComplaints() {
    fetch('Backend/get_user_complaints.php')
    .then(response => response.json())
    .then(complaints => {
        if(complaints.error) {
            console.log('No complaints or error:', complaints.error);
            return;
        }
        
        // Create complaints history section if it doesn't exist
        let historySection = document.getElementById('complaintsHistory');
        if(!historySection) {
            historySection = document.createElement('div');
            historySection.id = 'complaintsHistory';
            historySection.className = 'complaints-history';
            document.querySelector('.content').appendChild(historySection);
        }
        
        if(complaints.length === 0) {
            document.getElementById('complaintList').innerHTML = `
                <p class="no-data">No complaints yet.</p>
            `;
            return;
        }
        
        let html = '';
        complaints.forEach(complaint => {
            const statusClass = complaint.status === 'pending' ? 'status-pending' : 
                               complaint.status === 'resolved' ? 'status-resolved' : 'status-progress';
            const statusIcon = complaint.status === 'pending' ? '⏳' : 
                              complaint.status === 'resolved' ? '✅' : '🔄';
            
            html += `
                <div class="complaint-card">
                    <div class="complaint-header">
                        <span class="complaint-date"><i class="far fa-calendar-alt"></i> ${complaint.complaint_date}</span>
                        <span class="complaint-status ${statusClass}">${statusIcon} ${complaint.status}</span>
                    </div>
                    <div class="complaint-text">
                        <strong>[${complaint.category}] ${complaint.title}</strong><br>
                        Room: ${complaint.room_no}<br><br>
                        ${complaint.description}
                    </div>
                </div>
            `;
        });
        
        document.getElementById('complaintList').innerHTML = html;
    })
    .catch(error => console.error('Error loading complaints:', error));
}