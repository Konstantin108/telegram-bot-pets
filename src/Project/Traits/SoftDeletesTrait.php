<?php

namespace Project\Traits;

use Project\Exceptions\DbException;
use Project\Exceptions\DeletedAtPropertyNotExistsException;

trait SoftDeletesTrait
{
    public const string DELETED_AT_PROPERTY = "deletedAt";
    public const string DELETED_AT_FIELD = "deleted_at";

    //TODO доработать остальные методы ActiveRecordEntity с учетом SoftDeletesTrait
    // получение записей должно исключать те записи, у которых deleted_at не равен null
    // возможно ли повторно мягкое удаление ???
    // добавить restore() и forceDelete()
    // forceDelete() возможно должен использовать delete() из самой модели
    // добавить withTrashed для работы с мягкоудаленными записями
    // добавить scope для просмотра мягко удаленных заказов тоже

    //TODO проверить почему какие-то уведомления срабатывают сами по себе

    /**
     * @return void
     * @throws DeletedAtPropertyNotExistsException
     * @throws DbException
     */
    public function delete(): void
    {
        if (!static::modelHasDeletedAtProperty()) {
            throw DeletedAtPropertyNotExistsException::buildMessage(static::class, static::DELETED_AT_PROPERTY);
        }

        $fieldId = static::primaryKey();
        $fieldDeletedAt = static::DELETED_AT_FIELD;

        $values = [
            $fieldDeletedAt => date("Y-m-d H:i:s"),
            $fieldId => $this->id,
        ];

        $sql = sprintf(
            "/** @lang text */UPDATE `%s` SET `%s` = :%s WHERE `%s` = :%s;",
            static::table(),
            $fieldDeletedAt,
            $fieldDeletedAt,
            $fieldId,
            $fieldId
        );

        static::getDB()->query($sql, $values, static::class);
        $this->refresh();
    }

    //TODO мне надо везде добавить try catch

    //TODO может ли этот метод удалить мягко удаленную запись

    /**
     * @return void
     * @throws DbException
     */
    public function forceDelete(): void
    {
        parent::delete();
    }

    /**
     * @return void
     * @throws DbException
     */
    public function restore(): void
    {
        if (!static::trashed()) {
            return;
        }

        $fieldId = static::primaryKey();
        $fieldDeletedAt = static::DELETED_AT_FIELD;

        $values = [
            $fieldDeletedAt => null,
            $fieldId => $this->id,
        ];

        $sql = sprintf(
            "/** @lang text */UPDATE `%s` SET `%s` = :%s WHERE `%s` = :%s;",
            static::table(),
            $fieldDeletedAt,
            $fieldDeletedAt,
            $fieldId,
            $fieldId
        );

        static::getDB()->query($sql, $values, static::class);
        $this->refresh();
    }

    /**
     * @return bool
     */
    public function trashed(): bool
    {
        return !is_null($this->deletedAt);
    }

    /**
     * @return bool
     */
    private static function modelHasDeletedAtProperty(): bool
    {
        return property_exists(static::class, static::DELETED_AT_PROPERTY);
    }
}