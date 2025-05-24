<?php

namespace Project\Dto\Request;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class QueryParamsDto
{
    /**
     * @param array $params
     */
    public function __construct(
        public array $params
    )
    {
    }
}