<?php

namespace Project\Exceptions;

use Project\Routing\Route;

class MethodNotAllowedHttpException extends \Exception
{
    //TODO мне еще надо код выбрасывать

    /**
     * @param string $requestMethod
     * @param Route $route
     * @return MethodNotAllowedHttpException
     */
    public static function buildMessage(
        string $requestMethod,
        Route  $route,
    ): MethodNotAllowedHttpException
    {
        return new self(sprintf(
            "Роут \"%s\" не поддерживает метод %s. Поддерживаемые методы: %s.",
            $route->routeName,
            strtoupper($requestMethod),
            strtoupper(implode(", ", $route->allowedMethods))
        ));
    }
}