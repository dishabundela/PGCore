/* ===============================
   BOOKING FORM LOGIC
================================ */

const overlay = document.getElementById("overlay");

const roomNo = document.getElementById("roomNo");
const roomType = document.getElementById("roomType");
const priceInput = document.getElementById("price");

const bookingDate = document.getElementById("bookingDate");
const moveInDate = document.getElementById("moveInDate");
const paymentStatus = document.getElementById("paymentStatus");
const bookingStatus = document.getElementById("bookingStatus");

function openForm(r, t, p) {
  overlay.style.display = "flex";
  roomNo.value = r;
  roomType.value = t;
  priceInput.value = "₹" + p;
  bookingDate.value = new Date().toISOString().split("T")[0];
  moveInDate.value = "";
  paymentStatus.value = "";
  bookingStatus.value = "";
}

function closeForm() {
  overlay.style.display = "none";
}

function submitBooking() {
  if (moveInDate.value === "") {
    alert("⚠ Please select Move-in Date");
    return;
  }
  paymentStatus.value = "Paid";
  bookingStatus.value = "Confirmed";
  alert("🎉 Booking Confirmed!");
  setTimeout(closeForm, 1200);
}

/* ===============================
   IMAGE SLIDER
================================ */

let images = [];
let currentIndex = 0;

const imgOverlay = document.getElementById("imgOverlay");
const fullImg = document.getElementById("fullImg");
const roomTitle = document.getElementById("roomTitle");
const roomPrice = document.getElementById("roomPrice");
const roomDesc = document.getElementById("roomDesc");

function openRoom(imgArray, title, price, desc) {
  images = imgArray;
  currentIndex = 0;
  imgOverlay.style.display = "flex";
  fullImg.src = images[currentIndex];
  roomTitle.innerText = title;
  roomPrice.innerText = price;
  roomDesc.innerText = desc;
}

function closeImg() {
  imgOverlay.style.display = "none";
}

function nextImg() {
  currentIndex = (currentIndex + 1) % images.length;
  fullImg.src = images[currentIndex];
}

function prevImg() {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  fullImg.src = images[currentIndex];
}
