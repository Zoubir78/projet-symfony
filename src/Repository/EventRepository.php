<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public const PAGINATION_OFFSET = 15;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findUserOrganizedEvents(User $user, int $page)
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.author = :user')
            ->setParameter('user', $user)
            ->orderBy('e.date', 'DESC')
            ->setFirstResult(($page - 1) * self::PAGINATION_OFFSET)
            ->setMaxResults(self::PAGINATION_OFFSET);

        return new Paginator($query);
    }

    public function findUserAttendedEvents(User $user, int $page)
    {
        $query = $this->createQueryBuilder('e')
            ->innerJoin('e.participants', 'p')
            ->where('p = :user')
            ->setParameter('user', $user)
            ->orderBy('e.date', 'DESC')
            ->setFirstResult(($page - 1) * self::PAGINATION_OFFSET)
            ->setMaxResults(self::PAGINATION_OFFSET);

        return new Paginator($query);
    }

    public function findPaginated(int $page)
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.date', 'DESC')
            ->setFirstResult(($page - 1) * self::PAGINATION_OFFSET)
            ->setMaxResults(self::PAGINATION_OFFSET);

        return new Paginator($query);
    }
}