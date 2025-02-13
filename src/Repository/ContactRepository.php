<?php

namespace App\Repository; // Définit l'espace de noms du repository pour l'entité Contact.

use App\Entity\Contact; // Importation de l'entité Contact, qui représente les informations de contact dans la base de données.
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; // Importation de la classe de base ServiceEntityRepository pour utiliser avec Doctrine.
use Doctrine\Persistence\ManagerRegistry; // Importation de ManagerRegistry, nécessaire pour interagir avec la base de données.

class ContactRepository extends ServiceEntityRepository
{
    // Le constructeur initialise le repository pour l'entité Contact.
    public function __construct(ManagerRegistry $registry)
    {
        // Appel au constructeur parent pour lier ce repository à l'entité Contact.
        parent::__construct($registry, Contact::class);
    }

//    /**
//     * @return Contact[] Returns an array of Contact objects
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

//    public function findOneBySomeField($value): ?Contact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
