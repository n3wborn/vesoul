<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Order;
use App\Entity\Image;
use App\Repository\ImageRepository;


/**
 * @Route("/admin")
 */
class DashboardAdminController extends AbstractController
{
    private EntityManagerInterface $manager;
    private BookRepository $repoBook;
    private ImageRepository $imageRepo;
    private OrderRepository $orderRepo;

    public function __construct(
        EntityManagerInterface $manager,
        BookRepository $repoBook,
        ImageRepository $imageRepo,
        AuthorRepository $authorRepo,
        OrderRepository $orderRepo
    )
    {
        $this->manager = $manager;
        $this->repoBook = $repoBook;
        $this->imageRepo = $imageRepo;
        $this->authorRepo = $authorRepo;
        $this->orderRepo = $orderRepo;
    }



    /**
     * @Route("/accueil", name="dashboard_admin_home")
     * Dashboard admin Home
     */
    public function home()
    {
        return $this->render('dashboard-admin/home.html.twig');
    }



    /**
     * @Route("/livres", name="dashboard_admin_livres")
     */
    public function books()
    {
        $criteria = Criteria::create()
            ->orderBy(['year' => 'DESC'])
        ;

        return $this->render('dashboard-admin/books.html.twig', [
            'title' => 'Livres',
            'books' => $this->repoBook->matching($criteria)
        ]);
    }


    /**
     * @Route("/livres/new", name="admin_add_book")
     */
    public function addBook(Request $request) : Response
    {
        $book = new Book(); // Create the book
        $form = $this->createForm(BookType::class, $book); //Create the form
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // get uploaded images
            $images = $form->get('images')->getData();

            // TODO: allow 3 images only per book
            // For each image uploaded
            foreach($images as $image){
                // give them uniq filenames
                $file = md5(uniqid()) . '.' . $image->guessExtension();

                // move it to images_directory (cf: parameters in services.yaml)
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                } catch (FileException $e) {
                    // TODO: handle catched error message
                    $e->getMessage();
                }

                // link image file to an Image object
                $img = new Image();
                $img->setName($file);
                $img->setUrl($this->getParameter('images_directory') . '/' . $file);

                // and link it with book
                $book->addImage($img);
            }

