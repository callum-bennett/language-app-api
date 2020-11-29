<?php

namespace App\Command;

use App\Service\XPService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetXPCommand extends Command
{
    protected static $defaultName = 'app:reset-xp';

    private $xpService;

    /**
     * ResetXPCommand constructor.
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
                ->addArgument("type", InputArgument::REQUIRED, "The XP type you wish to clear")
                ->setName("Reset XP points command.")
                ->setDescription("Reset XP points for all users.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $type = $input->getArgument("type");
            $this->xpService->clearXP($type);
            $output->writeln(sprintf("%s XP cleared successfully!", ucfirst($type)));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
