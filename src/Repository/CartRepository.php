<?php

namespace App\Repository;

use App\Entity\Cart; // Importation de l'entité Cart, qui représente un panier dans l'application
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; // Importation de la classe de base pour les repositories avec Doctrine
use Doctrine\Persistence\ManagerRegistry; // Importation du ManagerRegistry, utilisé pour interagir avec la base de données

/**
 * @extends ServiceEntityRepository<Cart> // Le repository est étendu pour l'entité Cart, ce qui permet de gérer les opérations sur cette entité.
 */
class CartRepository extends ServiceEntityRepository
{
    // Le constructeur permet d'initialiser le repository et de lui fournir le ManagerRegistry pour interagir avec la base de données
    public function __construct(ManagerRegistry $registry)
    {
        // Appel au constructeur parent pour indiquer que ce repository est lié à l'entité Cart
        parent::__construct($registry, Cart::class);
    }


//    /**
//     * @return Cart[] Returns an array of Cart objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cart
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
