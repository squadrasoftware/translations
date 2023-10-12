<?php

namespace App\Client;

use App\Data\Configuration;
use App\Data\TranslationFile;
use App\Data\Translations;
use CrowdinApiClient\Crowdin;
use CrowdinApiClient\Model\File;

class CrowdinClient implements ClientInterface
{
    private Configuration $configuration;
    private ?Crowdin $crowdin = null;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getRemoteTranslations(): Translations
    {
        // Debug
        if (file_exists('/tmp/remote.json')) {
            return Translations::fromArray(json_decode(file_get_contents('/tmp/remote.json'), true));
        }

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
                $export = $this->getClient()->translation->exportProjectTranslation($this->getProjectId(), [
                    'targetLanguageId' => $language,
                    'fileIds' => [$fileId],
                ]);

                $translations->addFile(
                    new TranslationFile(
                        strtr($file, $languages[$language]),
                        file_get_contents($export->getUrl())
                    )
                );
            }
        }

        // Debug
        file_put_contents('/tmp/remote.json', json_encode($translations->toArray(), JSON_PRETTY_PRINT));

        return $translations;
    }

    protected function getClient(): Crowdin
    {
        if (null === $this->crowdin) {
            $this->crowdin = new Crowdin([
                'access_token' => $this->configuration->getAccessToken(),
            ]);
        }

        return $this->crowdin;
    }

    private function getProjectId(): string
    {
        $project = $this->configuration->getCurrentProject();

        if (null === $project) {
            throw new \RuntimeException('Project not selected');
        }

        return $project->getId();
    }
}