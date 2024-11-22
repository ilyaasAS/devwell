<?php

namespace App\Controller;

use App\Form\ProfileType; // Utiliser le formulaire ProfileType pour l'édition du profil
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Créer le formulaire pour l'édition du profil en utilisant ProfileType
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, mettre à jour les données de l'utilisateur
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Profile updated successfully.');

            // Rediriger l'utilisateur vers la page de profil
            return $this->redirectToRoute('app_profile');
        }

        // Rendre la vue avec le formulaire pour que l'utilisateur puisse le modifier
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(), // Créer la vue pour le formulaire
        ]);
    }
}
