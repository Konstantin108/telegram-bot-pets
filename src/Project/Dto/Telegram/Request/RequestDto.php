<?php

namespace Project\Dto\Telegram\Request;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;
use Project\Enums\User\UserStatusEnum;

class RequestDto implements DtoInterface
{
    public string|null $requestType;
    public int|null $messageId;
    public string|null $callbackId;
    public FromDto $from;
    public ChatDto $chat;
    public int $date;
    public string|null $text;
    public UserStatusEnum $status;
    public string|null $chatInstance;

    /**
     * @param string|null $requestType
     * @param int|null $messageId
     * @param string|null $callbackId
     * @param FromDto $from
     * @param ChatDto $chat
     * @param int $date
     * @param string|null $text
     * @param UserStatusEnum $status
     * @param string|null $chatInstance
     */
    private function __construct(
        ?string        $requestType,
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
        $this->requestType = $requestType;
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
     * @return array{request_type: null|string, message_id: int|null, callback_id: null|string, from: array, chat: array, date: int, text: null|string, status: string, chat_instance: null|string}
     */
    #[ArrayShape(shape: ["request_type" => "null|string", "message_id" => "int|null", "callback_id" => "null|string", "from" => "array", "chat" => "array", "date" => "int", "text" => "null|string", "status" => "string", "chat_instance" => "null|string"])]
    public function toArray(): array
    {
        return [
            "request_type" => $this->requestType,
            "message_id" => $this->messageId,
            "callback_id" => $this->callbackId,
            "from" => $this->from->toArray(),
            "chat" => $this->chat->toArray(),
            "date" => $this->date,
            "text" => $this->text,
            "status" => $this->status->value,
            "chat_instance" => $this->chatInstance
        ];
    }

    /**
     * @param array $data
     * @return RequestDto
     */
    public static function fromArray(array $data): RequestDto
    {
        if (isset($data["message"])) {
            $requestType = "message";
            $data = $data["message"];
        } elseif (isset($data["callback_query"])) {
            $requestType = "callback_query";
            $data = $data["callback_query"];
        } elseif (isset($data["my_chat_member"])) {
            $requestType = "my_chat_member";
            $data = $data["my_chat_member"];
        }

        return new self(
            $requestType ?? null,
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