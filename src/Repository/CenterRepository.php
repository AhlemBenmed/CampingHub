<?php

namespace App\Repository;

use App\Entity\Center;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Center>
 */
class CenterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Center::class);
    }
    public function findByFilters($governorate = null, $city = null, $name = null)
    {
        $qb = $this->createQueryBuilder('c');

        if ($governorate) {
            $qb->orWhere('c.location LIKE :governorate')
                ->setParameter('governorate', '%'.$governorate.'%');
        }

        if ($city) {
            $qb->orWhere('c.location LIKE :city')
                ->setParameter('city', '%'.$city.'%');
        }

        if ($name) {
            $qb->orWhere('c.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return Center[] Returns an array of Center objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Center
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
