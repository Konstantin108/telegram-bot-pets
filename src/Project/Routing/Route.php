<?php

namespace Project\Routing;

class Route
{
    private const string POST_METHOD = "post";
    private const string GET_METHOD = "get";

    /**
     * @param string $routeName
     * @param string $controllerName
     * @param string $actionName
     * @param array $allowedMethods
     */
    private function __construct(
        public string $routeName,
        public string $controllerName,
        public string $actionName,
        public array  $allowedMethods,
    )
    {
    }

    /**
     * @param string $routeName
     * @param array $controllerAndAction
     * @return Route
     */
    public static function any(
        string $routeName,
        array  $controllerAndAction,
    ): Route
    {
        return self::setRoute($routeName, $controllerAndAction);
    }

    /**
     * @param array $allowedMethods
     * @param string $routeName
     * @param array $controllerAndAction
     * @return Route
     */
    public static function match(
        array  $allowedMethods,
        string $routeName,
        array  $controllerAndAction
    ): Route
    {
        return self::setRoute($routeName, $controllerAndAction, ...$allowedMethods);
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
     * @param string ...$allowedMethods
     * @return Route
     */
    private static function setRoute(
        string $routeName,
        array  $controllerAndAction,
        string ...$allowedMethods
    ): Route
    {
        [$controllerName, $actionName] = $controllerAndAction;

        return new self(
            routeName: $routeName,
            controllerName: $controllerName,
            actionName: $actionName,
            allowedMethods: array_map(fn(string $method) => $method, $allowedMethods)
        );
    }
}