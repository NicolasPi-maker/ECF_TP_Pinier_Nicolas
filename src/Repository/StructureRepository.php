<?php

namespace App\Repository;

use App\Entity\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Structure>
 *
 * @method Structure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Structure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Structure[]    findAll()
 * @method Structure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Structure::class);
    }

    public function save(Structure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Structure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Structure[] Returns an array of Structure objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Structure
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getAll(): array
    {
      return $this->createQueryBuilder('s')
        ->getQuery()
        ->getArrayResult()
        ;
    }

    public function getAllByCurrentFranchise($franchiseId): array
    {
      return $this->createQueryBuilder('s')
        ->where('s.franchise_id = :franchiseId')
        ->setParameter('franchiseId', $franchiseId)
        ->getQuery()
        ->getArrayResult()
        ;
    }

  public function franchiseStructureFiltered($franchiseId, $filter = null)
  {
    return $this->createQueryBuilder('s')
      ->where('s.is_active = :filter')
      ->andWhere('s.franchise_id = :franchiseId')
      ->setParameter('filter', $filter)
      ->setParameter('franchiseId', $franchiseId)
      ->getQuery()
      ->getArrayResult()
      ;
  }

    public function structureFiltered($filter = null)
    {
      return $this->createQueryBuilder('s')
        ->where('s.is_active = :filter')
        ->setParameter('filter', $filter)
        ->getQuery()
        ->getArrayResult()
        ;
    }

    public function structureFilteredByFranchise($franchiseId, $filter = null): array
    {
      return $this->createQueryBuilder('s')
        ->where('s.is_active = :filter')
        ->andWhere('s.franchise_id = :franchiseId')
        ->setParameter('franchiseId', $franchiseId)
        ->setParameter('filter', $filter)
        ->getQuery()
        ->getArrayResult()
        ;
    }

  public function structureFilteredBySearch($search = null)
  {
    return $this->createQueryBuilder('s')
      ->where('s.manager_name LIKE :search_manager')
      ->orWhere('s.structure_name LIKE :search_structure_name')
      ->orWhere('s.structure_address LIKE :search_address')
      ->setParameter('search_manager', '%'.$search.'%')
      ->setParameter('search_structure_name', '%'.$search.'%')
      ->setParameter('search_address', '%'.$search.'%')
      ->getQuery()
      ->getArrayResult();
  }

    public function structureFilteredBySearchAndByFranchise($search = null, $franchiseId = null)
    {
      return $this->createQueryBuilder('s')
        ->where('s.manager_name LIKE :search_manager')
        ->orWhere('s.structure_name LIKE :search_structure_name')
        ->orWhere('s.structure_address LIKE :search_address')
        ->andWhere('s.franchise_id = :franchiseId')
        ->setParameter('franchiseId', $franchiseId)
        ->setParameter('search_manager', '%'.$search.'%')
        ->setParameter('search_structure_name', '%'.$search.'%')
        ->setParameter('search_address', '%'.$search.'%')
        ->getQuery()
        ->getArrayResult();
    }
}
