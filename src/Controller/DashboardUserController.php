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
use Doctrine\Common\Persistence\ObjectManager;
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

    /**
     * @Route("/accueil", name="dashboard_user_home")
     */
    public function home(SessionInterface $session)
    {
        $commande = $session->get('commande');

        if( isset( $commande['confirmation']) && $commande['confirmation']=== true){
            return $this->redirectToRoute('commande');
        }
        
        return $this->render('dashboard-user/mon-compte.html.twig', [
            'title' => 'Mon compte'
        ]);
    }


    /**
     * @Route("/informations", name="dashboard_user_informations")
     * 
     */
    public function showInformations(SessionInterface $session, Request $request, ObjectManager $manager, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $encoder)
    {
        $commande = $session->get('commande');

        if( isset( $commande['confirmation']) && $commande['confirmation']=== true){
            return $this->redirectToRoute('commande');
        }
        
        $user = $this->getUser();

        $form = $this->createForm(EditInformationsType::class, $user);
        $form->handleRequest($request);


            
        
        if ($form->isSubmitted() && $form->isValid()) {

            

               
                
                $em = $this->getDoctrine()->getManager();

                $hash = $encoder->encodePassword($user, $user->getPassword()); // Chiffrer le mot de passe de l'user
                
                $username_mail = $user->getUsername();
                $tel = $user->getTel();
                
                $user->setPassword($hash) // Enregistrer le mot de passee chiffré en BDD
                     ->setUsername($username_mail)
                     ->setTel($tel);
                
                $em->persist($user);
                $em->flush();

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
    public function showAdresses(AddressRepository $repo, Address $address = null, Request $request, ObjectManager $manager = null)
    {

        $user = $this->getUser();

        $id = $user->getId();

        if(!$address) {
            $address = new Address();
        }

        $form = $this->createForm(AddAddressesType::class, $address);
        $form_edit = $this->createForm(EditAddressesType::class, $address);
        $form->handleRequest($request);
        $form_edit->handleRequest($request);

       
        
        if($form->isSubmitted()) {
            
           
            $address->get;

            $address->setCity(strtoupper($address->getCity()))
            ->setCountry(strtoupper($address->getCountry()))
            ->setFirstname(ucfirst($address->getFirstname()))
            ->setLastname(ucfirst($address->getLastname()));
            
            $address->addUser($user);
            $manager->persist($address);
            $manager->flush();

            return $this->redirectToRoute('dashboard_user_addresses');

        }

        



        if($form_edit->isSubmitted()) {
            
           

            $address->setCity(strtoupper($address->getCity()))
            ->setCountry(strtoupper($address->getCountry()))
            ->setFirstname(ucfirst($address->getFirstname()))
            ->setLastname(ucfirst($address->getLastname()));
            
            $address->addUser($user);
            $manager->persist($address);
            $manager->flush();

            return $this->redirectToRoute('dashboard_user_addresses');

        }

       

        $adresses = $repo->findAddressByUserId($id);
       
        return $this->render('dashboard-user/compte-adresses.html.twig', [
            'adresses' => $adresses,
            'form' => $form->createView(),
            'form_edit' => $form_edit->createView()
            
        ]);
    }



    /**
     * @Route("/adresses/{id}/edit", name="dashboard_user_addresses_edit")
     */
    public function EditAddresses(AddressRepository $repo, Address $address = null, Request $request, ObjectManager $manager = null)
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
    public function delete($id, Address $address = null, Request $request, ObjectManager $manager)
    {
        
        // $repo = $this->getDoctrine()->getRepository(Address::class);
        // $address = $repo->find($id);
        
        
        // $manager->remove($address);
        // $manager->flush();
        
        //     // dump($address);
        //     // die();

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

        // dump($commandes);
        // dump($addresses);
        // dump($commandes_livraison);
        // dump($commandes_facturation);

        // die();
        
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
