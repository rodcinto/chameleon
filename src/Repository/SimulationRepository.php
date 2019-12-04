<?php

namespace App\Repository;

use App\Entity\Simulation;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

/**
 * @method Simulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Simulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Simulation[]    findAll()
 * @method Simulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimulationRepository extends ServiceEntityRepository
{
    const MAX_RESULTS = 2;

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
            ->getQuery()
            ->getResult();
    }

    /**
     * Find only fresh.
     *
     * @param integer $lastTime
     * @return void
     * @throws Exception
     */
    public function findFresh(int $lastTime)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.created > :val')
            ->setParameter('val', new DateTime('@' . $lastTime))
            ->orderBy('s.created', 'DESC')
            ->setMaxResults(self::MAX_RESULTS)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all.
     *
     * @param integer $currentPage
     * @return Paginator|void
     */
    public function findAllByPage(int $currentPage = 1)
    {
        $query = $this->createQueryBuilder('s')
            ->orderBy('s.created', 'DESC')
            ->getQuery();

        $paginatedResult = $this->paginate($query, $currentPage, self::MAX_RESULTS);

        return $paginatedResult;
    }

    /**
     * Paginate.
     *
     * @param Query $query
     * @param integer $page
     * @param integer $limit
     * @return Paginator
     */
    private function paginate(Query $query, int $page, int $limit = 5)
    {
        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}
