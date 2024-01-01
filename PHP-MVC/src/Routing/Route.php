<?php

namespace App\Routing;

class Route extends AbstractRoute
{
    public function __construct(
        string $uri,
        string $name,
        string $httpMethod,
        private string $controllerClass,
        private string $controller
    ) {
        parent::__construct($uri, $name, $httpMethod);
    }

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function getController(): string
    {
        return $this->controller;
    }
}
