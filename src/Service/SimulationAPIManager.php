<?php
namespace App\Service;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Simulation;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SimulationAPIManager
{
    const REQUEST_BODY_PROXIMITY_FACTOR = 90;
    const REQUEST_PARAMETERS_PROXIMITY_FACTOR = 90;
    const NEW_REQUEST_MESSAGE = 'New request saved. It will be loaded next time.';
    const REQUEST_FOUND_NO_CONTENT = 'Request found, but no response set yet.';
    const DEFAULT_TTL = 15;

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
    private $requestParameters;

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
        $simulations = $this->findSimulationByRequest();

        $response = new Response();

        if (empty($simulations)) {
            $this->logger->info('Nothing found. Save a new simulation.');
            $this->persistNewSimulation();
            return $response->setContent(self::NEW_REQUEST_MESSAGE);
        }

        if (count($simulations) === 1) {
            $this->logger->info('One Result', [$simulations]);
            $simulation = end($simulations);
        } else {
            $this->logger->info('Found multiple candidates:', [$simulations]);
            $simulation = $this->filterBestResult($simulations);
        }

        if (!$simulation) {
            $this->logger->info('No similar simulation found after filter. Save a new one.');
            $this->persistNewSimulation();
            return $response->setContent(self::NEW_REQUEST_MESSAGE);
        }

        if ($simulation instanceof Simulation && !empty($simulation->getResponseBodyContent())) {
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

        $this->logger->info('Request Criteria', [$this->requestCriteria]);

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
            'active'    => true,
        ];

        $parameters = $this->formatRequestParams($request->request->all());
        if ('' !== $parameters) {
            $this->logger->info('Search request parameters', [$parameters]);
            $this->requestParameters = $this->minifyText($parameters);
        }

        $requestBody = $request->getContent();
        if (!empty($requestBody)) {
            $this->logger->info('Search request content', [$requestBody]);
            $this->requestBody = $this->minifyText($requestBody);
        }

        $requestMethod = $request->getMethod();
        if (!empty($requestMethod)) {
            $this->requestCriteria['http_verb'] = $requestMethod;
            $this->logger->info('Search request method', [$requestMethod]);
        }

        $queryString = $this->formatQueryString($request->query->all());
        if ('' !== $queryString) {
            $this->requestCriteria['query_string'] = $queryString;
            $this->logger->info('Search request query_string', [$queryString]);
        }
    }

    /**
     * @param $parameters[]
     * @return string
     */
    private function formatRequestParams($parameters)
    {
        if (!empty($parameters)) {
            $exportedParameters = json_encode($parameters);
            return $this->minifyText($exportedParameters);
        }

        return '';
    }

    /**
     * @param string $text
     * @return string
     */
    private function minifyText(string $text):string
    {
        return trim(str_replace(["\r", "\n"], '', $text));
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
        if (isset($this->requestCriteria['query_string'])) {
            $simulation->setQueryString($this->requestCriteria['query_string']);
        }
        if (isset($this->requestParameters)) {
            $simulation->setParameters($this->requestParameters);
        }
        if (isset($this->requestBody)) {
            $simulation->setRequestBodyContent($this->requestBody);
        }

        $simulation->setActive(true);
        $simulation->setTtl(self::DEFAULT_TTL);
        $simulation->setResponseDelay(0);
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
     * @return Simulation|null
     */
    private function filterBestResult(array $simulations)
    {
        $filtered = array_filter($simulations, [$this, 'filterByProximity']);

        $this->logger->info('After Filter', [$filtered]);

        if (is_array($filtered) && count($filtered) > 0) {
            return end($filtered);
        }

        return null;
    }

    /**
     * @param Simulation $simCandidate
     * @return bool
     */
    private function filterByProximity(Simulation $simCandidate)
    {
        $requestParametersSimilarity = $this->compareTexts(
            $this->minifyText($simCandidate->getParameters() ?: ''),
            $this->requestParameters ?: '',
            sprintf('Request Parameters Similarity (%d)', $simCandidate->getId())
        );

        $bodyContentSimilarity = $this->compareTexts(
            $this->minifyText($simCandidate->getRequestBodyContent() ?: ''),
            $this->requestBody ?: '',
            sprintf('Request Body Similarity (%d)', $simCandidate->getId())
        );

        return  self::REQUEST_PARAMETERS_PROXIMITY_FACTOR <= $requestParametersSimilarity
                && self::REQUEST_BODY_PROXIMITY_FACTOR <= $bodyContentSimilarity;
    }

    /**
     * @param string $candidateText
     * @param string $requestText
     * @param string $logMessage
     * @return float
     */
    private function compareTexts(
        string $candidateText,
        string $requestText,
        string $logMessage
        ):float
    {
        if ('' === $candidateText && '' === $requestText) {
            $this->logger->info($logMessage . ' - Empty values. Similarity 100%');
            return 100;
        }

        $similarity = 0;

        similar_text(
            $requestText,
            $candidateText,
            $similarity
        );

        $this->logger->info($logMessage, [
            'Requested Content' => $requestText,
            'Candidate Found' => $candidateText,
            'Similarity' => $similarity
        ]);

        return $similarity;
    }
}
