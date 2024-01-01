<?php

namespace App\Routing;

use App\Routing\Attribute\Route as RouteAttribute;
use App\Routing\Exception\RouteNotFoundException;
use App\Middleware\AuthenticatedMiddleware; // import middleware
use App\Utils\Filesystem;
use Psr\Container\ContainerInterface;

class Router
{
    /** @var Route[] */
    private array $routes = [];

    private const CONTROLLERS_BASE_DIR = __DIR__ . "/../Controller/";
    private const CONTROLLERS_NAMESPACE_PREFIX = "App\\Controller\\";

    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function addRoute(Route $route): self
    {
        // TODO: Gestion doublon
        $this->routes[] = $route;
        return $this;
    }

    public function getRoute(string $uri, string $httpMethod): ?Route
    {
        foreach ($this->routes as $savedRoute) {
            if ($savedRoute->getUri() === $uri && $savedRoute->getHttpMethod() === $httpMethod) {
                return $savedRoute;
            }
        }

        return null;
    }

    public function registerRoutes(): void
    {

        $controllersFqcn = Filesystem::getFqcns(self::CONTROLLERS_BASE_DIR, self::CONTROLLERS_NAMESPACE_PREFIX);

        foreach ($controllersFqcn as $fqcn) {
            $classInfos = new \ReflectionClass($fqcn);

            if ($classInfos->isAbstract()) {
                continue;
            }

            /** @var \ReflectionMethod[] */
            $methods = $classInfos->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                if ($method->isConstructor()) {
                    continue;
                }

                $attributes = $method->getAttributes(RouteAttribute::class);

                if (!empty($attributes)) {
                    /** @var \ReflectionAttribute */
                    $routeAttribute = $attributes[0];
                    /** @var RouteAttribute */
                    $route = $routeAttribute->newInstance();
                    $this->addRoute(new Route(
                        $route->getUri(),
                        $route->getName(),
                        $route->getHttpMethod(),
                        $fqcn,
                        $method->getName()
                    ));
                }
            }
        }

        // Ajout de la nouvelle route de déconnexion
        $this->addRoute(new Route('/logout', 'logout', 'GET', IndexController::class, 'logout'));
    }


    public function execute(string $uri, string $httpMethod): string
    {
        $route = $this->getRoute($uri, $httpMethod);

        if ($route === null) {
            throw new RouteNotFoundException();
        }

        $this->executeMiddlewares();

        // Constructeur
        $controllerClass = $route->getControllerClass();
        $constructorParams = $this->getMethodParams($controllerClass . '::__construct');
        $controllerInstance = new $controllerClass(...$constructorParams);

        // Contrôleur
        $method = $route->getController();
        $controllerParams = $this->getMethodParams($controllerClass . '::' . $method);
        return $controllerInstance->$method(...$controllerParams);
    }

    private function executeMiddlewares(): void
    {
        $authenticatedMiddleware = new AuthenticatedMiddleware($this->container);
        $authenticatedMiddleware->handle();
    }

    private function getMethodParams(string $method): array
    {
        $methodInfos = new \ReflectionMethod($method);
        $methodParameters = $methodInfos->getParameters();

        $params = [];
        foreach ($methodParameters as $param) {
            $paramType = $param->getType();
            $paramTypeFQCN = $paramType->getName();
            $params[] = $this->container->get($paramTypeFQCN);
        }

        return $params;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
