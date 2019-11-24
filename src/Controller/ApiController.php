<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SimulationManager;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @var SimulationManager
     */
    private $simulationManager;

    /**
     * Constructor.
     *
     * @param SimulationManager $simulationManager
     */
    public function __construct(SimulationManager $simulationManager)
    {
        $this->simulationManager = $simulationManager;
    }

    /**
     * @param Request $request
     * @param string $category
     * @param string $token
     * @return Response
     */
    public function index(Request $request, string $category, string $token)
    {
        $this->simulationManager->createRequestCriteria($request, $category, $token);

        return $this->simulationManager->buildResponse();
    }
}
