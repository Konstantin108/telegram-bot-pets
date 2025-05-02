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
    public function __set(string $name, string|null $value = ""): void
    {
        try {
            $camelCaseName = $this->underscoreToCamelCase($name);
            $this->$camelCaseName = $value;
        } catch (Error $error) {
            throw new AccessModifiersException($error->getMessage());
        }
    }

    //TODO нужно добавить пармаметр в который буду записывать полученные из базы данных
    // возвращать буду данные из этого параметра в массиве или одним объектом

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
     * @param bool $getFirst
     * @return mixed
     * @throws DbException
     */
    public static function like(string $param, string $value, bool $getFirst = false): mixed
    {
        return static::search($param, $value, OperatorEnum::LIKE->value, $getFirst);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws DbException
     */
    public static function find(int $id): mixed
    {
        return static::first("id", $id);
    }

    //TODO добавить forceDelete() и firstOrFail(), так же проверять чтобы возвращался не null

    /**
     * @param string $param
     * @param bool $getFirst
     * @return mixed|null
     * @throws DbException
     */
    public static function whereNull(string $param, bool $getFirst = false): mixed
    {
        return static::search($param, null, OperatorEnum::IS->value, $getFirst);
    }

    /**
     * @param string $param
     * @param bool $getFirst
     * @return mixed
     * @throws DbException
     */
    public static function whereNotNull(string $param, bool $getFirst = false): mixed
    {
        return static::search($param, null, OperatorEnum::IS_NOT->value, $getFirst);
    }

    /**
     * @param string $param
     * @param array $values
     * @return bool|array|null
     * @throws DbException
     */
    public static function whereIn(string $param, array $values): bool|array|null
    {
        return static::searchIn($param, $values);
    }

    /**
     * @param string $param
     * @param array $values
     * @return bool|array|null
     * @throws DbException
     */
    public static function whereNotIn(string $param, array $values): bool|array|null
    {
        return static::searchIn($param, $values, true);
    }

    /**
     * @param ScopeInterface ...$scopes
     * @return array|bool|null
     * @throws DbException
     */
    public static function scoped(ScopeInterface ...$scopes): bool|array|null
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
        //TODO надо будет переделать save() и update() - будут разными методами
        // не нужно чтобы каждый раз происходило обновление записи
        empty($this->id)
            ? $this->insert()
            : $this->update();
    }

    /**
     * @return void
     * @throws DbException
     */
    public function delete(): void
    {
        $sql = sprintf(
            "/** @lang text */DELETE FROM `%s` WHERE `id` = :id",
            static::table(),
        );

        static::getDB()->query($sql, ["id" => $this->id], static::class);
    }

    /**
     * @return string
     */
    abstract protected static function table(): string;

    /**
     * @return array
     */
    abstract protected static function guarded(): array;

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
            static::table(),
            $filter
        );

        return static::getDB()->query($sql, $values, static::class);
    }

    /**
     * @param string $param
     * @param string|null $value
     * @param string|null $operator
     * @param bool $getFirst
     * @return mixed|null
     * @throws DbException
     */
    private static function search(string $param, ?string $value, ?string $operator, bool $getFirst = false): mixed
    {
        $sql = sprintf(
            "/** @lang text */SELECT * FROM `%s` WHERE `%s` %s :%s;",
            static::table(),
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
    private static function searchIn(string $param, array $values, bool $not = false): bool|array|null
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
            static::table(),
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
    protected function refresh(): void
    {
        $objectFromDb = static::find($this->id);
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
        $fieldId = "id";
        $guarded = static::guarded();

        foreach ($this as $fieldName => $value) {
            if (in_array($fieldName, $guarded)) {
                continue;
            }
            $fieldName = $this->camelCaseToUnderscore($fieldName);
            $fields[] = "`$fieldName` = :$fieldName";
            $values[$fieldName] = $value;
        }
        $values[$fieldId] = $this->id;

        $sql = sprintf(
            "/** @lang text */UPDATE `%s` SET %s WHERE `%s` = :%s;",
            static::table(),
            implode(", ", $fields),
            $fieldId,
            $fieldId
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