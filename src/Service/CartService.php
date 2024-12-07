<?php

// src/Service/CartService.php
namespace App\Service;

use App\Repository\CartRepository;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Security;

class CartService
{
    private $cartRepository;
    private $security;

    // Injection des dépendances : CartRepository pour accéder au panier et Security pour accéder à l'utilisateur connecté
    public function __construct(CartRepository $cartRepository, SecurityBundleSecurity $security)

    {
        $this->cartRepository = $cartRepository;
        $this->security = $security;
    }

    // Méthode pour obtenir le nombre total d'articles dans le panier
    public function getTotalItems(): int
    {
        $totalItems = 0;
        $user = $this->security->getUser(); // Récupérer l'utilisateur connecté
        
        if ($user) {
            $cartItems = $this->cartRepository->findBy(['user' => $user]); // Récupérer tous les articles du panier de cet utilisateur
            foreach ($cartItems as $item) {
                $totalItems += $item->getQuantity(); // Ajouter la quantité de chaque article au total
            }
        }
        
        return $totalItems; // Retourner le nombre total d'articles
    }
}
