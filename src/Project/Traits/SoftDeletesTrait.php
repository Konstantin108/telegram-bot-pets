<?php

namespace Project\Traits;

use Project\Exceptions\DbException;
use Project\Exceptions\DeletedAtPropertyNotExistsException;

trait SoftDeletesTrait
{
    public const string DELETED_AT_PROPERTY = "deletedAt";

    //TODO доработать остальные методы ActiveRecordEntity с учетом SoftDeletesTrait
    // получение записей должно исключать те записи, у которых deleted_at не равен null
    // возможно ли повторно мягкое удаление ???
    // добавить restore() и forceDelete()
    // forceDelete() возможно должен использовать delete() из самой модели
    // добавить withTrashed для работы с мягкоудаленными записями

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

        $values = [
            "deleted_at" => date("Y-m-d H:i:s"),
            "id" => $this->id,
        ];

        $sql = sprintf(
            "/** @lang text */UPDATE `%s` SET `deleted_at` = :deleted_at WHERE `id` = :id",
            static::table()
        );

        static::getDB()->query($sql, $values, static::class);
        $this->refresh();
    }

    /**
     * @return bool
     */
    private static function modelHasDeletedAtProperty(): bool
    {
        return property_exists(static::class, static::DELETED_AT_PROPERTY);
    }
}