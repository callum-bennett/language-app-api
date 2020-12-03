<?php

namespace App\Command;

use App\Service\XPService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLeaderboardCommand extends Command
{
    protected static $defaultName = 'app:update-leaderboard';

    private $xpService;

    /**
     * UpdateLeaderboardCommand constructor.
     *
     * @param $xpService
     */
    public function __construct(XPService $xpService)
    {
        $this->xpService = $xpService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
                ->setName("Update leaderboard command.")
                ->setDescription("Update leaderboards.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->xpService->updateLeaderboard();
            $output->writeln("Leaderboards updated successfully!");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
