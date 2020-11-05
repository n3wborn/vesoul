<?php

namespace App\Repository;

use App\Entity\Genra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Genra|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genra|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genra[]    findAll()
 * @method Genra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genra::class);
    }

    public function findAllGenra() : array
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.name')
            ->groupBy('g.name')
            ->distinct()
            ->getQuery();

        return $qb->execute();
    }
    
    // /**
    //  * @return Genra[] Returns an array of Genra objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Genra
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
