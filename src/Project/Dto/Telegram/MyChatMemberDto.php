<?php

namespace Project\Dto\Telegram;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class MyChatMemberDto implements DtoInterface
{
    protected ChatDto $chat;
    protected FromDto $from;
    protected int $date;
    protected string $status;

    /**
     * @param ChatDto $chat
     * @param FromDto $from
     * @param int $date
     * @param string $status
     */
    public function __construct(ChatDto $chat, FromDto $from, int $date, string $status)
    {
        $this->chat = $chat;
        $this->from = $from;
        $this->date = $date;
        $this->status = $status;
    }

    /**
     * @return array{chat: array, from: array, date: int, status: string}
     */
    #[ArrayShape(shape: ['chat' => "array", 'from' => "array", 'date' => "int", 'status' => "string"])]
    public function toArray(): array
    {
        return [
            'chat' => $this->chat->toArray(),
            'from' => $this->from->toArray(),
            'date' => $this->date,
            'status' => $this->status,
        ];
    }

    /**
     * @param array $data
     * @return MyChatMemberDto
     */
    public static function fromArray(array $data): MyChatMemberDto
    {
        return new self(
            ChatDto::fromArray($data['chat']),
            FromDto::fromArray($data['from']),
            $data['date'],
            $data['new_chat_member']['status']
        );
    }
}