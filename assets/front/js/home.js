
//Variables globales de la page home ====================================

const bookCollection = document.querySelector('#book-collection');
const checkNews = document.querySelector('#news');
const filterButtons = document.querySelectorAll('.expand-button');
const genra = document.querySelector('#genra-list');
const author = document.querySelector('#author-list');
const checksFilter = document.querySelectorAll('#genra-list .form-check-input, #author-list .form-check-input ');
const genraButton = document.querySelector('#expand-genra');
const authorButton = document.querySelector('#expand-author');
const slider = document.querySelector('#year-slider');
const yearButton = document.querySelector('#expand-year');
const itemList = document.getElementById('sort-select');
const loader = document.querySelector(".loader");
const wrapperBooks = document.querySelector("#book-collection");
const btnApplyFilter = document.querySelector("#applyFilter");
const btnDesactivateFilter = document.querySelector("#desactivateFilter");
const sliderYear = document.querySelectorAll('.range');
const btnSearch = document.querySelector('.btn-search');
const inptSarch = document.querySelector('.search-bar');
const btnQuantity = document.querySelectorAll('.btn-quantity');
const btnDelete = document.querySelectorAll('.delete-article');

const filter = {
  nouveaute: false,
  genre: [],
  author: [],
  year:{
    min: 0,
    max: 0
  },
  title : ''
}

let totalPages = 0;
let page = 1;
let ticking = false;
let orderBy = 'ascName';
//=======================================================================

//Objets de la page home ================================================

class filters {
    constructor(news, price, genra, author){
        this.news = false;
        this.price = [];
        this.genra = [];
        this.author = [];
    }
    //Getter
    get news(){

    }
    //Setter

    //Method

}

class book {
    constructor(image, title, author, year, price, news){
        this.image = '';
        this.title = '';
        this.author = {
            'firstname' : '', 
            'lastname' : ''
        };
        this.year = 0;
        this.price = 0;
        this.news = false;
    }
    //Getter

    //Setter

    //Method
}
//=======================================================================

// Scripts executés au chargement =======================================

window.addEventListener('load', function(){

    // Ouverture des sections de filtrage
    filterButtons.forEach((elem)=>{
        elem.addEventListener('click', (e)=>{
        
            e.preventDefault();
    
            let activeButton = e.target.id;
            let button = '';
            let target = '';

            switch(activeButton){
                case 'expand-genra' :
                    button = genraButton;
                    target = genra;
                    break;
                case 'expand-year' :
                    button = yearButton;
                    target = slider;
                    break;
                case 'expand-author' :
                    button = authorButton;
                    target = author;
                    break;
            }
            
            displayFilters(button, target);
        });
    });

    if( filterButtons.length > 0 ){
      //Récupération des années
      applyYearFilter();
    }
    
    booksCollection = document.querySelector('#book-collection');
    
    if( booksCollection !== null ){
      if( booksCollection.childElementCount === 0 ){
        fetchBooks();
        ticking = true;
      }
    }

    
});

if( itemList !== null){
  // Ecoute de la selection du tri apres chargement de la page   
  itemList.addEventListener('change', ()=>{

    areaDescribeSearch = document.querySelector('.search-result-phras');

    if( areaDescribeSearch !== null ){
      areaSearchKeyword = areaDescribeSearch.querySelector('.search-keyword');
      valueSearch = areaSearchKeyword.innerText;
      valueSearch = valueSearch.slice( 1, - 1 );
      filter.title = valueSearch;
    }

    orderBy = itemList.value;
    page = 1;

    wrapperBooks.innerHTML = '';
    loader.classList.add("loader-on");
    fetchBooks();
    ticking = true;
    
  });
}


//=============================================

if( checkNews !== null ){
  //Activation on non de la fonction nouveauté
  checkNews.addEventListener('change', function(){
    filter.nouveaute = !filter.nouveaute;
    orderBy = itemList.value;
    page = 1;

    wrapperBooks.innerHTML = '';
    loader.classList.add("loader-on");
    fetchBooks();
    ticking = true;
  })
}

