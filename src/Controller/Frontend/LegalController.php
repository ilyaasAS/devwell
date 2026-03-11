<?php

namespace App\Controller\Frontend;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    // Route pour les Conditions Générales d'Utilisation (CGU)
    #[Route('/terms-of-use', name: 'app_cgu')]
    public function termsOfUse(): Response
    {
        return $this->render('legal/terms_of_use.html.twig');
    }

    // Route pour les Conditions Générales de Vente (CGV)
    #[Route('/terms-of-sale', name: 'app_cgv')]
    public function termsOfSale(): Response
    {
        return $this->render('legal/terms_of_sale.html.twig');
    }

    // Route pour la page de contact et gestion du formulaire
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        // Création d'une nouvelle instance de l'entité Contact
        $contact = new Contact();

        // Création et gestion du formulaire de contact
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persistance des données dans la base de données
            $em->persist($contact);
            $em->flush();

            // Création et envoi de l'e-mail de notification
            $email = (new Email())
                ->from($contact->getEmail()) // Adresse e-mail de l'expéditeur (attention à la validation)
                ->to('admin@votre-domaine.com') // Destinataire du message
                ->subject('Nouveau message de contact') // Sujet de l'email
                ->html($this->renderView('emails/contact_notification.html.twig', [
                    'contact' => $contact,
                ]));

            // Envoi de l'email
            $mailer->send($email);

            // Message flash pour informer l'utilisateur de l'envoi réussi
            $this->addFlash('success', 'Votre message a bien été envoyé.');

            // Redirection vers la même page après soumission
            return $this->redirectToRoute('app_contact');
        }

        // Affichage du formulaire dans la vue contact
        return $this->render('legal/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour la page des mentions légales
    #[Route('/legal-notice', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/legal_notice.html.twig');
    }
}
