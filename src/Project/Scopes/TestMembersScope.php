<?php

namespace Project\Scopes;

use Project\Dto\DB\ScopeParamDto;

class TestMembersScope extends AbstractScope
{
    private const bool IS_TEST_MEMBER = true;

    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            new ScopeParamDto(
                column: "is_test",
                value: self::IS_TEST_MEMBER,
                operator: self::EQ
            ),
        ];
    }
}