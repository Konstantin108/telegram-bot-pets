<?php

namespace Project\Traits;

trait SingletonTrait
{
    private static self $item;

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        return empty(static::$item)
            ? static::$item = new static()
            : static::$item;
    }

    protected function __construct()
    {
    }

    /**
     * @return void
     */
    protected function __clone(): void
    {
    }

    /**
     * @return void
     */
    public function __wakeup(): void
    {
    }
}