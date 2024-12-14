<?php

namespace Project\Scopes;

interface ScopeInterface
{
    /**
     * @return array
     */
    public function __invoke(): array;
}