<?php

namespace App\Command;

use App\Client\ClientInterface;
use App\Data\Configuration\Configuration;
use App\Service\DiffService;
use App\Service\LocalService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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

        // -------------------------------------
        // Downloading files and computing diff
        // -------------------------------------

        $output->writeln('Downloading remote translations...');
        $remote = $this->remote->getRemoteTranslations($output);

        $output->writeln('Loading local translations...');
        $local = $this->local->getLocalTranslations($output);

        $diff = $this->diff->getCatalog($local, $remote);
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

            $output->writeln('');
        }

        // -------------------------------------
        // Extra files remotely
        // -> they can be added locally
        // -> they can be removed remotely (won't be implemented)
        // -------------------------------------
        foreach ($diff->getExtraFiles() as $removedFile) {
            $answer = $helper->ask($input, $output, new ChoiceQuestion(
                sprintf('File <info>%s</info> exists remotely, but is missing locally, what do you want to do?', $removedFile->getFilename()), [
                    $l = 'local' => 'Create it locally',
                    $r = 'remote' => 'Remove it remotely',
                    'skip' => 'Do nothing',
                ]
            ));

            if ($l === $answer) {
                $this->local->addFile($output, $removedFile);
            } elseif ($r === $answer) {
                $output->writeln('Removing files remotely is not supported, you\'ll need to remove them by yourself.');
            }

            $output->writeln('');
        }

        // -------------------------------------
        // Translation keys change between local & remote
        // -> we build the local file based on the changes
        // -------------------------------------
        foreach ($diff->getCatalog()->getKeys() as $key) {
            if ($key->hasNoChanges()) {
                continue;
            }

            $output->writeln(sprintf('Key <info>%s</info> has changes', $key->getKey()));
            $output->writeln('');

            $table = new Table($output);
            $table->setHeaders(['File', 'Local', 'Remote']);
            $table->setColumnMaxWidth(1, 50);
            $table->setColumnMaxWidth(2, 50);

            foreach ($key->getFilenames() as $locale) {
                $table->addRow([
                    basename($locale),
                    $key->getLocalValue($locale),
                    $key->getRemoteValue($locale),
                ]);
            }

            $table->render();
            $output->writeln('');

            $answer = $helper->ask($input, $output, new ChoiceQuestion(
                'Which update should be kept?', [
                    $l = 'local' => 'The local file',
                    $r = 'remote' => 'The remote file',
                    $s = 'detail' => 'Let me choose locale by locale',
                ]
            ));

            if ($l === $answer) {
                $key->keepLocal();
            } elseif ($r === $answer) {
                $key->keepRemote();
            } elseif ($s === $answer) {
                foreach ($key->getFilenames() as $locale) {
                    $table = new Table($output);
                    $table->setHeaders(['File', 'Local', 'Remote']);
                    $table->setColumnMaxWidth(1, 50);
                    $table->setColumnMaxWidth(2, 50);
                    $table->setHeaderTitle($key);

                    $table->addRow([
                        basename($locale),
                        $key->getLocalValue($locale),
                        $key->getRemoteValue($locale),
                    ]);

                    $output->writeln('');
                    $table->render();
                    $output->writeln('');

                    $answer = $helper->ask($input, $output, new ChoiceQuestion(
                        'Which value should be kept?', [
                            'local' => $l = 'The local value',
                            'remote' => $r = 'The remote value',
                        ]
                    ));

                    if ($l === $answer) {
                        $key->keepLocalForFilename($locale);
                    } elseif ($r === $answer) {
                        $key->keepRemoteForFilename($locale);
                    }
                }
            }

            $output->writeln('');
        }

        if (!$diff->getCatalog()->hasUpdates()) {
            $output->writeln('No changes to apply');

            return Command::SUCCESS;
        }

        $output->writeln('Writing local translations...');
        $newFiles = $this->diff->getUpdatedTranslations($diff->getCatalog());
        $this->local->writeNewFiles($output, $newFiles);

        // -------------------------------------
        // Uploading files
        // -------------------------------------

        return Command::SUCCESS;
    }
}