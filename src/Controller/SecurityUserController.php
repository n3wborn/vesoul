<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/panel-client")
 */
class SecurityUserController extends AbstractController
{

    private AuthenticationUtils $authenticationUtils;
    private SessionInterface $session;
    private Swift_Mailer $mailer;
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $manager;


    public function __construct(
        AuthenticationUtils $authenticationUtils,
        SessionInterface $session,
        Swift_Mailer $mailer,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $manager
    )
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->session = $session;
        $this->mailer = $mailer;
        $this->encoder = $encoder;
        $this->manager = $manager;
    }



    /**
     * @Route("/inscription", name="security_user_registration")
     */
    public function registration(Request $request)
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user)
            ->handleRequest($request);

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
                ->setFrom($user->getUsername())
                // TODO: Replace by site email
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
            return $this->redirectToRoute('security_user_login');
        }

        return $this->render('dashboard-user/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/connexion", name="security_user_login")
     */
    public function login()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security-user/connexionSecurityUser.html.twig', [
            'title' => "Connexion utilisateur",
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


    /**
     * @Route("/test/mail", name="mail_test")
     * @param Swift_Mailer $mailer
     */
    public function sendMail(Swift_Mailer $mailer)
    {

        $emailAddress = $this->getUser()->getUsername();

        // TODO: changer setTO et setFrom
        $message = (new Swift_Message("Bienvenue"))
            ->setFrom('testmail@exemple.com')
            ->setTo($emailAddress)
            ->setBody(
                $this->renderView(
                'email/confirm.html.twig',
                    [
                        // TODO: send first and lastname
                    ]
                ), 'text/html'
            )
        ;

        $mailer->send($message);

        return $this->redirectToRoute('dashboard_user_informations');
    }



    /**
     * @Route("/deconnexion", name="security_user_logout")
     */
    public function logout()
    {
        // TODO
    }
}
