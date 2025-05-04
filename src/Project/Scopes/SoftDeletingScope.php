<?php

namespace Project\Scopes;

use Project\Dto\DB\ScopeParamDto;

class SoftDeletingScope extends AbstractScope
{
    private const string DELETED_AT = "NULL";

    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            new ScopeParamDto(
                column: "deleted_at",
                value: self::DELETED_AT,
                operator: self::IS
            ),
        ];
    }
}