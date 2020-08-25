<?php

require_once './src/application/router/Request.php';
require_once './src/application/router/Router.php';
require_once '/var/www/camagru/config/setup.php';

$router = new Router(new Request);

foreach (glob('./src/application/controller/*.php') as $filename) {
    require_once $filename;
    $pathParts = explode('/', $filename);
    $classFile = end($pathParts);
    $class = explode('.', $classFile)[0];
    $router->addControllerRoutes(new $class);
}

$router->resolve();
