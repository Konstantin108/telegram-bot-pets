<?php

namespace Project\Enums\DB;

enum OperatorEnum: string
{
    case EQ = "=";
    case NE = "!=";
    case GT = ">";
    case LT = "<";
    case GE = ">=";
    case LE = "<=";
    case LIKE = "LIKE";
    case IS = "IS";
    case IS_NOT = "IS NOT";
}