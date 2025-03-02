<?php

namespace Project\Request;

use Project\Dto\RequestDto;

class Request
{
    protected InputDataResolver $inputDataResolver;

    public function __construct()
    {
        $this->inputDataResolver = new InputDataResolver();
    }

    /**
     * @return RequestDto
     */
    public function getData(): RequestDto
    {
        return $this->inputDataResolver->data();
    }
}