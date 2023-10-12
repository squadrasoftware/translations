<?php

namespace App\Service;

use App\Data\Configuration;
use App\Data\TranslationFile;
use App\Data\Translations;

class LocalService
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getLocalTranslations(): Translations
    {
        $pattern = sprintf(
            '%s/%s',
            trim($this->configuration->getCurrentProject()->getPath(), '/'),
            trim($this->configuration->getCurrentProject()->getPattern(), '/')
        );

        $translations = new Translations();
        foreach (glob($pattern) as $file) {
            $translations->addFile(
                new TranslationFile(
                    trim(str_replace(
                        $this->configuration->getCurrentProject()->getPath(),
                        '',
                        $file
                    ), '/'),
                    file_get_contents($file)
                )
            );
        }

        return $translations;
    }
}