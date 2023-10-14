<?php

namespace App\Client;

class ClientResolver
{
    /**
     * @var ClientInterface[]
     */
    private array $clients;

    public function addClient(ClientInterface $client) : self
    {
        $this->clients[] = $client;

        return $this;
    }

    public function getClient(string $client) : ClientInterface
    {
        foreach ($this->clients as $supportedClient) {
            if ($supportedClient->supports($client)) {
                return $supportedClient;
            }
        }

        throw new \RuntimeException('Client not supported');
    }
}