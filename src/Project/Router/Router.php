<?php

namespace Project\Router;

use Project\Controllers\Pets\MessageController;
use Project\Request\Request;

class Router
{
    public const string USE_BUTTONS = "use_buttons";
    //TODO файлы с роутами нужно будет переработать
    private Request $request;
    private array $routes;

    /**
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->request = new Request();
        $this->routes = $routes;
    }

    /**
     * @return void
     */
    public function routing(): void
    {
        $route = $this->findRoute() ?? $this->setRoute();
        $controllerName = $route->controllerName;
        $actionName = $route->actionName;

        (new $controllerName())->$actionName($this->request->getData()->inputDataDto);
    }

    /**
     * @return Route
     */
    private function setRoute(): Route
    {
        return Route::post(self::USE_BUTTONS, [MessageController::class, "useButtonsMessage"]);
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