<?php

namespace Project\Dto\DB;

class SoftDeletesDto
{
    /**
     * @param string $filter
     * @param array $values
     */
    public function __construct(
        public string $filter = "",
        public array  $values = []
    )
    {
    }
}