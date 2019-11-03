<?php

namespace App\Repository;

use App\Entity\Simulation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Simulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Simulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Simulation[]    findAll()
 * @method Simulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimulationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Simulation::class);
    }

    /**
     * Search the simulation with Request that satisfies the criteria.
     *
     * @param array $criteria
     * @return array
     */
    public function findRequestBy(array $criteria)
    {
        //@todo Where should I put the logic of content proximity?
        $queryBuilder = $this->createQueryBuilder('s');
        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere(sprintf('s.%s = :val_%s', $field, $field))
                ->setParameter('val_' . $field, $value);
        }

        return $queryBuilder
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
