// ===== INITIALIZE ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    loadVisitorLog();
    
    // Set today's date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').value = today;
    
    // Set current time for in time
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('in').value = `${hours}:${minutes}`;
});

// ===== ADD VISITOR =====
function addVisitor() {
    // Get form values
    const vname = document.getElementById('vname').value.trim();
    const relation = document.getElementById('relation').value;
    const contact = document.getElementById('contact').value.trim();
    const room = document.getElementById('room').value.trim();
    const date = document.getElementById('date').value;
    const inTime = document.getElementById('in').value;
    
    // Validation
    if(!vname || !relation || !contact || !room || !date || !inTime) {
        alert('❌ Please fill all required fields');
        return;
    }
    
    if(contact.length !== 10 || isNaN(contact)) {
        alert('❌ Please enter a valid 10-digit mobile number');
        return;
    }
    
    const btn = document.querySelector('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ADDING...';
    btn.disabled = true;
    
    // Create form data with separate fields
    const formData = new URLSearchParams();
    formData.append('visitor_name', vname);
    formData.append('relation', relation);
    formData.append('contact', contact);
    formData.append('room_no', room);
    formData.append('visit_date', date);
    formData.append('in_time', inTime);
    
    fetch('Backend/add_visitor.php', {
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
            alert('✅ Visitor added successfully!');
            
            // Clear form (keep date and time)
            document.getElementById('vname').value = '';
            document.getElementById('relation').value = '';
            document.getElementById('contact').value = '';
            document.getElementById('room').value = '';
            
            // Reload visitor log
            loadVisitorLog();
        } else if(data === 'not_logged_in') {
            alert('Please login first');
            window.location.href = 'login.html';
        } else if(data === 'empty') {
            alert('Please fill all fields');
        } else {
            alert('Error adding visitor. Please try again.');
            console.error('Server response:', data);
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

// ===== LOAD VISITOR LOG =====
function loadVisitorLog() {
    fetch('Backend/get_visitor_log.php')
    .then(response => response.json())
    .then(visitors => {
        if(visitors.error) {
            console.log('Error:', visitors.error);
            return;
        }
        
        // Create visitor history section if it doesn't exist
        let historySection = document.getElementById('visitorHistory');
        if(!historySection) {
            historySection = document.createElement('div');
            historySection.id = 'visitorHistory';
            historySection.className = 'visitor-history';
            historySection.innerHTML = '<h3><i class="fas fa-history"></i> Recent Visitors</h3>';
            document.querySelector('.content').appendChild(historySection);
        }
        
        if(visitors.length === 0) {
            historySection.innerHTML = `
                <h3><i class="fas fa-history"></i> Recent Visitors</h3>
                <p class="no-data"><i class="fas fa-info-circle"></i> No visitors yet.</p>
            `;
            return;
        }
        
        let html = '<h3><i class="fas fa-history"></i> Recent Visitors</h3>';
        
        visitors.forEach(visitor => {
            const outTime = visitor.out_time || 'Not checked out';
            const outButton = !visitor.out_time ? 
                `<button class="checkout-btn" onclick="checkoutVisitor(${visitor.id})"><i class="fas fa-sign-out-alt"></i> Check Out</button>` : '';
            
            html += `
                <div class="visitor-card" id="visitor-${visitor.id}">
                    <div class="visitor-header">
                        <span class="visitor-name"><i class="fas fa-user-circle"></i> ${visitor.visitor_name}</span>
                        <span class="visitor-badge"><i class="far fa-calendar-alt"></i> ${visitor.visit_date}</span>
                    </div>
                    
                    <div class="visitor-details">
                        <div class="visitor-detail-item">
                            <i class="fas fa-user-tag"></i> Relation: <strong>${visitor.relation || 'N/A'}</strong>
                        </div>
                        <div class="visitor-detail-item">
                            <i class="fas fa-phone"></i> Contact: <strong>${visitor.contact || 'N/A'}</strong>
                        </div>
                        <div class="visitor-detail-item">
                            <i class="fas fa-door-open"></i> Room: <strong>${visitor.room_no || 'N/A'}</strong>
                        </div>
                    </div>
                    
                    <div class="visitor-times">
                        <span><i class="fas fa-sign-in-alt" style="color:#28a745;"></i> In: ${visitor.in_time}</span>
                        <span><i class="fas fa-sign-out-alt" style="color:#dc3545;"></i> Out: ${outTime} ${outButton}</span>
                    </div>
                </div>
            `;
        });
        
        historySection.innerHTML = html;
    })
    .catch(error => console.error('Error loading visitors:', error));
}

// ===== CHECKOUT VISITOR =====
// ===== CHECKOUT VISITOR =====
function checkoutVisitor(visitorId) {
    if(!confirm('Mark this visitor as checked out?')) return;
    
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const outTime = `${hours}:${minutes}`; // Just time, no date
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    fetch('Backend/update_visitor_out.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `visitor_id=${visitorId}&out_time=${outTime}`
    })
    .then(response => response.text())
    .then(data => {
        data = data.trim();
        if(data === 'success') {
            loadVisitorLog(); // Reload the log
        } else if(data === 'unauthorized') {
            alert('You are not authorized to check out this visitor');
        } else {
            alert('Error checking out visitor: ' + data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}