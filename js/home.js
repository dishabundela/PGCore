function checkLogin() {
    // Check login status via session
    fetch('Backend/check_session.php')
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "logged_in") {
                // Already logged in → go to booking (Home Page 2)
                window.location.href = "booking.html";
            } else {
                // Not logged in → go to login
                localStorage.setItem("redirectAfterLogin", "booking.html");
                window.location.href = "login.html";
            }
        })
        .catch(error => {
            console.error("Session check failed:", error);
            localStorage.setItem("redirectAfterLogin", "booking.html");
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