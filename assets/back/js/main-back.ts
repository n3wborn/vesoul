require('./adminLogin');

// Date actuelle en français
var options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};
document.querySelector('.dateActuelle').textContent = `${new Date(Date.now()).toLocaleDateString('fr-FR', options)}`;

let burger = document.querySelector(".hamburger");
burger.addEventListener("click", function () {

    document.querySelector(".navigation").classList.add("displayMenu");
    burger.classList.remove("hamburger");

});

let closeBtn = document.querySelector(".closeBtn");
closeBtn.addEventListener("click", function () {

    document.querySelector(".navigation").classList.remove("displayMenu");
    burger.classList.add("hamburger");

});

let buttonMenu = document.querySelectorAll(".button-menu");
let urlPath: string = window.location.pathname;

let lastUrl: string = (urlPath.split("/")).slice(-1).pop();

if (lastUrl == "accueil") {
    for (let i: number; i < buttonMenu.length; i++) {
        buttonMenu[i].classList.remove("button-active");
    }
    buttonMenu[0].classList.add("button-active");
}
if (lastUrl == "commandes") {
    for (let i: number; i < buttonMenu.length; i++) {
        buttonMenu[i].classList.remove("button-active");
    }
    buttonMenu[1].classList.add("button-active");
}
if (lastUrl == "livres") {
    for (let i: number; i < buttonMenu.length; i++) {
        buttonMenu[i].classList.remove("button-active");
    }
    buttonMenu[2].classList.add("button-active");
}
if (lastUrl == "boutique") {
    for (let i: number; i < buttonMenu.length; i++) {
        buttonMenu[i].classList.remove("button-active");
    }
    buttonMenu[3].classList.add("button-active");
}
if (lastUrl == "mentions") {
    for (let i: number; i < buttonMenu.length; i++) {
        buttonMenu[i].classList.remove("button-active");
    }
    buttonMenu[4].classList.add("button-active");
}