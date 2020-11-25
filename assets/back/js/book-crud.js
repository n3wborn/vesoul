// ELEMENTS TO TARGET
const delLinks = document.getElementsByClassName('delete-link')
const delModal = document.getElementById('delete-modal')
const confirmBtn = document.getElementById('confirm-delete')
const cancelBtn = document.getElementById('cancel-delete')



// FUNCTIONS


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
function showSuccess(seconds = 2000){
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


// Delete Modal -> Confirmed
// Send "delete" order, close modal and hide product (ie: hide until next page reload)
if (confirmBtn !== null) {
    confirmBtn.addEventListener('click', function() {
        const elementsToDelete = document.getElementsByClassName('readyToDelete')

        for (let elementToDelete of elementsToDelete) {
            url = elementToDelete.getAttribute('href')
            sendDeleteOrder(url)
            hideModal(delModal)
            elementToDelete.closest('tr').style.display = "none"
            //showSuccess(2500)
        }
    })
}



// Delete Modal -> Canceled
// Remove readyToDelete class and
if (cancelBtn !== null) {
    cancelBtn.addEventListener('click', function (){
        const elementsToDelete = document.getElementsByClassName('readyToDelete')

        for (let elementToDelete of elementsToDelete) {
            elementToDelete.classList.toggle('readyToDelete')
        }
    })
}


// function to update form input label with image names
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

// Once DOM is loaded, we do our job
document.addEventListener("DOMContentLoaded", () => {
    checkDelLinks();
    closeAlerts();
    updateInputLabel();
});