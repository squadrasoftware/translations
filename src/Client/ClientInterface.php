<?php

namespace App\Client;

use App\Data\Translations;

interface ClientInterface
{
    public function getRemoteTranslations(): Translations;

}