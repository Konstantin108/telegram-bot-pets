<?php

namespace Project\Scopes;

use Project\Enums\User\UserStatusEnum;

class MembersWithNotificationScope implements ScopeInterface
{
    private const bool IS_NOTIFICATION_ENABLED = true;
    private const string USER_STATUS = UserStatusEnum::MEMBER->value;

    public function __invoke(): array
    {
        return [
            "=|notification" => self::IS_NOTIFICATION_ENABLED,
            "=|status" => self::USER_STATUS
        ];
    }
}