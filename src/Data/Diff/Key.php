<?php

namespace App\Data\Diff;

use App\Data\Translation\TranslationFile;

class Key
{
    private TranslationFile $file;
    private string $key;
    private string $value;
}