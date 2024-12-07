<?php

namespace Project\Scopes;

class TestScope implements ScopeInterface
{
    public function __invoke(): array
    {
        return [
            "=|chat_id" => "549853091"
        ];
    }
}