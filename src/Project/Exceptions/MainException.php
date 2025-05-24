<?php

namespace Project\Exceptions;

use Exception;
use Project\Logger\Logger;

//TODO переработать исключения

abstract class MainException extends Exception
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function show(): void
    {
        Logger::log($this->__toString());

        echo "<pre>";
        print_r($this->__toString());
        echo "</pre>";
    }
}