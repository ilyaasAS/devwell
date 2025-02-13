<?php

namespace App\Repository; // Définit l'espace de noms du repository pour l'entité Order.

use App\Entity\Order; // Importation de l'entité Order qui représente les commandes dans la base de données.
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository; // Importation de la classe de base ServiceEntityRepository pour interagir avec Doctrine.
use Doctrine\Persistence\ManagerRegistry; // Importation de ManagerRegistry, qui permet l'interaction avec la base de données via Doctrine.

class OrderRepository extends ServiceEntityRepository
{
    // Le constructeur initialise ce repository pour l'entité Order.
    public function __construct(ManagerRegistry $registry)
    {
        // Appel au constructeur parent pour lier ce repository à l'entité Order.
        parent::__construct($registry, Order::class);
    }

    /**
     * Sauvegarde ou met à jour une entité Order dans la base de données.
     * 
     * @param Order $order L'entité Order à sauvegarder.
     * @param bool $flush Indique si les changements doivent être persistés immédiatement.
     */
    public function save(Order $order, bool $flush = true): void
    {
        // Persister l'entité Order dans le gestionnaire d'entités de Doctrine.
        $this->_em->persist($order);

        // Si flush est vrai, les changements sont envoyés dans la base de données.
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Supprime une entité Order de la base de données.
     *
     * @param Order $order L'entité Order à supprimer.
     * @param bool $flush Indique si les changements doivent être persistés immédiatement.
     */
    public function remove(Order $order, bool $flush = true): void
    {
        // Supprimer l'entité Order de la gestion des entités.
        $this->_em->remove($order);

        // Si flush est vrai, les changements sont envoyés dans la base de données.
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Trouve toutes les commandes d'un utilisateur donné.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     * @return Order[] Un tableau d'objets Order correspondant à l'utilisateur.
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('o') // Créer une requête sur l'entité Order, alias 'o'.
            ->andWhere('o.user = :userId') // Filtrer les commandes par userId.
            ->setParameter('userId', $userId) // Définir la valeur du paramètre.
            ->orderBy('o.createdAt', 'DESC') // Trier les résultats par date de création, du plus récent au plus ancien.
            ->getQuery() // Exécuter la requête.
            ->getResult(); // Retourner les résultats sous forme de tableau.
    }

    /**
     * Trouve toutes les commandes avec un statut donné.
     *
     * @param string $status Le statut des commandes à rechercher.
     * @return Order[] Un tableau d'objets Order ayant ce statut.
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o') // Créer une requête sur l'entité Order, alias 'o'.
            ->andWhere('o.status = :status') // Filtrer les commandes par statut.
            ->setParameter('status', $status) // Définir la valeur du paramètre.
            ->orderBy('o.createdAt', 'DESC') // Trier les résultats par date de création, du plus récent au plus ancien.
            ->getQuery() // Exécuter la requête.
            ->getResult(); // Retourner les résultats sous forme de tableau.
    }

    /**
     * Trouve toutes les commandes créées dans une plage de dates donnée.
     *
     * @param \DateTime $startDate La date de début de la plage.
     * @param \DateTime $endDate La date de fin de la plage.
     * @return Order[] Un tableau d'objets Order créés entre les deux dates.
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('o') // Créer une requête sur l'entité Order, alias 'o'.
            ->andWhere('o.createdAt BETWEEN :startDate AND :endDate') // Filtrer les commandes créées entre les dates.
            ->setParameter('startDate', $startDate) // Définir la valeur du paramètre pour la date de début.
            ->setParameter('endDate', $endDate) // Définir la valeur du paramètre pour la date de fin.
            ->orderBy('o.createdAt', 'DESC') // Trier les résultats par date de création, du plus récent au plus ancien.
            ->getQuery() // Exécuter la requête.
            ->getResult(); // Retourner les résultats sous forme de tableau.
    }
}
