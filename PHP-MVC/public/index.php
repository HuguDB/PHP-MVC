<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\IndexController;
use App\Controller\NewsletterController;
use App\Controller\ProductController;
use App\DependencyInjection\Container;
use App\Repository\ProductRepository;
use App\Routing\Exception\RouteNotFoundException;
use App\Routing\Route;
use App\Routing\Router;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

if (
    php_sapi_name() !== 'cli' && // Environnement d'exécution != console
    preg_match('/\.(ico|png|jpg|jpeg|css|js|gif)$/', $_SERVER['REQUEST_URI'])
) {
    return false;
}

// --- DATABASE CONNECTION ----------------------------------
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

$dbParams = [
    'driver'   => $_ENV['DB_DRIVER'],
    'user'     => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname'   => $_ENV['DB_NAME'],
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
];

$paths = [__DIR__ . '/../src/Entity'];
$isDevMode = $_ENV['APP_ENV'] === 'dev';

$config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
$connection = DriverManager::getConnection($dbParams, $config);
$entityManager = new EntityManager($connection, $config);

$productRepository = new ProductRepository($entityManager);
// -----------------------------------------------------------

// --- TWIG --------------------------------------------------
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment(
    $loader,
    [
        'cache' => __DIR__ . '/../var/cache',
        'debug' => $_ENV['APP_ENV'] === 'dev'
    ]
);
// -----------------------------------------------------------

// --- SERVICE CONTAINER -------------------------------------
$container = new Container();

$container
    ->set(Environment::class, $twig)
    ->set(EntityManager::class, $entityManager)
    ->set(ProductRepository::class, $productRepository);
// -----------------------------------------------------------

// --- ROUTER ------------------------------------------------
$router = new Router($container);
$router->registerRoutes();
// -----------------------------------------------------------

if (php_sapi_name() === 'cli') {
    return;
}

[
    'REQUEST_URI'    => $uri,
    'REQUEST_METHOD' => $httpMethod
] = $_SERVER;

try {
    echo $router->execute($uri, $httpMethod);
} catch (RouteNotFoundException) {
    http_response_code(404);
    echo "Page non trouvée";
} catch (Exception $e) {
    http_response_code(500);
    echo "Erreur interne, veuillez contacter l'administrateur";
}
