<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;

class AuthenticatedMiddleware
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(): void
    {
    }
}
