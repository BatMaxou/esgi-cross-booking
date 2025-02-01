<?php

namespace App\Tests\Helper\Builder;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AccessBuilder
{
    private KernelBrowser $client;

    private string $method;

    private string $uri;

    public function __construct(KernelBrowser $client)
    {
        $this->client = $client;
    }

    public function build(): void
    {
        $this->client->request($this->method, $this->uri);
    }

    public function withMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function withUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }
}
