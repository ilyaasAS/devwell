<?php

namespace App\Controller\Frontend;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\CommandType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Stripe\Charge;

class CheckoutController extends AbstractController
{
    // Route pour afficher la page de paiement
    #[Route('/checkout', name: 'checkout')]
    public function checkout(
        Request $request,
        CartRepository $cartRepository,
        EntityManagerInterface $em
    ): Response {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Veuillez vous connecter pour procéder à l\'achat.');
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
        $order->setStatus('en attente');  // Mettre le statut sur "en attente" par défaut
        $order->setCreatedAt(new \DateTime());

        // Création du formulaire de commande
        $form = $this->createForm(CommandType::class);
        $form->handleRequest($request);

        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Token Stripe obligatoire : sans lui, on ne crée aucune commande et on ne touche pas au panier
            $stripeToken = $request->request->get('stripeToken');
            if (!$stripeToken) {
                $this->addFlash('error', 'Veuillez renseigner vos informations de paiement (carte bancaire).');
                return $this->redirectToRoute('checkout');
            }

            // 2. Créer la commande et les lignes, persister pour obtenir l'ID (metadata + webhook)
            foreach ($cartItems as $cartItem) {
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setPrice($cartItem->getProduct()->getPrice());
                $em->persist($orderItem);
            }
            $em->persist($order);
            $em->flush();

            // 3. Tenter le paiement Stripe ; en cas d'échec, annuler la commande et garder le panier
            try {
                Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
                Charge::create([
                    'amount' => $this->calculateTotal($cartItems),
                    'currency' => 'eur',
                    'description' => 'Commande ' . $order->getId(),
                    'source' => $stripeToken,
                    'metadata' => [
                        'order_id' => (string) $order->getId(),
                    ],
                ]);
            } catch (\Exception $e) {
                $em->remove($order); // cascade supprime les OrderItem
                $em->flush();
                $this->addFlash('error', 'Erreur de paiement : ' . $e->getMessage());
                return $this->redirectToRoute('checkout');
            }

            $order->setStatus('payée');
            $em->persist($order);

            // 4. Paiement réussi : vider le panier et rediriger vers la confirmation
            foreach ($cartItems as $cartItem) {
                $em->remove($cartItem);
            }
            $em->flush();

            $this->addFlash('success', 'Commande passée avec succès.');
            return $this->redirectToRoute('app_orders_confirmation', ['id' => $order->getId()]);
        }

        // Clé publique Stripe obligatoire : si vide, le champ carte ne s'affichera jamais.
        // Trim + suppression d'éventuels guillemets/espaces pour éviter 401 ou erreurs JS.
        $stripePublicKey = trim((string) ($_ENV['STRIPE_PUBLIC_KEY'] ?? ''), " \t\n\r\0\x0B\"'");
        return $this->render('frontend/checkout/index.html.twig', [
            'form' => $form->createView(),
            'cartItems' => $cartItems,
            'stripe_public_key' => $stripePublicKey,
        ]);
    }

    // Route pour confirmer la commande après paiement
    #[Route('/orders/confirmation/{id}', name: 'app_orders_confirmation')]
    public function confirmOrder(Order $order): Response
    {
        return $this->render('frontend/checkout/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    // Fonction pour calculer le total de la commande en centimes
    private function calculateTotal($cartItems): int
    {
        $total = 0;
        foreach ($cartItems as $cartItem) {
            $total += $cartItem->getQuantity() * $cartItem->getProduct()->getPrice() * 100; // Convertir en centimes
        }
        return $total;
    }
}
