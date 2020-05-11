<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;

use App\Repository\CartRepository;
use App\Repository\GenraRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\Query\Expr\GroupBy;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Security;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;

class VesoulEditionController extends AbstractController
{

    /**
     * @var float
     */
    public $totalCost = 0.00;

    /**
     * @Route("/", name="home")
     */
    public function home(Request $request, SessionInterface $session, BookRepository $repoBook, GenraRepository $repoGenra, AuthorRepository $repoAuthor)
    {
        

        if($session->get('panier')) {

            $panier = $session->get('panier');

        } else { 
            $session->set('panier', []);
        }
    
        $allBooks = $repoBook->findAllBooksByAscName();
        
        $genras = $repoGenra->findAll();
        $authors = $repoAuthor->findAll();
        $maxAndMinYear = $repoBook->maxAndMinYear();
        $minYear = $maxAndMinYear[0]['minyear'];
        $maxYear = $maxAndMinYear[0]['maxyear'];
        
        
        return $this->render('vesoul-edition/home.html.twig', [
            'genras' => $genras,
            'authors' => $authors,
            'minyear' => $minYear,
            'maxyear' => $maxYear

        ]);
    } 
    
    
     
   

     /**
     * @Route("/home/search/bytitle/{searchValue}", name="search-bytitle")
     */
    public function searchByTitle(Request $request, SessionInterface $session, BookRepository $repoBook,  GenraRepository $repoGenra, AuthorRepository $repoAuthor, string $searchValue) {
        
        $books = [];

        if( strlen( $searchValue ) > 0 ){
            $books = $repoBook->searchByTitle($searchValue);
        }
        
        if($session->get('panier')) {

            $panier = $session->get('panier');

        } else { 
            $session->set('panier', []);
        }

        $genras = $repoGenra->findAll();
        $authors = $repoAuthor->findAll();
        $maxAndMinYear = $repoBook->maxAndMinYear();
        $minYear = $maxAndMinYear[0]['minyear'];
        $maxYear = $maxAndMinYear[0]['maxyear'];
        
        return $this->render('vesoul-edition/home.html.twig', [
            'genras' => $genras,
            'authors' => $authors,
            'minyear' => $minYear,
            'maxyear' => $maxYear,
            'books'   => $books,
            'searchValue' => $searchValue

        ]);
        

    }

     /**
     * @Route("/home/search/ajax/{searchValue}", name="search-autocomplete")
     */
    public function autocomplete(Request $request, BookRepository $repoBook, string $searchValue) {
        
        $books = [];
        
        if( strlen( $searchValue ) >= 3 ){
            $books = $repoBook->findTitle($searchValue);
        }
        
        $response = new Response();
        if( count($books) > 0 ){
            
            $response->setContent(json_encode([
                'books' => $books,
            ]));
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');
            
        }else{
            
            $response->headers->set('Content-Type', 'text/plain');
            $response->setStatusCode(Response::HTTP_NO_CONTENT);

        }
        

        return $response;
    }

    /**
     * @Route("/home/load", name="load-home")
     */
    public function homeload(Request $request, BookRepository $repoBook) {
        
        $page = $request->get('page');
        $orderBy = $request->get('orderBy');
        $new = $request->get('new');
        $genre = strlen($request->get('genre')) > 0 ? explode(',', $request->get('genre')) : []; 
        $author = strlen($request->get('author')) > 0 ? explode(',', $request->get('author')) : [];
        $yearmin = $request->get('yearmin');
        $yearmax = $request->get('yearmax');
        $title = $request->get('title');

        $max_per_page = 9;

        $total_books = $repoBook->countBooks($new, $genre, $author, $yearmin, $yearmax, $title);
        $pages = ceil($total_books / $max_per_page);

        
        
        $offset = ($page - 1) * $max_per_page;

        

        $books = $repoBook->findPageOfListBook($offset, $orderBy, $new, $genre, $author, $yearmin, $yearmax, $title);
        $response = new Response();
       
        
        $response->setCharset('utf-8');
        $response->headers->set('Content-Type', 'text/html');
        $response->headers->set('X-TotalBooks', $total_books );
        $response->headers->set('X-TotalPage', $pages );
        $response->setStatusCode(Response::HTTP_OK);
        $response->send();
        return $this->render(
            'ajax/page-book.html.twig', 
            [
                'books' => $books
            ]
        );
        
        
    }


    /**
    * @Route("/ascName", name="sortByAscName")
    *
    * @param \App\Repository\BookRepository
    */
    public function sortByAscName(BookRepository $repo) : JsonResponse
    {
        $books = $repo->findAllBooksByAscName();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        $json = new JsonResponse($data, 200);

        return $json;
    }

    /**
     * 
     * 
    * @Route("/descName", name="sortByDescName")
    */
    public function sortByDescName(BookRepository $repo) : JsonResponse
    {
        $books = $repo->findAllBooksByDescName();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        $json = new JsonResponse($data, 200);

        return $json;
       
    }

    /**
    * @Route("/ascYear", name="sortByAscYear")
    */
    public function sortByAscYear(BookRepository $repo) : JsonResponse
    {
        $books = $repo->findAllBooksByAscYear();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        $json = new JsonResponse($data, 200);

        return $json;
    }

    /**
    * @Route("/descYear", name="sortByDescYear")
    */
    public function sortByDescYear(BookRepository $repo) : JsonResponse
    {
        $books = $repo->findAllBooksByDescYear();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        $json = new JsonResponse($data, 200);

        return $json;
    }

