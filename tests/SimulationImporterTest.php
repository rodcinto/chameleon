<?php
namespace App\Tests;

use App\Entity\Simulation;
use PHPUnit\Framework\TestCase;
use App\Service\SimulationImporter;

class SimulationImporterTest extends TestCase
{
    public function testImport()
    {
        $simulationJson = '{"category":"products","token":"search","httpVerb":"POST","requestBodyContent":"Quis quibusdam voluptatibus quaerat omnis non. Tempora cupiditate modi velit ea possimus illo a. Explicabo et repellat et hic quaerat cum.","responseBodyContent":"Response","responseContentType":"text","responseCode":200,"responseDelay":0,"alias":"Cute Alias"}';

        $simulationImporter = new SimulationImporter($simulationJson);

        $simulation = $simulationImporter->loadSimulation();

        $this->assertInstanceOf(Simulation::class, $simulation);
        $this->assertEquals('Cute Alias', $simulation->getAlias());
    }
}
