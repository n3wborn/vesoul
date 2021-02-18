<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Manager\CartManager;

class SecurityController extends AbstractController
{

    private AuthenticationUtils $authenticationUtils;
    private UserPasswordEncoderInterface $encoder;
    private Swift_Mailer $mailer;
    private EntityManagerInterface $manager;
    private CartManager $cartManager;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        UserPasswordEncoderInterface $encoder,
        Swift_Mailer $mailer,
        EntityManagerInterface $manager,
        CartManager $cartManager
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->manager = $manager;
        $this->cartManager = $cartManager;
    }

    /**
     * @Route("/connexion", name="login")
     */
    public function login(): Response
    {

         if ($this->getUser()) {
             return $this->redirectToRoute('dashboard_user_home');
         }

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $cart = $this->cartManager->getCurrentCart();


        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'cart' => $cart
        ]);
    }


    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout()
    {
    }


    /**
     * @Route("/inscription", name="registration")
     */
    public function registration(Request $request)
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user)
            ->handleRequest($request);
        $cart = $this->cartManager->getCurrentCart();


        if ($form->isSubmitted() && $form->isValid()) {

            $contact = $form->getData();

            // if form is ok : hash user's password and save infos
            $hash = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash)->setRoles(['ROLE_USER']);

            $this->manager->persist($user);
            $this->manager->flush();

            // Confirm subscription
            $this->addFlash('success', "Félicitation ! Un email de confirmation vous a été envoyé");

            // and confirm va email
            $mail = (new Swift_Message("Bienvenue sur Vesoul Edition !"))
                ->setFrom('vesouledition@sfr.fr')
                ->setTo($user->getUsername())
                ->setBody(
                    $this->renderView(
                        'email/confirm.html.twig',
                        [
                            'email' => $user->getUsername(),
                            'contact' => $contact
                        ]
                    ),'text/html'
                );

            // send email, then redirect
            $this->mailer->send($mail);
            return $this->redirectToRoute('login');
        }

        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart
        ]);
    }
}
