<?php

// src/Controller/ContactController.php

namespace App\Controller\Admin;

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

class AdminContactController extends AbstractController
{
    // Affichage de tous les messages de contact
    #[Route('/admin/messages', name: 'admin_messages')]
    public function adminMessages(EntityManagerInterface $em): Response
    {
        // Récupérer tous les messages de contact de la base de données, triés par date de création
        $messages = $em->getRepository(Contact::class)->findBy([], ['createdAt' => 'DESC']);

        // Affichage de la liste des messages avec la vue correspondante
        return $this->render('admin/contact/messages.html.twig', [
            'messages' => $messages, // Passer les messages récupérés à la vue
        ]);
    }

    // Suppression d'un message de contact
    #[Route('/admin/messages/delete/{id}', name: 'admin_message_delete')]
    public function deleteMessage(int $id, EntityManagerInterface $em): Response
    {
        // Récupérer le message à supprimer via l'ID
        $message = $em->getRepository(Contact::class)->find($id);

        if ($message) {
            // Si le message existe, le supprimer de la base de données
            $em->remove($message);
            $em->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Le message a été supprimé.');
        } else {
            // Si le message n'est pas trouvé, afficher un message d'erreur
            $this->addFlash('error', 'Le message n\'a pas été trouvé.');
        }

        // Rediriger vers la liste des messages après suppression
        return $this->redirectToRoute('admin_messages');
    }

    // Affichage des détails d'un message et possibilité de répondre
    #[Route('/admin/messages/view/{id}', name: 'admin_message_view')]
    public function viewMessage(int $id, Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        // Récupérer le message spécifique par son ID
        $message = $em->getRepository(Contact::class)->find($id);

        if (!$message) {
            // Si le message n'existe pas, afficher une erreur et rediriger
            $this->addFlash('error', 'Le message n\'existe pas.');
            return $this->redirectToRoute('admin_messages');
        }

        // Créer un formulaire pour que l'administrateur réponde au message
        $responseForm = $this->createForm(ResponseType::class, $message); // Assure-toi de créer ce formulaire

        // Gérer la soumission du formulaire de réponse
        $responseForm->handleRequest($request);

        if ($responseForm->isSubmitted() && $responseForm->isValid()) {
            // Récupérer le contenu de la réponse de l'administrateur
            $responseContent = $responseForm->get('response')->getData();

            // Marquer le message comme répondu dans la base de données
            $message->setIsResponded(true); // Marque le message comme répondu

            // Sauvegarder les modifications dans la base de données
            $em->flush(); // Le message est déjà persistant, donc on n'a pas besoin de le persister à nouveau

            // Créer un email de réponse et l'envoyer à l'utilisateur
            $email = (new Email())
                ->from('admin@votre-domaine.com') // L'adresse email de l'administrateur
                ->to($message->getEmail()) // L'adresse email de l'utilisateur qui a envoyé le message
                ->subject('Réponse à votre message')
                ->html($this->renderView('emails/contact_response.html.twig', [
                    'contact' => $message, // Passer le message d'origine à la vue
                    'response' => $responseContent, // La réponse de l'administrateur
                ]));

            // Envoyer l'email de réponse
            $mailer->send($email);

            // Afficher un message flash pour indiquer que la réponse a été envoyée
            $this->addFlash('success', 'Votre réponse a été envoyée par email.');

            // Rediriger vers la page de détails du message après la réponse
            return $this->redirectToRoute('admin_message_view', ['id' => $id]);
        }

        // Rendre la vue avec le message et le formulaire de réponse
        return $this->render('admin/contact/message_detail.html.twig', [
            'message' => $message, // Passer le message à la vue
            'form' => $responseForm->createView(), // Passer le formulaire à la vue
        ]);
    }
}
