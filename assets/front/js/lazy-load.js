


window.addEventListener('DOMContentLoaded', (e) => {
  
  fetchBooks();
  ticking = true;
});

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
    fetch(`home/load?page=${page}`, {
        method: 'GET'
      })
      .then(res => {      
        const totalBooks = res.headers.get('X-TotalBooks');
        totalPages = res.headers.get('X-TotalPage');
        elTotalBooks = document.querySelector('#totalPage')
        elTotalBooks.innerHTML = totalBooks;
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