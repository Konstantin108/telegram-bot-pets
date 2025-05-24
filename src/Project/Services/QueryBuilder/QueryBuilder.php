<?php

namespace Project\Services\QueryBuilder;

use Project\Enums\DB\OperatorEnum;
use Project\Exceptions\DbException;
use Project\Services\Database\DB;

class QueryBuilder
{
    //TODO все методы в этом классе надо будет переименовать,
    // так же нужен приватный метод который формирует итоговую строку
    // запрос SQL

    //TODO возможно надо будет добавить репозитории

    //TODO добавить методы orderBy() и take()
    // в ActiveRecordEntity останутся методы all(), delete(), create(), может что-то еще
    // надо подумать

    private string $query = "";
    private array $bindings = [];

    /**
     * @param string $table
     * @param string $className
     */
    public function __construct(
        public string $table,
        public string $className
    )
    {
        $this->querySelect();
    }

    /**
     * @return bool|array|null
     * @throws DbException
     */
    public function get(): bool|array|null
    {
        //TODO по такому же типу надо сделать для first()
        return $this->getDB()->query($this->query, $this->bindings, $this->className);
    }

    /**
     * @return bool|array|null
     * @throws DbException
     */
    public function list(): bool|array|null
    {
        return $this
            ->getDB()
            ->query($this->query, $this->bindings, $this->className);
    }

    /**
     * @param string $param
     * @param string|int $value
     * @param string|null $operator
     * @return QueryBuilder
     */
    public function where(string $param, string|int $value, ?string $operator = null): QueryBuilder
    {
        return $this->queryWhere($param, $value, $operator ?? OperatorEnum::EQ->value);
    }

    public function firstWhere(string $param, string|int $value, ?string $operator = null)
    {
        //
    }

    //TODO как поступить с $bindings в запросе whereIn()

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * @return string
     */
    public function toSql(): string
    {
        return $this->query;
    }

    //TODO проверить все пограничные случаи

    //TODO все дублирующие методы надо будет удалить из ActiveRecordEntity, там останутся только методы
    // по типу update() или delete()

    /**
     * @param string $param
     * @param string|int $value
     * @param string $operator
     * @return QueryBuilder
     */
    private function queryWhere(string $param, string|int $value, string $operator): QueryBuilder
    {
        //TODO надо доработать если в where() передан массив с условиями
        $this->concat();

        $this->query .= sprintf(
            "%s `%s` %s :%s;",
            count($this->bindings) < 1 ? "WHERE" : "AND",
            $param,
            $operator,
            $param
        );

        $this->bindings[$param] = $value;

        return $this;
    }

    /**
     * @return void
     */
    private function querySelect(): void
    {
        $this->query = "/** @lang text */SELECT * FROM `$this->table`;";
    }

    /**
     * @return void
     */
    private function concat(): void
    {
        if (mb_strlen($this->query) < 1) {
            return;
        }

        if (mb_substr($this->query, -1, 1, "UTF-8") === ";") {
            $this->query = mb_substr($this->query, 0, -1, "UTF-8") . " ";
        }
    }

    /**
     * @return DB
     */
    private function getDB(): DB
    {
        //TODO продумать логику добавления пользователя, который админ или который тестовый
        return DB::call();
    }
}