<?php

namespace App\Client;

use App\Data\Translation\Translations;
use Symfony\Component\Console\Output\OutputInterface;

interface ClientInterface
{
    public function pull(OutputInterface $output) : Translations;

}