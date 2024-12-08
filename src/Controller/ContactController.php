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

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $em): Response
    {
        // Créer une nouvelle instance de l'entité Contact
        $contact = new Contact();

        // Créer le formulaire basé sur l'entité Contact
        $form = $this->createForm(ContactType::class, $contact);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister les données dans la base de données
            $em->persist($contact);
            $em->flush();

            // Afficher un message flash de succès
            $this->addFlash('success', 'Votre message a bien été envoyé.');

            // Rediriger vers la page de contact après soumission
            return $this->redirectToRoute('app_contact');
        }

        // Rendre la vue avec le formulaire
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
