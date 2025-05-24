<?php

namespace Project\Exceptions;

class ModelNotFoundException extends \Exception
{
    /**
     * @param string $className
     * @param int|null $id
     * @return ModelNotFoundException
     */
    public static function buildMessage(string $className, int $id = null): ModelNotFoundException
    {
        return new self(sprintf(
            "Модель [%s] %s не найдена",
            $className,
            $id ? "с id $id" : ""
        ));
    }
}