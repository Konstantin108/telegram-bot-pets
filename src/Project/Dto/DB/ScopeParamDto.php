<?php

namespace Project\Dto\DB;

class ScopeParamDto
{
    /**
     * @param string $column
     * @param string $value
     * @param string $operator
     */
    public function __construct(
        public string $column,
        public string $value,
        public string $operator
    )
    {
    }
}