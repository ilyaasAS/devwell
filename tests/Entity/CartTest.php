<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Entity\User; // Assurez-vous d'importer la classe User si nécessaire
use App\Entity\Product; // Assurez-vous d'importer la classe Product
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    public function testCartEntity()
    {
        // Création d'un utilisateur mocké
        $user = $this->createMock(User::class);

        // Création d'un produit mocké
        $product = $this->createMock(Product::class);

        // Création d'une instance de l'entité Cart
        $cart = new Cart();

        // Test de la méthode setUser() et du getter getUser()
        $cart->setUser($user);
        $this->assertSame($user, $cart->getUser(), 'Le user n\'a pas été correctement assigné au panier.');

        // Test de la méthode setProduct() et du getter getProduct()
        $cart->setProduct($product);
        $this->assertSame($product, $cart->getProduct(), 'Le produit n\'a pas été correctement assigné au panier.');

        // Test de la méthode setQuantity() et du getter getQuantity()
        $cart->setQuantity(5);
        $this->assertSame(5, $cart->getQuantity(), 'La quantité n\'a pas été correctement assignée au panier.');
    }
}
