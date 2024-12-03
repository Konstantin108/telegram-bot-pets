<?php

namespace Project\Scopes;

interface ScopeInterface
{
    public function __invoke(): array;
}