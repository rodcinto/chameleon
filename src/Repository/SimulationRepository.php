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
        $queryBuilder = $this->createQueryBuilder('s');
        foreach ($criteria as $field => $value) {
            if (empty($value)) {
                continue;
            }
            if ($field === 'parameters') {
                $queryBuilder->andWhere(sprintf('s.%s LIKE :val_%s', $field, $field))
                    ->setParameter('val_' . $field, $value);
                continue;
            }

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
        $date = new DateTime();
        $date->setTimestamp($lastTime);

        return $this->createQueryBuilder('s')
            ->andWhere('s.created > :val')
            ->setParameter('val', $date)
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
    public function findAllByPage(int $currentPage = 1, $searchTerms = [])
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if ([] !== $searchTerms) {
            if (isset($searchTerms['alias'])) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->like('s.alias',
                        $queryBuilder->expr()->literal('%' . $searchTerms['alias'] . '%')
                    )
                );
            }
            if (isset($searchTerms['category'])) {
                // Break it.
                $categories = explode(',', $searchTerms['category']);

                // Clean it.
                $categories = array_map(function($term) {
                    return trim($term);
                }, $categories);

                // Glue it back.
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->in('s.category', $categories)
                );
            }

            if (isset($searchTerms['token'])) {
                // Break it.
                $tokens = explode(',', $searchTerms['token']);

                // Clean it.
                $tokens = array_map(function($term) {
                    return trim($term);
                }, $tokens);

                // Glue it back.
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->in('s.token', $tokens)
                );
            }
        }

        $queryBuilder->orderBy('s.created', 'DESC');

        $paginatedResult = $this->paginate(
            $queryBuilder->getQuery(),
            $currentPage,
            self::MAX_RESULTS
        );

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

    /**
     * @return Simulation[]
     */
    public function findAllWithTTL()
    {
        return $this->createQueryBuilder('s')
            ->where('s.ttl > :val')
            ->setParameter('val', 0)
            ->getQuery()
            ->getResult();
    }
}
