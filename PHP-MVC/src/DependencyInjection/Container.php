<?php

namespace App\DependencyInjection;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException("Unable to get service in container : Service $id not found");
        }

        return $this->services[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->services);
    }

    public function set(string $id, object $instance): self
    {
        $this->services[$id] = $instance;

        return $this;
    }
}