//=============================================
//Sur clique des cases genres 
for( let item of checksFilter){
  item.addEventListener('change', (evt)=>{
    
    const  elChecked = evt.currentTarget;
    let choiceId = elChecked.getAttribute('id');
    let  typeFilter = elChecked.dataset.type;
    const zoneBadge = document.querySelector('#badges');

    if( elChecked.checked ){
      
      filter[typeFilter].push(choiceId);
      

      //ajout du bagde      
      const newBadge = document.createElement('div');
      const listClass = ['badge-filter', 'px-2',  'd-flex', 'align-items-center', 'mr-1', 'mb-1'];
      const newBadgeTexte = document.createElement('p');
      const listClassTexte = ['m-0', 'p-0', 'mr-2'];
      const newBadgeClose = document.createElementNS('http://www.w3.org/2000/svg','svg');
      const listClassClose = ['svg-inline--fa', 'fa-times-circle', 'fa-w-16'];
      const newBadgeClosePath = document.createElementNS('http://www.w3.org/2000/svg','path');
      
      newBadge.classList.add(...listClass);
      newBadge.setAttribute('data-value', choiceId );
      newBadge.addEventListener('click', evt => {

        baliseHasClicked = evt.currentTarget;        
        choiceId = baliseHasClicked.dataset.value;
        baliseHasClicked.remove();
        inputWantDesactivate = document.querySelector('#'+ choiceId );
        inputWantDesactivate.checked = false;
        typeFilter = inputWantDesactivate.dataset.type
        removeAndUpdateFilter(choiceId, typeFilter);
      });
      
      newBadgeTexte.classList.add(...listClassTexte);
      newBadgeTexte.innerText = choiceId;
      newBadge.appendChild(newBadgeTexte);

      newBadgeClose.classList.add(...listClassClose);
      newBadgeClose.setAttribute('aria-hidden', "true");
      newBadgeClose.setAttribute('data-prefix', "fas");
      newBadgeClose.setAttribute('data-icon', "times-circle");
      newBadgeClose.setAttribute('role', "img");
      newBadgeClose.setAttribute('viewBox', "0 0 512 512");
      newBadgeClose.setAttribute('data-fa-i2svg', "");
      newBadge.appendChild(newBadgeClose);
      
      
      newBadgeClosePath.setAttribute('fill', "currentColor");
      newBadgeClosePath.setAttribute('d', "M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z");
      newBadgeClose.appendChild(newBadgeClosePath);      
      zoneBadge.appendChild(newBadge);

    }else{
      
      removeAndUpdateFilter(choiceId, typeFilter);
      badge = zoneBadge.querySelector('div[data-value="'+choiceId+'"]');
      badge.remove();
    }
    
    
    
    
  });
}


//=================================================
if( btnApplyFilter !== null ){
  //Apllique les filtres de recherches
  btnApplyFilter.addEventListener('click', function(){
    applyYearFilter();
    orderBy = itemList.value;
    page = 1;

    //Si je suis sur la page search
    //Ajouter le titre dans le filter
    if( document.querySelector('.search-keyword')){
      elSearchKeyword = document.querySelector('.search-keyword');
      searchKeyword = elSearchKeyword.innerText.slice(1,-1);
      filter.title = searchKeyword;
    }

    wrapperBooks.innerHTML = '';
    loader.classList.add("loader-on");
    fetchBooks();
    ticking = true;
    

  });
}

if( btnDesactivateFilter !== null ){
  btnDesactivateFilter.addEventListener('click', () => {

    resetFilter();

  });
}



btnSearch.addEventListener('click', (evt) =>{
  
  inptSarch.classList.remove('is-invalid');

  //Si pas de saisie ou saisi
  if( inptSarch.value.trim().length === 0 ){
    inptSarch.classList.add('is-invalid');
    return;
  }

  searchValue = inptSarch.value.trim();
  formSearch = document.querySelector('.form-search');
  formSearch.action = `/home/search/bytitle/${searchValue}`;
  formSearch.submit();


});

