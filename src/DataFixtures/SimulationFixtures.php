<?php

namespace App\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Simulation;
use Faker\Factory;

class SimulationFixtures extends Fixture
{
    private $faker;
    private $categories;
    private $tokens;
    private $HTTPVerbs;
    private $active;
    private $responseCodes;
    private $responseContentTypes;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $simulation = new Simulation();
            $simulation->setCategory($this->getRandomFrom($this->categories))
                ->setToken($this->getRandomFrom($this->tokens))
                ->setHttpVerb($this->getRandomFrom($this->HTTPVerbs))
                ->setActive($this->getRandomFrom($this->active))
                ->setRequestBodyContent($this->randomText())
                ->setTtl(0)
                ->setResponseCode($this->getRandomFrom($this->responseCodes))
                ->setResponseBodyContent($this->randomText())
                ->setCreated($this->randomDate());

            $manager->persist($simulation);
        }

        $identicalSimulations = $this->makeTwoSimulationsAndDifferentContents();
        $manager->persist($identicalSimulations[0]);
        $manager->persist($identicalSimulations[1]);

        $manager->flush();
    }

    /**
     * SimulationFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();

        $this->categories = [
            'products',
            'tags',
            'transactions',
        ];
        $this->tokens = [
            '',
            'list',
            'info',
            'request',
        ];
        $this->HTTPVerbs = [
            'GET',
            'POST',
            'PUT'
        ];
        $this->active = [
            true,
            false,
        ];
        $this->responseCodes = [
            null,
            200,
            300,
            400,
        ];
        $this->responseContentTypes = [
            '',
            'text/plain',
            'text/html',
        ];
    }

    /**
     * @param $anArray
     * @return mixed
     */
    private function getRandomFrom($anArray)
    {
        return $anArray[rand(0, count($anArray) - 1)];
    }

    /**
     * @return string
     */
    protected function randomText():string
    {
        $emptyOrText = ['', $this->faker->text];
        return $this->getRandomFrom($emptyOrText);
    }

    /**
     * @return DateTime
     */
    protected function randomDate()
    {
        return $this->faker->dateTimeThisMonth('now');
    }

    /**
     * @return Simulation[]
     */
    protected function makeTwoSimulationsAndDifferentContents()
    {
        $simulations = [];

        do {
            $simulation = new Simulation();

            $simulation->setCategory('contentTest')
                ->setToken('content')
                ->setHttpVerb('POST')
                ->setActive(true)
                ->setTtl(15)
                ->setResponseCode(200)
                ->setCreated($this->randomDate());

            $simulations[] = $simulation;
        } while (count($simulations) < 2);

        $simulations[0]->setRequestBodyContent('<request><text><title>Request with title</title></text></request>');
        $simulations[1]->setRequestBodyContent('<request><text><body>Request with body</body></text></request>');

        $simulations[0]->setResponseBodyContent('<response><text><title>Response with title</title></text></response>');
        $simulations[1]->setResponseBodyContent('<response><text><body>Response with body</body></text></response>');

        return $simulations;
    }
}
