<?php

namespace App\Controller\Navbar;

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
    // Route pour afficher et modifier le profil de l'utilisateur
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        SecurityBundleSecurity $security,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Récupérer l'utilisateur connecté via le service de sécurité
        $user = $security->getUser();

        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer toutes les commandes associées à l'utilisateur
        $orders = $entityManager->getRepository(Order::class)->findBy(['user' => $user]);

        // Sauvegarder l'ancien mot de passe de l'utilisateur pour le comparer plus tard
        $currentPassword = $user->getPassword();

        // Créer le formulaire de modification de profil à partir de la classe ProfileType
        $form = $this->createForm(ProfileType::class, $user);
        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe entré par l'utilisateur
            $plainPassword = $form->get('password')->getData();

            // Si l'utilisateur a entré un nouveau mot de passe, on le hash et le met à jour
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            } else {
                // Sinon, conserver l'ancien mot de passe
                $user->setPassword($currentPassword);
            }

            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Ajouter un message flash pour indiquer que le profil a été mis à jour
            $this->addFlash('success', 'Profil mis à jour avec succès.');
            // Rediriger vers la même page pour afficher les modifications
            return $this->redirectToRoute('app_profile');
        }

        // Afficher la vue avec le formulaire et les commandes de l'utilisateur
        return $this->render('navbar/profile/edit.html.twig', [
            'form' => $form->createView(),
            'orders' => $orders, // Passer les commandes à la vue Twig
        ]);
    }

    // Route pour supprimer une commande
    #[Route('/order/delete/{id}', name: 'order_delete', methods: ['POST'])]
    public function deleteOrder(
        Order $order,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est bien le propriétaire de la commande
        if ($this->getUser() !== $order->getUser()) {
            // Si l'utilisateur n'est pas le propriétaire, lancer une exception
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette commande.');
        }

        // Vérifier si la commande n'a pas encore été livrée
        if ($order->getStatus() !== 'livrée') {
            // Si la commande n'est pas livrée, la supprimer
            $entityManager->remove($order);
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur que la commande a été supprimée
            $this->addFlash('success', 'Commande supprimée avec succès.');
        } else {
            // Si la commande est livrée, on ne peut pas la supprimer
            $this->addFlash('error', 'Impossible de supprimer une commande déjà livrée.');
        }

        // Rediriger vers la page de profil après la suppression de la commande
        return $this->redirectToRoute('app_profile');
    }

    // Route pour demander un remboursement pour une commande
    #[Route('/order/refund/{id}', name: 'order_refund', methods: ['POST'])]
    public function refundOrder(
        Order $order,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si l'utilisateur est bien le propriétaire de la commande
        if ($this->getUser() !== $order->getUser()) {
            // Si l'utilisateur n'est pas le propriétaire, lancer une exception
            throw $this->createAccessDeniedException('Vous ne pouvez pas demander un remboursement pour cette commande.');
        }

        // Vérifier si la commande est livrée
        if ($order->getStatus() === 'livrée') {
            // Si la commande est livrée, changer son statut en "remboursée"
            $order->setStatus('remboursée');
            $entityManager->flush();

            // Ajouter un message flash pour informer l'utilisateur que le remboursement a été demandé avec succès
            $this->addFlash('success', 'Remboursement demandé avec succès.');
        } else {
            // Si la commande n'est pas livrée, afficher un message d'erreur
            $this->addFlash('error', 'Seules les commandes livrées peuvent être remboursées.');
        }

        // Rediriger vers la page de profil après la demande de remboursement
        return $this->redirectToRoute('app_profile');
    }
}
