<?php

namespace Project\Models\Users;

use Project\Enums\User\UserStatusEnum;
use Project\Models\ActiveRecordEntity;

class User extends ActiveRecordEntity
{
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
     */
    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    /**
     * @param bool|null $isBot
     */
    public function setIsBot(?bool $isBot): void
    {
        $this->isBot = $isBot;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @param UserStatusEnum $status
     */
    public function setStatus(UserStatusEnum $status): void
    {
        $this->status = $status->value;
    }

    /**
     * @param string|null $languageCode
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
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    protected static function getTableName(): string
    {
        return "users";
    }
}