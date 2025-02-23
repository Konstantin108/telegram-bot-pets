<?php

namespace Project\Scopes;

use Project\Dto\DB\ScopeParamDto;
use Project\Enums\User\UserStatusEnum;

class MembersWithNotificationScope extends AbstractScope
{
    private const bool IS_NOTIFICATION_ENABLED = true;
    private const string USER_STATUS = UserStatusEnum::MEMBER->value;

    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            new ScopeParamDto(
                column: "notification",
                value: self::IS_NOTIFICATION_ENABLED,
                operator: self::EQ
            ),
            new ScopeParamDto(
                column: "status",
                value: self::USER_STATUS,
                operator: self::EQ
            ),
        ];
    }
}