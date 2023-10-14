<?php

namespace App\Client;

use App\Data\Translation\Translations;
use Symfony\Component\Console\Output\OutputInterface;

interface ClientInterface
{
    public function supports(string $client) : bool;

    public function pull(OutputInterface $output) : Translations;

    public function push(OutputInterface $output, Translations $translations) : void;
}