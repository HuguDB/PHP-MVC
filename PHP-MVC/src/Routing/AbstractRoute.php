<?php

namespace App\Routing;

abstract class AbstractRoute
{
    public function __construct(
        protected string $uri,
        protected string $name,
        protected string $httpMethod = 'GET'
    ) {
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHttpMethod()
    {
        return $this->httpMethod;
    }
}
