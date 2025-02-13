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

            // Gestion du paiement via Stripe
            $stripeToken = $request->request->get('stripeToken');

            if ($stripeToken) {
                // Initialisation de Stripe
                Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

                // Création d'une charge Stripe
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

                // Mettre à jour le statut après paiement réussi
                $order->setStatus('payée');
            }

            // Sauvegarder la commande dans la base de données
            $em->persist($order);
            $em->flush();

            // Suppression des articles du panier après achat
            foreach ($cartItems as $cartItem) {
                $em->remove($cartItem);
            }
            $em->flush();

            $this->addFlash('success', 'Commande passée avec succès.');
            return $this->redirectToRoute('order_confirmation', ['id' => $order->getId()]);
        }

        // Affichage de la page de paiement
        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'cartItems' => $cartItems,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'], // Passer la clé publique à Twig
        ]);
    }

    // Route pour confirmer la commande après paiement
    #[Route('/order/confirmation/{id}', name: 'order_confirmation')]
    public function confirmOrder(Order $order): Response
    {
        return $this->render('checkout/confirmation.html.twig', [
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
