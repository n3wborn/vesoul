<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/pannel-client")
 */
class SecurityUserController extends AbstractController
{
    /**
     * @Route("/connexion", name="security_user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        

        

        return $this->render('security-user/connexionSecurityUser.html.twig', [
            'title' => "Connexion utilisateur",
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
    * @Route("/deconnexion", name="security_user_logout")
    *
    */
    public function logout() 
    {
        
    }
    
    /**
    * @Route("/inscription", name="security_user_registration") 
    */ 
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer)
    {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user); // Créer le formulaire
        $form->handleRequest($request); // Bind automatique avec l'objet user des champs remplis dans le formulaire

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword()); // Chiffrer le mot de passe de l'user
            $user->setPassword($hash); // Enregistrer le mot de passee chiffré en BDD
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user); // Faire persister les données en BDD
            $manager->flush(); // Envois le tout en BDD
            
            $mail = (new \Swift_Message("Bienvenue sur Vesoul Edition !"))
            ->setFrom($user->getUsername())
            ->setTo('vesouledition@sfr.fr')
            ->setBody(
                $this->renderView(
                    'email/confirm.html.twig',
                    [
                        'firstname' => $user->getFirstname(),
                        // 'lastname' => $user->getLastname(),
                        // 'email' => $user->getUsername()
                        
                    ]                                             
                    ),
                'text/html'
            );

            $mailer->send($mail);
            return $this->redirectToRoute('security_user_login'); // Redirige sur la route de login (plus bas)
        }
     
        return $this->render('dashboard-user/inscription.html.twig', [
            'form' => $form->createView() // Rendu du formulaire
        ]);
    }


    /**
     * @Route("/test/mail", name="mail_test")
     */
    public function sendMail(\Swift_Mailer $mailer)
    {

        $mail = (new \Swift_Message("Bonjour"))
        ->setFrom('lucas.rob1@live.fr')
        ->setTo('lucas.r@codeur.online')
        ->setBody("<h1>Ceci est un message de test.</h1>", 'text/html');

        $mailer->send($mail);

        return $this->redirectToRoute('security_user_login');

    }



}
