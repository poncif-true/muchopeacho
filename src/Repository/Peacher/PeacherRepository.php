<?php

namespace App\Repository\Peacher;

use App\Entity\Peacher\Peacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Peacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Peacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Peacher[]    findAll()
 * @method Peacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeacherRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Peacher::class);
    }

//    /**
//     * @return Peacher[] Returns an array of Peacher objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Peacher
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
