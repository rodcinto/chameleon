<?php

namespace App\Command;

use App\Service\SimulationCleanup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SimulationsCleanupCommand extends Command
{
    protected static $defaultName = 'simulations-cleanup';

    protected $simulationCleanup;

    /**
     * SimulationsCleanupCommand constructor.
     * @param SimulationCleanup $simulationCleanup
     */
    public function __construct(SimulationCleanup $simulationCleanup)
    {
        $this->simulationCleanup = $simulationCleanup;
        parent::__construct(static::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Cleans expired Simulations')
            ->setHelp('Deletes the simulations with TTS (Time To Live) expired.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $numberOfDeleted = $this->simulationCleanup->cleanup();
        } catch (\Exception $e) {
            $io->error('Error on SimulationCleanup: ' . $e->getMessage());
            return 10;
        }

        $io->success(sprintf('Simulations cleaned. %d removed.', $numberOfDeleted));

        return 0;
    }
}
