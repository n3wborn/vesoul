let buttonEdit:NodeListOf<Element> = document.querySelectorAll('.edit-address');
let modalAddress:NodeListOf<Element> = document.querySelectorAll('.modal-address');
let addressId:Array<string> = [];

for (let i = 0; i < buttonEdit.length; i++) {

    let addressLink:string = modalAddress[i].id;
    let id:string = addressLink.slice(11);

    console.log(id);
    
    buttonEdit[i].addEventListener('click', () => {
        fetch('/panel-client/adresses/' + id + '/edit', {
            method: 'POST',
        })

        .then(res => res.json())
        .then(data => {

            console.log(data);
            //target.innerHTML = data

        })
        .catch((err) => { if (err) throw err;})
    })
}