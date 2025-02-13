<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminOrderController extends AbstractController
{
    // Affichage de toutes les commandes
    #[Route('/admin/orders', name: 'admin_orders_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): Response
    {
        // Récupérer toutes les commandes de la base de données
        return $this->render('admin/orders/index.html.twig', [
            // Passer toutes les commandes à la vue
            'orders' => $orderRepository->findAll(),
        ]);
    }

    // Affichage des détails d'une commande
    #[Route('/admin/orders/{id}', name: 'admin_orders_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        // Rendu de la vue pour afficher les détails d'une commande spécifique
        return $this->render('admin/orders/show.html.twig', [
            // Passer l'objet de commande à la vue
            'order' => $order,
        ]);
    }

    // Édition d'une commande existante
    #[Route('/admin/orders/{id}/edit', name: 'admin_orders_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        // Créer et afficher le formulaire de modification de la commande
        $form = $this->createForm(OrderType::class, $order);  // Crée le formulaire lié à l'entité Order
        $form->handleRequest($request);  // Traiter la requête HTTP

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Appliquer les modifications à la base de données
            $entityManager->flush();

            // Message flash de succès après mise à jour de la commande
            $this->addFlash('success', 'La commande a été mise à jour avec succès.');

            // Rediriger vers la liste des commandes
            return $this->redirectToRoute('admin_orders_index');
        }

        // Afficher la vue d'édition avec le formulaire de modification
        return $this->render('admin/orders/edit.html.twig', [
            // Passer la commande et le formulaire à la vue
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'une commande
    #[Route('/admin/orders/{id}', name: 'admin_orders_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        // Vérification de la validité du token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            // Si le token est valide, supprimer la commande
            $entityManager->remove($order);
            $entityManager->flush();

            // Message flash de succès après suppression de la commande
            $this->addFlash('success', 'La commande a été supprimée avec succès.');
        } else {
            // Si le token est invalide, afficher un message d'erreur
            $this->addFlash('error', 'Échec de la suppression de la commande.');
        }

        // Rediriger vers la liste des commandes
        return $this->redirectToRoute('admin_orders_index');
    }
}
