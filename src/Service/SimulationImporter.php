<?php
namespace App\Service;

use DateTime;
use App\Entity\Simulation;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SimulationImporter
{
    const SERIALIZE_FORMAT = 'json';
    const DEFAULT_TTL = 15;

    private $serializedSimulation;

    public function __construct(string $serializedSimulation)
    {
        $this->serializedSimulation = $serializedSimulation;
    }

    public function loadSimulation()
    {
        $simulation = $this->createRawSimulation();

        $normalizer = [new ObjectNormalizer()];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizer, $encoders);
        $serializer->deserialize(
            $this->serializedSimulation,
            Simulation::class,
            self::SERIALIZE_FORMAT,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $simulation,
            ]
        );

        return $simulation;
    }

    private function createRawSimulation()
    {
        $simulation = new Simulation();
        $simulation
            ->setParameters('')
            ->setAlias('')
            ->setCategory('')
            ->setToken('')
            ->setActive(true)
            ->setResponseDelay(0)
            ->setTtl(self::DEFAULT_TTL)
            ->setCreated(new DateTime());

        return $simulation;
    }
}
