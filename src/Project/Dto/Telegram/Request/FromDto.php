<?php

namespace Project\Dto\Telegram\Request;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class FromDto implements DtoInterface
{
    public int $id;
    public bool|null $isBot;
    public string $firstName;
    public string $lastName;
    public string $username;
    public string|null $languageCode;

    /**
     * @param int $id
     * @param bool|null $isBot
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string|null $languageCode
     */
    private function __construct(
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
    #[ArrayShape(shape: ["id" => "int", "isBot" => "bool|null", "firstName" => "string", "lastName" => "string", "username" => "string", "languageCode" => "null|string"])]
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "isBot" => $this->isBot,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "username" => $this->username,
            "languageCode" => $this->languageCode
        ];
    }

    /**
     * @param array $data
     * @return FromDto
     */
    public static function fromArray(array $data): FromDto
    {
        return new self(
            (int)$data["id"],
            $data["is_bot"],
            $data["first_name"],
            $data["last_name"],
            $data["username"],
            $data["language_code"]
        );
    }
}