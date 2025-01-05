<?php

namespace Project\Request;

use Project\Dto\Request\QueryParamsDto;
use Project\Dto\Telegram\Request\InputDataDto;

class Request
{
    protected InputDataResolver $inputDataResolver;

    public function __construct()
    {
        $this->inputDataResolver = new InputDataResolver();
    }

    /**
     * @return InputDataDto|null
     */
    public function getInputData(): ?InputDataDto
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