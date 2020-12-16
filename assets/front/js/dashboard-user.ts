let editAddressBtns:NodeListOf<Element> = document.querySelectorAll('.btn-edit-address');
let addressId:Array<string> = [];

for (let editAddressBtn:Element of editAddressBtns) {

    editAddressBtn.addEventListener('click', function(e) {
        //e.preventDefault()
        console.log();

        fetch(this.getAttribute('href'), {
            method: 'POST',
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                "datas": this.dataset
            })
        })

        .then(res => res.json())
        .then(data => {
            console.log(data);
            //target.innerHTML = data

        })
        .catch((err) => { if (err) throw err;})
    })
}