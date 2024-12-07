<?php

namespace Project\Scopes;

abstract class AbstractScope implements ScopeInterface
{
    protected const string EQ = "=";
    protected const string NE = "!=";
    protected const string GT = ">";
    protected const string LT = "<";
    protected const string GE = ">=";
    protected const string LE = "<=";
}