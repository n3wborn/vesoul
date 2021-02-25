<?php


namespace App\Controller;

use App\Entity\Order;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Response;

class Debug extends AbstractController
{

    private Swift_Mailer $mailer;

    public function __construct
    (
        Swift_Mailer $mailer
    ) {
        $this->mailer = $mailer;
    }


    /**
     * @Route("/test/mail", name="mail_test")
     * @return RedirectResponse
     */
    public function testMail(): RedirectResponse
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

        $this->mailer->send($message);

        return $this->redirectToRoute('dashboard_user_informations');
    }


    /**
     * @Route("/bill/{id}", name="test_bill")
     * @param Order $order
     * @return Response
     */
    public function testBill(Order $order): Response
    {

        return $this->render('bill/bill.html.twig', [
            'order' => $order,
        ]);
    }
}
