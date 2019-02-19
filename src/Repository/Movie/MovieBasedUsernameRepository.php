<?php

namespace App\Repository\Movie;

use App\Entity\Movie\MovieBasedUsername;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MovieBasedUsername|null find($id, $lockMode = null, $lockVersion = null)
 * @method MovieBasedUsername|null findOneBy(array $criteria, array $orderBy = null)
 * @method MovieBasedUsername[]    findAll()
 * @method MovieBasedUsername[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieBasedUsernameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MovieBasedUsername::class);
    }

//    /**
//     * @return MovieBasedUsername[] Returns an array of MovieBasedUsername objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MovieBasedUsername
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
