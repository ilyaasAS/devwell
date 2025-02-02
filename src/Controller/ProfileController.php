<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{



    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        SecurityBundleSecurity $security,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
    
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }
    
        // Récupérer les commandes de l'utilisateur
        $orders = $entityManager->getRepository(\App\Entity\Order::class)->findBy(['user' => $user]);
    
        // Sauvegarder l'ancien mot de passe
        $currentPassword = $user->getPassword();
    
        // Créer le formulaire
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
    
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            } else {
                $user->setPassword($currentPassword);
            }
    
            $entityManager->flush();
    
            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_profile');
        }
    
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'orders' => $orders, // Passer les commandes à la vue
        ]);
    }    

    
}