    /**
     * @Route("/panier/add/{id}", name="addItem")
     */
    public function addItem(Book $book, SessionInterface $session, ObjectManager $manager, BookRepository $repoBook)
    {
        $id = $book->getId();
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $price = $book->getPrice();
        $stock = $book->getStock();
        $images = $book->getImages();
        $image = $images[0]->getUrl(); // Juste la couverture du livre.


        

            
            $panier = $session->get('panier'); 
              
            if (array_key_exists($id, $panier)) {

                $quantityInPanier = $panier[$id]['quantity'];
                if ( ($stock - $quantityInPanier - 1 ) > 0) { 
                    $panier[$id]['quantity']++;
                }

            } else {
                
                $panier[$id] = [
                    'id' => $id,
                    'title'=> $title,
                    'firstname'=> $author->getFirstname(),
                    'lastname'=> $author->getLastname(),
                    'quantity'=> 1,
                    'price'=> $price,
                    'image' => $image               
                ];   
            }

            
            $session->set('panier', $panier);            
            return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/ajax/add/{id}", name="ajaxaddItem")
     */
    public function ajaxAddItem(Book $book, SessionInterface $session, ObjectManager $manager, BookRepository $repoBook)
    {
        $id = $book->getId();
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $price = $book->getPrice();
        $stock = $book->getStock();
        $images = $book->getImages();
        $image = $images[0]->getUrl(); // Juste la couverture du livre.
        $panier = $session->get('panier');
        $quantityInPanier = $panier[$id]['quantity'];
        
        if ( ($stock - $quantityInPanier - 1 ) > 0) { 
              
            if (array_key_exists($id, $panier)) {

                $panier[$id]['quantity']++;

            } else {
                
                $panier[$id] = [
                    'id' => $id,
                    'title'=> $title,
                    'firstname'=> $author->getFirstname(),
                    'lastname'=> $author->getLastname(),
                    'quantity'=> 1,
                    'price'=> $price,
                    'image' => $image               
                ];   
            }

            $session->set('panier', $panier);
            
            return new Response("OK", Response::HTTP_OK);
        } else {
            return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * @Route("/panier/ajax/reduce/{id}", name="reduceAjaxItem")
     */
    public function reduceAjaxItem(Book $book, SessionInterface $session, ObjectManager $manager)
    {   
        
        $id = $book->getId();
        
        $panier = $session->get('panier');
       
        if (array_key_exists($id, $panier) && ($panier[$id]['quantity'] - 1 ) >= 1) {
            
            $panier[$id]['quantity']--;            
            $session->set('panier', $panier);
            return new Response("OK", Response::HTTP_OK);

        } 

        return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Route("/panier/reduce/{id}", name="reduceItem")
     */
    public function reduceItem(Book $book, SessionInterface $session, ObjectManager $manager)
    {   
        
        $id = $book->getId();
        
        $panier = $session->get('panier');
       
        if (array_key_exists($id, $panier) && ($panier[$id]['quantity'] - 1 ) >= 1) {
            
            $panier[$id]['quantity']--;            
            $session->set('panier', $panier);
            return new Response("OK", Response::HTTP_OK);

        } 

        return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Route("/panier/ajax/delete/{id}", name="deleteAjaxItem")
     */
    public function deleteAjaxItem(Book $book, SessionInterface $session, ObjectManager $manager)
    {
        $id = $book->getId();
        $panier = $session->get('panier');
        
       
        if (array_key_exists($id, $panier)){
            unset($panier[$id]);
            $session->set('panier', $panier);
            return new Response("OK", Response::HTTP_OK);
        }
        

        return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
        
    }

    /**
     * @Route("/panier/delete/{id}", name="deleteItem")
     */
    public function deleteItem(Book $book, SessionInterface $session, ObjectManager $manager)
    {
        $id = $book->getId();
        $panier = $session->get('panier');
        
        

        unset($panier[$id]);
        $session->set('panier', $panier);

        

        return $this->redirectToRoute('panier');
        
    }

    /**
     * @Route("/product/{id}", name="product")
     */
    public function showProduct($id, BookRepository $repo)
    {
        $book = $repo->findBook($id);

        return $this->render('vesoul-edition/product.html.twig', [
            'book' => $book
        ]);
    }

    /**
     * @Route("/panier", name="panier")
     */
    public function showPanier(SessionInterface $session)
    {

        $panier = $session->get('panier');

        if( $panier === null ){
            return $this->render('vesoul-edition/panier.html.twig', [
                'total' => 0
            ]);
        }


        foreach ($panier as $elem) {
            $this->totalCost += $elem['price'] * $elem['quantity'];                
        }

        return $this->render('vesoul-edition/panier.html.twig', [
            'total' => $this->totalCost
        ]);
    }

    /**
     * @Route("/commande", name="commande")
     */
    public function showCommande(Security $security, SessionInterface $session)
    {
        $panier = $session->get('panier');
        $user = $security->getUser();
        
        //Si le panier est vide alors pas de commande
        //Prévenir que la personn
        if( $panier === null){
            return $this->redirectToRoute('panier');
        }
           
        
        
        if( $user === null ){
            
            $commande['confirmation'] = true;
            $session->set('commande', $commande);
            return $this->redirectToRoute('security_user_login');
        }
        

        return $this->render('vesoul-edition/commande.html.twig',
        [
            'user' => $user
        ]);
    }

    /**
     * @Route("/confirmation", name="commander")
     */
    public function showConfirmation()
    {
        return $this->render('vesoul-edition/confirmation.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

   
}
