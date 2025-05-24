<?php

namespace Project\Models;

use Error;
use Project\Enums\DB\OperatorEnum;
use Project\Exceptions\AccessModifiersException;
use Project\Exceptions\DbException;
use Project\Exceptions\ModelNotFoundException;
use Project\Scopes\ScopeInterface;
use Project\Services\Database\DB;
use Project\Services\QueryBuilder\QueryBuilder;
use ReflectionObject;

abstract class ActiveRecordEntity
{
    protected int $id;

    /**
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::table(), static::class);
    }

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

    /**
     * @param string $param
     * @param string|int $value
     * @param string|null $operator
     * @return mixed
     * @throws DbException
     */
    public static function firstWhere(string $param, string|int $value, ?string $operator = null): mixed
    {
        return static::search($param, $value, $operator, true);
    }

    /**
     * @param string $param
     * @param string|int $value
     * @param string|null $operator
     * @return mixed
     * @throws DbException
     */
    public static function where(string $param, string|int $value, ?string $operator = null): mixed
    {
        return static::search($param, $value, $operator);
    }

    /**
     * @param string $param
     * @param string|int $value
     * @param bool $getFirst
     * @return mixed
     * @throws DbException
     */
    public static function like(string $param, string|int $value, bool $getFirst = false): mixed
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
        return static::firstWhere(static::primaryKey(), $id);
    }

    /**
     * @param int $id
     * @return void
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function findOrFail(int $id): void
    {
        $result = static::firstWhere(static::primaryKey(), $id);

        if (is_null($result)) {
            throw ModelNotFoundException::buildMessage(static::class, $id);
        }
    }

    //TODO методы, что принимают массив должны принимать и один объект

    /**
     * @param array $ids
     * @return bool|array|null
     * @throws DbException
     */
    public static function findMany(array $ids): bool|array|null
    {
        return static::searchIn(static::primaryKey(), $ids);
    }

    /**
     * @return mixed|null
     * @throws DbException
     */
    public static function first(): mixed
    {
        $result = static::list("", [], 1);

        return count($result) > 0
            ? array_shift($result)
            : null;
    }

    /**
     * @return mixed
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public static function firstOrFail(): mixed
    {
        $result = static::list("", [], 1);

        if (count($result) <= 0) {
            throw ModelNotFoundException::buildMessage(static::class);
        }

        return array_shift($result);
    }

    /**
     * @throws DbException
     */
    public static function last(): bool|array|null
    {
        return static::list("", [], 1, "DESC");
    }

    //TODO добавить forceDelete(), так же проверять чтобы возвращался не null

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
     * @return bool|array|null
     * @throws DbException
     */
    public static function all2(): bool|array|null
    {
        return static::query()->list();
    }

    //TODO надо добавить методы select(), pluck(), keys()
    // надо избавиться от 1=1

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
        $fieldId = static::primaryKey();

        $sql = sprintf(
            "/** @lang text */DELETE FROM `%s` WHERE `%s` = :%s;",
            static::table(),
            $fieldId,
            $fieldId
        );

        static::getDB()->query($sql, [$fieldId => $this->id], static::class);
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
     * @return string
     */
    abstract protected static function primaryKey(): string;

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
     * @param int|null $limit
     * @param string $orderBy
     * @param string $sortedBy
     * @return array|bool|null
     * @throws DbException
     */
    private static function list(
        string $filter = "",
        array  $values = [],
        int    $limit = null,
        string $orderBy = "ASC",
        string $sortedBy = "id"
    ): bool|array|null
    {
        $sql = sprintf(
            "/** @lang text */SELECT * FROM `%s` WHERE 1=1%s%s ORDER BY `%s` %s%s;",
            static::table(),
            $filter,
            static::softDeletes(),
            $sortedBy,
            $orderBy,
            $limit ? " LIMIT $limit" : ""
        );

        return static::getDB()->query($sql, $values, static::class);
    }

    /**
     * @param string $param
     * @param string|int|null $value
     * @param string|null $operator
     * @param bool $getFirst
     * @return mixed|null
     * @throws DbException
     */
    private static function search(
        string          $param,
        null|string|int $value,
        ?string         $operator,
        bool            $getFirst = false
    ): mixed
    {
        $sql = sprintf(
            "/** @lang text */SELECT * FROM `%s` WHERE `%s` %s :%s%s;",
            static::table(),
            $param,
            $operator ?? OperatorEnum::EQ->value,
            $param,
            static::softDeletes()
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
            " AND `%s`%s IN (%s)",
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
        $this->id = (int)static::getDB()->getLastInsertId();
        $this->refresh();
    }

    /**
     * @return void
     * @throws DbException
     */
    protected function refresh(): void
    {
        $objectFromDB = static::find($this->id);
        $reflector = new ReflectionObject($objectFromDB);
        $properties = $reflector->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $this->$propertyName = $property->getValue($objectFromDB);
        }
    }

    /**
     * @return string
     */
    protected static function softDeletes(): string
    {
        return "";
    }

    /**
     * @return void
     * @throws DbException
     */
    private function update(): void
    {
        $fields = $values = [];
        $fieldId = static::primaryKey();
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