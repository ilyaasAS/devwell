<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product; // Assurez-vous d'importer la classe Product
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCategoryEntity()
    {
        // Création d'une instance de l'entité Category
        $category = new Category();

        // Test du setter et du getter pour le nom de la catégorie
        $category->setName('Electronics');
        $this->assertSame('Electronics', $category->getName(), 'Le nom de la catégorie n\'a pas été correctement assigné.');

        // Test du setter et du getter pour la description de la catégorie
        $category->setDescription('Category for electronic products');
        $this->assertSame('Category for electronic products', $category->getDescription(), 'La description de la catégorie n\'a pas été correctement assignée.');

        // Création d'un produit mocké
        $product = $this->createMock(Product::class);

        // Test de la méthode addProduct()
        $category->addProduct($product);
        $this->assertCount(1, $category->getProducts(), 'Le produit n\'a pas été ajouté correctement à la catégorie.');

        // Test de la méthode removeProduct()
        $category->removeProduct($product);
        $this->assertCount(0, $category->getProducts(), 'Le produit n\'a pas été supprimé correctement de la catégorie.');
    }
}
