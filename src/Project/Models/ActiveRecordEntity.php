<?php

namespace Project\Models;

use Error;
use Project\Enums\DB\OperatorEnum;
use Project\Exceptions\AccessModifiersException;
use Project\Exceptions\DbException;
use Project\Scopes\ScopeInterface;
use Project\Services\Database\DB;
use ReflectionObject;

abstract class ActiveRecordEntity
{
    protected int $id;

    /**
     * @param string $name
     * @param string|null $value
     * @return void
     * @throws AccessModifiersException
     */
    public function __set(string $name, string|null $value = "")
    {
        try {
            $camelCaseName = $this->underscoreToCamelCase($name);
            $this->$camelCaseName = $value;
        } catch (Error $error) {
            throw new AccessModifiersException($error->getMessage());
        }
    }

    /**
     * @param string $param
     * @param string $value
     * @param string|null $operator
     * @return mixed
     * @throws DbException
     */
    public static function first(string $param, string $value, string $operator = null): mixed
    {
        return static::search($param, $value, $operator, true);
    }

    /**
     * @param string $param
     * @param string $value
     * @param string|null $operator
     * @return mixed
     * @throws DbException
     */
    public static function where(string $param, string $value, string $operator = null): mixed
    {
        return static::search($param, $value, $operator);
    }

    /**
     * @param string $param
     * @param string $value
     * @return mixed
     * @throws DbException
     */
    public static function like(string $param, string $value): mixed
    {
        return static::search($param, $value, OperatorEnum::LIKE->value);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DbException
     */
    public static function getById(int $id): mixed
    {
        return static::first("id", $id);
    }

    /**
     * @param string $param
     * @param array $values
     * @return bool|array|null
     * @throws DbException
     */
    public static function whereIn(string $param, array $values): bool|array|null
    {
        return static::searchInArray($param, $values);
    }

    /**
     * @param string $param
     * @param array $values
     * @return bool|array|null
     * @throws DbException
     */
    public static function whereNotIn(string $param, array $values): bool|array|null
    {
        return static::searchInArray($param, $values, true);
    }

    /**
     * @param ScopeInterface ...$scopes
     * @return array|bool|null
     * @throws DbException
     */
    public static function filter(ScopeInterface ...$scopes): bool|array|null
    {
        $filter = " WHERE 1=1";
        $values = [];
        if (count($scopes) > 0) {
            foreach ($scopes as $scope) {
                foreach ($scope() as $paramDto) {
                    $filter .= " AND `$paramDto->column` $paramDto->operator :$paramDto->column";
                    $values[$paramDto->column] = $paramDto->value;
                }
            }
        }

        return static::list($filter, $values);
    }

    /**
     * @throws DbException
     */
    public static function all(): bool|array|null
    {
        return static::list();
    }

    /**
     * @return void
     * @throws DbException
     */
    public function save(): void
    {
        empty($this->id)
            ? $this->insert()
            : $this->update();
    }

    /**
     * @return string
     */
    abstract protected static function getTableName(): string;

    /**
     * @return DB
     */
    protected static function getDB(): DB
    {
        return DB::call();
    }

    /**
     * @param string $filter
     * @param array $values
     * @return array|bool|null
     * @throws DbException
     */
    private static function list(string $filter = "", array $values = []): bool|array|null
    {
        $sql = sprintf(
            "/** @lang text */SELECT * FROM `%s`%s;",
            static::getTableName(),
            $filter
        );

        return static::getDB()->query($sql, $values, static::class);
    }

    /**
     * @param string $param
     * @param string $value
     * @param string|null $operator
     * @param bool $getFirst
     * @return mixed|null
     * @throws DbException
     */
    private static function search(string $param, string $value, ?string $operator, bool $getFirst = false): mixed
    {
        $sql = sprintf(
            "/** @lang text */SELECT * FROM `%s` WHERE `%s` %s :%s;",
            static::getTableName(),
            $param,
            $operator ?? OperatorEnum::EQ->value,
            $param
        );

        $result = static::getDB()->query($sql, [$param => $value], static::class);

        if (!$getFirst) {
            return $result;
        }

        return $result
            ? array_shift($result)
            : null;
    }

    /**
     * @param string $param
     * @param array $values
     * @param bool $not
     * @return bool|array|null
     * @throws DbException
     */
    private static function searchInArray(string $param, array $values, bool $not = false): bool|array|null
    {
        $filter = sprintf(
            " WHERE `%s`%s IN (%s)",
            $param,
            $not ? " NOT" : "",
            rtrim(str_repeat("?, ", count($values)), ", ")
        );

        return static::list($filter, $values);
    }

    /**
     * @return void
     * @throws DbException
     */
    private function insert(): void
    {
        $fields = $values = $columns = [];
        foreach ($this as $fieldName => $value) {
            if (is_null($value)) {
                continue;
            }
            $fieldName = $this->camelCaseToUnderscore($fieldName);
            $fields[] = ":$fieldName";
            $values[$fieldName] = $value;
            $columns[] = "`$fieldName`";
        }
        $sql = sprintf(
            "/** @lang text */INSERT INTO `%s` (%s) VALUES (%s);",
            static::getTableName(),
            implode(", ", $columns),
            implode(", ", $fields)
        );
        static::getDB()->query($sql, $values, static::class);
        $this->id = static::getDB()->getLastInsertId();
        $this->refresh();
    }

    /**
     * @return void
     * @throws DbException
     */
    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);
        $reflector = new ReflectionObject($objectFromDb);
        $properties = $reflector->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $this->$propertyName = $property->getValue($objectFromDb);
        }
    }

    /**
     * @return void
     * @throws DbException
     */
    private function update(): void
    {
        $fields = $values = [];
        foreach ($this as $fieldName => $value) {
            if ($fieldName === "id") {
                continue;
            }
            $fieldName = $this->camelCaseToUnderscore($fieldName);
            $fields[] = "`$fieldName` = :$fieldName";
            $values[$fieldName] = $value;
        }

        $sql = sprintf(
            "/** @lang text */UPDATE `%s` SET %s WHERE `id` = %d;",
            static::getTableName(),
            implode(", ", $fields),
            $this->id
        );

        static::getDB()->query($sql, $values, static::class);
    }

    /**
     * @param string $name
     * @return string
     */
    private function underscoreToCamelCase(string $name): string
    {
        return lcfirst(str_replace("_", "", ucwords($name, "_")));
    }

    /**
     * @param string $name
     * @return string
     */
    private function camelCaseToUnderscore(string $name): string
    {
        return strtolower(preg_replace("/(?<!^)[A-Z]/", "_$0", $name));
    }
}