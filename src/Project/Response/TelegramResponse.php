<?php

namespace Project\Response;

use Project\Dto\Telegram\MessageDto;

class TelegramResponse extends Response
{
    public function __construct()
    {
        parent::__construct();
    }

    //TODO так же надо сделать с ответами от GoogleApi
    // работа с массивом только в Response - далее будут классы и дто
    /**
     * @return MessageDto|null
     */
    public function body(): ?MessageDto
    {
        if ($this->data) {
            return MessageDto::fromArray($this->data);
        }
        return null;
    }
}