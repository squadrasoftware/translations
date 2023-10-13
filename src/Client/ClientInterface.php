<?php

namespace App\Client;

use App\Data\Translation\Translations;
use Symfony\Component\Console\Output\OutputInterface;

interface ClientInterface
{
    public function getRemoteTranslations(OutputInterface $output): Translations;

}