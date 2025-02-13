<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductEntity()
    {
        // Création d'une instance de Category pour tester la relation ManyToOne
        $category = new Category();
        $category->setName('Test Category');  // On suppose que la catégorie a une méthode setName()

        // Création d'une instance de Product
        $product = new Product();

        // Test du setter et du getter pour le nom du produit
        $product->setName('Test Product');
        $this->assertSame('Test Product', $product->getName(), 'Le nom du produit n\'a pas été correctement assigné.');

        // Test du setter et du getter pour le prix du produit
        $product->setPrice(99.99);
        $this->assertSame(99.99, $product->getPrice(), 'Le prix du produit n\'a pas été correctement assigné.');

        // Test du setter et du getter pour le stock du produit
        $product->setStock(10);
        $this->assertSame(10, $product->getStock(), 'Le stock du produit n\'a pas été correctement assigné.');

        // Test du setter et du getter pour l'image du produit
        $product->setImage('image.jpg');
        $this->assertSame('image.jpg', $product->getImage(), 'L\'image du produit n\'a pas été correctement assignée.');

        // Test du setter et du getter pour la description du produit
        $product->setDescription('This is a test product.');
        $this->assertSame('This is a test product.', $product->getDescription(), 'La description du produit n\'a pas été correctement assignée.');

        // Test du setter et du getter pour la catégorie du produit (relation ManyToOne)
        $product->setCategory($category);
        $this->assertSame($category, $product->getCategory(), 'La catégorie du produit n\'a pas été correctement assignée.');

        // Test du setter et du getter pour la nouvelle catégorie (propriété temporaire)
        $product->setNewCategory('New Test Category');
        $this->assertSame('New Test Category', $product->getNewCategory(), 'La nouvelle catégorie n\'a pas été correctement assignée.');
    }
}
