<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\OrderRepository;
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
        UserPasswordHasherInterface $passwordHasher,
        OrderRepository $orderRepository // Inject OrderRepository
    ): Response {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if (!$user instanceof \App\Entity\User) {
            // Rediriger si l'utilisateur n'est pas connecté ou n'est pas une instance valide
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les commandes de l'utilisateur
        $orders = $orderRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);

        // Sauvegarder l'ancien mot de passe
        $currentPassword = $user->getPassword();

        // Créer le formulaire
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe brut du formulaire
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                // Si un nouveau mot de passe est défini, le hacher et l'assigner
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            } else {
                // Si aucun mot de passe n'est fourni, conserver l'ancien
                $user->setPassword($currentPassword);
            }

            // Sauvegarder les modifications
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
