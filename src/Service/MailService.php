<?php

namespace App\Service; // Déclare l'espace de noms pour cette classe, indiquant qu'elle fait partie du service de l'application.

use Symfony\Bridge\Twig\Mime\TemplatedEmail; // Importation de la classe TemplatedEmail pour envoyer des emails avec des templates Twig.
use Symfony\Component\Mailer\MailerInterface; // Importation de l'interface MailerInterface pour envoyer des emails.
use Symfony\Component\Mime\Address; // Importation de la classe Address pour définir l'adresse de l'expéditeur de l'email.

class MailService
{
    private MailerInterface $mailer; // Déclare la propriété mailer qui sera utilisée pour envoyer des emails.

    // Le constructeur de la classe MailService. Il reçoit une instance de MailerInterface pour l'injection de dépendances.
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer; // Assigne l'instance de MailerInterface à la propriété mailer.
    }

    // Méthode pour envoyer une confirmation de commande par email.
    public function sendOrderConfirmation(string $toEmail, array $orderDetails): void
    {
        // Crée un nouvel email utilisant le template Twig 'order_confirmation.html.twig'.
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@devwell.com', 'Devwell')) // Définit l'adresse de l'expéditeur et le nom de l'expéditeur.
            ->to($toEmail) // Définit l'adresse email du destinataire.
            ->subject('Confirmation de votre commande') // Définit l'objet de l'email.
            ->htmlTemplate('emails/order_confirmation.html.twig') // Spécifie le template Twig utilisé pour l'email.
            ->context([ // Passe les détails de la commande au template Twig.
                'orderDetails' => $orderDetails,
            ]);

        $this->mailer->send($email); // Envoie l'email via l'interface mailer.
    }
}
