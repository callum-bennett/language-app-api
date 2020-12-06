<?php

namespace App\Command;

use App\Service\XPService;
use Carbon\Carbon;
use Carbon\Exceptions\Exception;
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
        $today = (Carbon::now())->format("d-m-y");
        $startOfWeek = (Carbon::now())->startOfWeek()->format("d-m-y");
        $startOfMonth = (Carbon::now())->startOfMonth()->format("d-m-y");

        try {
            $this->xpService->clearXP(XPService::DAILY);
            $output->writeln(sprintf("%s XP cleared successfully!", ucfirst(XPService::DAILY)));

            if ($today === $startOfWeek) {
                $this->xpService->clearXP(XPService::WEEKLY);
                $output->writeln(sprintf("%s XP cleared successfully!", ucfirst(XPService::WEEKLY)));
            }

            if ($today === $startOfMonth) {
                $this->xpService->clearXP(XPService::MONTHLY);
                $output->writeln(sprintf("%s XP cleared successfully!", ucfirst(XPService::MONTHLY)));
            }

        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
