<?php

// src/Repository/ProductRepository.php

namespace App\Repository; // Définition de l'espace de noms pour ce repository de l'entité Product.

use App\Entity\Product; // Importation de l'entité Product, qui représente les produits dans la base de données.
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; // Importation de la classe de base ServiceEntityRepository pour interagir avec Doctrine.
use Doctrine\Persistence\ManagerRegistry; // Importation de ManagerRegistry, utilisé pour accéder à la base de données via Doctrine.

class ProductRepository extends ServiceEntityRepository
{
    // Le constructeur initialise ce repository pour l'entité Product.
    public function __construct(ManagerRegistry $registry)
    {
        // Appel au constructeur parent pour lier ce repository à l'entité Product.
        parent::__construct($registry, Product::class);
    }

    /**
     * Trouve tous les produits d'une catégorie spécifique.
     *
     * @param int $categoryId L'identifiant de la catégorie.
     * @return Product[] Retourne un tableau d'objets Product correspondant à la catégorie.
     */
    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('p') // Crée une requête sur l'entité Product, alias 'p'.
            ->andWhere('p.category = :categoryId') // Filtre les produits par l'ID de la catégorie.
            ->setParameter('categoryId', $categoryId) // Associe l'ID de la catégorie au paramètre.
            ->orderBy('p.name', 'ASC') // Trie les produits par nom, par ordre croissant.
            ->getQuery() // Exécute la requête.
            ->getResult(); // Retourne les résultats sous forme de tableau d'objets Product.
    }

    /**
     * Trouve les produits dont le stock est inférieur à un seuil donné.
     *
     * @param int $threshold Le seuil de stock.
     * @return Product[] Retourne un tableau d'objets Product dont le stock est inférieur au seuil.
     */
    public function findLowStock(int $threshold): array
    {
        return $this->createQueryBuilder('p') // Crée une requête sur l'entité Product, alias 'p'.
            ->andWhere('p.stock < :threshold') // Filtre les produits dont le stock est inférieur au seuil.
            ->setParameter('threshold', $threshold) // Associe la valeur du seuil au paramètre.
            ->getQuery() // Exécute la requête.
            ->getResult(); // Retourne les résultats sous forme de tableau d'objets Product.
    }
}
