<?php

require_once 'IBaseController.php';

class BaseController implements IBaseController
{
    private $routes;

    function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    function getRoutes(): array
    {
        return $this->routes;
    }
}
