<?php

// src/Controller/ContactController.php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
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
}
