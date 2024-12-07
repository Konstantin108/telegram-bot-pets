<?php

namespace Project\Dto\DB;

use JetBrains\PhpStorm\ArrayShape;

class ScopeParamDto
{
    public string $column;
    public string $value;
    public string $operator;

    /**
     * @param string $column
     * @param string $value
     * @param string $operator
     */
    public function __construct(string $column, string $value, string $operator)
    {
        $this->column = $column;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * @return array{column: string, value: string, operator: string}
     */
    #[ArrayShape(shape: ["column" => "string", "value" => "string", "operator" => "string"])]
    public function toArray(): array
    {
        return [
            "column" => $this->column,
            "value" => $this->value,
            "operator" => $this->operator
        ];
    }

    public static function fromArray(array $array): ScopeParamDto
    {
        return new ScopeParamDto(
            $column = $array["column"],
            $value = $array["value"],
            $operator = $array["operator"]
        );
    }
}