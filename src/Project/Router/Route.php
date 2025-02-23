<?php

namespace Project\Router;

class Route
{
    /**
     * @param string $routeName
     * @param string $controllerName
     * @param string $actionName
     */
    private function __construct(
        public string $routeName,
        public string $controllerName,
        public string $actionName
    )
    {
    }

    /**
     * @param string $routeName
     * @param array $controllerAndAction
     * @return Route
     */
    public static function setRoute(string $routeName, array $controllerAndAction): Route
    {
        [$controllerName, $actionName] = $controllerAndAction;

        return new self(
            routeName: $routeName,
            controllerName: $controllerName,
            actionName: $actionName
        );
    }
}