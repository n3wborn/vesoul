require('./adminLogin');

// add_book checkbox / button
const chkBox:HTMLElement = document.getElementById('book_new');
const newBtn:HTMLElement = document.getElementById('book_newbtn');


// Date actuelle en français
var options = {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'};


// Function to toggle checked state of a checkbox
function toggleCheck(el) {
    if (el.checked === false) {
        el.checked = true;
        el.value = 1;
        return innerChange(newBtn, 'Livre inédit !');
    } else {
        el.checked = false;
        el.value = 0;
        return innerChange(newBtn, 'Livre déjà édité');
    }
}

// Function to replace el element innnerHTML value with string value
function innerChange(el, string) {
    el.innerHTML = '';
    return el.innerHTML = string;
}

// flip from true/false 1/0
newBtn.addEventListener('click', function () {
    toggleCheck(chkBox);
});


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
    if (window.location.pathname.startsWith(el.getAttribute('href'))) {
        el.classList.add("button-active");
    }
}