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
            new ScopeParamDto("is_test", self::IS_TEST_MEMBER, self::EQ),
        ];
    }
}