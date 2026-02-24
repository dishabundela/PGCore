// ===== LOAD PAYMENT DATA WITH CORRECT ROOM RENT =====
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentData();
    
    const payButton = document.getElementById('payButton');
    if(payButton) {
        payButton.addEventListener('click', payRent);
    }
});

// ===== DISPLAY CURRENT DATE =====
function displayCurrentDate() {
    const today = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const dateElement = document.getElementById('currentDate');
    if(dateElement) {
        dateElement.textContent = today.toLocaleDateString('en-IN', options);
    }
}

// ===== CALCULATE LATE FEE BASED ON CURRENT DATE =====
function calculateLateFee() {
    const today = new Date();
    const currentDay = today.getDate();
    const dueDay = 5; // Due on 5th of every month
    const lateFeeAmount = 800;
    
    // If today is AFTER the 5th, late fee applies
    return currentDay > dueDay ? lateFeeAmount : 0;
}

function loadPaymentData() {
    // Show loading state
    document.getElementById('roomNumber').textContent = 'Loading...';
    document.getElementById('monthlyRent').textContent = '₹0';
    document.getElementById('lastPayment').textContent = 'Loading...';
    document.getElementById('paymentStatus').textContent = 'Checking...';
    document.getElementById('lateFeeDisplay').textContent = '₹0';
    
    // Display today's date
    displayCurrentDate();
    
    // Hide late fee warning initially
    const lateWarning = document.getElementById('lateFeeWarning');
    if(lateWarning) lateWarning.style.display = 'none';
    
    fetch('Backend/get_user_profile.php')
    .then(response => response.json())
    .then(profile => {
        if(profile.error === 'not_logged_in') {
            window.location.href = 'login.html';
            return;
        }
        
        // Get the ACTUAL rent from user's room
        const monthlyRent = parseInt(profile.room_rent) || 0;
        const lateFee = calculateLateFee();
        const totalAmount = monthlyRent + lateFee;
        const currentDay = new Date().getDate();
        
        // Display room and rent info
        document.getElementById('roomNumber').textContent = profile.room_number || 'N/A';
        document.getElementById('monthlyRent').textContent = '₹' + monthlyRent;
        document.getElementById('lateFeeDisplay').textContent = '₹' + lateFee;
        
        // Show/hide late fee warning
        if(lateWarning) {
            if(lateFee > 0) {
                lateWarning.style.display = 'flex';
                document.getElementById('paymentStatus').textContent = 'Late';
                document.getElementById('paymentStatus').className = 'pending';
                
                // Update pay button for late payment
                const payButton = document.getElementById('payButton');
                payButton.classList.add('pay-with-late');
                document.getElementById('payButtonText').textContent = 'Pay Rent (with ₹800 Late Fee)';
            } else {
                lateWarning.style.display = 'none';
                document.getElementById('paymentStatus').textContent = 'On Time';
                document.getElementById('paymentStatus').className = 'paid';
                
                // Update pay button for on-time payment
                const payButton = document.getElementById('payButton');
                payButton.classList.remove('pay-with-late');
                document.getElementById('payButtonText').textContent = 'Pay Rent';
            }
        }
        
        // Load payment history
        return fetch('Backend/get_user_payments.php');
    })
    .then(response => response.json())
    .then(payments => {
        let tableHtml = '<tr><th>Month</th><th>Amount</th><th>Date</th><th>Status</th></tr>';
        
        if(payments && payments.length > 0) {
            // Get last payment for display
            const lastPayment = payments[0];
            document.getElementById('lastPayment').textContent = lastPayment.payment_date || 'N/A';
            
            payments.forEach(p => {
                const statusClass = p.status === 'completed' ? 'paid' : 'pending';
                tableHtml += `<tr>
                    <td>${p.payment_month}</td>
                    <td>₹${p.amount}</td>
                    <td>${p.payment_date}</td>
                    <td><span class="${statusClass}">${p.status}</span></td>
                </tr>`;
            });
        } else {
            document.getElementById('lastPayment').textContent = 'No payments yet';
            tableHtml += '<tr><td colspan="4" class="no-data">No payment history found</td></tr>';
        }
        
        document.getElementById('paymentTable').innerHTML = tableHtml;
    })
    .catch(error => {
        console.error('Error loading payment data:', error);
        document.getElementById('paymentTable').innerHTML = '<tr><td colspan="4" class="no-data">Error loading payments</td></tr>';
        document.getElementById('lastPayment').textContent = 'Error';
        document.getElementById('paymentStatus').textContent = 'Error';
    });
}

// ===== PAY RENT =====
function payRent() {
    const today = new Date();
    const currentMonth = today.toLocaleString('default', { month: 'long', year: 'numeric' });
    
    // Get the actual rent from display
    const rentText = document.getElementById('monthlyRent').textContent;
    const monthlyRent = parseInt(rentText.replace('₹', '')) || 0;
    
    if(monthlyRent === 0) {
        alert('Cannot process payment: Rent amount not found');
        return;
    }
    
    const lateFee = calculateLateFee();
    const totalAmount = monthlyRent + lateFee;
    
    let message = `Pay rent for ${currentMonth}?\n`;
    message += `Monthly Rent: ₹${monthlyRent}\n`;
    if(lateFee > 0) {
        message += `Late Fee: ₹${lateFee}\n`;
    }
    message += `───────────────\n`;
    message += `Total: ₹${totalAmount}`;
    
    if(!confirm(message)) return;
    
    const btn = document.getElementById('payButton');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> PROCESSING...';
    
    fetch('Backend/create_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `month=${encodeURIComponent(currentMonth)}`
    })
    .then(response => response.text())
    .then(text => {
        text = text.trim();
        if(text === 'success') {
            alert('✅ Payment successful!');
            loadPaymentData(); // Reload to show new payment
        } else if(text === 'not_logged_in') {
            alert('Session expired. Please login again.');
            window.location.href = 'login.html';
        } else if(text === 'empty') {
            alert('Missing payment information');
        } else {
            alert('Payment failed: ' + text);
        }
    })
    .catch(error => {
        alert('Network error: ' + error);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}