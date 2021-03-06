<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findBestUsers($limit = 2)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.ads', 'a')
            ->join('a.comments', 'c')
            ->select('u as user, AVG(c.rating) as avgRatings, COUNT(c) as sumComments')
            ->groupBy('u')
            ->having('sumComments > 3')
            ->orderBy('avgRatings', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }
}
