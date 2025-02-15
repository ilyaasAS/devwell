<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Cart; // Assurez-vous que Cart est correctement importé
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity()
    {
        // Créer un objet User
        $user = new User();

        // Test des setters et getters pour les propriétés simples
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('john.doe@example.com');
        $user->setPassword('password123');

        // Vérification des valeurs
        $this->assertSame('John', $user->getFirstName());
        $this->assertSame('Doe', $user->getLastName());
        $this->assertSame('john.doe@example.com', $user->getEmail());
        $this->assertSame('password123', $user->getPassword());

        // Test des rôles
        $user->setRoles(['ROLE_ADMIN']);

        // Vérifier que le rôle ROLE_USER est aussi présent par défaut
        $this->assertContains('ROLE_USER', $user->getRoles()); // Ajout du test pour ROLE_USER par défaut
        $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());

        // Vérifier l'email comme identifiant
        $this->assertSame('john.doe@example.com', $user->getUserIdentifier());

        // Test de la relation avec Cart (ajout et retrait de panier)
        $cart = new Cart(); // Supposons que la classe Cart soit définie correctement.
        $user->addCart($cart);

        // Vérifier que le panier a bien été ajouté
        $this->assertCount(1, $user->getCarts());
        $this->assertTrue($user->getCarts()->contains($cart));

        // Test du retrait d'un panier
        $user->removeCart($cart);

        // Vérifier que le panier a bien été retiré
        $this->assertCount(0, $user->getCarts());
        $this->assertFalse($user->getCarts()->contains($cart));
    }
}
