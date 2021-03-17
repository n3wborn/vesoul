<?php

namespace App\Manager;

use App\Entity\Order;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailManager
{

    private MailerInterface $mailer;
    private string $adminEmailAddress;


    public function __construct(
        MailerInterface $mailer,
        string $adminEmailAddress
    ) {
        $this->mailer = $mailer;
        $this->adminEmailAddress = $adminEmailAddress;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function sendNewOrderMail(Order $order): void
    {
        $user = $order->getUser()->getUsername();
        $admin = $this->adminEmailAddress;

        // send mail to user
        $mail = (new TemplatedEmail())
           ->from($admin)
           ->to($user)
           ->subject('Confirmation de votre commande')
           ->htmlTemplate('email/user-confirm-order.html.twig');

        $this->mailer->send($mail);
    }

}
