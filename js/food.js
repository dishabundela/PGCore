const foodMenu = {
  Monday:{
    breakfast:"Dhokla <br><b>Beverage (Compulsory):</b> Chai / Doodh",
    lunch:"Dal Tadka, Roti, Jeera Rice, Salad",
    dinner:"Paneer Butter Masala, Roti"
  },
  Tuesday:{
    breakfast:"Poha <br><b>Beverage (Compulsory):</b> Chai / Doodh",
    lunch:"Rajma, Rice, Roti, Onion Salad",
    dinner:"Aloo Gobhi, Dal, Roti"
  },
  Wednesday:{
    breakfast:"Idli Sambhar <br><b>Beverage (Compulsory):</b> Chai / Doodh",
    lunch:"Kadhi, Rice, Roti",
    dinner:"Mix Veg, Dal Fry, Roti"
  },
  Thursday:{
    breakfast:"Upma <br><b>Beverage (Compulsory):</b> Chai / Doodh",
    lunch:"Chole, Rice, Roti",
    dinner:"Paneer Bhurji, Roti"
  },
  Friday:{
    breakfast:"Paratha <br><b>Beverage (Compulsory):</b> Chai / Doodh",
    lunch:"Dal Makhani, Rice, Roti",
    dinner:"Veg Kofta, Roti"
  },
  Saturday:{
    breakfast:"Puri Bhaji <br><b>Beverage (Compulsory):</b> Chai / Doodh",
    lunch:"Veg Biryani, Raita",
    dinner:"Light Dinner – Dal, Roti"
  },
  Sunday:{
    breakfast:"Bread Sandwich <br><b>Beverage:</b> Chai / Doodh",
    lunch:"Special Thali (Paneer + Sweet)",
    dinner:"Khichdi / Daliya"
  }
};

function openMenu(day){
  const data = foodMenu[day];
  document.getElementById("menuModal").style.display="flex";
  document.getElementById("dayTitle").innerText = day+" – Food Details";
  document.getElementById("breakfastText").innerHTML = data.breakfast;
  document.getElementById("lunchText").innerText = data.lunch;
  document.getElementById("dinnerText").innerText = data.dinner;
}

function closeMenu(){
  document.getElementById("menuModal").style.display="none";
}
