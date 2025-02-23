<?php

namespace Project\Dto\Telegram\Request;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class FromDto implements DtoInterface
{
    /**
     * @param int $id
     * @param bool|null $isBot
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string|null $languageCode
     */
    private function __construct(
        public int         $id,
        public bool|null   $isBot,
        public string      $firstName,
        public string      $lastName,
        public string      $username,
        public string|null $languageCode
    )
    {
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
            id: (int)$data["id"],
            isBot: $data["is_bot"],
            firstName: $data["first_name"],
            lastName: $data["last_name"],
            username: $data["username"],
            languageCode: $data["language_code"]
        );
    }
}