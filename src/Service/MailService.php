<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendOrderConfirmation(string $toEmail, array $orderDetails): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@devwell.com', 'Devwell'))
            ->to($toEmail)
            ->subject('Confirmation de votre commande')
            ->htmlTemplate('emails/order_confirmation.html.twig')
            ->context([
                'orderDetails' => $orderDetails,
            ]);

        $this->mailer->send($email);
    }
}
