let checkHomme = document.querySelector(".homme") as HTMLInputElement;
let checkFemme = document.querySelector(".femme") as HTMLInputElement;

if (checkHomme && checkFemme) {

    checkHomme.addEventListener('click', (e) => {
        checkFemme.checked = false;
    })
    
    checkFemme.addEventListener('click', (e) => {
        checkHomme.checked = false;
    })
}