            $this->manager->persist($book); // persist
            $this->manager->flush();        // save in db
            $this->addFlash('success', 'Envoi OK');         // show success message
            return $this->redirectToRoute('admin_add_book');    // TODO: Where to redirect once done??
        }

        return $this->render('dashboard-admin/book-crud/add-book.html.twig', [
            'title' => 'Ajouter un livre',
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/livres/delete/{id}", name="admin_delete_book", methods={"POST"})
     * @param Book $book
     * @return Response
     */
    public function removeBook(Book $book): Response
    {
        $bookID = $this->manager->getRepository(Book::class)->find($book);

        if (!$bookID) {
            return new Response("Internal Server Error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // fetch image collection from this book
        $images = $book->getImages();

        // remove each one
        foreach ($images as $image) {
            $filename = $image->getName();
            $file = $this->getParameter('images_directory') . '/' .$filename;

            if (is_file($file)) {
                unlink($file);
            }
       }

        // and modify/persist all this in database
        $this->manager->remove($book);
        $this->manager->flush();

        return new Response("Ok", Response::HTTP_OK);
    }


    /**
     * @Route("/livres/edit/{id} ", name="admin_edit_book")
     * @param Book $book
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editBooks(Book $book, Request $request)
    {
        // Instanciate e new BookType form
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        // find which book we're editing
        $img_collection = $this->imageRepo->findBy(['book' => $book]);


        // if valid form submission
        if($form->isSubmitted() && $form->isValid()){


            // get uploaded images
            $images = $form->get('images')->getData();

            // For each image uploaded
            foreach($images as $image){
                // rename with uniq id
                $file = md5(uniqid()) . '.' . $image->guessExtension();

                // move to images_directory (cf: services.yaml parameters)
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                } catch (FileException $e) {
                    // TODO: handle FileException error message
                    $e->getMessage();
                }

                // Instanciate a new Image with corresponding spec's and link it to book entity
                $img = new Image();
                $img->setName($file);
                $img->setUrl($this->getParameter('images_directory') . '/' . $file);
                $book->addImage($img);
            }

            // update book entity in db
            $this->manager->persist($book);
            $this->manager->flush();
            // TODO: replace addFlash with Bootstrap toasts
            $this->addFlash('success', 'Modification effectuée OK');
            return $this->redirectToRoute('dashboard_admin_livres');
        }

        // render the page
        return $this->render('dashboard-admin/book-crud/edit-book.html.twig', [
            'title' => 'Modifier un livre',
            'images' => $img_collection,
            'form' => $form->createView() //Display the form
        ]);
    }


    /**
     * @Route("/image/delete/{id}", name="book_delete_image", methods={"GET|DELETE"})   // GET needed for Symfo debug
     */
    public function deleteImage(Image $image, Request $request) {

        // json decode the request
        $data = json_decode($request->getContent(), true);

        // if csrf is ok...
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){

            // get image filename
            $file = $image->getName();

            // remove file
            unlink($this->getParameter('images_directory').'/'.$file);

            // keep modifications in db
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // send success message
            // Todo: add bootsrap toast
            return new JsonResponse([
                'success' => 1
            ], 200);

        // if csrf is not a valid one, send error message accordingly
        // TODO: add bootstrap toast
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/author/new", name="admin_add_auteur", methods={"POST"})
     */
    public function authorNew(Request $request): JsonResponse
    {
        // json decode the request
        $data = json_decode($request->getContent(), true);

        // if addauthor csrf is ok...

        if($this->isCsrfTokenValid('addauthor', $data['_token'])){

            // try to add an author in db
            try {
                $author = new Author();
                $author->setLastname($data['lastname']);
                $author->setFirstname($data['firstname']);

                $this->manager->persist($author);
                $this->manager->flush();

                // and return success and infos
                // (for the browser only Id is unknown, but why not ?!)
                return new JsonResponse([
                    'success' => 1,
                    'firstname' => $author->getFirstname(),
                    'lastname' => $author->getLastname(),
                    'author_id' => $author->getId()
                ], 200);

            // say if something went wrong
            } catch (Exception $e) {
                return new JsonResponse([
                    'error' => $e
                    , 400
                ]);
            }

        // invalid token is a bad thing !
        } else {
            return new JsonResponse([
                'error' => 'csrf token invalid'
                , 400
            ]);
        }
    }


    /**
     * @Route("/genre/new", name="admin_add_genre", methods={"POST"})
     */
    public function genreNew(Request $request): JsonResponse
    {
        // json decode the request
        $data = json_decode($request->getContent(), true);

        // if addgenre csrf token is ok...
        if($this->isCsrfTokenValid('addgenre', $data['_token'])){

            // try to add a genre in db
            try {
                $genre = new Genre();
                $genre->setName($data['genre']);

                $this->manager->persist($genre);
                $this->manager->flush();

                // and return success and infos (even if only id is really needed for js)
                return new JsonResponse([
                    'success' => 1,
                    'genre' => $genre->getName(),
                    'genre_id' => $genre->getId()
                ], 200);

                // say if something went wrong
            } catch (Exception $e) {
                return new JsonResponse([
                    'error' => $e
                    , 400
                ]);
            }

            // invalid token is a bad thing !
        } else {
            return new JsonResponse([
                'error' => 'csrf token invalid'
                , 400
            ]);
        }
    }


    /**
     * /admin/commandes will show the admin every orders his customers
     * made before and which orders are in which state.
     * For example: waiting for payement, sent,...
     *
     * @Route("/commandes", name="dashboard_admin_orders")
     */
    public function showOrders(): Response
    {
        $orders = $this->orderRepo->findAll();
        $enCours = 0;
        $expedie = 0;
        $total = 0;

        foreach ($orders as $order) {

            $total++;

            if ($order->getStatus == "en cours") {
                $enCours++;
            }
            if ($order->getStatus() == "expédié") {
                $expedie++;
            }
        }

        return $this->render('dashboard-admin/orders.html.twig', [
            'title' => 'Commandes',
            'orders' => $orders,
            'total' => $total,
            'enCours' => $enCours,
            'expedie' => $expedie,
        ]);
    }


    /**
     * @Route("/factures/imprimer/{id}", name="dashboard_admin_print_bill")
     * @param Order $order
     * @param OrderRepository $repo
     */
    public function printBill(Order $order)
    {
        $ref = $order->getId();

        // Instantiate Dompdf with our options
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('bill/bill.html.twig', [
            'commandNumero' => $ref,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Facture " . $ref. "-IT.pdf", [
            "Attachment" => true
        ]);

        return $this->redirectToRoute('dashboard_admin_print_bill');
    }


    /**
     * @Route("/boutique", name="dashboard_admin_boutique")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function info(Request $request)
    {
        $toggle = false;
        if ($request->get('id') != null) {
            $toggle = $request->get('id');
            $info = $this->adminRepo->findBy(['id' => $request->get('id')]);
            $info = $this->getDoctrine()
                ->getRepository(Admin::class)
                ->find($request->get('id'));
        } else {
            $info = new Admin();
        }

        $form = $this->createForm(AdminType::class, $info);
        $form->handleRequest($request);
        $allInfo = $this->adminRepo->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($info);
            $this->manager->flush();
            return $this->redirectToRoute('dashboard_admin_boutique');
        } else {
            return $this->render('dashboard-admin/info.html.twig', [
                'title' => 'Information Boutique',
                'infos' => $allInfo,
                'form' => $form->createView(),
                'toggle' => $toggle,
            ]);
        }
    }



    /**
     * @Route("/mentions", name="dashboard_admin_mentions")
     */
    public function mentions()
    {
        return $this->render('dashboard-admin/mentions.html.twig', [
            'title' => 'Mentions légales',
        ]);
    }


    /**
     * @Route("/bill/{id}", name="test_bill")
     * @param Order $order
     * @return Response
     */
    public function testFacture(Order $order)
    {

        // dump($order);
        $reference = $order->getOrderRef();
        $date = $order->getDate();

        // Titre et prix des livres
        foreach ($order->getItems() as $item) {
            $title = $item->getTitle();
            $price = $item->getPrice();
            $quantity = $item->getQuantity();
        }


        return $this->render('bill/bill.html.twig', [
            'reference' => $reference,
        ]);
    }
}
