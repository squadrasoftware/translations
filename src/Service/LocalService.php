<?php

namespace App\Service;

use App\Data\Configuration\Configuration;
use App\Data\Translation\TranslationFile;
use App\Data\Translation\Translations;
use Symfony\Component\Console\Output\OutputInterface;

class LocalService
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getLocalTranslations(OutputInterface $output) : Translations
    {
        $pattern = sprintf(
            '%s/%s',
            trim($this->configuration->getCurrentProject()->getPath(), '/'),
            trim($this->configuration->getCurrentProject()->getPattern(), '/')
        );

        $translations = new Translations();
        foreach (glob($pattern) as $file) {
            $filename = trim(str_replace(
                $this->configuration->getCurrentProject()->getPath(),
                '',
                $file
            ), '/');

            $output->writeln(sprintf('Loading <info>%s</info>', $filename));

            $translations->addFile(
                new TranslationFile(
                    $filename,
                    file_get_contents($file)
                )
            );
        }

        return $translations;
    }

    private function getPath(TranslationFile $file) : string
    {
        return sprintf(
            '%s/%s',
            trim($this->configuration->getCurrentProject()->getPath(), '/'),
            trim($file->getFilename(), '/')
        );
    }

    public function addFile(OutputInterface $output, TranslationFile $file) : void
    {
        $path = $this->getPath($file);

        $output->writeln(sprintf('Creating <info>%s</info>', $path));

        file_put_contents($path, $file->getContent());
    }

    public function removeFile(OutputInterface $output, TranslationFile $file) : void
    {
        $path = $this->getPath($file);

        $output->writeln(sprintf('Removing <info>%s</info>', $path));

        unlink($path);
    }
}