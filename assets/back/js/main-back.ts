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

// cible les boutons menus et "active" celui de la page courante
const navBtn:HTMLCollectionOf<Element> = document.getElementsByClassName('button-menu');

for (let el of Array.from(navBtn)) {
    if (window.location.pathname.startsWith(el.getAttribute('href')) && el.getAttribute('href') == window.location.pathname) {
        el.classList.add("button-active");
    }
}