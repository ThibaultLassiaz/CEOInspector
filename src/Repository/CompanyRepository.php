<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function save(Company $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Company $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countTreatedAndNonTreatedByFileId(File $file): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c) as total, SUM(CASE WHEN c.treated = true THEN 1 ELSE 0 END) as treated, SUM(CASE WHEN c.treated = false THEN 1 ELSE 0 END) as non_treated')
            ->where('c.file = :file')
            ->setParameter('file', $file);
        return $qb->getQuery()->getSingleResult();
    }
}
