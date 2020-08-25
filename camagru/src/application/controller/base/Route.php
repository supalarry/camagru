<?php

require_once 'IRoute.php';
require_once '/var/www/camagru/templates/views/IView.php';

class Route implements IRoute
{
    private $type;

    private $path;

    private $method;

    private $isProtected;

    private $cantAccessWhileLoggedIn;

    function __construct(string $type, string $path, $method, bool $protected, bool $cantAccessWhileLoggedIn)
    {
        $this->type = $type;
        $this->path = $path;
        $this->method = $method;
        $this->isProtected = $protected;
        $this->cantAccessWhileLoggedIn = $cantAccessWhileLoggedIn;
    }

    function getType(): string
    {
        return $this->type;
    }

    function getPath(): string
    {
        return $this->path;
    }

    function getMethod()
    {
        return $this->method;
    }

    function getIsProtected()
    {
        return $this->isProtected;
    }

    function getCantAccessWhileLoggedIn()
    {
        return $this->cantAccessWhileLoggedIn;
    }
}
