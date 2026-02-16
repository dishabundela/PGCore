function checkLogin() {
    fetch('Backend/check_session.php')
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "logged_in") {
                window.location.href = "home.html?open=booking";
            } else {
                localStorage.setItem("redirectAfterLogin", "home.html?open=booking");
                window.location.href = "login.html";
            }
        })
        .catch(error => {
            console.error("Session check failed:", error);
            window.location.href = "login.html";
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

// Auto-open booking modal if URL has parameter
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.search.includes('open=booking')) {
        setTimeout(() => {
            if (typeof openBookingModal === 'function') {
                openBookingModal();
            }
        }, 500);
    }
});