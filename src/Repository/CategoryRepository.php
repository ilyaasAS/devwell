<?php

// src/Repository/CategoryRepository.php

namespace App\Repository; // Définition de l'espace de noms pour le repository des catégories.

use App\Entity\Category; // Importation de l'entité Category, qui représente les catégories dans la base de données.
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; // Importation de la classe de base pour un repository Doctrine.
use Doctrine\Persistence\ManagerRegistry; // Importation du ManagerRegistry, nécessaire pour interagir avec la base de données.

class CategoryRepository extends ServiceEntityRepository
{
    // Le constructeur initialise le repository pour l'entité Category.
    public function __construct(ManagerRegistry $registry)
    {
        // Appel au constructeur parent pour lier ce repository à l'entité Category.
        parent::__construct($registry, Category::class);
    }

    /**
     * Retourne une liste de catégories triées par nom.
     * 
     * Cette méthode crée une requête Doctrine pour récupérer toutes les catégories, triées par leur nom (ordre croissant).
     *
     * @return Category[] Retourne un tableau d'objets Category.
     */
    public function findAllOrderedByName(): array
    {
        return $this->createQueryBuilder('c') // Création d'un query builder pour l'entité 'Category' (alias 'c').
            ->orderBy('c.name', 'ASC') // Tri des résultats par nom (ordre croissant).
            ->getQuery() // Préparation de la requête.
            ->getResult(); // Exécution de la requête et récupération des résultats.
    }

    /**
     * Trouve une catégorie par son nom.
     * 
     * Cette méthode crée une requête Doctrine pour récupérer une seule catégorie en fonction de son nom.
     * Elle renvoie la catégorie trouvée ou null si aucune catégorie ne correspond au nom donné.
     * 
     * @param string $name Le nom de la catégorie à rechercher.
     * @return Category|null Retourne un objet Category si trouvé, sinon null.
     */
    public function findOneByName(string $name): ?Category
    {
        return $this->createQueryBuilder('c') // Création d'un query builder pour l'entité 'Category' (alias 'c').
            ->andWhere('c.name = :val') // Condition pour filtrer par le nom de la catégorie.
            ->setParameter('val', $name) // Définition du paramètre pour la requête (nom de la catégorie).
            ->getQuery() // Préparation de la requête.
            ->getOneOrNullResult(); // Exécution de la requête et récupération d'une seule catégorie ou null si aucune correspondance.
    }
}
