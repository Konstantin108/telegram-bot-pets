<?php

namespace Project\Dto\Telegram;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;
use Project\Enums\User\UserStatusEnum;

class MessageDto implements DtoInterface
{
    public int|null $messageId;
    public string|null $callbackId;
    public FromDto $from;
    public ChatDto $chat;
    public int $date;
    public string|null $text;
    public UserStatusEnum $status;
    public string|null $chatInstance;

    /**
     * @param int|null $messageId
     * @param string|null $callbackId
     * @param FromDto $from
     * @param ChatDto $chat
     * @param int $date
     * @param string|null $text
     * @param UserStatusEnum $status
     * @param string|null $chatInstance
     */
    public function __construct(
        ?int           $messageId,
        ?string        $callbackId,
        FromDto        $from,
        ChatDto        $chat,
        int            $date,
        ?string        $text,
        UserStatusEnum $status,
        ?string        $chatInstance
    )
    {
        $this->messageId = $messageId;
        $this->callbackId = $callbackId;
        $this->from = $from;
        $this->chat = $chat;
        $this->date = $date;
        $this->text = $text;
        $this->status = $status;
        $this->chatInstance = $chatInstance;
    }

    /**
     * @return array{message_id: int|null, callback_id: null|string, from: array, chat: array, date: int, text: null|string, status: string, chat_instance: null|string}
     */
    #[ArrayShape(shape: ["message_id" => "int|null", "callback_id" => "null|string", "from" => "array", "chat" => "array", "date" => "int", "text" => "null|string", "status" => "string", "chat_instance" => "null|string"])]
    public function toArray(): array
    {
        return [
            "message_id" => $this->messageId,
            "callback_id" => $this->callbackId,
            "from" => $this->from->toArray(),
            "chat" => $this->chat->toArray(),
            "date" => $this->date,
            "text" => $this->text,
            "status" => $this->status->value,
            "chat_instance" => $this->chatInstance,
        ];
    }

    /**
     * @param array $data
     * @return MessageDto
     */
    public static function fromArray(array $data): MessageDto
    {
        $data = $data["message"] ?? $data["callback_query"] ?? $data["my_chat_member"];

        return new self(
            isset($data["message_id"])
                ? (int)$data["message_id"]
                : null,
            $data["id"] ?? null,
            FromDto::fromArray($data["from"]),
            ChatDto::fromArray($data["chat"] ?? $data["message"]["chat"]),
            (int)$data["date"] ?? (int)$data["message"]["date"],
            self::processText($data),
            isset($data["new_chat_member"]["status"])
                ? UserStatusEnum::from($data["new_chat_member"]["status"])
                : UserStatusEnum::MEMBER,
            $data["chat_instance"] ?? null
        );
    }

    /**
     * @param array $data
     * @return string|null
     */
    private static function processText(array $data): ?string
    {
        if (isset($data["text"])) {
            return mb_strtolower($data["text"]);
        } elseif (isset($data["data"])) {
            return mb_strtolower($data["data"]);
        }
        return null;
    }
}