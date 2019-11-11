<?php

namespace App\Service;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class Stats
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM '.User::class.' u')->getSingleScalarResult();
    }

    public function getAdsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM '.Ad::class.' a')->getSingleScalarResult();
    }

    public function getBookingsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM '.Booking::class.' b')->getSingleScalarResult();
    }

    public function geCommentsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM '.Comment::class.' c')->getSingleScalarResult();
    }

    public function getAdsStats($direction)
    {
        $bestAds = $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
                FROM '.Comment::class.' c
                JOIN c.ad a
                JOIN a.author u
                GROUP BY a
                ORDER BY note '.$direction
        )
        ->setMaxResults(5)
        ->getResult();

        return $bestAds;
    }

    public function getBestAds()
    {
        return $this->getAdsStats('DESC');
    }

    public function getWorstAds()
    {
        return $this->getAdsStats('ASC');
    }
}

