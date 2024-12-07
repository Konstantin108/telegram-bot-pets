<?php

namespace Project\Dto\Telegram;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class ChatDto implements DtoInterface
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $username;
    public string $type;

    /**
     * @param int $id
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $type
     */
    private function __construct(int $id, string $firstName, string $lastName, string $username, string $type)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->type = $type;
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
            (int)$data["id"],
            $data["first_name"],
            $data["last_name"],
            $data["username"],
            $data["type"]
        );
    }
}