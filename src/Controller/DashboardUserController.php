<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\AddressRepository;
use App\Repository\CommandRepository;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use App\Repository\UserRepository;
use App\Form\EditInformationsType;
use App\Form\EditAddressesType;
use App\Form\AddAddressesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/pannel-client")
 */
class DashboardUserController extends AbstractController
{
    private EntityManagerInterface $manager;
    private SessionInterface $session;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(EntityManagerInterface $manager,
                                SessionInterface $session,
                                UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->session = $session;
        $this->encoder = $encoder;
    }


    /**
     * @Route("/accueil", name="dashboard_user_home")
     */
    public function home()
    {
        $commande = $this->session->get('commande');

        if( isset( $commande['confirmation']) && $commande['confirmation']=== true){
            return $this->redirectToRoute('commande');
        }
        
        return $this->render('dashboard-user/mon-compte.html.twig', [
            'title' => 'Mon compte'
        ]);
    }


    /**
     * @Route("/informations", name="dashboard_user_informations")
     */
    public function showInformations(Request $request)
    {
        $commande = $this->session->get('commande');

        if( isset( $commande['confirmation']) && $commande['confirmation']=== true){
            return $this->redirectToRoute('commande');
        }
        
        $user = $this->getUser();
        $form = $this->createForm(EditInformationsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $this->encoder->encodePassword($user, $user->getPassword()); // Chiffrer le mot de passe de l'user
            $username_mail = $user->getUsername();
            $tel = $user->getTel();

            $user->setPassword($hash) // Enregistrer le mot de passee chiffré en BDD
                 ->setUsername($username_mail)
                 ->setTel($tel);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('notice', 'Votre mot de passe à bien été changé !');

            return $this->redirectToRoute('security_user_login');
            } else {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
            }

        return $this->render('dashboard-user/mon-compte.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/adresses", name="dashboard_user_addresses")
     */
    public function showAdresses(AddressRepository $repo, Request $request)
    {

        $user = $this->getUser();
        $address = new Address();

        $form = $this->createForm(AddAddressesType::class, $address);
        $form_edit = $this->createForm(EditAddressesType::class, $address);
        $form->handleRequest($request);
        $form_edit->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $address->setCity(strtoupper($address->getCity()))
                    ->setCountry(strtoupper($address->getCountry()))
                    ->setFirstname(ucfirst($address->getFirstname()))
                    ->setLastname(ucfirst($address->getLastname()));

            $address->addUser($user);
            $this->manager->persist($address);
            $this->manager->flush();

            return $this->redirectToRoute('dashboard_user_addresses');

        }


        if($form_edit->isSubmitted() && $form->isValid()) {
            
            $address->setCity(strtoupper($address->getCity()))
                    ->setCountry(strtoupper($address->getCountry()))
                    ->setFirstname(ucfirst($address->getFirstname()))
                    ->setLastname(ucfirst($address->getLastname()));
            
            $address->addUser($user);
            $this->manager->persist($address);
            $this->manager->flush();

            return $this->redirectToRoute('dashboard_user_addresses');

        }

        $adresses = $repo->findBy(['users' => $user]);
       
        return $this->render('dashboard-user/compte-adresses.html.twig', [
            'adresses' => $adresses,
            'form' => $form->createView(),
            'form_edit' => $form_edit->createView()
        ]);
    }



    /**
     * @Route("/adresses/{id}/edit", name="dashboard_user_addresses_edit")
     */
    public function EditAddresses(AddressRepository $repo, Address $address = null, Request $request)
    {
        die();

        // $user = $this->getUser();

        // $id = $user->getId();

        // $form = $this->createForm(EditAddressesType::class, $address);
        // $form->handleRequest($request);

        // if($form->isSubmitted()) {
            
        //     $address->get;

        //     $address->setCity(strtoupper($address->getCity()))
        //     ->setCountry(strtoupper($address->getCountry()))
        //     ->setFirstname(ucfirst($address->getFirstname()))
        //     ->setLastname(ucfirst($address->getLastname()));
            
        //     $address->addUser($user);
        //     $manager->persist($address);
        //     $manager->flush();

        //     return $this->redirectToRoute('dashboard_user_addresses');

        // }

        // $adresses = $repo->findAddressByUserId($id);
        // return $this->render('dashboard-user/compte-adresses.html.twig', [
        //     'adresses' => $adresses,
        //     'form' => $form->createView(),
        // ]);
    }
    

    /**
     * @Route("/adresses/{id}/delete", name="dashboard_user_addresses_delete")
     */
    public function delete($id, Address $address = null, Request $request)
    {
        
        // $repo = $this->getDoctrine()->getRepository(Address::class);
        // $address = $repo->find($id);
        // $manager->remove($address);
        // $manager->flush();
        // dump($address);
        // die();

        return $this->redirectToRoute('dashboard_user_addresses');

    }


    /**
     * @Route("/commandes", name="dashboard_user_commands")
     */
    public function showCommandes(CommandRepository $repo_commande, AddressRepository $repo_adresse)
    {
        $user = $this->getUser();
        $id = $user->getId();

        $commandes = $repo_commande->findCommandByUserId($id);
        
        // $address = $this->getId();
        // $test = $repo_commande->findCommandById(2);
        // $addresses = $repo_adresse->findAddressByUserId($id);

        return $this->render('dashboard-user/compte-commandes.html.twig', [
            'commandes' => $commandes
            // 'addresses' => $addresses
        ]);
    }

}

/* 

SELECT address.id, address.title, address.firstname, address.lastname, address.number, address.type, address.street, address.city, address.cp, address.country, address.additional
			FROM address
            INNER JOIN command ON address.id = command.livraison_id
            WHERE command.id = 2

*/
