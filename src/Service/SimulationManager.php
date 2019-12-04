<?php
namespace App\Service;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Simulation;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SimulationManager
{
    const PROXIMITY_PERCENTAGE = 90;
    const NEW_REQUEST_MESSAGE = 'New request saved in the database.';
    const REQUEST_FOUND_NO_CONTENT = 'Request found, but no response set yet.';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $requestCriteria;

    /**
     * @var string
     */
    private $requestBody;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function buildResponse()
    {
        $this->logger->debug('buildResponse');
        $simulation = $this->findSimulationByRequest();

        $response = new Response();

        if (empty($simulation)) {
            $this->persistNewSimulation();
            return $response->setContent(self::NEW_REQUEST_MESSAGE);
        }

        if (count($simulation) > 0) {
            $this->logger->info('Found many candidates:', [print_r($simulation, true)]);
            $simulation = $this->filterBestResult($simulation);
        }

        if ($simulation instanceof Simulation && $simulation->getResponseBodyContent()) {
            return $this->modifyResponseForSimulation($response, $simulation);
        }

        return $response->setContent(self::REQUEST_FOUND_NO_CONTENT);
    }

    /**
     * @return mixed
     */
    private function findSimulationByRequest()
    {
        $repo = $this->entityManager->getRepository(Simulation::class);

        return $repo->findRequestBy($this->requestCriteria);
    }

    /**
     * @param Request $request
     * @param $category
     * @param $token
     * @return void
     */
    public function createRequestCriteria(Request $request, $category, $token)
    {
        $this->requestCriteria = [
            'category'  => $category,
            'token'     => $token,
        ];

        if (!empty($request->getMethod())) {
            $this->requestCriteria['http_verb'] = $request->getMethod();
        }

        if (!empty($request->getContent())) {
            $this->requestBody = trim($request->getContent());
            $this->logger->info('Search request content', [$this->requestBody]);
        }

        $parameters = $this->formatRequestParams($request->request->all());
        if ('' !== $parameters) {
            $this->requestCriteria['parameters'] = $parameters;
        }

        $queryString = $this->formatQueryString($request->query->all());
        if ('' !== $queryString) {
            $this->requestCriteria['query_string'] = $queryString;
        }
    }

    /**
     * @param $parameters[]
     * @return string
     */
    private function formatRequestParams($parameters)
    {
        if (!empty($parameters)) {
            return var_export($parameters, true);
        }

        return '';
    }

    /**
     * @param $parameters[]
     * @return string
     */
    private function formatQueryString($parameters)
    {
        if (empty($parameters)) {
            return '';
        }

        $parsed = [];
        foreach ($parameters as $paramKey => $paramValue) {
            $parsed[] = $paramKey . '=' . $paramValue;
        }

        return implode('&', $parsed);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function persistNewSimulation()
    {
        $simulation = new Simulation();
        if (isset($this->requestCriteria['category'])) {
            $simulation->setCategory($this->requestCriteria['category']);
        }
        if (isset($this->requestCriteria['token'])) {
            $simulation->setToken($this->requestCriteria['token']);
        }
        if (isset($this->requestCriteria['http_verb'])) {
            $simulation->setHttpVerb($this->requestCriteria['http_verb']);
        }
        if (isset($this->requestCriteria['parameters'])) {
            $simulation->setParameters($this->requestCriteria['parameters']);
        }
        if (isset($this->requestCriteria['query_string'])) {
            $simulation->setQueryString($this->requestCriteria['query_string']);
        }
        if (isset($this->requestCriteria['request_body_content'])) {
            $simulation->setRequestBodyContent($this->requestCriteria['request_body_content']);
        }

        $simulation->setActive(true);
        $simulation->setTtl(15);
        $simulation->setCreated(new DateTime('now'));

        $this->entityManager->persist($simulation);

        $this->entityManager->flush();
    }

    /**
     * @param Response $response
     * @param Simulation $simulation
     * @return Response
     */
    private function modifyResponseForSimulation(Response $response, Simulation $simulation)
    {
        $response->setContent($simulation->getResponseBodyContent());

        if ($simulation->getResponseContentType()) {
            $response->headers->set('Content-Type', $simulation->getResponseContentType());
        }

        if ($simulation->getResponseCode()) {
            $response->setStatusCode((int) $simulation->getResponseCode());
        }

        if ($simulation->getResponseDelay()) {
            sleep($simulation->getResponseDelay());
        }

        return $response;
    }

    /**
     * @param array $simulations
     * @return Simulation
     */
    private function filterBestResult(array $simulations)
    {
        $filtered = array_filter($simulations, function(Simulation $simCandidate) {
            $similarity = 0;

            similar_text($simCandidate->getRequestBodyContent(), $this->requestBody, $similarity);

            $this->logger->info('Similarity: ', [
                'request' => $this->requestBody,
                'candidate' => $simCandidate->getRequestBodyContent(),
                'similarity' => $similarity]);

            return self::PROXIMITY_PERCENTAGE <= $similarity;
        });

        if (is_array($filtered) && count($filtered) > 0) {
            return end($filtered);
        }

        return $filtered;
    }
}
