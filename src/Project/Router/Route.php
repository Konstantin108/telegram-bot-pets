<?php

namespace Project\Router;

class Route
{
    private const string POST_METHOD = "post";
    private const string GET_METHOD = "get";

    /**
     * @param string $routeName
     * @param string $controllerName
     * @param string $actionName
     * @param string $method
     */
    private function __construct(
        public string $routeName,
        public string $controllerName,
        public string $actionName,
        public string $method,
    )
    {
    }

    /**
     * @param string $routeName
     * @param array $controllerAndAction
     * @return Route
     */
    public static function post(
        string $routeName,
        array  $controllerAndAction,
    ): Route
    {
        return self::setRoute($routeName, $controllerAndAction, self::POST_METHOD);
    }

    /**
     * @param string $routeName
     * @param array $controllerAndAction
     * @return Route
     */
    public static function get(
        string $routeName,
        array  $controllerAndAction,
    ): Route
    {
        return self::setRoute($routeName, $controllerAndAction, self::GET_METHOD);
    }

    /**
     * @param string $routeName
     * @param array $controllerAndAction
     * @param string $method
     * @return Route
     */
    private static function setRoute(
        string $routeName,
        array  $controllerAndAction,
        string $method
    ): Route
    {
        [$controllerName, $actionName] = $controllerAndAction;

        return new self(
            routeName: $routeName,
            controllerName: $controllerName,
            actionName: $actionName,
            method: $method
        );
    }
}