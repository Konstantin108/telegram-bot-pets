<?php

namespace Project\Exceptions;

class MethodNotAllowedHttpException extends \Exception
{
    /**
     * @param string $requestMethod
     * @param string $routeName
     * @param string $allowedMethods
     * @return MethodNotAllowedHttpException
     */
    public static function buildMessage(
        string $requestMethod,
        string $routeName,
        string $allowedMethods
    ): MethodNotAllowedHttpException
    {
        return new self(sprintf(
            "Роут \"%s\" не поддерживает метод %s. Поддерживаемые методы: %s.",
            $routeName,
            strtoupper($requestMethod),
            strtoupper($allowedMethods)
        ));
    }
}