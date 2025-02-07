<?php

namespace App\Controller\Navbar;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;  // Ajouter cette importation
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; // Importation nécessaire pour récupérer les données du formulaire

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add')]
public function addToCart(Product $product, Request $request, EntityManagerInterface $em): Response
{
    // Vérifier si l'utilisateur est connecté
    $user = $this->getUser();

    if (!$user) {
        $this->addFlash('error', 'You need to be logged in to add items to your cart.');
        return $this->redirectToRoute('app_login');
    }

    // Récupérer la quantité à ajouter, ou 1 par défaut
    $quantity = (int) $request->request->get('quantity', 1);
    
    // Valider la quantité (doit être entre 1 et le stock disponible)
    if ($quantity < 1) {
        $this->addFlash('error', 'La quantité doit être d\'au moins 1.');
        return $this->redirectToRoute('product_details', ['id' => $product->getId()]);
    }
    
    if ($quantity > $product->getStock()) {
        $this->addFlash('error', 'La quantité demandée dépasse le stock disponible.');
        return $this->redirectToRoute('product_details', ['id' => $product->getId()]);
    }

    // Recherche s'il existe déjà un produit dans le panier de l'utilisateur
    $cart = $em->getRepository(Cart::class)->findOneBy([
        'user' => $user,
        'product' => $product,
    ]);

    if ($cart) {
        // Si le produit existe déjà dans le panier, on met à jour la quantité
        $cart->setQuantity($cart->getQuantity() + $quantity);
    } else {
        // Si le produit n'existe pas, on l'ajoute avec la quantité spécifiée
        $cart = new Cart();
        $cart->setUser($user);
        $cart->setProduct($product);
        $cart->setQuantity($quantity);
        $em->persist($cart);
    }

    // Sauvegarder les modifications dans la base de données
    $em->flush();

    // Afficher un message de succès
    $this->addFlash('success', 'Produit ajouté à votre panier !');

    // Rediriger vers la page du panier
    return $this->redirectToRoute('cart_view');
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
    // Initialiser le total des prix
    $totalPrice = 0;

    foreach ($cartItems as $item) {
        // Nombre total d'articles
        $totalItems += $item->getQuantity();
        
        // Calcul du prix total : prix du produit * quantité
        $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
    }

    return $this->render('cart/index.html.twig', [
        'cartItems' => $cartItems,
        'totalItems' => $totalItems, // Passer totalItems au template
        'totalPrice' => $totalPrice, // Passer totalPrice au template
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

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function updateQuantity(Cart $cart, Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer la nouvelle quantité depuis la requête POST
        $quantity = (int) $request->request->get('quantity');
        
        // Vérifier que la quantité est valide
        if ($quantity < 1) {
            $this->addFlash('error', 'La quantité doit être au moins 1.');
            return $this->redirectToRoute('cart_view');
        }
    
        // Vérifier que la quantité ne dépasse pas le stock disponible
        $product = $cart->getProduct();
        if ($quantity > $product->getStock()) {
            $this->addFlash('error', 'La quantité demandée dépasse le stock disponible.');
            return $this->redirectToRoute('cart_view');
        }
    
        // Mise à jour de la quantité
        $cart->setQuantity($quantity);
        $em->flush();
    
        // Calculer le nombre total d'articles et le prix total du panier
        $user = $this->getUser();
        $cartItems = $em->getRepository(Cart::class)->findBy(['user' => $user]);
    
        $totalItems = 0;
        $totalPrice = 0;
    
        foreach ($cartItems as $item) {
            $totalItems += $item->getQuantity();
            $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
        }
    
        // Retourner la page du panier avec les informations mises à jour
        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'totalItems' => $totalItems,
            'totalPrice' => number_format($totalPrice, 2, ',', ' '),  // Formater le prix total
        ]);
    }
    



}
