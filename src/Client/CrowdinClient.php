<?php

namespace App\Client;

use App\Data\Configuration\Configuration;
use App\Data\Translation\TranslationFile;
use App\Data\Translation\Translations;
use CrowdinApiClient\Crowdin;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * For individual strings:
 * https://developer.crowdin.com/api/v2/#operation/api.projects.strings.getMany
 */
class CrowdinClient implements ClientInterface
{
    private Configuration $configuration;
    private ?Crowdin $crowdin = null;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function pull(OutputInterface $output) : Translations
    {
        // Debug
        //        if (file_exists('/tmp/remote.json')) {
        //            return Translations::fromArray(json_decode(file_get_contents('/tmp/remote.json'), true));
        //        }

        $output->writeln('Reading project information...');
        $project = $this->getClient()->project->get($this->getProjectId());

        // Getting languages, and convert their configuration array keys into placeholders
        // https://developer.crowdin.com/configuration-file/#placeholders
        $languages = [];
        $languages[$project->getSourceLanguageId()] = $project->getDataProperty('sourceLanguage');
        foreach ($project->getDataProperty('targetLanguages') as $targetLanguage) {
            $languages[$targetLanguage['id']] = $targetLanguage;
        }
        foreach ($languages as $id => $language) {
            $languages[$id] = array_combine(
                array_map(function ($key) {
                    return '%'.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key)).'%';
                }, array_keys($language)),
                $language
            );
        }

        // Fetching all files to get their export pattern
        $files = [];
        foreach ($this->getClient()->file->list($this->getProjectId()) as $file) {
            $files[$file->getId()] = $file->getExportOptions()['exportPattern'];
        }

        // Downloading all translation keys for each file and language
        $translations = new Translations();
        foreach (array_keys($languages) as $language) {
            foreach ($files as $fileId => $file) {
                $path = trim(strtr($file, $languages[$language]), '/');

                $output->writeln(sprintf('Downloading <info>%s</info> (%s)', $path, $languages[$language]['%name%']));

                $export = $this->getClient()->translation->exportProjectTranslation($this->getProjectId(), [
                    'targetLanguageId' => $language,
                    'fileIds' => [$fileId],
                ]);

                $object = new TranslationFile($path, file_get_contents($export->getUrl()));
                $object->setFileId($fileId);
                $object->setLanguageId($language);
                $object->setLanguage($languages[$language]['%name%']);
                $object->setIsMainLanguage($language === $project->getSourceLanguageId());

                $translations->addFile($object);
            }
        }

        // Debug
        // file_put_contents('/tmp/remote.json', json_encode($translations->toArray(), JSON_PRETTY_PRINT));

        return $translations;
    }

    public function push(OutputInterface $output, Translations $translations) : void
    {
        foreach ($translations->getFiles() as $file) {
            $output->writeln(sprintf('Uploading <info>%s</info> (%s)', $file->getFilename(), $file->getLanguage()));

            $storageId = $this->getClient()->storage->create(new \SplFileInfo($this->getPath($file)))->getId();

            if ($file->isMainLanguage()) {
                $this->getClient()->file->update($this->getProjectId(), $file->getFileId(), [
                    'storageId' => $storageId,
                ]);
            } else {
                $this->getClient()->translation->uploadTranslations($this->getProjectId(), $file->getLanguageId(), [
                    'storageId' => $storageId,
                    'fileId' => $file->getFileId(),
                ]);
            }
        }
    }

    protected function getClient() : Crowdin
    {
        if (null === $this->crowdin) {
            $this->crowdin = new Crowdin([
                'access_token' => $this->configuration->getAccessToken(),
            ]);
        }

        return $this->crowdin;
    }

    private function getProjectId() : string
    {
        $project = $this->configuration->getCurrentProject();

        if (null === $project) {
            throw new \RuntimeException('Project not selected');
        }

        return $project->getId();
    }

    private function getPath(TranslationFile $file) : string
    {
        return sprintf(
            '%s/%s',
            trim($this->configuration->getCurrentProject()->getPath(), '/'),
            trim($file->getFilename(), '/')
        );
    }
}