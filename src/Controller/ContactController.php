<?php

// src/Controller/ContactController.php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Form\ResponseType; // Ajouter ce formulaire pour gérer la réponse
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
{
    $contact = new Contact();
    $form = $this->createForm(ContactType::class, $contact);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Persiste l'entité Contact
        $em->persist($contact);
        $em->flush();

        // Envoi de l'email
        $email = (new Email())
            ->from($contact->getEmail()) // Adresse de l'utilisateur
            ->to('admin@votre-domaine.com') // Adresse où envoyer le message
            ->subject('Nouveau message de contact')
            ->html($this->renderView('emails/contact_notification.html.twig', [
                'contact' => $contact,
            ]));

        $mailer->send($email);

        // Message flash
        $this->addFlash('success', 'Votre message a bien été envoyé.');

        return $this->redirectToRoute('app_contact');
    }

    return $this->render('contact/index.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/admin/messages', name: 'admin_messages')]
    public function adminMessages(EntityManagerInterface $em): Response
    {
        // Récupérer tous les messages de contact
        $messages = $em->getRepository(Contact::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('contact/messages.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/admin/messages/delete/{id}', name: 'admin_message_delete')]
    public function deleteMessage(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer le message à supprimer
        $message = $em->getRepository(Contact::class)->find($id);

        if ($message) {
            $em->remove($message);
            $em->flush();

            $this->addFlash('success', 'Le message a été supprimé.');
        } else {
            $this->addFlash('error', 'Le message n\'a pas été trouvé.');
        }

        return $this->redirectToRoute('admin_messages');
    }

    #[Route('/admin/messages/view/{id}', name: 'admin_message_view')]
    public function viewMessage(int $id, Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer le message spécifique par son ID
        $message = $em->getRepository(Contact::class)->find($id);

        if (!$message) {
            // Si le message n'existe pas, afficher une erreur
            $this->addFlash('error', 'Le message n\'existe pas.');
            return $this->redirectToRoute('admin_messages');
        }

        // Créer un formulaire de réponse pour l'administrateur
        $responseForm = $this->createForm(ResponseType::class, $message); // Assure-toi de créer ce formulaire

        // Gérer la soumission du formulaire de réponse
        $responseForm->handleRequest($request);

        if ($responseForm->isSubmitted() && $responseForm->isValid()) {
            // Marquer le message comme répondu
            $message->setIsResponded(true); // Marque le message comme répondu

            // Persister la réponse de l'administrateur
            $em->flush(); // Le message est déjà persistant, donc on n'a pas besoin de le persister à nouveau

            $this->addFlash('success', 'Votre réponse a été envoyée.');

            return $this->redirectToRoute('admin_message_view', ['id' => $id]);
        }

        // Rendre la vue avec le message et le formulaire de réponse
        return $this->render('contact/message_detail.html.twig', [
            'message' => $message,
            'form' => $responseForm->createView(),
        ]);
    }
}
