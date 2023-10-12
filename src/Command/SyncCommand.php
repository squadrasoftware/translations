<?php

namespace App\Command;

use App\Client\ClientInterface;
use App\Data\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    private ClientInterface $client;
    private Configuration $configuration;

    public function __construct(ClientInterface $client, Configuration $configuration)
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
        if (null === $this->configuration->setCurrentProject($input->getArgument('project'))) {
            $output->writeln('Project not found');

            return Command::FAILURE;
        }

        $output->writeln('Downloading remote translations...');
        $remote = $this->client->getRemoteTranslations();

        $output->writeln('Loading local translations...');



        return Command::SUCCESS;
    }
}