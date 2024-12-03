<?php

namespace Project\Dto\Telegram;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class FromDto implements DtoInterface
{
    protected int $id;
    protected bool|null $isBot;
    protected string $firstName;
    protected string $lastName;
    protected string $username;
    protected string|null $languageCode;

    /**
     * @param int $id
     * @param bool|null $isBot
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string|null $languageCode
     */
    public function __construct(
        int         $id,
        bool|null   $isBot,
        string      $firstName,
        string      $lastName,
        string      $username,
        string|null $languageCode
    )
    {
        $this->id = $id;
        $this->isBot = $isBot;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->languageCode = $languageCode;
    }

    /**
     * @return array{id: int, isBot: bool|null, firstName: string, lastName: string, username: string, languageCode: null|string}
     */
    #[ArrayShape(shape: ['id' => "int", 'isBot' => "bool|null", 'firstName' => "string", 'lastName' => "string", 'username' => "string", 'languageCode' => "null|string"])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'isBot' => $this->isBot,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'username' => $this->username,
            'languageCode' => $this->languageCode,
        ];
    }

    /**
     * @param array $data
     * @return FromDto
     */
    public static function fromArray(array $data): FromDto
    {
        return new self(
            (int)$data['id'],
            $data['is_bot'],
            $data['first_name'],
            $data['last_name'],
            $data['username'],
            $data['language_code']
        );
    }
}