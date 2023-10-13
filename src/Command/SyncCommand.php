<?php

namespace App\Command;

use App\Client\ClientInterface;
use App\Data\Configuration\Configuration;
use App\Service\DiffService;
use App\Service\LocalService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    private ClientInterface $remote;
    private LocalService $local;
    private DiffService $diff;
    private Configuration $configuration;

    public function __construct(ClientInterface $client,
        LocalService $local,
        DiffService $diff,
        Configuration $configuration)
    {
        parent::__construct();

        $this->remote = $client;
        $this->local = $local;
        $this->diff = $diff;
        $this->configuration = $configuration;
    }

    protected function configure() : void
    {
        $this
            ->setDescription('Sync translations')
            ->addArgument('project', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if (null === $this->configuration->setCurrentProject($input->getArgument('project'))) {
            $output->writeln('Project not found');

            return Command::FAILURE;
        }

        $output->writeln('Downloading remote translations...');
        $remote = $this->remote->getRemoteTranslations($output);

        $output->writeln('Loading local translations...');
        $local = $this->local->getLocalTranslations($output);

        $diff = $this->diff->compute($local, $remote);
        $helper = $this->getHelper('question');

        // -------------------------------------
        // Files missing remotely
        // -> they can be removed locally
        // -> they can be added remotely (won't be implemented)
        // -------------------------------------
        foreach ($diff->getMissingFiles() as $removedFile) {
            $answer = $helper->ask($input, $output, new ChoiceQuestion(
                sprintf('File <info>%s</info> exists locally, but is missing remotely, what do you want to do?', $removedFile->getFilename()), [
                    $l = 'Remove it locally',
                    $r = 'Create it remotely',
                    $s = 'Do nothing',
                ]
            ));

            if ($l === $answer) {
                $this->local->removeFile($output, $removedFile);
            } elseif ($r === $answer) {
                $output->writeln('Adding files remotely is not supported, you\'ll need to upload them by yourself.');
            }
        }

        // -------------------------------------
        // Extra files remotely
        // -> they can be added locally
        // -> they can be removed remotely (won't be implemented)
        // -------------------------------------
        foreach ($diff->getExtraFiles() as $removedFile) {
            $answer = $helper->ask($input, $output, new ChoiceQuestion(
                sprintf('File <info>%s</info> exists remotely, but is missing locally, what do you want to do?', $removedFile->getFilename()), [
                    $l = 'Create it locally',
                    $r = 'Remove it remotely',
                    $s = 'Do nothing',
                ]
            ));

            if ($l === $answer) {
                $this->local->addFile($output, $removedFile);
            } elseif ($r === $answer) {
                $output->writeln('Removing files remotely is not supported, you\'ll need to remove them by yourself.');
            }
        }

        return Command::SUCCESS;
    }
}