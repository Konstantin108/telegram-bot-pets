<?php

namespace Project\Exceptions;

class MethodNotAllowedHttpException extends \Exception
{
    /**
     * @param string $requestMethod
     * @param string $routeName
     * @param string $routeMethod
     * @return MethodNotAllowedHttpException
     */
    public static function buildMessage(
        string $requestMethod,
        string $routeName,
        string $routeMethod
    ): MethodNotAllowedHttpException
    {
        return new self(sprintf(
            "Роут %s не поддерживает метод %s. Поддерживаемый метод: %s",
            $routeName,
            strtoupper($requestMethod),
            strtoupper($routeMethod)
        ));
    }
}