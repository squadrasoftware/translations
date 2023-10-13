<?php

namespace App\Service;

use App\Data\Diff\Diff;
use App\Data\Translation\Translations;

class DiffService
{
    public function compute(Translations $local, Translations $remote) : Diff
    {
        $diff = new Diff();

        $commonFiles = $this->computeFiles($diff, $local, $remote);

        return $diff;
    }

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