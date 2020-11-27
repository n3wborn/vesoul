<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Admin;
use App\Form\BookType;
use App\Form\AdminType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\AdminRepository;
use App\Repository\CommandRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Command;
use App\Entity\Image;
use App\Repository\ImageRepository;


/**
 * @Route("/admin")
 */
class DashboardAdminController extends AbstractController
{
    private EntityManagerInterface $manager;
    private BookRepository $repoBook;
    private AdminRepository $adminRepo;
    private ImageRepository $imageRepo;
    private AuthorRepository $authorRepo;


    public function __construct(
        EntityManagerInterface $manager,
        BookRepository $repoBook,
        AdminRepository $adminRepo,
        ImageRepository $imageRepo,
        AuthorRepository $authorRepo
    )
    {
        $this->manager = $manager;
        $this->repoBook = $repoBook;
        $this->adminRepo = $adminRepo;
        $this->imageRepo = $imageRepo;
        $this->authorRepo= $authorRepo;
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
                    $e->getMessage();
                }

                // We keep image name in db
                $img = new Image();
                $img->setName($file);
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
                    $e->getMessage();
                }

                // Instanciate a new Image with corresponding spec's and link it to book entity
                $img = new Image();
                $img->setName($file);
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
     * @Route("/commandes", name="dashboard_admin_commandes")
     * @param CommandRepository $repo
     * @return Response
     */
    public function commandes(CommandRepository $repo)
    {
        $allCommands = $repo->findAll();
        $enCours = 0;
        $expedie = 0;
        $total = 0;
        foreach ($allCommands as $value) {
            $total++;
            if ($value->getState() == "en cours") {
                $enCours++;
            }
            if ($value->getState() == "expédié") {
                $expedie++;
            }
        }
        return $this->render('dashboard-admin/commandes.html.twig', [
            'title' => 'Commandes',
            'commands' => $allCommands,
            'total' => $total,
            'enCours' => $enCours,
            'expedie' => $expedie,
        ]);
    }



    /**
     * @Route("/commandes/imprimer/{id}", name="dashboard_admin_commandes_imprime")
     * @param Command $command
     * @param CommandRepository $repo
     * @return RedirectResponse
     */
    public function printBill(Command $command, CommandRepository $repo)
    {
        // Information de la boutique
        $admin = $this->adminRepo->find(2);      // TODO: Virer cette horreur
        $boutiqueNom = $admin->getCompany();
        $boutiqueTelephone = $admin->getTel();
        $boutiqueEmail = $admin->getEmail();
        $boutiqueLibelleAdresse = $admin->getLibelle();
        $boutiqueCp = $admin->getCp();
        $boutiqueVille = $admin->getCity();
        $boutiquePays = $admin->getCountry();

        // Information de la commande
        $commandNumero = $command->getId();
        $commandDate = $command->getDate();

        // Titre et prix des livres
        $book = [];
        $books = [];
        $prixTotal = 0;
        foreach ($command->getBooks() as $value) {
            array_push($book, $value->getIsbn());
            array_push($book, $value->getTitle());
            array_push($book, $value->getPrice());
            $prixTotal = $prixTotal + $value->getPrice();
            array_push($books, $book);
            $book = [];
        }

        // Adresse de facturation
        $billNumber = $command->getFacturation()->getNumber();
        $billType = $command->getFacturation()->getType();
        $billStreet = $command->getFacturation()->getStreet();
        $billCity = $command->getFacturation()->getCity();
        $billCp = $command->getFacturation()->getCp();
        $billCountry = $command->getFacturation()->getCountry();
        $billAdditional = $command->getFacturation()->getAdditional();
        $billFirstname = $command->getFacturation()->getFirstname();
        $billLastname = $command->getFacturation()->getLastname();

        // Adresse de facturation
        $shipNumber = $command->getLivraison()->getNumber();
        $shipType = $command->getLivraison()->getType();
        $shipStreet = $command->getLivraison()->getStreet();
        $shipCity = $command->getLivraison()->getCity();
        $shipCp = $command->getLivraison()->getCp();
        $shipCountry = $command->getLivraison()->getCountry();
        $shipAdditional = $command->getLivraison()->getAdditional();
        $shipFirstname = $command->getLivraison()->getFirstname();
        $shipLastname = $command->getLivraison()->getLastname();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('bill/facture.html.twig', [
            'boutiqueNom' => $boutiqueNom,
            'boutiqueTelephone' => $boutiqueTelephone,
            'boutiqueEmail' => $boutiqueEmail,
            'boutiqueLibelleAdresse' => $boutiqueLibelleAdresse,
            'boutiqueCp' => $boutiqueCp,
            'boutiqueVille' => $boutiqueVille,
            'boutiquePays' => $boutiquePays,
            'commandNumero' => $commandNumero,
            'commandDate' => $commandDate,
            'livres' => $books,
            'prixTotal' => $prixTotal,
            'afPrenom' => $billFirstname,
            'afNom' => $billLastname,
            'afNumero' => $billNumber,
            'afType' => $billType,
            'afRue' => $billStreet,
            'afComplement' => $billAdditional,
            'afVille' => $billCity,
            'afCp' => $billCp,
            'afPays' => $billCountry,
            'alPrenom' => $shipFirstname,
            'alNom' => $shipLastname,
            'alNumero' => $shipNumber,
            'alType' => $shipType,
            'alRue' => $shipStreet,
            'alComplement' => $shipAdditional,
            'alVille' => $shipCity,
            'alCp' => $shipCp,
            'alPays' => $shipCountry,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Facture " . $commandNumero . "-IT.pdf", [
            "Attachment" => true
        ]);

        return $this->redirectToRoute('dashboard_admin_commandes');
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
     * @Route("/bill/{id}", name="test_facture")
     * @param Command $command
     * @return Response
     */
    public function testFacture(Command $command)
    {

        // dump($command);
        $commandNumero = $command->getId();
        $commandDate = $command->getDate();

        // Titre et prix des livres
        foreach ($command->getBooks() as $value) {
            $bookTitle = $value->getTitle();
            $bookPrice = $value->getPrice();
            // dump($bookTitle);
            // dump($bookPrice);
        }

        // Prénom et nom de l'utilisateur passant la commande
        $userFirstname = $command->getUser()->getFirstname();
        $userLastname = $command->getUser()->getLastname();
        // dump($userFirstname, $userLastname);

        // Adresse de facturation 
        $billNumber = $command->getFacturation()->getNumber();
        $billType = $command->getFacturation()->getType();
        $billStreet = $command->getFacturation()->getStreet();
        $billCity = $command->getFacturation()->getCity();
        $billCp = $command->getFacturation()->getCp();
        $billCountry = $command->getFacturation()->getCountry();
        $billAdditional = $command->getFacturation()->getAdditional();
        $billFirstname = $command->getFacturation()->getFirstname();
        $billLastname = $command->getFacturation()->getLastname();

        // Adresse de facturation 
        $shipNumber = $command->getLivraison()->getNumber();
        $shipType = $command->getLivraison()->getType();
        $shipStreet = $command->getLivraison()->getStreet();
        $shipCity = $command->getLivraison()->getCity();
        $shipCp = $command->getLivraison()->getCp();
        $shipCountry = $command->getLivraison()->getCountry();
        $shipAdditional = $command->getLivraison()->getAdditional();
        $shipFirstname = $command->getLivraison()->getFirstname();
        $shipLastname = $command->getLivraison()->getLastname();

        return $this->render('bill/facture.html.twig', [
            'commandNumero' => $commandNumero,
            'commandDate' => $commandDate,
            'livreTitre' => $bookTitle,
            'livrePrix' => $bookPrice,
            'afNumero' => $billNumber,
            'afType' => $billType,
            'afRue' => $billStreet,
            'afVille' => $billCity,
            'afCp' => $billCp,
            'afPays' => $billCountry,
            'afComplement' => $billAdditional,
            'afPrenom' => $billFirstname,
            'afNom' => $billLastname,
            'alNumero' => $shipNumber,
            'alType' => $shipType,
            'alRue' => $shipStreet,
            'alVille' => $shipCity,
            'alCp' => $shipCp,
            'alPays' => $shipCountry,
            'alComplement' => $shipAdditional,
            'alPrenom' => $shipFirstname,
            'alNom' => $shipLastname,
        ]);
    }
}
