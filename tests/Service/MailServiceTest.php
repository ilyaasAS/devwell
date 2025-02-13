<?php

namespace App\Tests\Service;

use App\Service\MailService;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailServiceTest extends TestCase
{
    public function testSendOrderConfirmation()
    {
        // Création d'un mock pour MailerInterface
        $mailerMock = $this->createMock(MailerInterface::class);

        // On s'attend à ce que la méthode send() soit appelée une fois avec un objet TemplatedEmail.
        $mailerMock->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));  // Vérifier que l'argument passé est bien un TemplatedEmail

        // Détails fictifs de la commande à envoyer
        $orderDetails = [
            'orderId' => 12345,
            'productName' => 'Test Product',
            'quantity' => 2,
            'totalPrice' => 50.00,
        ];

        // Création de l'instance de MailService avec le mock MailerInterface
        $mailService = new MailService($mailerMock);

        // Appel de la méthode sendOrderConfirmation
        $mailService->sendOrderConfirmation('test@example.com', $orderDetails);

        // Pas besoin d'assertions supplémentaires, PHPUnit vérifie si send() a bien été appelée
    }
}
