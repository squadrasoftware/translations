<?php

namespace App\Command;

use App\Client\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    private ClientInterface $client;
    private array $configuration;

    public function __construct(ClientInterface $client, array $configuration)
    {
        parent::__construct();

        $this->client = $client;
        $this->configuration = $configuration;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Sync translations')
            ->addArgument('project', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Syncing translations...');

        return Command::SUCCESS;
    }
}