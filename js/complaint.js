document.addEventListener('DOMContentLoaded', function() {
    loadComplaints();
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
    document.getElementById('submitBtn').addEventListener('click', submitComplaint);
});

function submitComplaint() {
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category').value;
    const room = document.getElementById('room').value.trim();
    const desc = document.getElementById('desc').value.trim();
    
    if(!title || !room || !desc) return alert('Fill all fields');
    
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = 'Submitting...';
    btn.disabled = true;
    
    const formData = new FormData();
    formData.append('title', title);
    formData.append('category', category);
    formData.append('room_no', room);
    formData.append('description', desc);
    
    fetch('Backend/create_complaint.php', { method: 'POST', body: formData })
    .then(r => r.text())
    .then(data => {
        if(data.trim() === 'success') {
            alert('Complaint submitted');
            document.getElementById('title').value = '';
            document.getElementById('desc').value = '';
            loadComplaints();
        } else alert('Error');
    })
    .catch(() => alert('Network error'))
    .finally(() => {
        btn.innerHTML = 'Submit Complaint';
        btn.disabled = false;
    });
}

function loadComplaints() {
    const container = document.getElementById('complaintList');
    container.innerHTML = '<div class="loading">Loading...</div>';
    
    fetch('Backend/get_user_complaints.php')
    .then(r => r.json())
    .then(data => {
        if(!data || data.length === 0) {
            container.innerHTML = '<div class="no-data">No complaints yet</div>';
            return;
        }
        
        let html = '';
        for(let c of data) {
            let date = c.complaint_date || 'N/A';
            if(date !== 'N/A' && date !== '0000-00-00') {
                let parts = date.split('-');
                if(parts.length === 3) date = parts[2] + '-' + parts[1] + '-' + parts[0];
            }
            
            html += `
                <div class="complaint-card">
                    <div class="complaint-date"><i class="far fa-calendar-alt"></i> ${date}</div>
                    <div class="complaint-category"><i class="fas fa-tag"></i> ${c.category || 'General'}</div>
                    <div class="complaint-title">${escapeHtml(c.title || 'No Title')}</div>
                    <div class="complaint-room"><i class="fas fa-door-open"></i> Room: ${c.room_no || 'N/A'}</div>
                    <div class="complaint-description">${escapeHtml(c.description || c.complaint_text || 'No description')}</div>
                </div>
            `;
        }
        container.innerHTML = html;
    })
    .catch(() => container.innerHTML = '<div class="no-data">Error loading</div>');
}

function escapeHtml(text) {
    if(!text) return '';
    return text.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}