<?php

namespace App\Tests\Entity;

use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function testOrderItemEntity()
    {
        // Création d'une instance de Product
        $product = new Product();
        // On pourrait potentiellement initialiser un produit avec certaines valeurs si nécessaire.
        // Exemple: $product->setName("Produit Test");

        // Création d'une instance de Order
        $order = new Order();
        // De même pour l'ordre, ajouter des propriétés comme le statut, utilisateur, etc.

        // Création de l'entité OrderItem
        $orderItem = new OrderItem();

        // Test du setter et du getter pour le produit
        $orderItem->setProduct($product);
        $this->assertSame($product, $orderItem->getProduct(), 'Le produit n\'a pas été correctement assigné à l\'article de commande.');

        // Test du setter et du getter pour la quantité
        $orderItem->setQuantity(5);
        $this->assertSame(5, $orderItem->getQuantity(), 'La quantité n\'a pas été correctement assignée à l\'article de commande.');

        // Test du setter et du getter pour le prix
        $orderItem->setPrice(100.5);
        $this->assertSame(100.5, $orderItem->getPrice(), 'Le prix n\'a pas été correctement assigné à l\'article de commande.');

        // Test du setter et du getter pour la commande associée
        $orderItem->setOrder($order);
        $this->assertSame($order, $orderItem->getOrder(), 'La commande n\'a pas été correctement assignée à l\'article de commande.');
    }
}
