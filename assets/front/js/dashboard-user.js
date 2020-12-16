// set current user dashboard section link active
const activeCurrentLink = () => {
    let userInfosLinks = document.getElementsByClassName('list')

    for (let infoLink of userInfosLinks) {
        if (infoLink.href.endsWith(window.location.pathname)) {
            infoLink.classList.add('active')
        }
    }
}


// Once DOM is loaded, we do our job
document.addEventListener("DOMContentLoaded", () => {
    activeCurrentLink();
});