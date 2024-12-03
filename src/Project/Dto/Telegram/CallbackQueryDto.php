<?php

namespace Project\Dto\Telegram;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;

class CallbackQueryDto implements DtoInterface
{
    protected string $id;
    protected FromDto $from;
    protected string $chatInstance;
    protected string $data;

    /**
     * @param string $id
     * @param FromDto $from
     * @param string $chatInstance
     * @param string $data
     */
    public function __construct(string $id, FromDto $from, string $chatInstance, string $data)
    {
        $this->id = $id;
        $this->from = $from;
        $this->chatInstance = $chatInstance;
        $this->data = $data;
    }

    /**
     * @return array{id: string, from: array, chat_instance: string, data: string}
     */
    #[ArrayShape(shape: ['id' => "string", 'from' => "array", 'chat_instance' => "string", 'data' => "string"])]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from->toArray(),
            'chat_instance' => $this->chatInstance,
            'data' => $this->data
        ];
    }

    /**
     * @param array $data
     * @return CallbackQueryDto
     */
    public static function fromArray(array $data): CallbackQueryDto
    {
        return new self(
            $data['id'],
            FromDto::fromArray($data['from']),
            $data['chat_instance'],
            $data['data']
        );
    }
}