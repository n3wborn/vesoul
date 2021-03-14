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
    ) {
        $this->manager = $manager;
        $this->repoBook = $repoBook;
        $this->imageRepo = $imageRepo;
        $this->authorRepo = $authorRepo;
        $this->orderRepo = $orderRepo;
    }



    /**
     * @Route("/accueil", name="dashboard_admin_home")
     */
    public function home(): Response
    {
        return $this->render('dashboard-admin/home.html.twig');
    }

    /**
     * @Route("/livres", name="dashboard_admin_livres")
     */
    public function books(): Response
    {
        $criteria = Criteria::create()
            ->orderBy(['year' => 'DESC']);

        return $this->render(
            'dashboard-admin/books.html.twig', [
            'title' => 'Livres',
            'books' => $this->repoBook->matching($criteria)
            ]
        );
    }


    /**
     * @Route("/livres/new", name="admin_add_book")
     *
     * @param  Request $request
     * @return Response
     */
    public function addBook(Request $request) : Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
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
                    // TODO: handle caught error message
                    $e->getMessage();
                }

                // link image file to an Image object
                $img = new Image();
                $img->setName($file);
                $img->setUrl(
                    $this
                    ->getParameter('images_directory') . '/' . $file
                );

                // and link it with book
                $book->addImage($img);
            }

            $this->manager->persist($book); // persist
            $this->manager->flush();        // save in db
            $this->addFlash('success', 'Envoi OK');
            // TODO: Where to redirect once done??
            return $this->redirectToRoute('admin_add_book');
        }

        return $this->render(
            'dashboard-admin/book-crud/add-book.html.twig', [
            'title' => 'Ajouter un livre',
            'form' => $form->createView()
            ]
        );
    }


    /**
     * @Route("/livres/delete/{id}", name="admin_delete_book", methods={"POST"})
     *
     * @param  Book $book
     * @return Response
     */
    public function removeBook(Book $book): Response
    {
        $bookID = $this->manager
            ->getRepository(Book::class)
            ->find($book);

        if (!$bookID) {
            return new Response(
                "Internal Server Error",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // fetch image collection from this book
        $images = $book->getImages();

        // remove each one
        foreach ($images as $image) {
            $filename = $image->getName();
            $file = $this
                ->getParameter('images_directory') . '/' .$filename;

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
     *
     * @param  Book    $book
     * @param  Request $request
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
        if ($form->isSubmitted() && $form->isValid()) {

            // get uploaded images
            $images = $form->get('images')->getData();

            // For each image uploaded
            foreach ($images as $image){
                // rename with uniq id
                $file = md5(uniqid()) . '.' . $image->guessExtension();

                // move to images_directory (cf: services.yaml parameters)
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', $e->getMessage());
                }

                // Instanciate a new Image with corresponding spec's and link it
                // to book entity
                $img = new Image();
                $img->setName($file);
                $img->setUrl(
                    $this
                    ->getParameter('images_directory') . '/' . $file
                );
                $book->addImage($img);
            }

            // update book entity in db
            $this->manager->persist($book);
            $this->manager->flush();

            // TODO: replace addFlash with Bootstrap toasts
            $this->addFlash('success', 'Modification effectuée');
            return $this->redirectToRoute('dashboard_admin_livres');
        }

        // render the page
        return $this->render(
            'dashboard-admin/book-crud/edit-book.html.twig', [
            'title' => 'Modifier un livre',
            'images' => $img_collection,
            'form' => $form->createView() //Display the form
            ]
        );
    }


    /**
     * @Route("/image/delete/{id}", name="book_delete_image", methods={"DELETE"})
     *
     * @param  Image   $image
     * @param  Request $request
     * @return JsonResponse
     */
    public function deleteImage(Image $image, Request $request): JsonResponse
    {
        // json decode the request
        $data = json_decode($request->getContent(), true);

        // if csrf is ok...
        if ($this->isCsrfTokenValid(
            'delete'.$image->getId(),
            $data['_token']
        )
        ) {
            // get image filename
            $file = $image->getName();

            // remove file
            unlink(
                $this->getParameter(
                    'images_directory'
                ) . '/' . $file
            );

            // keep modifications in db
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // send success message
            // Todo: add bootstrap toast
            return new JsonResponse(
                [
                'success' => 1
                ], 200
            );

            // if csrf is not a valid one, send error message accordingly
            // TODO: add bootstrap toast
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }


    /**
     * @Route("/author/new", name="admin_add_auteur", methods={"POST"})
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function authorNew(Request $request): JsonResponse
    {
        // json decode the request
        $data = json_decode($request->getContent(), true);

        // if addauthor csrf is ok...
        if ($this->isCsrfTokenValid('addauthor', $data['_token'])) {

            // try to add an author in db
            try {
                $author = new Author();
                $author->setLastname($data['lastname']);
                $author->setFirstname($data['firstname']);

                $this->manager->persist($author);
                $this->manager->flush();

                // and return success and infos
                // (for the browser only Id is unknown, but why not ?!)
                return new JsonResponse(
                    [
                    'success' => 1,
                    'firstname' => $author->getFirstname(),
                    'lastname' => $author->getLastname(),
                    'author_id' => $author->getId()
                    ], 200
                );

                // say if something went wrong
            } catch (Exception $e) {
                return new JsonResponse(
                    [
                    'error' => $e
                    , 400
                    ]
                );
            }

            // invalid token is a bad thing !
        } else {
            return new JsonResponse(
                [
                'error' => 'csrf token invalid'
                , 400
                ]
            );
        }
    }


    /**
     * @Route("/genre/new", name="admin_add_genre", methods={"POST"})
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function genreNew(Request $request): JsonResponse
    {
        // json decode the request
        $data = json_decode($request->getContent(), true);

        // if addgenre csrf token is ok...
        if($this->isCsrfTokenValid('addgenre', $data['_token'])) {

            // try to add a genre in db
            try {
                $genre = new Genre();
                $genre->setName($data['genre']);

                $this->manager->persist($genre);
                $this->manager->flush();

                // and return success and infos (even if only id is really needed for js)
                return new JsonResponse(
                    [
                    'success' => 1,
                    'genre' => $genre->getName(),
                    'genre_id' => $genre->getId()
                    ], 200
                );

                // say if something went wrong
            } catch (Exception $e) {
                return new JsonResponse(
                    [
                    'error' => $e
                    , 400
                    ]
                );
            }

            // invalid token is a bad thing !
        } else {
            return new JsonResponse(
                [
                'error' => 'csrf token invalid'
                , 400
                ]
            );
        }
    }


    /**
     * /admin/commandes will show the admin every orders his customers
     * made before and which orders are in which state.
     * For example: "new", "fullfiled",...
     *
     * @Route("/commandes", name="dashboard_admin_orders")
     */
    public function showOrders(): Response
    {
        $orders = $this->orderRepo->findBy(
            [
            'status' => [
                Order::STATUS_NEW_ORDER,
                Order::STATUS_ORDER_FULLFILLED
            ]], [
            'id' => 'DESC'
            ]
        );

        // find new/fulfilled (already sent to customers) orders quantity
        $new = $this->orderRepo->findBy(
            [
            'status' => Order::STATUS_NEW_ORDER
            ]
        );

        $fulfilled = $this->orderRepo->findBy(
            [
            'status' => Order::STATUS_ORDER_FULLFILLED
            ]
        );

        return $this->render(
            'dashboard-admin/orders.html.twig', [
            'orders' => $orders,
            'new' => $new,
            'fulfilled' => $fulfilled
            ]
        );
    }


    /**
     * @Route("/commandes/valider/{id}", name="dashboard_admin_fulfill_order")
     *
     * @param Order $order
     * @return Response
     */
    public function fulfillOrder(Order $order)
    {
        if ($order->getStatus() === Order::STATUS_NEW_ORDER) {
            $order->setStatus(Order::STATUS_ORDER_FULLFILLED);

            $this->manager->persist($order);
            $this->manager->flush();

        } else {
            $this->addFlash('info', "Cette commande ne peut être validée");
        }

        return $this->redirectToRoute('dashboard_admin_orders');

    }


    /**
     * NOTE: This is only used to cancel a "new" order. Another controller
     * or service will remove old carts and canceled orders
     *
     * @Route("/commandes/supprimer/{id}", name="dashboard_admin_delete_order")
     *
     * @param Order $order
     * @return Response
     */
    public function deleteOrder(Order $order)
    {
        if ($order->getStatus() === Order::STATUS_NEW_ORDER) {
            $order->setStatus(Order::STATUS_ORDER_ABORTED);

            $this->manager->persist($order);
            $this->manager->flush();

        } else {
            $this->addFlash('info', "Cette commande ne peut être annulée");
        }

        return $this->redirectToRoute('dashboard_admin_orders');
    }


    /**
     * @Route("/factures/imprimer/{id}", name="dashboard_admin_print_bill")
     *
     * @param  Order $order
     * @return RedirectResponse
     */
    public function printBill(Order $order): RedirectResponse
    {

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView(
            'bill/bill.html.twig', [
            'order' => $order,
            ]
        );


        $dompdf = new Dompdf();
        $dompdf->getOptions()
            ->setChroot($this->getParameter('kernel.project_dir'));

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait')->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream(
            "Facture-" . $order->getId() . ".pdf", [
            "Attachment" => true
            ]
        );

        return $this->redirectToRoute('dashboard_admin_print_bill');
    }


    /**
     * @TODO Remove/Update this old remaining thing
     *  (no more admin entity/repo and... whatever, this must be removed)
     *
     * @Route("/boutique", name="dashboard_admin_boutique")
     * @param              Request $request
     * @return             Response
     */
    public function info()
    {
        $admin = $this->adminRepo->findBy(['role' => 'ROLE_ADMIN']);

        return $this->render(
            'dashboard-admin/info.html.twig', [
            'title' => 'Information Boutique',
            'admin' => $admin
            ]
        );
    }


    /**
     * @Route("/mentions", name="dashboard_admin_mentions")
     */
    public function mentions(): Response
    {
        return $this->render(
            'dashboard-admin/mentions.html.twig', [
            'title' => 'Mentions légales',
            ]
        );
    }

}
