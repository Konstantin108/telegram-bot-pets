<?php

namespace Project\Dto\Request;

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