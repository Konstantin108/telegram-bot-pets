<?php

namespace Project\Dto\Telegram\Request;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;
use Project\Enums\User\UserStatusEnum;

class InputDataDto implements DtoInterface
{
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
     * @param string|null $rawInput
     */
    private function __construct(
        public ?string        $requestType,
        public ?int           $messageId,
        public ?string        $callbackId,
        public FromDto        $from,
        public ChatDto        $chat,
        public int            $date,
        public ?string        $text,
        public UserStatusEnum $status,
        public ?string        $chatInstance,
        public ?string        $rawInput
    )
    {
    }

    /**
     * @return array{request_type: null|string, message_id: int|null, callback_id: null|string, from: array, chat: array, date: int, text: null|string, status: string, chat_instance: null|string, raw_input: null|string}
     */
    #[ArrayShape(shape: ["request_type" => "null|string", "message_id" => "int|null", "callback_id" => "null|string", "from" => "array", "chat" => "array", "date" => "int", "text" => "null|string", "status" => "string", "chat_instance" => "null|string", "raw_input" => "null|string"])]
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
            "chat_instance" => $this->chatInstance,
            "raw_input" => $this->rawInput
        ];
    }

    /**
     * @param array $data
     * @return InputDataDto
     */
    public static function fromArray(array $data): InputDataDto
    {
        $rawInput = $data["raw_input"] ?? null;

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
            requestType: $requestType ?? null,
            messageId: isset($data["message_id"])
                ? (int)$data["message_id"]
                : null,
            callbackId: $data["id"] ?? null,
            from: FromDto::fromArray($data["from"]),
            chat: ChatDto::fromArray($data["chat"] ?? $data["message"]["chat"]),
            date: (int)$data["date"] ?? (int)$data["message"]["date"],
            text: self::processText($data),
            status: isset($data["new_chat_member"]["status"])
                ? UserStatusEnum::from($data["new_chat_member"]["status"])
                : UserStatusEnum::MEMBER,
            chatInstance: $data["chat_instance"] ?? null,
            rawInput: $rawInput
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