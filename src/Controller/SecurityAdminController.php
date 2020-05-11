<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/pannel-admin")
 */
class SecurityAdminController extends AbstractController
{
    /**
     * @Route("/connexion", name="security_admin_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security-admin/connexionSecurityAdmin.html.twig', [
            'title' => "Connexion administrateur",
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
    * @Route("/deconnexion", name="security_admin_logout")
    *
    */
    public function logout() 
    {
        
    }
    

}
