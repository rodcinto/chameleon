<?php
namespace App\Service;

use App\Entity\Simulation;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class SimulationExporter
{
    const SERIALIZE_JSON = 'json';

    /**
     * @var Simulation
     */
     private $simulation;

     /**
      * @var array
      */
     private $ignoredAttributes;

    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
        $this->defineIgnoredAttributes();
    }

    public function exportToJson()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize(
            $this->simulation,
            self::SERIALIZE_JSON,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => $this->ignoredAttributes]
        );
    }

    private function defineIgnoredAttributes()
    {
        $this->ignoredAttributes = [
            'id',
            'ttl',
            'active',
            'created',
            'updated',
        ];
    }
}
