<?php

namespace Project\Models;

use Error;
use Project\Exceptions\AccessModifiersException;
use Project\Exceptions\DbException;
use Project\Scopes\ScopeInterface;
use Project\Services\DB;
use ReflectionObject;

abstract class ActiveRecordEntity
{
    protected int $id;

    /**
     * @param string $name
     * @param ?string $value
     * @return void
     * @throws AccessModifiersException
     */
    public function __set(string $name, string|null $value = "")
    {
        try {
            $camelCaseName = $this->underscoreToCamelCase($name);
            $this->$camelCaseName = $value;
        } catch (Error $e) {
            throw new AccessModifiersException($e->getMessage());
        }
    }

    /**
     * @param string $param
     * @param string $value
     * @return mixed|null
     * @throws DbException
     */
    public static function where(string $param, string $value): mixed
    {
        $result = static::getDB()->query(
            "/** @lang text */SELECT * FROM `" . static::getTableName() . "` WHERE `$param` = :$param;",
            [$param => $value],
            static::class
        );
        return $result
            ? array_shift($result)
            : null;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DbException
     */
    public static function getById(int $id): mixed
    {
        return static::where("id", $id);
    }

    /**
     * @param ScopeInterface ...$scopes
     * @return array|bool|null
     * @throws DbException
     */
    public static function filter(ScopeInterface ...$scopes): bool|array|null
    {
        $filter = "";
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
        return DB::getInstance();
    }

    /**
     * @param string $filter
     * @param array $values
     * @return array|bool|null
     * @throws DbException
     */
    private static function list(string $filter = "", array $values = []): bool|array|null
    {
        return static::getDB()->query(
            "/** @lang text */SELECT * FROM `" . static::getTableName() . "` WHERE 1=1$filter;",
            $values,
            static::class
        );
    }

    /**
     * @return void
     * @throws DbException
     */
    private function insert(): void
    {
        $fields = $values = $columns = [];
        foreach ($this as $fieldName => $value) {
            if (is_null($value)) continue;
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
            if ($fieldName === "id") continue;
            $fieldName = $this->camelCaseToUnderscore($fieldName);
            $fields[] = "`$fieldName` = :$fieldName";
            $values[$fieldName] = $value;
        }
        $sql = "UPDATE `" . static::getTableName() . "` SET " . implode(", ", $fields) . " WHERE `id` = $this->id;";
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