<?php

namespace Project\Scopes;

use Project\Enums\DB\OperatorEnum;

abstract class AbstractScope implements ScopeInterface
{
    protected const string EQ = OperatorEnum::EQ->value;
    protected const string NE = OperatorEnum::NE->value;
    protected const string GT = OperatorEnum::GT->value;
    protected const string LT = OperatorEnum::LT->value;
    protected const string GE = OperatorEnum::GE->value;
    protected const string LE = OperatorEnum::LE->value;
}