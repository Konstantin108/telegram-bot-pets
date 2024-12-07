<?php

namespace Project\Scopes;

use Project\Dto\DB\ScopeParamDto;
use Project\Enums\User\UserStatusEnum;

class MembersWithNotificationScope extends AbstractScope
{
    private const bool IS_NOTIFICATION_ENABLED = true;
    private const string USER_STATUS = UserStatusEnum::MEMBER->value;

    public function __invoke(): array
    {
        return [
            new ScopeParamDto("notification", self::IS_NOTIFICATION_ENABLED, self::EQ),
            new ScopeParamDto("status", self::USER_STATUS, self::EQ),
        ];
    }
}