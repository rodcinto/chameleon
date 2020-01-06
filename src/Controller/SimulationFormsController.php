<?php

namespace App\Controller;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Simulation;
use App\Form\SimulationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SimulationFormsController extends AbstractController
{
    private $repository;

    private $logger;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->repository = $entityManager->getRepository(Simulation::class);
        $this->logger = $logger;
    }

    /**
     * Index.
     *
     * @param integer $page
     * @return Response
     */
    public function index(Request $request, int $page)
    {
        $simulationsByPage = $this->repository->findAllByPage($page, $this->parseSearchTermsFromRequest($request));

        $forms = $this->populateForms($simulationsByPage);

        return $this->render('simulation_forms/index.html.twig', [
            'controller_name' => 'SimulationFormsController',
            'forms' => $forms,
            'is_page' => true,
        ]);
    }

    /**
     * Populate Forms.
     *
     * @param $simulations
     * @return array|void
     */
    private function populateForms($simulations)
    {
        $forms = [];

        /** @var Simulation $simulation */
        foreach ($simulations as $simulation) {
            $form = $this->createForm(SimulationType::class, $simulation);
            $forms[] = [
                'simulation_id' => $simulation->getId(),
                'simulation_created' => $simulation->getCreated(),
                'simulation_updated' => $simulation->getUpdated(),
                'form' => $form->createView(),
            ];
        }

        return $forms;
    }

    /**
     * Fresh forms.
     *
     * @param integer $lastTime
     * @return Response
     */
    public function fresh(int $lastTime)
    {
        $simulationsByFresh = $this->repository->findFresh($lastTime);
        $forms = $this->populateForms($simulationsByFresh);

        return $this->render('simulation_forms/index.html.twig', [
            'controller_name' => 'SimulationFormsController',
            'forms' => $forms
        ]);
    }

    /**
     * Edit Simulation.
     *
     * @param Request $request
     * @param Simulation $simulation
     * @return Response
     */
    public function edit(Request $request, Simulation $simulation)
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(SimulationType::class, $simulation);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($form->getData());
                $entityManager->flush();

                return new Response(200);
            }
        }

        return new Response(400);
    }

    /**
     * @param Request $request
     * @param Simulation $simulation
     *
     * @return Response
     */
    public function delete(Request $request, Simulation $simulation)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->remove($simulation);
                $entityManager->flush();

                return new Response('', Response::HTTP_OK);
            } catch (Exception $e) {
                $this->logger->error('Error deleting Simulation', [$e->getMessage()]);
            }
        }

        return new Response('', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function parseSearchTermsFromRequest(Request $request)
    {
        return [
            'alias'    => $request->query->get('alias'),
            'token'    => $request->query->get('token'),
            'category' => $request->query->get('category'),
        ];
    }
}
