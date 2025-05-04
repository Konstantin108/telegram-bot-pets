<?php

namespace Project\Traits;

use Project\Exceptions\DbException;
use Project\Exceptions\DeletedAtPropertyNotExistsException;
use Project\Scopes\SoftDeletingScope;

trait SoftDeletesTrait
{
    public const string DELETED_AT_PROPERTY = "deletedAt";

    //TODO доработать остальные методы ActiveRecordEntity с учетом SoftDeletesTrait
    // получение записей должно исключать те записи, у которых deleted_at не равен null
    // добавить withTrashed для работы с мягкоудаленными записями
    // добавить scope для просмотра мягко удаленных записей тоже

    //TODO проверить почему какие-то уведомления срабатывают сами по себе

    //TODO надо добавить onlyTrashed() и withTrashed()

    //TODO добавить метод count() в ActiveRecordEntity
    // count() так же будет восприимчив к softDeletes

    /**
     * @return void
     * @throws DbException
     * @throws DeletedAtPropertyNotExistsException
     */
    public function delete(): void
    {
        if (!static::modelHasDeletedAtProperty()) {
            throw DeletedAtPropertyNotExistsException::buildMessage(static::class, static::DELETED_AT_PROPERTY);
        }

        $dateTime = date("Y-m-d H:i:s");
        $this->setUpdatedAt($dateTime);
        $this->setDeletedAt($dateTime);
        $this->save();
    }

    //TODO мне надо везде добавить try catch
    // сопоставить методы SoftDeletesTrait и ActiveRecordEntity по порядку

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

        $this->setUpdatedAt(date("Y-m-d H:i:s"));
        $this->setDeletedAt(null);
        $this->save();
    }

    /**
     * @return bool
     */
    public function trashed(): bool
    {
        return !is_null($this->deletedAt);
    }

    /**
     * @return string
     */
    protected static function softDeletes(): string
    {
        $scope = new SoftDeletingScope();

        return array_reduce(
            $scope(),
            fn($carry, $paramDto) => " AND `$paramDto->column` $paramDto->operator $paramDto->value",
            ""
        );
    }

    /**
     * @return bool
     */
    private static function modelHasDeletedAtProperty(): bool
    {
        return property_exists(static::class, static::DELETED_AT_PROPERTY);
    }
}