<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user]);
    
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




    #[Route('/order/delete/{id}', name: 'order_delete', methods: ['POST'])]
    public function deleteOrder(
        Order $order, 
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est le propriétaire de la commande
        if ($this->getUser() !== $order->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette commande.');
        }

        // Vérifier si la commande n'est pas encore livrée
        if ($order->getStatus() !== 'livrée') {
            $entityManager->remove($order);
            $entityManager->flush();

            $this->addFlash('success', 'Commande supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Impossible de supprimer une commande déjà livrée.');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/order/refund/{id}', name: 'order_refund', methods: ['POST'])]
    public function refundOrder(
        Order $order, 
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est le propriétaire de la commande
        if ($this->getUser() !== $order->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas demander un remboursement pour cette commande.');
        }

        // Vérifier si la commande est livrée
        if ($order->getStatus() === 'livrée') {
            // Mettre à jour le statut de la commande en "remboursé"
            $order->setStatus('remboursée');
            $entityManager->flush();

            $this->addFlash('success', 'Remboursement demandé avec succès.');
        } else {
            $this->addFlash('error', 'Seules les commandes livrées peuvent être remboursées.');
        }

        return $this->redirectToRoute('app_profile');
    }
}
