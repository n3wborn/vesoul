<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Order;
use App\Manager\CartManager;
use App\Factory\OrderFactory;
use App\Manager\MailManager;
use App\Form\CartType;
use App\Repository\AddressRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\Repository\AuthorRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VesoulEditionController extends AbstractController
{
    private CartManager $cartManager;
    private AuthorRepository $authorRepo;
    private BookRepository $bookRepo;
    private GenreRepository $genreRepo;
    private AddressRepository $addressRepo;
    private OrderFactory $orderFactory;
    private MailManager $mailManager;

    public function __construct(
        BookRepository $bookRepo,
        GenreRepository $genreRepo,
        AuthorRepository $authorRepo,
        AddressRepository $addressRepo,
        CartManager $cartManager,
        OrderFactory $orderFactory,
        MailManager $mailManager
    ) {
        $this->bookRepo = $bookRepo;
        $this->genreRepo = $genreRepo;
        $this->authorRepo = $authorRepo;
        $this->addressRepo = $addressRepo;
        $this->cartManager = $cartManager;
        $this->orderFactory = $orderFactory;
        $this->mailManager = $mailManager;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        $cart = $this->cartManager->getCurrentCart();
        $genres = $this->genreRepo->findAll();
        $authors = $this->authorRepo->findAll();
        $maxAndMinYear = $this->bookRepo->maxAndMinYear();
        $minYear = $maxAndMinYear[0]['minyear'];
        $maxYear = $maxAndMinYear[0]['maxyear'];

        return $this->render(
            'vesoul-edition/home.html.twig', [
            'genres' => $genres,
            'authors' => $authors,
            'minyear' => $minYear,
            'maxyear' => $maxYear,
            'cart' => $cart
            ]
        );
    }


    /**
     * @Route("/home/search/bytitle/{searchValue}", name="search-bytitle")
     * @param                                       string $searchValue
     * @return                                      Response
     */
    public function searchByTitle(string $searchValue): Response
    {

        $books = [];

        if (strlen($searchValue) > 0 ) {
            $books = $this->bookRepo->searchByTitle($searchValue);
        }

        $genres = $this->genreRepo->findAll();
        $authors = $this->authorRepo->findAll();
        $maxAndMinYear = $this->bookRepo->maxAndMinYear();
        $minYear = $maxAndMinYear[0]['minyear'];
        $maxYear = $maxAndMinYear[0]['maxyear'];

        return $this->render(
            'vesoul-edition/home.html.twig', [
            'genres' => $genres,
            'authors' => $authors,
            'minyear' => $minYear,
            'maxyear' => $maxYear,
            'books'   => $books,
            'searchValue' => $searchValue
            ]
        );
    }


    /**
     * @Route("/home/search/ajax/{searchValue}", name="search-autocomplete")
     * @param                                    string $searchValue
     * @return                                   Response
     */
    public function autocomplete(string $searchValue): Response
    {

        $books = [];

        if(strlen($searchValue) >= 3 ) {
            $books = $this->bookRepo->findTitle($searchValue);
        }

        $response = new Response();
        if(count($books) > 0 ) {

            $response->setContent(
                json_encode(
                    [
                    'books' => $books,
                    ]
                )
            );
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
     * @param               Request $request
     * @return              Response
     */
    public function homeload(Request $request): Response
    {

        $page = $request->get('page');
        $orderBy = $request->get('orderBy');
        $new = $request->get('new');
        $genre = strlen($request->get('genre')) > 0 ? explode(',', $request->get('genre')) : [];
        $author = strlen($request->get('author')) > 0 ? explode(',', $request->get('author')) : [];
        $yearmin = $request->get('yearmin');
        $yearmax = $request->get('yearmax');
        $title = $request->get('title');

        $max_per_page = 9;
        $total_books = $this->bookRepo->countBooks($new, $genre, $author, $yearmin, $yearmax, $title);
        $pages = ceil($total_books / $max_per_page);
        $offset = ($page - 1) * $max_per_page;
        $books = $this->bookRepo->findPageOfListBook($offset, $orderBy, $new, $genre, $author, $yearmin, $yearmax, $title);

        $response = new Response();
        $response->setCharset('utf-8');
        $response->headers->set('Content-Type', 'text/html');
        $response->headers->set('X-TotalBooks', $total_books);
        $response->headers->set('X-TotalPage', $pages);
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
     */
    public function sortByAscName() : JsonResponse
    {
        $books = $this->bookRepo->findAllBooksByAscName();
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
        $books = $this->bookRepo->findAllBooksByDescName();
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
        $books = $this->bookRepo->findAllBooksByAscYear();
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
     * @param              BookRepository $bookRepo
     * @return             JsonResponse
     */
    public function sortByDescYear(BookRepository $bookRepo) : JsonResponse
    {
        $books = $bookRepo->findAllBooksByDescYear();
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
     * @Route("/panier/ajoute/{id}", name="addItem")
     * @param                        Book $book
     * @return                       RedirectResponse
     */
    public function addItem(Book $book): RedirectResponse
    {
        // get current/new cart
        $cart = $this->cartManager->getCurrentCart();

        // set this book as a order item
        $item = $this->orderFactory->createItem($book);
        $cart->addItem($item);

        // persis in db
        $this->cartManager->save($cart);

        // TODO: Redirect really wanted ??
        // Redirect to cart
        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/panier/ajax/ajoute/{id}", name="ajaxAddItem")
     * @param                             Book $book
     * @return                            Response
     */
    public function ajaxAddItem(Book $book): Response
    {
        // get current/new cart
        $cart = $this->cartManager->getCurrentCart();

        // set this book as a order item
        $item = $this->orderFactory->createItem($book);
        $cart->addItem($item);

        // persis in db
        $this->cartManager->save($cart);

        return new Response("OK", Response::HTTP_OK);
    }


    /**
     * @Route("/panier/ajax/reduit/{id}", name="ajaxReduceItem")
     * @param                             Book $book
     * @return                            Response
     */
    public function reduceAjaxItem(Book $book): Response
    {
        // get current cart and items
        $cart = $this->cartManager->getCurrentCart();
        $items = $cart->getItems();

        // remove item if already in cart
        foreach ($items as $cartItem) {

            if ($cartItem->getBook() === $book && $cartItem->getQuantity() > 1) {
                $cart->reduceItem($cartItem);
                $this->cartManager->save($cart);

                // Return Ok if reduced
                return new Response(Response::HTTP_OK);
            }
        }
        // Return 302 if anything else goes wrong
        return new Response("Book not in cart", Response::HTTP_FOUND);
    }


    /**
     * @Route("/panier/reduit/{id}", name="reduceItem")
     * @param                        Book $book
     * @return                       Response
     */
    public function reduceItem(Book $book): Response
    {
        // get current cart and items
        $cart = $this->cartManager->getCurrentCart();
        $items = $cart->getItems();

        // remove item if already in cart
        foreach ($items as $cartItem) {

            if ($cartItem->getBook() === $book && $cartItem->getQuantity() > 1) {
                $cart->reduceItem($cartItem);
                $this->cartManager->save($cart);

                // Return Ok if reduced
                return new Response(Response::HTTP_OK);
            }
        }
        // Return 302 if anything else goes wrong
        return new Response("Book not in cart", Response::HTTP_FOUND);
    }


    /**
     * @Route("/panier/ajax/supprime/{id}", name="ajaxDeleteItem")
     * @param                               Book $book
     * @return                              Response
     */
    public function ajaxDeleteItem(Book $book): Response
    {
        // get current cart and items
        $cart = $this->cartManager->getCurrentCart();
        $items = $cart->getItems();

        // remove item if already in cart
        foreach ($items as $item) {
            if ($item->getBook() === $book) {

                $cart->removeItem($item);
                $this->cartManager->save($cart);

                // Return Ok
                return new Response(Response::HTTP_OK);
            }
        }
        // Return 302 if $book not in cart
        return new Response("Book not in cart", Response::HTTP_FOUND);
    }


    /**
     * @Route("/panier/supprime/{id}", name="deleteItem")
     * @param                          Book $book
     * @return                         RedirectResponse|Response
     */
    public function deleteItem(Book $book)
    {
        // get current cart and items
        $cart = $this->cartManager->getCurrentCart();
        $items = $cart->getItems();

        // remove item if already in cart
        foreach ($items as $item) {
            if ($item->getBook() === $book) {

                $cart->removeItem($item);
                $this->cartManager->save($cart);

                // Return Ok
                return new Response(Response::HTTP_OK);
            }
        }
        // Return 302 if $book not in cart
        return new Response("Book not in cart", Response::HTTP_FOUND);
    }


    /**
     * @Route("/product/{id}", name="product")
     * @param                  Book $book
     * @return                 Response
     */
    public function showProduct(Book $book): Response
    {
        $cart = $this->cartManager->getCurrentCart();

        return $this->render(
            'vesoul-edition/product.html.twig', [
            'images' => $book->getImages(),
            'book' => $book,
            'cart' => $cart
            ]
        );
    }


    /**
     * @Route("/panier/vide", name="cartEmpty")
     * @param                 Request $request
     * @return                Response
     */
    public function emptyCart(Request $request): Response
    {
        // get cart infos
        $cart = $this->cartManager->getCurrentCart();

        // remove every items and persist in db
        $cart->removeItems();
        $this->cartManager->save($cart);

        // if ajax request, send ok, else show cart page
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(
                [
                'status' => 'OK',
                ], 200
            );
        }

        // else go to cart page
        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/panier", name="cart")
     */
    public function showCart(): Response
    {
        // get cart infos
        $cart = $this->cartManager->getCurrentCart();

        // render cart infos
        return $this->render(
            'vesoul-edition/cart.html.twig', [
            'cart' => $cart,
            ]
        );
    }


    /**
     * /commande is where a user can order something. prepareOrder will search
     * for every products in cart and render the form (OrderType) used to make an
     * order.
     *
     * User will be able to choose where his books will be delivered
     * and where to send the bill.
     *
     * @Route("/commande", name="order")
     * @param              Security $security
     * @param              Request  $request
     * @return             RedirectResponse|Response
     */
    public function prepareOrder(Security $security, Request $request)
    {
        // check user/cart infos
        $user = $security->getUser();
        $cart = $this->cartManager->getCurrentCart();

        // if no cart ~> go back home
        if ($cart->getId() === null) {
            return $this->redirectToRoute('home');
        }

        // if user anonymous ~> get login
        if ($user === null) {
            return $this->redirectToRoute('login');
        }

        // get user addresses and cart infos
        $total = $cart->getTotal();
        $addresses = $this->addressRepo->findBy(['user' => $user]);

        // build/handle Order form
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        // if form is ok ~> go on
        if ($form->isSubmitted() && $form->isValid()) {
            $cart->setUser($user);
            $cart->setUpdatedAt(new \DateTime());
            $this->orderFactory->submit($cart);
            $this->cartManager->save($cart);
            // - Show every Items
            // - Let user choose delivery/billing address
            // - Update Cart Status from 'cart 'to 'Ordered' (?) once form
            //   validated
            // - Send email to admin so that he can process customer order
            // - Send email to user so he can see seller will take care of him
            // - Update Stocks
            //
            return $this->redirectToRoute('showConfirmation');
        }

        // render
        return $this->render(
            'vesoul-edition/order/order.html.twig', [
            'user' => $user,
            'addresses' => $addresses,
            'cart' => $cart,
            'total' =>$total,
            'form' => $form->createView()
            ]
        );
    }


    /**
     * @Route("/confirmation", name="showConfirmation")
     */
    public function showConfirmation(Security $security): Response
    {
        // get last user's order
        $order = $this->getDoctrine()->getRepository(Order::class)->findOneBy(
            [
            'user' => $security->getUser(),
            'status' => 'new'
            ], [
            'id' => 'DESC'
            ],
            1, 0
        );

        $this->mailManager->sendNewOrderMail($order);

        // render last order infos/confirmation
        return $this->render(
            'vesoul-edition/confirmation.html.twig', [
            'controller_name' => 'FrontController',
            'order' => $order
            ]
        );
    }
}
