<?php

namespace App\Tests\Service;

use App\Service\CartService; // On importe la classe CartService qui contient la logique métier
use App\Repository\CartRepository; // On importe le repository CartRepository qui permet d'interagir avec la base de données pour les paniers
use Symfony\Bundle\SecurityBundle\Security; // On importe le service de sécurité qui permet de récupérer l'utilisateur actuellement connecté
use PHPUnit\Framework\TestCase; // On importe la classe TestCase de PHPUnit, qui est la classe de base pour écrire des tests unitaires
use Symfony\Component\Security\Core\User\UserInterface; // On importe l'interface UserInterface pour simuler un utilisateur dans les tests

class CartServiceTest extends TestCase
{
    // Déclaration des variables privées utilisées pour stocker les mocks des objets nécessaires
    private $cartRepository; // Représente le repository pour interagir avec les paniers
    private $security; // Représente le service de sécurité pour gérer l'utilisateur connecté
    private $cartService; // L'instance du service CartService qui sera testée

    // La méthode setUp() est exécutée avant chaque test pour initialiser les objets nécessaires
    protected function setUp(): void
    {
        // Création des mocks pour le repository et le service de sécurité
        $this->cartRepository = $this->createMock(CartRepository::class);
        $this->security = $this->createMock(Security::class);

        // Création de l'objet CartService avec les mocks comme dépendances
        $this->cartService = new CartService($this->cartRepository, $this->security);
    }

    // Teste la méthode getTotalItems() lorsqu'un utilisateur est connecté
    public function testGetTotalItemsWithUser()
    {
        // Création d'un mock pour l'utilisateur connecté
        $user = $this->createMock(UserInterface::class);

        // Configuration du mock pour que la méthode getUser retourne cet utilisateur simulé
        $this->security->method('getUser')->willReturn($user);

        // Création de deux articles de panier simulés avec des quantités différentes
        $cartItem1 = $this->createMock(\App\Entity\Cart::class);
        $cartItem1->method('getQuantity')->willReturn(2); // Le premier article a une quantité de 2

        $cartItem2 = $this->createMock(\App\Entity\Cart::class);
        $cartItem2->method('getQuantity')->willReturn(3); // Le deuxième article a une quantité de 3

        // Configuration du mock du repository pour que la méthode findBy retourne les deux articles simulés pour cet utilisateur
        $this->cartRepository->method('findBy')->with(['user' => $user])->willReturn([$cartItem1, $cartItem2]);

        // Vérification que la méthode getTotalItems retourne la somme des quantités des articles du panier
        $this->assertEquals(5, $this->cartService->getTotalItems()); // 2 + 3 = 5
    }

    // Teste la méthode getTotalItems() lorsqu'aucun utilisateur n'est connecté
    public function testGetTotalItemsWithoutUser()
    {
        // Configuration du mock pour que la méthode getUser retourne null (aucun utilisateur connecté)
        $this->security->method('getUser')->willReturn(null);

        // Vérification que la méthode getTotalItems retourne 0 si aucun utilisateur n'est connecté
        $this->assertEquals(0, $this->cartService->getTotalItems()); // Pas d'articles sans utilisateur
    }
}
