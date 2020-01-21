<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SimulationAPIManager;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @var SimulationAPIManager
     */
    private $simulationAPIManager;

    /**
     * Constructor.
     *
     * @param SimulationAPIManager $simulationAPIManager
     */
    public function __construct(SimulationAPIManager $simulationAPIManager)
    {
        $this->simulationAPIManager = $simulationAPIManager;
    }

    /**
     * @param Request $request
     * @param string $category
     * @param string $token
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, string $category, string $token)
    {
        $this->simulationAPIManager->createRequestCriteria($request, $category, $token);

        return $this->simulationAPIManager->buildResponse();
    }
}
