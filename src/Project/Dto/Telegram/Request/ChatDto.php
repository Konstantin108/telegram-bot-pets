<?php

namespace Project\Dto\Telegram\Request;

use JetBrains\PhpStorm\ArrayShape;
use Project\Interfaces\DtoInterface;

class ChatDto implements DtoInterface
{
    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $type
     */
    private function __construct(
        public int    $id,
        public string $firstName,
        public string $lastName,
        public string $username,
        public string $type
    )
    {
    }

    /**
     * @return array{id: int, firstName: string, lastName: string, username: string, type: string}
     */
    #[ArrayShape(shape: ["id" => "int", "firstName" => "string", "lastName" => "string", "username" => "string", "type" => "string"])]
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "username" => $this->username,
            "type" => $this->type
        ];
    }

    /**
     * @param array $data
     * @return ChatDto
     */
    public static function fromArray(array $data): ChatDto
    {
        return new self(
            id: (int)$data["id"],
            firstName: $data["first_name"],
            lastName: $data["last_name"],
            username: $data["username"],
            type: $data["type"]
        );
    }
}