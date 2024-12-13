<?php

namespace Project\Request;

use Project\Dto\Telegram\Request\RequestDto;

class TelegramRequest extends Request
{
    //TODO так же надо сделать с ответами от GoogleApi
    // работа с массивом только в Request - далее будут классы и дто
    /**
     * @return RequestDto|null
     */
    public function body(): ?RequestDto
    {
        if (!is_null($this->data)) {
            return RequestDto::fromArray($this->data);
        }
        return null;
    }
}