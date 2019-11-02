<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Simulation;

class SimulationFixtures extends Fixture
{
    public function __construct()
    {
        $this->faker = \Faker\Factory::create();

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

    private function getRandomFrom($anArray)
    {
        return $anArray[rand(0, count($anArray) - 1)];
    }

    protected function randomText():string
    {
        $emptyOrText = ['', $this->faker->text];
        return $this->getRandomFrom($emptyOrText);
    }

    protected function randomDate()
    {
        return $this->faker->dateTimeThisMonth('now');
    }
}
