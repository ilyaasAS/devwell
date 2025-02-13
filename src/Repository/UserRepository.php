<?php

namespace App\Repository; // Déclare l'espace de noms du repository pour cette entité (ici, User).

use App\Entity\User; // Importation de l'entité User pour travailler avec cette classe.
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; // Importation de la classe ServiceEntityRepository de Doctrine, qui permet de manipuler les entités en base de données.
use Doctrine\Persistence\ManagerRegistry; // Importation du ManagerRegistry, utilisé pour interagir avec Doctrine et la base de données.

class UserRepository extends ServiceEntityRepository // Déclare la classe UserRepository, qui étend ServiceEntityRepository pour effectuer des opérations CRUD sur l'entité User.
{
    // Le constructeur initialisant le repository avec l'objet ManagerRegistry, permettant l'accès à l'entité User.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class); // Appel au constructeur parent de ServiceEntityRepository pour lier le repository à l'entité User.
    }

    // Méthode pour enregistrer ou mettre à jour une entité User dans la base de données.
    public function save(User $user, bool $flush = false): void
    {
        $this->_em->persist($user); // Marque l'entité User pour être persistée (ajoutée ou mise à jour dans la base de données).
        if ($flush) {
            $this->_em->flush(); // Si $flush est true, applique les changements dans la base de données immédiatement.
        }
    }
}
