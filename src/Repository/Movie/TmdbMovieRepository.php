<?php

namespace App\Repository\Movie;

use App\Entity\Movie\TmdbMovie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TmdbMovie|null find($id, $lockMode = null, $lockVersion = null)
 * @method TmdbMovie|null findOneBy(array $criteria, array $orderBy = null)
 * @method TmdbMovie[]    findAll()
 * @method TmdbMovie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TmdbMovieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TmdbMovie::class);
    }

//    /**
//     * @return TmdbMovie[] Returns an array of TmdbMovie objects
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
    public function findOneBySomeField($value): ?TmdbMovie
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
