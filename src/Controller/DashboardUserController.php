<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
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
    private CommandRepository $commandRepo;

    public function __construct(
        EntityManagerInterface $em,
        SessionInterface $session,
        UserPasswordEncoderInterface $encoder,
        AddressRepository $addressRepo,
        UserRepository $userRepo,
        CommandRepository $commandRepo
    )
    {
        $this->em = $em;
        $this->session = $session;
        $this->encoder = $encoder;
        $this->addressRepo = $addressRepo;
        $this->userRepo = $userRepo;
        $this->commandRepo = $commandRepo;
    }


    /**
     * @Route("/accueil", name="dashboard_user_home")
     */
    public function home(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO
            dd($form->get('gender')->getData());
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
        $form = $this->createForm(UserType::class, $user);
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
     *
     * User addresses
     * if user already have addresse(s) he must be able to edit or remove them
     * In every cases, he must be able to create a new one
     */
    public function showAdresses(Request $request)
    {

        $user = $this->getUser();
//        $address = new Address();
//
//        $form = $this->createForm(AddAddressesType::class, $address);
//        $form->handleRequest($request);
//
//        // if user already owns an address, edit/add/remove
//        $form_edit = $this->createForm(EditAddressesType::class, $address);
//        $form_edit->handleRequest($request);
//
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $address->setCity(strtoupper($address->getCity()))
//                    ->setCountry(strtoupper($address->getCountry()))
//                    ->setFirstname(ucfirst($address->getFirstname()))
//                    ->setLastname(ucfirst($address->getLastname()));
//
//            $address->setUser($user);
//            $this->em->persist($address);
//            $this->em->flush();
//
//            return $this->redirectToRoute('dashboard_user_addresses');
//
//        }
//
//
//        if ($form_edit->isSubmitted() && $form->isValid()) {
//
//            $address->setCity(strtoupper($address->getCity()))
//                    ->setCountry(strtoupper($address->getCountry()))
//                    ->setFirstname(ucfirst($address->getFirstname()))
//                    ->setLastname(ucfirst($address->getLastname()));
//
//            $this->em->persist($address);
//            $this->em->flush();
//
//            return $this->redirectToRoute('dashboard_user_addresses');
//
//        }

        $adresses = $this->addressRepo->findUserAddresses($user);

        return $this->render('dashboard-user/compte-adresses.html.twig', [
            'adresses' => $adresses,
//            'form' => $form->createView(),
//            'form_edit' => $form_edit->createView()
        ]);
    }



    /**
     * Check if user owns address
     * if yes, edit address and return Ok
     * if not owned by user, return access denied, else return 404
     *
     * @Route("/adresses/edit/{address}", name="dashboard_user_addresses_edit")
     */
    public function EditAddresses(Address $address, Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditAddressesType::class, $address);
        $form->handleRequest($request);

        // if submitted, update user address
        if ($form->isSubmitted()) {

            $address->setCity(strtoupper($address->getCity()))
                ->setCountry(strtoupper($address->getCountry()))
                ->setFirstname(ucfirst($address->getFirstname()))
                ->setLastname(ucfirst($address->getLastname()));

            $this->em->persist($address);
            $this->em->flush();

            return $this->redirectToRoute('dashboard_user_addresses');
        }

        $addresses = $this->addressRepo->findUserAddresses($user);

        return $this->render('dashboard-user/compte-adresses.html.twig', [
            'adresses' => $addresses,
            'form' => $form->createView(),
        ]);



    }


    /**
     * Remove address if owned by current user
     * if not owned by user, return access denied, else return 404
     *
     * @Route("/adresses/delete/{address}", name="dashboard_user_addresses_delete")
     */
    public function deleteAddress(Address $address): Response
    {
        $user = $this->getUser();

        if ($address->getUser() === $user) {

            $this->em->remove($address);
            $this->em->flush();
            return new Response("Ok", Response::HTTP_OK);

        } else if (!$address->getUser() === $user) {
            throw new AccessDeniedException(AccessDeniedException::class);

        } else {
            Return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * @Route("/commandes", name="dashboard_user_commands")
     */
    public function showCommands(): Response
    {
        $user = $this->getUser();
        $addresses = $this->addressRepo->findUserAddresses($user);
        $commands = $this->commandRepo->findUserCommands($user);

        return $this->render('dashboard-user/compte-commandes.html.twig', [
            'user' => $user,
            'commandes' => $commands,
            'addresses' => $addresses,
        ]);
    }

}