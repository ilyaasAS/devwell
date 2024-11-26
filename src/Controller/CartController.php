<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;  // Ajouter cette importation
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart(Product $product, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'You need to be logged in to add items to your cart.');
            return $this->redirectToRoute('app_login');
        }

        // Recherche s'il existe déjà un produit dans le panier de l'utilisateur
        $cart = $em->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'product' => $product,
        ]);

        if ($cart) {
            $cart->setQuantity($cart->getQuantity() + 1);
        } else {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setProduct($product);
            $cart->setQuantity(1);
            $em->persist($cart);
        }

        // Sauvegarder les modifications dans la base de données
        $em->flush();

        // Calcul du nombre total d'articles dans le panier
        $cartItems = $em->getRepository(Cart::class)->findBy(['user' => $user]);
        $totalItems = 0;
        foreach ($cartItems as $item) {
            $totalItems += $item->getQuantity();
        }

        // Renvoi du nombre d'articles dans le panier sous forme de réponse JSON
        return new JsonResponse(['totalItems' => $totalItems]);
    }

    #[Route('/cart', name: 'cart_view')]
    public function viewCart(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Utilisation de l'EntityManager pour récupérer les éléments du panier
        $cartItems = $em->getRepository(Cart::class)->findBy(['user' => $user]);

        // Calcul du nombre total d'articles dans le panier
        $totalItems = 0;
        foreach ($cartItems as $item) {
            $totalItems += $item->getQuantity();
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'totalItems' => $totalItems, // Passer totalItems au template
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeFromCart(Cart $cart, EntityManagerInterface $em): RedirectResponse
    {
        // Suppression de l'élément du panier
        $em->remove($cart);
        $em->flush();

        $this->addFlash('success', 'Item removed from cart!');
        return $this->redirectToRoute('cart_view');
    }
}
