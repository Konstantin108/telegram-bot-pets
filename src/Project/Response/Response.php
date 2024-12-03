<?php

namespace Project\Response;

use Grpc\Call;
use Project\Dto\Telegram\CallbackQueryDto;
use Project\Dto\Telegram\MessageDto;
use Project\Dto\Telegram\MyChatMemberDto;

class Response
{
    public array $data;

    public function __construct()
    {
        $this->data = json_decode(file_get_contents("php://input"), true);
    }

    //TODO надо переделать создание типов респонсов в зависимости от того какой пришел ответ от бота
    // так же надо сделать с ответами от GoogleApi
    // работа с массивом только в Response - далее будут классы и дто
    // вынести логику для каждого бота в отдкльный класс
    /**
     * @return MessageDto|CallbackQueryDto|MyChatMemberDto|null
     */
    public function telegramData(): MessageDto|CallbackQueryDto|MyChatMemberDto|null
    {
        return match (true) {
            isset($this->data["message"]) => MessageDto::fromArray($this->data["message"]),
            isset($this->data["callback_query"]) => CallbackQueryDto::fromArray($this->data["callback_query"]),
            isset($this->data["my_chat_member"]) => MyChatMemberDto::fromArray($this->data["my_chat_member"]),
            default => null
        };
    }
}