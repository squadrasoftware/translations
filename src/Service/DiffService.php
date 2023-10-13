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

        foreach ($this->computeFiles($diff, $local, $remote) as $commonFile) {
            $formatter = $this->formatResolver->getFormat(
                $this->configuration->getCurrentProject()->getFormat()
            );

            $catalog->addLocalKeys(
                $commonFile->getFilename(),
                FlatArrayService::flatten($formatter->unpack($local->getFile($commonFile)->getContent()))
            );

            $catalog->addRemoteKeys(
                $commonFile->getFilename(),
                FlatArrayService::flatten($formatter->unpack($remote->getFile($commonFile)->getContent()))
            );
        }

        return $diff;
    }

    public function getUpdatedTranslations(Catalog $catalog) : Translations
    {
        $translations = new Translations();
        $files = [];

        foreach ($catalog->getKeys() as $key) {
            foreach ($key->getFilenames() as $filename) {
                $files[$filename][$key->getKey()] = $key->getLocalValue($filename) ?? '';
            }
        }

        $formatter = $this->formatResolver->getFormat(
            $this->configuration->getCurrentProject()->getFormat()
        );

        foreach ($files as $filename => $keys) {
            $translations->addFile(
                new TranslationFile(
                    $filename,
                    $formatter->pack(FlatArrayService::unflatten($keys))
                )
            );
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

        return array_intersect($local->getFiles(), $remote->getFiles());
    }
}