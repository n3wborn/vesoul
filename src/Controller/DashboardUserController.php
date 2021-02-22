<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Form\AddAddressesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Manager\CartManager;


/**
 * @Route("/panel-client")
 */
class DashboardUserController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $encoder;
    private AddressRepository $addressRepo;
    private OrderRepository $orderRepo;
    private CartManager $cartManager;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        AddressRepository $addressRepo,
        OrderRepository $orderRepo,
        CartManager $cartManager
    )
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->addressRepo = $addressRepo;
        $this->orderRepo = $orderRepo;
        $this->cartManager = $cartManager;
    }


    /**
     * Connected User home page
     * From here he can show and update his infos/password
     *
     * @Route("/accueil", name="dashboard_user_home")
     */
    public function home(Request $request)
    {
        $user = $this->getUser();
        $cart = $this->cartManager->getCurrentCart();

        // if user update his infos, update db and refresh
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('dashboard_user_informations');
        }

        // if user wants to change his password, update and refresh
        $changePassword = $this->createForm(ChangePasswordType::class);
        $changePassword->handleRequest($request);

        if ($changePassword->isSubmitted() && $changePassword->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $changePassword->get('newPassword')->getData()));
            $this->em->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('dashboard_user_home');
        }


        // render default template
        return $this->render('dashboard-user/mon-compte.html.twig', [
            'title' => 'Mon compte',
            'user' => $user,
            'form' => $form->createView(),
            'form_password' => $changePassword->createView(),
            'cart' => $cart,
        ]);
    }


    /**
     * TODO: Suppress this route (same as user home page)
     *       or make useful
     * Connected User home Infos page
     * From here he can show and update his infos/password
     *
     * @Route("/informations", name="dashboard_user_informations")
     */
    public function showInformations(Request $request)
    {
        // get current user
        $user = $this->getUser();
        $cart = $this->cartManager->getCurrentCart();

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
            'form_password' => $changePassword->createView(),
            'cart' => $cart
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
        $newAddress = new Address();
        $cart = $this->cartManager->getCurrentCart();

        $formNew = $this->createForm(AddAddressesType::class, $newAddress);
        $formNew->handleRequest($request);

        // if submitted, create user a new address
        if ($formNew->isSubmitted()) {

            $newAddress->setCity(ucfirst($newAddress->getCity()))
                ->setCountry(strtoupper($newAddress->getCountry()))
                ->setFirstname(ucfirst($newAddress->getFirstname()))
                ->setLastname(ucfirst($newAddress->getLastname()));
            $newAddress->setUser($user);

            $this->em->persist($newAddress);
            $this->em->flush();

            return $this->redirectToRoute('dashboard_user_addresses');
        }

        return $this->render('dashboard-user/compte-adresses.html.twig', [
            'adresses' => $this->addressRepo->findBy(['user' => $user]),
            'formNew' => $formNew->createView(),
            'cart' => $cart
        ]);
    }



    /**
     * Check if user owns address
     * if yes, edit address and return Ok
     * if not owned by user, return access denied, else return 404
     *
     * @Route("/adresses/edit/{id}", name="dashboard_user_addresses_edit")
     */
    public function editAddress(Address $address, Request $request)
    {

        if ($address->getUser() !== $this->getUser()) {
            throw new AccessDeniedException(AccessDeniedException::class);
        }

        $user = $this->getUser();
        $data = $request->request->get('edit_addresses');

        // if submitted, update user address
        if ($address->getUser() === $user) {

            $address->setTitle(ucfirst($data['title']))
                ->setFirstname(ucfirst($data['firstname']))
                ->setLastname(ucfirst($data['lastname']))
                ->setNumber($data['number'])
                ->setType($data['type'])
                ->setCp($data['cp'])
                ->setCity(ucfirst($data['city']))
                ->setCountry(ucfirst($data['country']))
                ->setAdditional($data['additional'])
            ;

            $this->em->flush();

            return $this->redirectToRoute('dashboard_user_addresses');

        } else {

            Return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * Remove address if owned by current user
     * if not owned by user, return access denied, else return 404
     *
     * @Route("/adresses/delete/{id}", name="dashboard_user_addresses_delete")
     */
    public function deleteAddress(Address $address): Response
    {
        $user = $this->getUser();

        if ($address->getUser() === $user) {

            $this->em->remove($address);
            $this->em->flush();
            $this->addFlash('success', 'Adresse supprimée !');
            return $this->redirectToRoute('dashboard_user_addresses');

        } else if (!$address->getUser() === $user) {
            throw new AccessDeniedException(AccessDeniedException::class);

        } else {
            Return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * /panel-client/commandes is where a user can see every orders
     * he has done before. He can check in which "state" they are, for
     * example, if the last one he did has been send or not.
     *
     * A search icon, once clicked, shows details about an order and
     * the modal displayed owns a "download order" that user can use
     * to download the order in pdf.
     *
     * @Route("/commandes", name="dashboard_user_orders")
     */
    public function showOrders(): Response
    {
        $user = $this->getUser();
        $addresses = $this->addressRepo->findBy(['user' => $user]);
        $cart = $this->cartManager->getCurrentCart();
        $orders = $this->orderRepo->findBy(['user' => $user]);

        return $this->render('dashboard-user/orders.html.twig', [
            'user' => $user,
            'addresses' => $addresses,
            'cart' => $cart,
            'orders' => $orders,
        ]);
    }

}
