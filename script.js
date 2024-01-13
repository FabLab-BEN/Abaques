"use strict";

var boucle = 1;
var visible = false;

function get() {
  var select = document.getElementById("machine");
  var value = select.options[select.selectedIndex].value;
  console.log(value); // en
}

function inputed() {
  document.getElementById("lastV").removeAttribute("oninput");
  document.getElementById("lastV").removeAttribute("id");
  document.getElementById("lastT").removeAttribute("oninput");
  document.getElementById("lastT").removeAttribute("id");
  var row = document.createElement("tr");
  var cellV = document.createElement("td");
  var cellT = document.createElement("td");
  var inputV = document.createElement("input");
  inputV.setAttribute("type", "number");
  inputV.setAttribute("name", "volume" + boucle);
  inputV.setAttribute("value", "0");
  inputV.setAttribute("min", "0");
  inputV.classList.add("volume");
  inputV.setAttribute("oninput", "inputed()");
  inputV.setAttribute("id", "lastV");
  inputV.setAttribute("step", "any");
  var inputT = document.createElement("input");
  inputT.setAttribute("type", "number");
  inputT.setAttribute("name", "temps" + boucle);
  inputT.setAttribute("value", "0");
  inputT.setAttribute("min", "0");
  inputT.classList.add("temps");
  inputT.setAttribute("oninput", "inputed()");
  inputT.setAttribute("id", "lastT");
  inputT.setAttribute("step", "any");
  cellV.appendChild(inputV);
  cellT.appendChild(inputT);
  row.appendChild(cellV);
  row.appendChild(cellT);
  document.getElementById("table").appendChild(row);
  boucle += 1;
}

function emergencyLevel() {
  var value = document.getElementById("urgence").value;
  var label = document.getElementById("urgenceLabel");
  var text = "Urgent : ";
  // console.log(value);
  switch (value) {
    case "0":
      label.innerHTML = text + "Oui";
      break;
    case "1":
      label.innerHTML = text + "Non";
      break;
    case "2":
      label.innerHTML = text + "Soirée";
      break;
  }
}

function numberLevel() {
  var value = document.getElementById("copy").value;
  var label = document.getElementById("copyLevel");
  var text = "Nombre de copies :</br>";
  // console.log(value);
  label.innerHTML = text + value;
}

function suppr() {
  document.getElementById("erreur").remove();
  window.location.assign("/");
}

function resetAll() {
  window.location.assign("/?reset=true");
}

function logout() {
  window.location.assign("./logout.php");
}

window.onload = function () {
  numberLevel();
  emergencyLevel();
  boucle =
    Number(document.getElementById("lastV").name.replace("volume", "")) + 1;
  console.log(boucle);
};
// Language: javascript
