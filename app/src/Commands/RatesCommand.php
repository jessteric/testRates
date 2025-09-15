<?php

namespace App\Commands;

use App\Services\Ingestors\Ingestor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:rates:fetch')]
final class RatesCommand extends Command
{
    public function __construct(private readonly Ingestor $ingestor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ingestor->ingestAll();
        $output->writeln('<info>Rates ingested</info>');
        return Command::SUCCESS;
    }
}