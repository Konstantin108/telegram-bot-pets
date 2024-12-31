<?php

namespace Project\Enums\Telegram;

enum ErrorCodeEnum: int
{
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case BOT_WAS_BLOCKED = 403;
    case NOT_FOUND = 404;
    case TOO_MANY_REQUESTS = 429;

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this === self::BOT_WAS_BLOCKED;
    }
}