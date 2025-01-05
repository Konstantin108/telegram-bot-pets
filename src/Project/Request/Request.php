<?php

namespace Project\Request;

use Project\Dto\Request\QueryParamsDto;
use Project\Dto\Telegram\Request\RequestDto;

class Request
{
    protected InputDataResolver $inputDataResolver;

    public function __construct()
    {
        $this->inputDataResolver = new InputDataResolver();
    }

    /**
     * @return RequestDto|null
     */
    public function getInputData(): ?RequestDto
    {
        return $this->inputDataResolver->resolveInputData();
    }

    /**
     * @return QueryParamsDto|null
     */
    public function getQueryParams(): ?QueryParamsDto
    {
        return $this->inputDataResolver->resolveQueryParams();
    }
}