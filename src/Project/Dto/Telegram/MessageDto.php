<?php

namespace Project\Dto\Telegram;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class MessageDto implements DtoInterface
{
    protected int $messageId;
    protected FromDto $from;
    protected ChatDto $chat;
    protected int $date;
    protected string $text;

    /**
     * @param int $messageId
     * @param FromDto $from
     * @param ChatDto $chat
     * @param int $date
     * @param string $text
     */
    public function __construct(
        int     $messageId,
        FromDto $from,
        ChatDto $chat,
        int     $date,
        string  $text
    )
    {
        $this->messageId = $messageId;
        $this->from = $from;
        $this->chat = $chat;
        $this->date = $date;
        $this->text = $text;
    }

    /**
     * @return array{message_id: int, from: array, chat: array, date: int, text: string}
     */
    #[ArrayShape(shape: ['message_id' => "int", 'from' => "array", 'chat' => "array", 'date' => "int", 'text' => "string"])]
    public function toArray(): array
    {
        return [
            'message_id' => $this->messageId,
            'from' => $this->from->toArray(),
            'chat' => $this->chat->toArray(),
            'date' => $this->date,
            'text' => $this->text
        ];
    }

    /**
     * @param array $data
     * @return MessageDto
     */
    public static function fromArray(array $data): MessageDto
    {
        return new self(
            (int)$data['message_id'],
            FromDto::fromArray($data['from']),
            ChatDto::fromArray($data['chat']),
            (int)$data['date'],
            $data['text']
        );
    }
}