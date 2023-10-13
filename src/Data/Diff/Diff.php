<?php

namespace App\Data\Diff;

use App\Data\Catalog\Catalog;
use App\Data\Translation\TranslationFile;

class Diff
{
    /**
     * Files present locally, but that are missing remotely.
     *
     * @var TranslationFile[]
     */
    private array $extraFiles = [];

    /**
     * Files missing remotely, but that are still present locally.
     *
     * @var TranslationFile[]
     */
    private array $missingFiles = [];

    private ?Catalog $catalog = null;

    public function getExtraFiles() : array
    {
        return $this->extraFiles;
    }

    public function addExtraFile(TranslationFile $file) : self
    {
        $this->extraFiles[] = $file;

        return $this;
    }

    public function getMissingFiles() : array
    {
        return $this->missingFiles;
    }

    public function addMissingFile(TranslationFile $file) : self
    {
        $this->missingFiles[] = $file;

        return $this;
    }

    public function getCatalog() : Catalog
    {
        return $this->catalog;
    }

    public function setCatalog(Catalog $catalog) : self
    {
        $this->catalog = $catalog;

        return $this;
    }
}