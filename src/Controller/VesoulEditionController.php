<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\CartRepository;
use App\Repository\GenraRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $manager;
    private SessionInterface $session;
    private BookRepository $repoBook;
    private GenraRepository $repoGenra;
    private AuthorRepository $repoAuthor;


    public function __construct(EntityManagerInterface $manager,
                                SessionInterface $session,
                                BookRepository $repoBook,
                                GenraRepository $repoGenra,
                                AuthorRepository $repoAuthor)
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->repoBook = $repoBook;
        $this->repoGenra = $repoGenra;
        $this->repoAuthor = $repoAuthor;
    }


    /**
     * @var float
     */
    public $totalCost = 0.00;


    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        if($this->session->get('panier')) {
            $panier = $this->session->get('panier');
        } else {
            $this->session->set('panier', []);
        }

        $allBooks = $this->repoBook->findAllBooksByAscName();
        $genras = $this->repoGenra->findAll();
        $authors = $this->repoAuthor->findAllAuthors();
        $maxAndMinYear = $this->repoBook->maxAndMinYear();
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
    public function searchByTitle(Request $request, string $searchValue) {
        
        $books = [];

        if (strlen($searchValue) > 0 ) {
            $books = $this->repoBook->searchByTitle($searchValue);
        }
        
        if($this->session->get('panier')) {
            $panier = $this->session->get('panier');
        } else {
            $this->session->set('panier', []);
        }

        $genras = $this->repoGenra->findAll();
        $authors = $this->repoAuthor->findAllAuthors();
        $maxAndMinYear = $this->repoBook->maxAndMinYear();
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
    public function autocomplete(string $searchValue) {
        
        $books = [];
        
        if( strlen( $searchValue ) >= 3 ){
            $books = $this->repoBook->findTitle($searchValue);
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
    public function homeload(Request $request) {
        
        $page = $request->get('page');
        $orderBy = $request->get('orderBy');
        $new = $request->get('new');
        $genre = strlen($request->get('genre')) > 0 ? explode(',', $request->get('genre')) : []; 
        $author = strlen($request->get('author')) > 0 ? explode(',', $request->get('author')) : [];
        $yearmin = $request->get('yearmin');
        $yearmax = $request->get('yearmax');
        $title = $request->get('title');

        $max_per_page = 9;
        $total_books = $this->repoBook->countBooks($new, $genre, $author, $yearmin, $yearmax, $title);
        $pages = ceil($total_books / $max_per_page);
        $offset = ($page - 1) * $max_per_page;
        $books = $this->repoBook->findPageOfListBook($offset, $orderBy, $new, $genre, $author, $yearmin, $yearmax, $title);

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
    * @param \App\Repository\BookRepository
    */
    public function sortByAscName() : JsonResponse
    {
        $books = $this->repoBook->findAllBooksByAscName();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        return new JsonResponse($data, 200);
    }


    /**
     * @Route("/descName", name="sortByDescName")
     */
    public function sortByDescName() : JsonResponse
    {
        $books = $this->repoBook->findAllBooksByDescName();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        return new JsonResponse($data, 200);
    }


    /**
    * @Route("/ascYear", name="sortByAscYear")
    */
    public function sortByAscYear() : JsonResponse
    {
        $books = $this->repoBook->findAllBooksByAscYear();
        $arrayBooks = [];
        $data = [];
        $i = 0;

        foreach($books as $key => $book){
            $i++;
            $arrayBooks[$key + 1] = $this->render('ajax/book.html.twig', ['book' => $book]);
            $data[] = $arrayBooks[$i]->getContent();
        }

        return new JsonResponse($data, 200);
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
    public function addItem(Book $book)
    {
        $id = $book->getId();
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $price = $book->getPrice();
        $stock = $book->getStock();
        $images = $book->getImages();
        $image = $images[0]->getUrl(); // Juste la couverture du livre.
        $panier = $this->session->get('panier');

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
        $this->session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }


    /**
     * @Route("/panier/ajax/add/{id}", name="ajaxaddItem")
     */
    public function ajaxAddItem(Book $book)
    {
        $id = $book->getId();
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $price = $book->getPrice();
        $stock = $book->getStock();
        $images = $book->getImages();
        $image = $images[0]->getUrl(); // Juste la couverture du livre.
        $panier = $this->session->get('panier');
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

            $this->session->set('panier', $panier);
            
            return new Response("OK", Response::HTTP_OK);
        } else {
            return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
        }
    }


    /**
     * @Route("/panier/ajax/reduce/{id}", name="reduceAjaxItem")
     */
    public function reduceAjaxItem(Book $book)
    {   
        
        $id = $book->getId();
        
        $panier = $this->session->get('panier');
       
        if (array_key_exists($id, $panier) && ($panier[$id]['quantity'] - 1 ) >= 1) {
            
            $panier[$id]['quantity']--;            
            $this->session->set('panier', $panier);
            return new Response("OK", Response::HTTP_OK);

        } 

        return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
    }


    /**
     * @Route("/panier/reduce/{id}", name="reduceItem")
     */
    public function reduceItem(Book $book)
    {   
        
        $id = $book->getId();
        $panier = $this->session->get('panier');
       
        if (array_key_exists($id, $panier) && ($panier[$id]['quantity'] - 1 ) >= 1) {
            
            $panier[$id]['quantity']--;            
            $this->session->set('panier', $panier);
            return new Response("OK", Response::HTTP_OK);

        } 

        return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
    }


    /**
     * @Route("/panier/ajax/delete/{id}", name="deleteAjaxItem")
     */
    public function deleteAjaxItem(Book $book)
    {
        $id = $book->getId();
        $panier = $this->session->get('panier');

        if (array_key_exists($id, $panier)){
            unset($panier[$id]);
            $this->session->set('panier', $panier);
            return new Response("OK", Response::HTTP_OK);
        }

        return new Response("Not Acceptable", Response::HTTP_NOT_ACCEPTABLE);
    }


    /**
     * @Route("/panier/delete/{id}", name="deleteItem")
     */
    public function deleteItem(Book $book)
    {
        $id = $book->getId();
        $panier = $this->session->get('panier');

        unset($panier[$id]);
        $this->session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }


    /**
     * @Route("/product/{id}", name="product")
     */
    public function showProduct($id)
    {
        $book = $this->repoBook->findBook($id);

        return $this->render('vesoul-edition/product.html.twig', [
            'book' => $book
        ]);
    }


    /**
     * @Route("/panier", name="panier")
     */
    public function showPanier()
    {

        $panier = $this->session->get('panier');

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
    public function showCommande(Security $security)
    {
        $panier = $this->session->get('panier');
        $user = $security->getUser();
        
        //Si le panier est vide alors pas de commande
        //Prévenir que la personn
        if( $panier === null){
            return $this->redirectToRoute('panier');
        }

        if( $user === null ){
            
            $commande['confirmation'] = true;
            $this->session->set('commande', $commande);
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
