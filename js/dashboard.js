function showSection(id){
  document.querySelectorAll('.content').forEach(sec=>{
    sec.classList.remove('active');
  });
  document.getElementById(id).classList.add('active');
}

// login protection
if(localStorage.getItem("isLoggedIn") !== "true"){
    window.location.href = "login.html";
}

// logout
function logout(){
    localStorage.clear();
    window.location.href = "login.html";
}




