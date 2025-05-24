<?php

namespace Project\Dto\Telegram\Request;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;
use Project\Interfaces\DtoInterface;

#[Immutable]
class ChatDto implements DtoInterface
{
    /**
     * @param int $id
     * @param string $firstName
     * @param string|null $lastName
     * @param string $username
     * @param string $type
     */
    private function __construct(
        public int     $id,
        public string  $firstName,
        public ?string $lastName,
        public string  $username,
        public string  $type
    )
    {
    }

    /**
     * @return array
     */
    #[ArrayShape(shape: ["id" => "int", "firstName" => "string", "lastName" => "null|string", "username" => "string", "type" => "string"])]
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
            lastName: $data["last_name"] ?? null,
            username: $data["username"],
            type: $data["type"]
        );
    }
}