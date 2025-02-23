<?php

namespace Project\Dto;

use Project\Dto\Request\QueryParamsDto;
use Project\Dto\Telegram\Request\InputDataDto;

class RequestDto
{
    /**
     * @param string $method
     * @param InputDataDto|null $inputDataDto
     * @param QueryParamsDto|null $queryParamsDto
     */
    public function __construct(
        public string          $method,
        public ?InputDataDto   $inputDataDto,
        public ?QueryParamsDto $queryParamsDto
    )
    {
    }
}