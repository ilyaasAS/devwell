<?php

namespace App\Controller;

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

    // Création d'une nouvelle commande
    #[Route('/admin/orders/new', name: 'admin_orders_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('admin_orders_index');
        }

        return $this->render('admin/orders/new.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
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
        }

        return $this->redirectToRoute('admin_orders_index');
    }

    // Suppression d'un article d'une commande
    #[Route('/admin/orders/{orderId}/remove-item/{itemId}', name: 'admin_orders_remove_item', methods: ['POST'])]
    public function removeItem(int $orderId, int $itemId, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($orderId);
        if (!$order) {
            throw $this->createNotFoundException("Commande non trouvée.");
        }

        $orderItem = $entityManager->getRepository(OrderItem::class)->find($itemId);
        if (!$orderItem || $orderItem->getOrder() !== $order) {
            throw $this->createNotFoundException("Article non trouvé dans cette commande.");
        }

        $entityManager->remove($orderItem);
        $entityManager->flush();

        return $this->redirectToRoute('admin_orders_edit', ['id' => $orderId]);
    }
}
