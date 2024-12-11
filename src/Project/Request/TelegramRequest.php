<?php

namespace Project\Request;

use Project\Dto\Telegram\MessageDto;

class TelegramRequest extends Request
{
    //TODO так же надо сделать с ответами от GoogleApi
    // работа с массивом только в Request - далее будут классы и дто
    /**
     * @return MessageDto|null
     */
    public function body(): ?MessageDto
    {
        if (!is_null($this->data)) {
            return MessageDto::fromArray($this->data);
        }
        return null;
    }
}