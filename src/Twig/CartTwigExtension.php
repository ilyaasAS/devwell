<?php

// Déclare l'espace de noms pour cette classe, indiquant qu'elle fait partie de l'extension Twig de l'application.
namespace App\Twig;

use Twig\Extension\AbstractExtension; // Importation de la classe AbstractExtension de Twig pour créer une extension Twig personnalisée.
use Twig\Extension\GlobalsInterface; // Importation de l'interface GlobalsInterface pour rendre des variables globales disponibles dans Twig.
use App\Service\CartService; // Importation de la classe CartService pour obtenir des informations liées au panier.

class CartTwigExtension extends AbstractExtension implements GlobalsInterface
{
    private $cartService; // Déclare la propriété cartService qui sera utilisée pour accéder aux informations du panier.

    // Le constructeur de la classe CartTwigExtension. Il reçoit une instance de CartService pour l'injection de dépendance.
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService; // Assigne l'instance de CartService à la propriété cartService.
    }

    // Méthode obligatoire de l'interface GlobalsInterface pour rendre des variables globales disponibles dans les templates Twig.
    public function getGlobals(): array
    {
        return [
            'totalItems' => $this->cartService->getTotalItems(), // Expose la variable 'totalItems' à tous les templates Twig, qui contient le total des articles dans le panier.
        ];
    }
}
