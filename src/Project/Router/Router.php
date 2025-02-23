<?php

namespace Project\Router;

use Project\Request\Request;
use Project\Traits\SingletonTrait;

class Router
{
    //TODO файлы с роутами нужно будет переработать

    use SingletonTrait;

    private Request $request;
    private array $routes;

    private function __construct()
    {
        $this->request = new Request();
        $this->routes = require_once __DIR__ . "/../../routes.php";
    }

    /**
     * @return void
     */
    public function routing(): void
    {
        $route = $this->findRoute();
        $controllerName = $route->controllerName;
        $actionName = $route->actionName;

        (new $controllerName())->$actionName($this->request->getData()->inputDataDto);
    }

    /**
     * @return Router
     */
    public static function run(): Router
    {
        return static::getInstance();
    }

    /**
     * @return Route|null
     */
    private function findRoute(): ?Route
    {
        $requestRoute = $this->request->getData()->route;

        foreach ($this->routes as $route) {
            if ($requestRoute === $route->routeName) {
                return $route;
            }
        }

        return null;
    }
}