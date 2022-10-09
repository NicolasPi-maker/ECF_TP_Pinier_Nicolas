<?php

namespace App\Repository;

use App\Entity\Franchise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Franchise>
 *
 * @method Franchise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Franchise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Franchise[]    findAll()
 * @method Franchise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FranchiseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Franchise::class);
    }

    public function save(Franchise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Franchise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Franchise[] Returns an array of Franchise objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Franchise
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
      public function getAll(): array
      {
        return $this->createQueryBuilder('f')
          ->getQuery()
          ->getArrayResult()
          ;
      }

      public function franchiseFiltered($filter = null): array
      {
        return $this->createQueryBuilder('f')
          ->where('f.is_active = :filter')
          ->setParameter('filter', $filter)
          ->getQuery()
          ->getArrayResult()
        ;
      }

      public function franchiseFilteredBySearch($search = null)
      {
        return $this->createQueryBuilder('f')
          ->where('f.client_name LIKE :search')
          ->orWhere('f.client_address LIKE :search_address')
          ->orWhere('f.id LIKE :search_id')
          ->setParameter('search', '%'.$search.'%')
          ->setParameter('search_address', '%'.$search.'%')
          ->setParameter('search_id','%'.$search.'%')
          ->getQuery()
          ->getArrayResult();
      }
}
