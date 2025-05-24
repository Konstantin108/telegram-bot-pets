<?php

namespace Project\Dto\DB;

//TODO проверить как это работает, не ломает ли логику и добавить во всех других Dto
use JetBrains\PhpStorm\Immutable;

#[Immutable]
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