// ===== LOAD PAYMENT DATA =====
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentData();
    document.getElementById('payButton').addEventListener('click', payRent);
});

function loadPaymentData() {
    fetch('Backend/get_user_profile.php')
    .then(r => r.json())
    .then(profile => {
        if(profile.error === 'not_logged_in') {
            window.location.href = 'login.html';
            return;
        }
        
        document.getElementById('roomNumber').textContent = profile.room_number || 'N/A';
        document.getElementById('monthlyRent').textContent = '₹' + (profile.room_rent || '0');
        
        return fetch('Backend/get_user_payments.php');
    })
    .then(r => r.json())
    .then(payments => {
        let html = '<tr><th>Month</th><th>Amount</th><th>Date</th><th>Status</th></tr>';
        
        if(payments && payments.length > 0) {
            payments.forEach(p => {
                html += `<tr>
                    <td>${p.payment_month}</td>
                    <td>₹${p.amount}</td>
                    <td>${p.payment_date}</td>
                    <td>${p.status}</td>
                </tr>`;
            });
        } else {
            html += '<tr><td colspan="4" class="no-data">No payments</td></tr>';
        }
        
        document.getElementById('paymentTable').innerHTML = html;
    });
}

// ===== PAY RENT - ULTRA SIMPLE =====
function payRent() {
    const currentMonth = new Date().toLocaleString('default', { month: 'long', year: 'numeric' });
    
    if(!confirm(`Pay rent for ${currentMonth}?`)) return;
    
    const btn = document.getElementById('payButton');
    btn.disabled = true;
    btn.innerHTML = 'PROCESSING...';
    
    fetch('Backend/create_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `month=${encodeURIComponent(currentMonth)}`
    })
    .then(response => response.text())
    .then(text => {
        alert('Server response: ' + text); // THIS WILL SHOW WHAT SERVER RETURNS
        loadPaymentData();
        btn.disabled = false;
        btn.innerHTML = 'Pay Rent';
    })
    .catch(error => {
        alert('Error: ' + error);
        btn.disabled = false;
        btn.innerHTML = 'Pay Rent';
    });
}