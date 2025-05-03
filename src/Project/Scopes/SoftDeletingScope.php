<?php

namespace Project\Scopes;

use Project\Dto\DB\ScopeParamDto;

class SoftDeletingScope extends AbstractScope
{
    private const null IS_DELETED_AT = null;

    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            new ScopeParamDto(
                column: "deleted_at",
                value: self::IS_DELETED_AT,
                operator: self::IS
            ),
        ];
    }
}