inptSarch.addEventListener('keypress', (evt) => {
  
  //Récup des éléments du script
  element = evt.currentTarget;
  autocomplete = document.querySelector('.search-autocomplete');

  //test si on appui sur la touche return et si c'est le cas on sort de l'événement
  if( (evt.keyCode || evt.which) == 13   ){
    evt.preventDefault();
    return false;
  }

  //Tester si moins de 3 caractères saisie
  if( element.value.trim().length < 3 ){
    if( autocomplete !== null ){
      autocomplete.remove();
    }

    return;
  }

  

  
    
  //On récupère le contenu saisie
  searchValue = element.value;

  //appel ajax
  fetch(`/home/search/ajax/${searchValue}`)
  .then( response => {

    //Si pas de réponse 
    if(response.status === 204 ){
      
      //on supprime la liste
      if( autocomplete !== null ){
        autocomplete.remove();
      }

      //on arrête la promesse
      throw new Error('handled');

    }

    //Conversion en json si reponse
    return response.json();

  })
  .then( data => {

    //on va vérifier si on n'est null
    if( data === null   ){
      
      if( autocomplete !== null){
        autocomplete.remove();
      }

      return;
    }
    


    
    //on va créer créer la zone autocomplete
    if( document.querySelector('.search-autocomplete') === null ){
      formSearch = document.querySelector('.form-search');
      autocomplete = document.createElement('div');
      autocomplete.style.position = 'absolute';
      autocomplete.style.top = element.offsetHeight+"px";
      autocomplete.style.left = ( element.offsetLeft  ) +"px";
      autocomplete.style.width = (element.offsetWidth ) +"px";
      autocomplete.style.minHeight = "100px";
      autocomplete.style.backgroundColor = "#fff";
      autocomplete.style.borderLeft = "solid #00BCD4 1px";
      autocomplete.style.borderRight = "solid #00BCD4 1px";
      autocomplete.style.zIndex = 10;
      autocomplete.classList.add('search-autocomplete');
      formSearch.appendChild(autocomplete);
    }

    listPropositionExist = autocomplete.querySelectorAll('.search-autocomplete-proposition');
    
    for( item of listPropositionExist){
      item.remove();
    }

    for( item of data.books ){      
      newP = document.createElement('p');
      newP.innerText = item.title;
      newP.setAttribute('data-title' , item.title );
      newP.classList.add('search-autocomplete-proposition');
      newP.addEventListener('click', evt => {
        elementHasClicked = evt.currentTarget;
        element.value = elementHasClicked.innerText;
        autocomplete.remove();
      })
      autocomplete = document.querySelector('.search-autocomplete');
      autocomplete.appendChild(newP);
    }


  })
  .catch(error => {
    console.log(error);
  });

  


});

document.addEventListener('click', evt =>{
  elementHasClicked = evt.target;
  if( !elementHasClicked.classList.contains('search-autocomplete-proposition')){
    areaAutocomplete = document.querySelector('.search-autocomplete');
    if( areaAutocomplete !== null ){
      areaAutocomplete.remove();
    }
  }
})

function removeAndUpdateFilter(choiceId, typeFilter){

  
  indexInArrayGenre = filter[typeFilter].findIndex( (element)=>  element == choiceId );
  filter[typeFilter].splice(indexInArrayGenre,1);

}


function resetFilter(){
  
  //Si on n'est sur la page search alors
  //on redirige la personne sur la page d'acceuil
  areaDescribeSearch = document.querySelector('.search-result-phras');
  if( areaDescribeSearch !== null ){
    window.location.replace("/");
    return;
  }
  
  //Désactiver new
  if( checkNews.checked == true ){
    checkNews.checked = false;
    filter.nouveaute = false;
  } 

  //Désactiver les années
  initialValues = resetSlider();
  filter.year.min = initialValues[0];
  filter.year.max = initialValues[1];


 //Désactiver les genres et auteurs
  for( let item of checksFilter ){
    if( item.checked === true ){
      item.checked = false;
    }
  }

  badgeFilter = document.querySelectorAll('.badge-filter');
  for( let item of badgeFilter){
    item.remove();
  }

  filter.genre = [];
  filter.author = [];

  //réinitialisation de la case de formulaire
  inptSarch.value = '';
  inptSarch.classList.remove('is-invalid');
  
  

  //on recharge la page
  orderBy = itemList.value;
  page = 1;

  wrapperBooks.innerHTML = '';
  loader.classList.add("loader-on");
  fetchBooks();
  ticking = true;
}

