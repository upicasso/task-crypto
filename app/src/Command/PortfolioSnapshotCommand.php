<?php

namespace App\Command;

use App\Service\PortfolioValuationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:portfolio:snapshot',
    description: 'Make a portfolio value snapshot',
)]
class PortfolioSnapshotCommand extends Command
{
    public function __construct(
        private readonly PortfolioValuationService $portfolioValuationService,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $portfolioValue = $this->portfolioValuationService->createNewPortfolioValuation();
        } catch (\Exception $e) {
            $this->logger->error('Portfolio snapshot failed', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf('Portfolio snapshot created: %s USDT at %s', $portfolioValue->getAmountUsdt()?->getAmount(), $portfolioValue->getCalculatedAt()?->format('Y-m-d H:i:s')));

        return Command::SUCCESS;
    }
}
