<?php

// src/Service/CartService.php
namespace App\Service; // Déclare l'espace de noms pour cette classe, indiquant qu'elle fait partie du service de l'application.

use App\Repository\CartRepository; // Importation de CartRepository pour interagir avec la base de données des paniers.
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity; // Importation de la classe Security de Symfony pour gérer la sécurité et les utilisateurs.
use Symfony\Component\Security\Core\Security; // Importation de la classe Security, qui permet d'accéder à l'utilisateur actuellement authentifié.

class CartService
{
    private $cartRepository; // Déclare la propriété cartRepository pour accéder au repository du panier.
    private $security; // Déclare la propriété security pour gérer l'utilisateur connecté.

    // Injection des dépendances dans le constructeur : CartRepository pour interagir avec les paniers et Security pour accéder à l'utilisateur.
    public function __construct(CartRepository $cartRepository, SecurityBundleSecurity $security)
    {
        $this->cartRepository = $cartRepository; // Assigne le CartRepository à la propriété cartRepository.
        $this->security = $security; // Assigne l'objet Security à la propriété security.
    }

    // Méthode pour obtenir le nombre total d'articles dans le panier de l'utilisateur connecté.
    public function getTotalItems(): int
    {
        $totalItems = 0; // Initialise le compteur d'articles à 0.
        $user = $this->security->getUser(); // Récupère l'utilisateur actuellement authentifié.

        if ($user) { // Vérifie si un utilisateur est connecté.
            // Si un utilisateur est connecté, récupère tous les éléments du panier associés à cet utilisateur.
            $cartItems = $this->cartRepository->findBy(['user' => $user]);

            // Parcourt tous les éléments du panier et ajoute la quantité de chaque article au total.
            foreach ($cartItems as $item) {
                $totalItems += $item->getQuantity(); // Incrémente totalItems en fonction de la quantité de chaque article.
            }
        }
        
        return $totalItems; // Retourne le nombre total d'articles dans le panier.
    }
}
