<?php

namespace Project\Exceptions;

use Exception;
use Project\Logger\Logger;

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
        Logger::create()->log($this->__toString());

        echo "<pre>";
        print_r($this->__toString());
        echo "</pre>";
    }
}