<?php


namespace App\Service;


use App\Entity\Simulation;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class SimulationCleanup
{
    const TTL_VALUE_FALLBACK = 15;

    private $entityManager;
    private $repository;

    /**
     * SimulationCleanup constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Simulation::class);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function cleanup()
    {
        $simulations = $this->repository->findAllWithTTL();

        $numberOfDeleted = 0;

        foreach($simulations as $simulation) {
            if ($this->checkSimulationExpired($simulation)) {
                $this->entityManager->remove($simulation);
                $numberOfDeleted++;
            }
        }

        $this->entityManager->flush();

        return $numberOfDeleted;
    }

    /**
     * @param Simulation $simulation
     * @throws Exception
     * @return bool
     */
    protected function checkSimulationExpired($simulation)
    {
        $ttl = $simulation->getTtl() ?: self::TTL_VALUE_FALLBACK;

        $dateEstimation = $simulation->getCreated()->add(new DateInterval(sprintf('PT%dM', $ttl)));

        $currentTime = new DateTime();

        return $currentTime > $dateEstimation;
    }
}