<?php

namespace Project\Routing;

use Project\Exceptions\MethodNotAllowedHttpException;
use Project\Request\Request;

class Router
{
    private Request $request;
    private array $routes;
    private Route $anyInputTextRoute;

    /**
     * @param array $routes
     * @param Route $anyInputTextRoute
     */
    public function __construct(array $routes, Route $anyInputTextRoute)
    {
        $this->request = new Request();
        $this->routes = $routes;
        $this->anyInputTextRoute = $anyInputTextRoute;
    }

    /**
     * @return void
     * @throws MethodNotAllowedHttpException
     */
    public function routing(): void
    {
        $route = $this->findRoute() ?? $this->anyInputTextRoute;
        $controllerName = $route->controllerName;
        $actionName = $route->actionName;

        //TODO нужно будет переработать то как я отлавливаю исключения
        // возможно события происходят дважды
        if (count($route->allowedMethods) > 0 && !in_array($this->request->getData()->method, $route->allowedMethods)) {
            throw MethodNotAllowedHttpException::buildMessage(
                $this->request->getData()->method,
                $route->routeName,
                implode(", ", $route->allowedMethods)
            );
        }

        (new $controllerName())->$actionName($this->request->getData()->inputDataDto);
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