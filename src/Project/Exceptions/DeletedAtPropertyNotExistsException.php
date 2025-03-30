<?php

namespace Project\Exceptions;

class DeletedAtPropertyNotExistsException extends \Exception
{
    /**
     * @param string $className
     * @param string $propertyName
     * @return DeletedAtPropertyNotExistsException
     */
    public static function buildMessage(string $className, string $propertyName): DeletedAtPropertyNotExistsException
    {
        return new self(sprintf(
            "Свойство %s отстуствует в классе %s",
            $propertyName,
            $className
        ));
    }
}