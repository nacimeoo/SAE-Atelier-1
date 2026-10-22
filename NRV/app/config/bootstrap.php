<?php
declare(strict_types=1);

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/services.php');
$builder->addDefinitions(__DIR__ . '/api.php');
$c=$builder->build();
$app = AppFactory::createFromContainer($c);
$app->addBodyParsingMiddleware();
$app->add(function ($request, $handler) {
    return $handler->handle($request)->withHeader('Access-Control-Allow-Origin', '*');
});
$app->addErrorMiddleware(true, true, true);

$routes = require __DIR__ . '/routes.php';

return $routes($app);