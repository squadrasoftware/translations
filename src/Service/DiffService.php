<?php

namespace App\Service;

use App\Data\Catalog\Catalog;
use App\Data\Configuration\Configuration;
use App\Data\Diff\Diff;
use App\Data\Translation\TranslationFile;
use App\Data\Translation\Translations;
use App\Format\FormatResolver;

class DiffService
{
    private Configuration $configuration;
    private FormatResolver $formatResolver;

    public function __construct(Configuration $configuration, FormatResolver $formatResolver)
    {
        $this->configuration = $configuration;
        $this->formatResolver = $formatResolver;
    }

    public function getCatalog(Translations $local, Translations $remote) : Diff
    {
        $diff = new Diff();
        $diff->setCatalog($catalog = new Catalog());

        foreach ($this->computeFiles($diff, $local, $remote) as $file) {
            $formatter = $this->formatResolver->getFormat(
                $this->configuration->getCurrentProject()->getFormat()
            );

            $catalog->addFile($file);

            $catalog->addLocalKeys(
                $file->getLanguage(),
                FlatArrayService::flatten($formatter->unpack($local->getFile($file)->getContent()))
            );

            $catalog->addRemoteKeys(
                $file->getLanguage(),
                FlatArrayService::flatten($formatter->unpack($remote->getFile($file)->getContent()))
            );
        }

        return $diff;
    }

    public function getUpdatedTranslations(Catalog $catalog) : Translations
    {
        $translations = new Translations();
        $files = [];

        foreach ($catalog->getKeys() as $key) {
            foreach ($key->getLanguages() as $language) {
                $files[$language][$key->getKey()] = $key->getLocalValue($language) ?? '';
            }
        }

        $formatter = $this->formatResolver->getFormat(
            $this->configuration->getCurrentProject()->getFormat()
        );

        foreach ($files as $language => $keys) {
            $config = $catalog->getFile($language);

            $file = new TranslationFile(
                $config->getFilename(),
                $formatter->pack(FlatArrayService::unflatten($keys)),
            );

            $file->setFileId($config->getFileId());
            $file->setLanguageId($config->getLanguageId());
            $file->setLanguage($language);

            $translations->addFile($file);
        }

        return $translations;
    }

    /**
     * @return TranslationFile[]
     */
    private function computeFiles(Diff $diff, Translations $local, Translations $remote) : array
    {
        foreach (array_diff($local->getFiles(), $remote->getFiles()) as $removedFile) {
            $diff->addMissingFile($removedFile);
        }

        foreach (array_diff($remote->getFiles(), $local->getFiles()) as $addedFile) {
            $diff->addExtraFile($addedFile);
        }

        return array_intersect($remote->getFiles(), $local->getFiles());
    }
}