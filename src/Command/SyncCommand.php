<?php

namespace App\Command;

use App\Client\ClientInterface;
use App\Data\Configuration;
use App\Service\LocalService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    private ClientInterface $remote;
    private LocalService $local;
    private Configuration $configuration;

    public function __construct(ClientInterface $client, LocalService $local, Configuration $configuration)
    {
        parent::__construct();

        $this->remote = $client;
        $this->local = $local;
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
        $remote = $this->remote->getRemoteTranslations();

        $output->writeln('Loading local translations...');
        $local = $this->local->getLocalTranslations();




        $helper = $this->getHelper('question');
        $helper->ask($input, $output, new Question('Do you want to continue?'));



        return Command::SUCCESS;
    }
}