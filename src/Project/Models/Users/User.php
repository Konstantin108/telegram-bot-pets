<?php

namespace Project\Models\Users;

use Project\Enums\User\UserStatusEnum;
use Project\Models\ActiveRecordEntity;
use Project\Traits\SoftDeletesTrait as HasSoftDeletes;

class User extends ActiveRecordEntity
{
    use HasSoftDeletes;

    private const string TABLE = "users";
    private const array GUARDED = ["id"];
    private const string PRIMARY_KEY = "id";
    protected int $id;
    protected string $chatId;
    protected bool|null $isBot;
    protected string|null $firstName;
    protected string|null $lastName;
    protected string|null $username;
    protected bool $isAdmin;
    protected bool $isTest;
    protected string $status;
    protected bool|null $notification;
    protected string|null $languageCode;
    protected string|null $createdAt;
    protected string|null $updatedAt;
    protected string|null $deletedAt;

    //TODO возможно переработать, убрать лишние методы

    /**
     * @param string $chatId
     * @return void
     */
    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    //TODO у перезаписываемых методов можно добавить атрибут #[\Override]
    // надо добавить и другие использования атрибутов

    /**
     * @param bool|null $isBot
     * @return void
     */
    public function setIsBot(?bool $isBot): void
    {
        $this->isBot = $isBot;
    }

    /**
     * @param string|null $firstName
     * @return void
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string|null $lastName
     * @return void
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string|null $username
     * @return void
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @param bool $isAdmin
     * @return void
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @param UserStatusEnum $status
     * @return void
     */
    public function setStatus(UserStatusEnum $status): void
    {
        $this->status = $status->value;
    }

    /**
     * @param string|null $languageCode
     * @return void
     */
    public function setLanguageCode(?string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string|null $deletedAt
     * @return void
     */
    public function setDeletedAt(?string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return string
     */
    public function getChatId(): string
    {
        return $this->chatId;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    protected static function table(): string
    {
        return static::TABLE;
    }

    /**
     * @return array|string[]
     */
    protected static function guarded(): array
    {
        return static::GUARDED;
    }

    /**
     * @return string
     */
    protected static function primaryKey(): string
    {
        return static::PRIMARY_KEY;
    }
}