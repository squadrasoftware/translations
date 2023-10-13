<?php

namespace App\Data\Diff;

use App\Data\Translation\TranslationFile;

class Value
{
    private TranslationFile $file;
    private string $key;
    private string $remoteValue;
    private string $localValue;
}