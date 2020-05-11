$('.slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: false,
    // adaptiveHeight: true,
    asNavFor: '.slider-nav'
});

console.log(document.querySelectorAll('.elem').length)

// TEST si moins de 4 elements, pas de carousel inférieur pour eviter les bugs
let slide = 0;
if(document.querySelector('.slick-track').childNodes.length == 0){
    let divNoImg = document.createElement('div');
    let noImg = document.createElement('h2');
    noImg.textContent = "Pas d'image";
    divNoImg.setAttribute('class', 'd-flex justify-content-center align-items-center')
    noImg.setAttribute('class', 'text-muted');
    divNoImg.appendChild(noImg);
    document.querySelector('.slider-for').appendChild(divNoImg);
} else if (document.querySelector('.slick-track').childNodes.length < 4) {
    slide = document.querySelectorAll('.elem').length;
}
else {
    slide = 3;
}
$('.slider-nav').slick({
    slidesToShow: slide,
    slidesToScroll: 1,
    asNavFor: '.slider-for',
    dots: false,
    arrows: false,
    centerMode: true,
    focusOnSelect: true
});
// TEST click sur l'element .slick-current afin d'avoir le carousel actif

document.querySelectorAll('.elem').forEach(element => {
    if (element.classList.contains('slick-current')) {
        element.click()
        element.focus()
    }
});