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
    for (let delLink of delLinks) {
        delLink.addEventListener('click', function(e){
            e.preventDefault()
            this.classList.toggle('readyToDelete')
        })
    }
}


// Delete Modal -> Confirmed
// Send "delete" order, close modal and hide product (ie: hide until next page reload)
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



// Delete Modal -> Canceled
// Remove readyToDelete class and
cancelBtn.addEventListener('click', function (){
    const elementsToDelete = document.getElementsByClassName('readyToDelete')

    for (let elementToDelete of elementsToDelete) {
        elementToDelete.classList.toggle('readyToDelete')
    }
})




// Once DOM is loaded, we do our job
document.addEventListener("DOMContentLoaded", () => {
    checkDelLinks();
    //checkEditLinks()
    //checkDetailsLinks()
});