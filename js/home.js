function checkLogin() {
    // check login status
    let isLoggedIn = localStorage.getItem("isLoggedIn");

    if (isLoggedIn === "true") {
        // already logged in → go to booking
        window.location.href = "booking.html";
    } else {
        // not logged in → remember where to go
        localStorage.setItem("redirectAfterLogin", "booking.html");
        window.location.href = "login.html";
    }

}
// ✅ IMAGE SLIDER (auto run on page load)
let slides = document.querySelectorAll(".slides");
let index = 0;

setInterval(() => {
    slides[index].classList.remove("active");

    index = (index + 1) % slides.length;

    slides[index].classList.add("active");
}, 4000);
