<?php

namespace App\Manager;

use App\Entity\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class MailManager
{

    private  $mailer;

    public function __construct(
        MailerInterface $mailer
    ) {
        $this->mailer = $mailer;
    }

    /**
     * @param Order $order
     */
    public function sendNewOrderMail()
    {
        // $user = $order->getUser->getUsername()
        //
        // send mail to user and
        $userEmail = (new TemplatedEmail())
           ->from('admin@email.com')
           ->to('user+1@email.com')
           ->subject('Merci pour votre commande !')
           ->htmlTemplate('email/user-confirm-order.html.twig');

        $this->mailer->send($userEmail);

        // same for the Admin

    }

}
