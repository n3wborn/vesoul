let url:string = 'http://localhost:8080/pannel-client/adresses';
let buttonEdit:NodeListOf<Element> = document.querySelectorAll('.edit-address');
let modalAddress:NodeListOf<Element> = document.querySelectorAll('.modal-address');
let addressId:Array<string> = [];

for (let i = 0; i < buttonEdit.length; i++) {

    let addressLink:string = modalAddress[i].id;
    let id:string = addressLink.slice(11);

    console.log(id);
    
    buttonEdit[i].addEventListener('click', () => {
        fetch('http://localhost:8080/pannel-client/adresses/' + id + '/edit', {
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

// fetch('http://localhost:8080/pannel-client/adresses/', {
//     method: 'POST',
// })
// .then(res => res.json())
// .then(data => {

//     console.log(data);
//     target.innerHTML = data

// })
// .catch((err) => { if (err) throw err;})