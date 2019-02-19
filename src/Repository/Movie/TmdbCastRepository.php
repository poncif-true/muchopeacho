<?php

namespace App\Repository\Movie;

use App\Entity\Movie\TmdbCast;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TmdbCast|null find($id, $lockMode = null, $lockVersion = null)
 * @method TmdbCast|null findOneBy(array $criteria, array $orderBy = null)
 * @method TmdbCast[]    findAll()
 * @method TmdbCast[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TmdbCastRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TmdbCast::class);
    }

//    /**
//     * @return TmdbCast[] Returns an array of TmdbCast objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TmdbCast
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
