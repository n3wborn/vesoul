// ELEMENTS TO TARGET
const delLinks = document.getElementsByClassName('delete-link')
const delModal = document.getElementById('delete-modal')
const confirmBtn = document.getElementById('confirm-delete')
const cancelBtn = document.getElementById('cancel-delete')
const chkBox = document.getElementById('book_new')
const newBtn = document.getElementById('book_newbtn')


// FUNCTIONS

// Toggle checked state of a checkbox
function toggleCheck(el) {
    if (el.checked === false) {
        el.checked = true;
        el.value = 1;
        newBtn.classList.replace('btn-outline-secondary', 'btn-success')
        return innerChange(newBtn, 'Nouveauté !');
    } else {
        el.checked = false;
        el.value = 0;
        newBtn.classList.replace('btn-success', 'btn-outline-secondary') // be sure to have good color
        return innerChange(newBtn, 'Livre déjà édité');
    }
}


// Replace el element "innerHTML" value with string value
function innerChange(el, string) {
    el.innerHTML = '';
    return el.innerHTML = string;
}


// jQuery stuff to hide Bootstrap modal
function hideModal(el){
    $(el).modal('hide');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
}


// Auto close alerts
const closeAlerts = () => {
    setTimeout(function() {
        $(".alert").alert('close');
    }, 1000);
}


// During seconds, show the first hidden (d-none) element
// having an alert-success class
const showSuccess = (seconds = 2000) => {
    console.log('show success message')
    setTimeout( function() {
    console.log('hide success message')
    }, seconds)
}


// Send deletion order to the server
async function sendDeleteOrder(url) {
    let {status} = await fetch(url, {method: 'POST'})
    return status === 200;
}


// Check for product to delete
function checkDelLinks() {
    if (delLinks !== null) {
        for (let delLink of delLinks) {
            delLink.addEventListener('click', function(e){
                e.preventDefault()
                this.classList.toggle('readyToDelete')
            })
        }
    }
}


// Update form input label with image names
const updateInputLabel = () => {
    let input = document.querySelector('.custom-file-input')

    if(input !== null) {

        input.addEventListener('change', () => {

            // target and empty label
            let label = document.querySelector('label.custom-file-label')
            label.innerText = ''

            // fill label with image(s) name(s)
            for (let i = 0; i < input.files.length; i++) {
                label.innerText += ` ${input.files[i].name}`
            }
        })
    }
}


// Check if images need to be deleted
const checkImgDelLinks = () => {
    let links = document.querySelectorAll("[data-delete]")

    if (links !== null) {
        for(link of links){
            link.addEventListener("click", function(e){
                e.preventDefault()

                fetch(this.getAttribute("href"), {
                    method: 'DELETE',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    response => response.json()
                ).then(data => {
                    if(data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            })
        }
    }
}


// Check if a new author must be created
// TODO: make this a bit less quick and dirty
const checkAddAuthor = () => {
    let links = document.querySelectorAll("[data-addauthor]")

    if (links !== null) {

        for(link of links){
            link.addEventListener("click", function(e){
                e.preventDefault()

                // trigger modal
                $('#new-author-modal').modal('show')

                // if "submit"
                $('#new-author-modal').on('click','#confirm-new-author', function (e) {

                    // get user input
                    let firstname = $('#firstname').val()
                    let lastname =  $('#lastname').val()

                    // fetch infos to server using json
                    fetch('/admin/author/new', {
                        method: 'POST',
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            "_token": link.dataset.token,
                            "firstname": firstname,
                            "lastname": lastname,
                        })
                    }).then(
                        response => response.json()
                    ).then(
                        data => {
                            if(data.success) {
                                // if server ok, remove previous select
                                let selectElement = document.getElementById('book_author')
                                selectElement.selectedIndex = -1

                                // add and select new option
                                let newOption = new Option(`${data.firstname} ${data.lastname}`, `${data.author_id}`, true, true);
                                selectElement.add(newOption, undefined);

                                // show result in selectpicker
                                $('.selectpicker').selectpicker('refresh')

                                //  and hide modal
                                //hideModal(document.getElementById('#new-author-modal'))
                                $('#new-author-modal').modal('toggle')

                            } else {
                                // if error show message
                                alert(data.error)
                            }
                    }).catch(
                        e => alert(e)
                    )
                })

            })
        }
    }
}


// Check if a new genre must be created
// TODO: make this a bit less quick and dirty
const checkAddGenre = () => {
    let links = document.querySelectorAll("[data-addgenre]")

    if (links !== null) {

        for(link of links){
            link.addEventListener("click", function(e){
                e.preventDefault()

                // trigger modal
                $('#new-genre-modal').modal('show')

                // if "submit"
                $('#new-genre-modal').on('click','#confirm-new-genre', function (e) {

                    // get user input
                    let genre =  $('#genre').val()

                    // fetch infos to server using json
                    fetch('/admin/genre/new', {
                        method: 'POST',
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            "_token": link.dataset.token,
                            "genre": genre,
                        })
                    }).then(
                        response => response.json()
                    ).then(
                        data => {
                            if(data.success) {
                                // if server ok, add and select new genre
                                let selectElement = document.getElementById('book_genres')
                                let newOption = new Option(`${data.genre}`, `${data.genre_id}`, true, true);
                                selectElement.add(newOption, undefined);

                                // show result in selectpicker
                                // and hide modal
                                $('.selectpicker').selectpicker('refresh')
                                $('#new-genre-modal').modal('toggle')

                            } else {
                                // if error show message
                                alert(data.error)
                            }
                    }).catch(
                        e => alert(e)
                    )
                })
            })
        }
    }
}


// LOGIC

// If chkBox lives in, newBtn flip it's state from checked/unchecked
if (chkBox !== null) {
    newBtn.addEventListener('click', function () {
        toggleCheck(chkBox);
    })
}


// if a book deletion is confirmed :
// Send "delete" order, close modal and hide product (ie: hide until next page reload)
if (confirmBtn !== null) {
    confirmBtn.addEventListener('click', function() {
        const elementsToDelete = document.getElementsByClassName('readyToDelete')

        for (let elementToDelete of elementsToDelete) {
            url = elementToDelete.getAttribute('href')
            sendDeleteOrder(url)
            hideModal(delModal)
            elementToDelete.closest('tr').style.display = "none"
            // TODO: show Bootstrap Toast to show success message
            //showSuccess(2500)
        }
    })
}



// If book deletion is canceled :
// Remove readyToDelete class
if (cancelBtn !== null) {
    cancelBtn.addEventListener('click', function (){
        const elementsToDelete = document.getElementsByClassName('readyToDelete')

        for (let elementToDelete of elementsToDelete) {
            elementToDelete.classList.toggle('readyToDelete')
        }
    })
}


// Once DOM is loaded, we do our job
document.addEventListener("DOMContentLoaded", () => {
    checkDelLinks();
    closeAlerts();
    updateInputLabel();
    checkImgDelLinks();
    checkAddAuthor();
    checkAddGenre();
});