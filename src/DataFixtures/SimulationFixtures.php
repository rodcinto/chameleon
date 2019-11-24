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
                ->setTtl(rand(10, 20))
                ->setResponseCode($this->getRandomFrom($this->responseCodes))
                ->setResponseBodyContent($this->randomText())
                ->setCreated($this->randomDate());

            $manager->persist($simulation);
        }

        $manager->flush();
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
}
