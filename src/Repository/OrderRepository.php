<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Save or update an Order entity.
     *
     * @param Order $order
     * @param bool $flush
     */
    public function save(Order $order, bool $flush = true): void
    {
        $this->_em->persist($order);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Remove an Order entity.
     *
     * @param Order $order
     * @param bool $flush
     */
    public function remove(Order $order, bool $flush = true): void
    {
        $this->_em->remove($order);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Find orders by user.
     *
     * @param int $userId
     * @return Order[]
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find orders by status.
     *
     * @param string $status
     * @return Order[]
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find orders created within a specific date range.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return Order[]
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
