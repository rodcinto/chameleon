<?php
namespace App\Tests;

use DateTime;
use App\Entity\Simulation;
use PHPUnit\Framework\TestCase;
use App\Service\SimulationExporter;

class SimulationExporterTest extends TestCase
{
    public function testExport()
    {
        $simulation = new Simulation();
        $simulation
            ->setAlias('Simulation Exported')
            ->setCategory('Cat')
            ->setToken('Token')
            ->setHttpVerb('POST')
            ->setParameters("array ( 'email' => 'my@email.com',  'a' => '2',)")
            ->setQueryString('somevar=hello')
            ->setRequestBodyContent('Request Body Content')
            ->setResponseContentType('text')
            ->setResponseCode(200)
            ->setResponseDelay(3)
            ->setTtl(30)
            ->setActive(true)
            ->setCreated(new DateTime('2020-01-01 13:00:00'))
            ->setUpdated(new DateTime('2020-01-02 18:00:00'));

        $simulationExporter = new SimulationExporter($simulation);

        $exportedJson = json_decode($simulationExporter->exportToJson());

        $expectedData = (object) [
            'category' => 'Cat',
            'token' => 'Token',
            'httpVerb' => 'POST',
            'parameters' => 'array ( \'email\' => \'my@email.com\',  \'a\' => \'2\',)',
            'requestBodyContent' => 'Request Body Content',
            'responseBodyContent' => NULL,
            'responseContentType' => 'text',
            'responseCode' => 200,
            'responseDelay' => 3,
            'queryString' => 'somevar=hello',
            'alias' => 'Simulation Exported',
        ];
        
        $this->assertEquals($expectedData, $exportedJson);
    }
}
