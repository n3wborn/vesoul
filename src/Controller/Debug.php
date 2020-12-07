<?php


namespace App\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Swift_Mailer;
use Swift_Message;

class Debug extends AbstractController
{

    private Swift_Mailer $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * @Route("/test/mail", name="mail_test")
     * @param Swift_Mailer $mailer
     */
    public function testMail(Swift_Mailer $mailer)
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
}