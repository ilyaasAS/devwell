<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\OrderItem;
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
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    // Affichage des détails d'une commande
    #[Route('/admin/orders/{id}', name: 'admin_orders_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    // Édition d'une commande existante
    #[Route('/admin/orders/{id}/edit', name: 'admin_orders_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La commande a été mise à jour avec succès.');
            return $this->redirectToRoute('admin_orders_index');
        }

        return $this->render('admin/orders/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    // Suppression d'une commande
    #[Route('/admin/orders/{id}', name: 'admin_orders_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
        $entityManager->remove($order);
        $entityManager->flush();

        $this->addFlash('success', 'La commande a été supprimée avec succès.');
    } else {
        $this->addFlash('error', 'Échec de la suppression de la commande.');
    }

    return $this->redirectToRoute('admin_orders_index');
}

}
