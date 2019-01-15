<?php

namespace App\Repository;

use App\Entity\Likes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Likes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Likes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Likes[]    findAll()
 * @method Likes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikesRepository extends ServiceEntityRepository
{
    private const LAST_LIKES_LIMIT = 5;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Likes::class);
    }

    public function getCountLikes()
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function findLastLikes()
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->setMaxResults(self::LAST_LIKES_LIMIT)
            ->orderBy('l.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Like[] Returns an array of Like objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
