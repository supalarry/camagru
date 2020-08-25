<?php

require_once '/var/www/camagru/src/application/controller/base/IBaseController.php';

class Router
{
    private $request;

    private $supportedHttpMethods = array(
        "GET",
        "POST"
    );

    function __construct(IRequest $request)
    {
        $this->request = $request;
    }

    function addControllerRoutes(IBaseController $controller)
    {
        foreach ($controller->getRoutes() as $route) {
            $type = $route->getType();
            $path = $route->getPath();
            $method = $route->getMethod();
            $isProtected = $route->getIsProtected();
            $cantAccessWhileLoggedIn = $route->getCantAccessWhileLoggedIn();

            if (!in_array(strtoupper($type), $this->supportedHttpMethods)) {
                $this->invalidMethodHandler();
            }

            $this->{strtolower($type)}[$this->formatRoute($path)]['method'] = $method;
            $this->{strtolower($type)}[$this->formatRoute($path)]['isProtected'] = $isProtected;
            $this->{strtolower($type)}[$this->formatRoute($path)]['cantAccessWhileLoggedIn'] = $cantAccessWhileLoggedIn;
        }
    }

    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '') {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler()
    {
        header('Location: http://localhost:8098/catalog');
    }

    function resolve()
    {
        $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formatedRoute = strtok($this->formatRoute($this->request->requestUri), '?');
        $route = $this->extractParameters($formatedRoute, $methodDictionary);

        if (is_null($route)) {
            $this->defaultRequestHandler();
            return;
        }
        if ($route['isProtected']) {
            if (!isset($this->request->getSession()['id'])) {
                header('Location: http://localhost:8098/login');
                return;
            }
        }
        if ($route['cantAccessWhileLoggedIn']) {
            if (isset($this->request->getSession()['id'])) {
                header('Location: http://localhost:8098/catalog');
                return;
            }
        }
        echo call_user_func_array($route['method'], array($this->request));
    }

    function extractParameters($formatedRoute, $methodDictionary)
    {
        $formatedRouteParts = explode('/', $formatedRoute);
        $formatedRoutePartsCount = count($formatedRouteParts);
        $routes = array_keys($methodDictionary);

        foreach ($routes as $route) {
            $routeParts = explode('/', $route);
            if (count($routeParts) !== $formatedRoutePartsCount) {
                continue;
            }

            for ($x = 0; $x < $formatedRoutePartsCount; $x++) {
                if (empty($routeParts[$x])) {
                    continue;
                }
                if ($routeParts[$x][0] == '{' && $routeParts[$x][strlen($routeParts[$x]) - 1] == '}') {
                    $parameter = $routeParts[$x];
                    $parameter = trim($parameter, '{');
                    $parameter = trim($parameter, '}');
                    $this->request->setParameter($parameter, $formatedRouteParts[$x]);
                } elseif ($routeParts[$x] !== $formatedRouteParts[$x]) {
                    $this->request->clearParameters();
                    break;
                }
                if ($x === $formatedRoutePartsCount - 1) {
                    return $methodDictionary[$route];
                }
            }
        }
        return null;
    }
}
