<?php
//lol
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Admin;
use App\Form\BookType;
use App\Form\AdminType;
use App\Repository\BookRepository;
use App\Repository\AdminRepository;
use App\Repository\CommandRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Command;

/**
 * @Route("/pannel-admin")
 */
class DashboardAdminController extends AbstractController
{
    /**
     * @Route("/accueil", name="dashboard_admin_home")
     */
    public function home()
    {
        return $this->render('dashboard-admin/home.html.twig', [
            'title' => 'Accueil',
        ]);
    }

    /**
     * @Route("/commandes", name="dashboard_admin_commandes")
     */
    public function commandes(CommandRepository $repo)
    {
        $allCommands = $repo->findAll();
        $enCours = 0;
        $expedie = 0;
        $total = 0;
        foreach ($allCommands as $value) {
            $total++;
            if($value->getState() == "en cours"){
                $enCours++;
            }
            if($value->getState() == "expédié"){
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
     */
    public function printBill(Command $command,AdminRepository $adminRepo, CommandRepository $repo)
    {
        // Information de la boutique
        $admin = $adminRepo->findOneById(2);
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
            $prixTotal = $prixTotal+$value->getPrice();
            array_push($books, $book);
            $book = [];

            // dump($bookTitle, $bookPrice);
        }
        // dump($books);
        // die;
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
     * @Route("/livres", name="dashboard_admin_livres")
     * @Route("/livres/modifier/{id}", name="dashboard_admin_modif_livres")
     */
    public function books(Request $request, BookRepository $repo, ObjectManager $manager)
    {
        $toggle = false;
        if($request->get('id')!= null){
            $toggle = $request->get('id');
            $book = $repo->findBy(['id' => $request->get('id')]);
            $book = $this->getDoctrine()->getRepository(Book::class)->find($request->get('id'));
        } else {
            $book = new Book();
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        $allBooks = $repo->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($book);
            $manager->flush();
            return $this->redirectToRoute('dashboard_admin_livres');
        } else {
            return $this->render('dashboard-admin/books.html.twig', [
                'title' => 'Livres',
                'books' => $allBooks,
                'form' => $form->createView(),
                'toggle' => $toggle,
                ]);
            }
    }

    /**
     * @Route("/livres/redit/{id} ", name="dashboard_admin_redit_book")
     */
    public function reditBooks(Book $book, Request $request, ObjectManager $manager, RouterInterface $router )
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
           
            $manager->persist($book);
            $manager->flush();
            // return new RedirectResponse($router->generate('handle_tools'));
            return $this->redirectToRoute('dashboard_admin_livres');
 
        }
 
    }

    /**
     * @Route("/boutique", name="dashboard_admin_boutique")
     */
    public function info(Request $request, AdminRepository $repo, ObjectManager $manager)
    {

        $toggle = false;
        if($request->get('id')!= null){
            $toggle = $request->get('id');
            $info = $repo->findBy(['id' => $request->get('id')]);
            $info = $this->getDoctrine()->getRepository(Admin::class)->find($request->get('id'));
        } else {
            $info = new Admin();
        }

        $form = $this->createForm(AdminType::class, $info);
        $form->handleRequest($request);
        $allInfo = $repo->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($info);
            $manager->flush();
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
     */
    public function testFacture(Command $command){


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

        // dump($billFirstname, $billLastname, $billNumber, $billType, $billStreet, $billCity, $billCp, $billCountry, $billAdditional);
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

        // dump($shipFirstname, $shipLastname, $shipNumber, $shipType, $shipStreet, $shipCity, $shipCp, $shipCountry, $shipAdditional);

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
