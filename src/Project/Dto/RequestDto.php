<?php

namespace Project\Dto;

use JetBrains\PhpStorm\Immutable;
use Project\Dto\Request\QueryParamsDto;
use Project\Dto\Telegram\Request\InputDataDto;

#[Immutable]
class RequestDto
{
    /**
     * @param string $route
     * @param string $method
     * @param InputDataDto|null $inputDataDto
     * @param QueryParamsDto|null $queryParamsDto
     */
    public function __construct(
        public string          $route,
        public string          $method,
        public ?InputDataDto   $inputDataDto,
        public ?QueryParamsDto $queryParamsDto
    )
    {
    }
}