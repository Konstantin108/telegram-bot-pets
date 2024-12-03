<?php

namespace Project\Enums\User;

enum UserStatusEnum: string
{
    case MEMBER = "member";
    case KICKED = "kicked";
}