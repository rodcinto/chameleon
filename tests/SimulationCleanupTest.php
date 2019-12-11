<?php

namespace App\Tests;

use App\Entity\Simulation;
use App\Service\SimulationCleanup;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class SimulationCleanupTest extends TestCase
{
    const TTL = 15;

    private $simulationCleanup;

    public function setUp()
    {
        parent::setUp();

        $this->simulationCleanup = $this->getMockBuilder(SimulationCleanup::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCheckSimulationExpired()
    {
        $modifiedCreated = new DateTime();
        $modifiedCreated->modify('-30 minutes');

        $simulation = new Simulation();
        $simulation
            ->setCreated($modifiedCreated)
            ->setTtl(self::TTL);

        // Assert Simulation out of date.
        $this->assertTrue($this->invokeMethod('checkSimulationExpired', [$simulation]));

        $modifiedCreated = new DateTime();
        $modifiedCreated->modify('-14 minutes');

        $simulation->setCreated($modifiedCreated);

        // Assert Simulation valid.
        $this->assertFalse($this->invokeMethod('checkSimulationExpired', [$simulation]));
    }

    /**
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     * @throws ReflectionException
     */
    private function invokeMethod(string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(SimulationCleanup::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->simulationCleanup, $parameters);
    }
}
