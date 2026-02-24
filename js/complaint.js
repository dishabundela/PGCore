// ===== LOAD COMPLAINTS ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('Complaints page loaded');
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
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category').value;
    const room = document.getElementById('room').value.trim();
    const date = document.getElementById('date').value;
    const description = document.getElementById('desc').value.trim();
    
    console.log('Submitting complaint:', {title, category, room, date, description});
    
    // Validation
    if(!title || !category || !room || !date || !description) {
        alert('❌ Please fill all fields');
        return;
    }
    
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SUBMITTING...';
    btn.disabled = true;
    
    // Create form data
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
        console.log('Server response:', data);
        
        if(data === 'success') {
            // Show success message
            const msg = document.getElementById('successMsg');
            msg.style.display = 'block';
            msg.innerHTML = '✅ Complaint submitted successfully!';
            
            setTimeout(() => {
                msg.style.display = 'none';
            }, 3000);
            
            // Clear form (keep date and room)
            document.getElementById('title').value = '';
            document.getElementById('desc').value = '';
            // Keep room number as is
            // Keep category as default
            
            // Reload complaints list
            loadUserComplaints();
            
        } else if(data === 'not_logged_in') {
            alert('Please login first');
            window.location.href = 'login.html';
            
        } else if(data === 'empty') {
            alert('Please fill all fields');
            
        } else {
            // Show error message with the actual error
            const errorMsg = document.getElementById('errorMsg');
            errorMsg.style.display = 'block';
            errorMsg.innerHTML = '❌ Error: ' + data;
            setTimeout(() => {
                errorMsg.style.display = 'none';
            }, 5000);
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        alert('Network error. Please check your connection.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// ===== LOAD USER COMPLAINTS =====
function loadUserComplaints() {
    console.log('Loading complaints...');
    const complaintList = document.getElementById('complaintList');
    
    if(!complaintList) {
        console.error('complaintList element not found!');
        return;
    }
    
    complaintList.innerHTML = '<p class="no-data"><i class="fas fa-spinner fa-spin"></i> Loading complaints...</p>';
    
    fetch('Backend/get_user_complaints.php')
    .then(response => response.json())
    .then(complaints => {
        console.log('Complaints loaded:', complaints);
        
        if(complaints.error) {
            if(complaints.error === 'not_logged_in') {
                window.location.href = 'login.html';
                return;
            }
            complaintList.innerHTML = `<p class="no-data"><i class="fas fa-exclamation-triangle"></i> ${complaints.error}</p>`;
            return;
        }
        
        if(!complaints || complaints.length === 0) {
            complaintList.innerHTML = `
                <p class="no-data">
                    <i class="fas fa-info-circle"></i> No complaints yet. Submit your first complaint above.
                </p>
            `;
            return;
        }
        
        let html = '';
        complaints.forEach(complaint => {
            // Determine status class and icon
            let statusClass, statusIcon;
            const status = (complaint.status || 'pending').toLowerCase();
            
            switch(status) {
                case 'pending':
                    statusClass = 'status-pending';
                    statusIcon = '⏳';
                    break;
                case 'resolved':
                    statusClass = 'status-resolved';
                    statusIcon = '✅';
                    break;
                case 'in-progress':
                case 'in progress':
                    statusClass = 'status-progress';
                    statusIcon = '🔄';
                    break;
                default:
                    statusClass = 'status-pending';
                    statusIcon = '⏳';
            }
            
            html += `
                <div class="complaint-card">
                    <div class="complaint-header">
                        <span class="complaint-date">
                            <i class="far fa-calendar-alt"></i> ${complaint.complaint_date || 'N/A'}
                        </span>
                        <span class="complaint-status ${statusClass}">
                            ${statusIcon} ${complaint.status || 'Pending'}
                        </span>
                    </div>
                    <div class="complaint-text">
                        <strong>[${complaint.category || 'General'}] ${complaint.title || 'No Title'}</strong>
                        <br>
                        <span style="color: #0f3c4c; font-size: 13px;">
                            <i class="fas fa-door-open"></i> Room: ${complaint.room_no || 'N/A'}
                        </span>
                        <br><br>
                        ${complaint.description || complaint.complaint_text || 'No description provided'}
                    </div>
                </div>
            `;
        });
        
        complaintList.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading complaints:', error);
        complaintList.innerHTML = `
            <p class="no-data">
                <i class="fas fa-exclamation-triangle"></i> Error loading complaints. Please try again.
            </p>
        `;
    });
}