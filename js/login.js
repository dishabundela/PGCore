const residentForm = document.getElementById("residentForm");
const adminForm = document.getElementById("adminForm");
const signupForm = document.getElementById("signupForm");

/* SHOW FUNCTIONS */
function showResident(){
    residentForm.classList.remove("hidden");
    adminForm.classList.add("hidden");
    signupForm.classList.add("hidden");
}

function showAdmin(){
    adminForm.classList.remove("hidden");
    residentForm.classList.add("hidden");
    signupForm.classList.add("hidden");
}

function showSignup(){
    signupForm.classList.remove("hidden");
    residentForm.classList.add("hidden");
    adminForm.classList.add("hidden");
}

/* SHORTCUT KEYS */
document.addEventListener("keydown", e=>{
    if(e.ctrlKey && e.key==="a"){ e.preventDefault(); showAdmin(); }
    if(e.ctrlKey && e.key==="r"){ e.preventDefault(); showResident(); }
});

/* RESIDENT LOGIN */
residentForm.addEventListener("submit", e=>{
    e.preventDefault();

    // ✅ NEW (login memory)
    localStorage.setItem("isLoggedIn","true");
    localStorage.setItem("userType","resident");

    // ✅ NEW (dashboard redirect)
    window.location.href="dashboard.html";
});

/* ADMIN LOGIN */
adminForm.addEventListener("submit", e=>{
    e.preventDefault();
    alert("Admin Access Granted ✅");
    window.location.href="home.html";
});

/* SIGN UP VALIDATION */
signupForm.addEventListener("submit", e=>{
    e.preventDefault();

    const phone = document.getElementById("phone").value;
    const pass = document.getElementById("pass").value;
    const cpass = document.getElementById("cpass").value;

    if(phone.length !== 10 || isNaN(phone)){
        alert("Enter valid 10-digit phone number");
        return;
    }

    if(pass !== cpass){
        alert("Passwords do not match");
        return;
    }

    alert("Account Created Successfully ✅");
    showResident();

    residentForm.addEventListener("submit", e => {
        e.preventDefault();

        // ✅ SAME login logic after signup
        localStorage.setItem("isLoggedIn","true");
        localStorage.setItem("userType","resident");

        window.location.href = "dashboard.html";
    });
});



