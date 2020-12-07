<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AddressRepository;
use App\Repository\CommandRepository;
use App\Repository\UserRepository;
use App\Form\EditAddressesType;
use App\Form\AddAddressesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Address;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/panel-client")
 */
class DashboardUserController extends AbstractController
{
    private EntityManagerInterface $em;
    private SessionInterface $session;
    private UserPasswordEncoderInterface $encoder;
    private AddressRepository $addressRepo;
    private UserRepository $userRepo;

    public function __construct(
        EntityManagerInterface $em,
        SessionInterface $session,
        UserPasswordEncoderInterface $encoder,
        AddressRepository $addressRepo,
        UserRepository $userRepo
    )
    {
        $this->em = $em;
        $this->session = $session;
        $this->encoder = $encoder;
        $this->addressRepo = $addressRepo;
        $this->userRepo = $userRepo;
    }


    /**
     * @Route("/accueil", name="dashboard_user_home")
     */
    public function home(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO
            return true;
        }

        $changePassword = $this->createForm(ChangePasswordType::class);
        $changePassword->handleRequest($request);

        if ($changePassword->isSubmitted() && $changePassword->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $changePassword->get('newPassword')->getData()));
            $this->em->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('dashboard_user_home');
        }


        return $this->render('dashboard-user/mon-compte.html.twig', [
            'title' => 'Mon compte',
            'user' => $user,
            'form' => $form->createView(),
            'form_password' => $changePassword->createView()
        ]);
    }


    /**
     * @Route("/informations", name="dashboard_user_informations")
     */
    public function showInformations(Request $request)
    {
        // get current user
        $user = $this->getUser();


        // remember if "commande" is confirmed
        $commande = $this->session->get('commande');

        if (isset( $commande['confirmation']) && $commande['confirmation'] === true){
            return $this->redirectToRoute('commande');
        }

        // SHOW and/or MAY CHANGE user infos
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        // if user submit infos  update are valid
        if ($form->isSubmitted() && $form->isValid()) {

            // persist them and show success message
            $this->em->flush();

            $this->addFlash('success', 'Infos mises à jour');
            return $this->redirectToRoute('dashboard_user_informations');
        }

        // here, user can change his password
        $changePassword = $this->createForm(ChangePasswordType::class);
        $changePassword->handleRequest($request);

        // if user password has been updated correctly
        if ($changePassword->isSubmitted() && $changePassword->isValid()) {

            // persist password update in db and show success message
            $user->setPassword($this->encoder->encodePassword($user, $changePassword->get('newPassword')->getData()));
            $this->em->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');

            // and redirect to the same page (?)
            return $this->redirectToRoute('dashboard_user_informations');
        }


        // render template
        return $this->render('dashboard-user/mon-compte.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'form_password' => $changePassword->createView()
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
            $this->em->persist($address);
            $this->em->flush();

            return $this->redirectToRoute('dashboard_user_addresses');

        }


        if($form_edit->isSubmitted() && $form->isValid()) {
            
            $address->setCity(strtoupper($address->getCity()))
                    ->setCountry(strtoupper($address->getCountry()))
                    ->setFirstname(ucfirst($address->getFirstname()))
                    ->setLastname(ucfirst($address->getLastname()));
            
            $address->addUser($user);
            $this->em->persist($address);
            $this->em->flush();

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
        //     $em->persist($address);
        //     $em->flush();

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
        // $em->remove($address);
        // $em->flush();
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
