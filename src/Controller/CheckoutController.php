<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\CommandType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(
        Request $request, 
        CartRepository $cartRepository, 
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les articles du panier
        $cartItems = $cartRepository->findBy(['user' => $user]);
        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('cart_view');
        }

        // Créer un nouvel objet commande
        $order = new Order();
        $order->setUser($user);
        $order->setStatus('Pending');
        $order->setCreatedAt(new \DateTime());

        $form = $this->createForm(CommandType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Ajouter les articles du panier à la commande
            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setPrice($cartItem->getProduct()->getPrice());
                $em->persist($orderItem);
            }

            // Sauvegarder la commande
            $em->persist($order);
            $em->flush();

            // Supprimer les articles du panier
            foreach ($cartItems as $cartItem) {
                $em->remove($cartItem);
            }
            $em->flush();

            $this->addFlash('success', 'Commande passée avec succès.');
            return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'cartItems' => $cartItems,
        ]);
    }

    #[Route('/order/confirmation/{id}', name: 'order_confirmation')]
    public function confirmOrder(Order $order): Response
    {
        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
        ]);
    }
}