//=======================================================================

// Fonctions ============================================================

// Fonction qui va chercher en Ajax le nouvel ordre des livres à afficher
function fetchNewOrder(route, target){
    
    let url = 'http://localhost:8080'.concat(route);

    fetch(url, {
        method: 'POST',
    })
    .then(res => res.json())
    .then(data => {

        displayBooks(target, data);

    })
    .catch((err) => { if (err) throw err;})
}

//Fonction qui applique les filtres au livres 
// function applyFilters(){

// }

//Fonction qui affiche les livres dans le DOM
function displayBooks(target, content){
    
    let strContent = content.join('');
    console.log(strContent);
    target.innerHTML = strContent;

}

//Fonction d'affichage/masquage des filtres
function displayFilters(button, target)
{
        button.classList.toggle('active');
        target.classList.toggle('active');
}


function applyYearFilter(){
    amount = document.querySelector('#amount');
    years  = amount.value.split('-');
    filter.year.min = years[0].trim();
    filter.year.max = years[1].trim();
}

//========================================================================





  
  window.addEventListener('scroll', function (e) {
    pageHeight = document.querySelector('.wrapper').offsetHeight;
    footer = document.querySelector('footer').offsetHeight;
    windowHeight = window.innerHeight;
    scrollPosition = window.scrollY ||  window.pageYOffset || document.body.scrollTop + (document.documentElement && document.documentElement.scrollTop || 0 );
    if( (pageHeight-footer) <= windowHeight+scrollPosition ){
      if( page <= totalPages ){
        if (ticking === false) {
          onScrollFetch();
          ticking = true;
        }
      }
    }
  });
  
  function onScrollFetch() {    
    if( page <= totalPages ){
      if(ticking === false){
        loader.classList.add("loader-on");
        fetchBooks();
      } 
    } 
  }
  
  function fetchBooks() {
    
    if(ticking === false){

      fetch(`/home/load?page=${page}&orderBy=${orderBy}&new=${filter.nouveaute}&genre=${[...filter.genre]}&author=${[...filter.author]}&yearmin=${filter.year.min}&yearmax=${filter.year.max}&title=${filter.title}`, {
          method: 'GET'
        })
        .then(res => {      
          const totalBooks = res.headers.get('X-TotalBooks');
          totalPages = res.headers.get('X-TotalPage');
          elTotalBooks = document.querySelector('#totalPage')
          elTotalBooks.innerHTML = totalBooks;
          if( totalBooks == 0 ){
            if( document.querySelector('.search-keyword') !== null ){
              wrapperBooks.innerHTML += `<p class="p-4 mt-2 alert alert-dark" > Aucun livre ne correspond à votre recherche.  <span class="font-italic" >Modifier vos mots-clés</span> et <span class="font-weight-bold" >rechercher à nouveau</span> ou cliquez sur le lien <span class="font-weight-bold" >'Afficher tous les livres'.</span></p>
              <p class="w-100" ><a href="/"  class="btn btn-info btn-lg btn-block">Afficher tous les livres</a></p>`;
            }else{
              wrapperBooks.innerHTML += `<p class="p-4 ml-2 mt-2 alert alert-dark" > Aucun livre ne correspond à vos critères de recherches. <span class="font-italic" >Modifiez vos critères et cliquez</span> à nouveau sur <span class="font-weight-bold" >'Appliquer les filtres'</span> ou cliquez sur le bouton <span class="font-weight-bold" >'Enlever tous les filtres'.</span></p>`;
            }
            loader.classList.remove('loader-on');
            page = 1;
            ticking = false;
            throw new Error('handled');
          }
          return res.text();
          
        })
        .then(res => {
  
          if (loader.classList.contains("loader-on")) {
            loader.classList.remove("loader-on");
  
          }
          
          
          wrapperBooks.innerHTML+= res;
          ticking = false;
          page++;
          
          
        })
        .catch(err => {
          if (err) throw err;
        });
    }
  }

  //Page panier
 
  if( btnQuantity.length > 0 ){
    btnQuantity.forEach( (element) => {
      element.addEventListener('click', async (e) => {  
        
        e.preventDefault();  
        
        const  elementHasClicked = e.currentTarget;
        const action = elementHasClicked.dataset.action;
        const idProduct = parseInt( elementHasClicked.dataset.id, 10 );
        const price = parseFloat( elementHasClicked.dataset.price, 10 );
        const  elementQuantity = elementHasClicked.parentNode.children[0];
        const elementTotalProduct = elementHasClicked.parentNode.nextElementSibling;
        const elementTotalArticles = document.querySelectorAll('.article .total');
        const elementsTotals = document.querySelectorAll('#subtotal, #totalpanier')
        let quantity = ( elementQuantity === null )? 0 : parseInt(elementQuantity.innerText, 10 );
        let total = 0;

        //Vérifier si l'objet est en stock

        let response = await fetch(`/panier/ajax/${action}/${idProduct}`);
        let status = await response.status
       
        if( status === 406 ){          
          return;
        }

        if( elementQuantity === null){
          return;
        }

        if( elementTotalProduct === null ){
          return;
        }

        if( elementTotalArticles.length === 0 ){
          return;
        }

        if( elementsTotals.length === 0 ){
          return;
        }

        if( isNaN( idProduct )){
          return;
        }        

        if( isNaN( price )){
          return;
        }       



        switch( action ){
          case 'add':
            quantity += 1;
            break;

          case 'reduce' :
            if( quantity > 1 ){
              quantity -= 1;
            } 
            break;

          default : 
            return;
        }
        

        
          
        elementQuantity.innerText = quantity;
        total = quantity * price;
        elementTotalProduct.innerText = new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR' }).format(total);

        //Mise à jour du panier
        
        let totalPannier = 0;

        elementTotalArticles.forEach( (element ) => {
          totalPannier += parseFloat(element.innerText);
        }); 
        
        elementsTotals.forEach( element => {          
          element.innerText = new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR' }).format(totalPannier);
        });


        //Mise à jour du panier côté back



      }, false);
    });
  }

  if( btnDelete.length > 0 ){
    btnDelete.forEach( (element)=>{
      element.addEventListener('click', async (e) =>{
        e.preventDefault();
        const  elementHasClicked = e.currentTarget;
        const idProduct = elementHasClicked.dataset.id;
        const rowArticle = elementHasClicked.parentNode.parentNode;
        let elementTotalArticles = null;
        let elementsTotals = null;
        const badge = document.querySelector('#nb-items');

        
        //Suppression session panier
        let response = await fetch(`/panier/ajax/delete/${idProduct}`);
        let status = await response.status;

        if( status === 406){
          return;
        }

        if( rowArticle === null){
          return;
        }

        if( badge === null){
          return;
        }

        rowArticle.remove();

        //Mise à jour du panier
        
        let totalPannier = 0;
        elementTotalArticles = document.querySelectorAll('.article .total');
        elementsTotals = document.querySelectorAll('#subtotal, #totalpanier');

        elementTotalArticles.forEach( (element ) => {
          totalPannier += parseFloat(element.innerText);
        }); 
        
        elementsTotals.forEach( element => {  
          element.innerText = new Intl.NumberFormat('fr-FR', {style: 'currency', currency: 'EUR' }).format(totalPannier);
        });

        badge.innerText = elementTotalArticles.length;
        
      });
    });
  }




  
    