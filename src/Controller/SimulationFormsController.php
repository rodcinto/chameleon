<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Simulation;
use App\Form\SimulationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SimulationFormsController extends AbstractController
{
    private $repository;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Simulation::class);
    }

    /**
     * Index.
     *
     * @param integer $page
     * @return void
     */
    public function index(int $page)
    {
        $simulationsByPage = $this->repository->findAll($page);

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
     * @return void
     */
    private function populateForms($simulations)
    {
        $forms = [];
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
     * @param integer $id
     * @return void
     */
    public function fresh(int $id)
    {
        $simulationsByFresh = $this->repository->findFresh($id);
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
     * @return void
     */
    public function edit(Request $request, Simulation $simulation)
    {
        $form = $this->createForm(SimulationType::class, $simulation);

        if ($request->isMethod('POST')) {
            $data = $request->getContent();

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
}
