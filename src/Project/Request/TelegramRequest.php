<?php

namespace Project\Request;

use Project\Dto\Telegram\Request\RequestDto;

class TelegramRequest extends Request
{
    //TODO так же надо сделать с ответами от GoogleApi
    // работа с массивом только в Request - далее будут классы и дто
    /**
     * @param bool $withRaw
     * @return RequestDto|null
     */
    public function body(bool $withRaw = false): ?RequestDto
    {
        if (is_null($this->input)) {
            return null;
        }

        $data = json_decode($this->input, true);
        if ($withRaw) {
            $input = $this->input;
            $data["raw_input"] = $input;
        }

        return RequestDto::fromArray($data);
    }
}