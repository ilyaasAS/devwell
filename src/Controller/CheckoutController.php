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
use Stripe\Stripe;
use Stripe\Charge;
use App\Service\MailService;


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

        // Si le formulaire est soumis et valide
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

            // Paiement via Stripe
            $stripeToken = $request->request->get('stripeToken');

            if ($stripeToken) {
                // Initialiser Stripe
                Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

                // Créer une charge Stripe
                try {
                    Charge::create([
                        'amount' => $this->calculateTotal($cartItems), // Montant en centimes
                        'currency' => 'eur',
                        'description' => 'Commande ' . $order->getId(),
                        'source' => $stripeToken,
                    ]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur de paiement : ' . $e->getMessage());
                    return $this->redirectToRoute('checkout');
                }

                $order->setStatus('Paid');
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
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'], // Passer la clé publique à Twig
        ]);
    }

    #[Route('/order/confirmation/{id}', name: 'order_confirmation')]
    public function confirmOrder(Order $order): Response
    {
        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    // Calculer le total de la commande en centimes
    private function calculateTotal($cartItems): int
    {
        $total = 0;
        foreach ($cartItems as $cartItem) {
            $total += $cartItem->getQuantity() * $cartItem->getProduct()->getPrice() * 100; // Convertir en centimes
        }
        return $total;
    }
}
