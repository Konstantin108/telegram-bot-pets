<?php

namespace Project\Services\Pets;

use Project\Configuration\Config;
use Project\Dto\Telegram\Request\InputDataDto;
use Project\Exceptions\ConnException;
use Project\Keyboards\Pets\Keyboard;
use Project\Telegram\Telegram;

class MessageService
{
    //TODO проверить все константы долдны быть в начале классов
    public Telegram $telegram;

    public function __construct()
    {
        $this->telegram = new Telegram(Config::get("telegram.bots.pets.token"));
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function startBot(InputDataDto $inputDataDto): void
    {
        $this->telegram->sendMessage("Бот активирован", $inputDataDto->from->id, Keyboard::DEFAULT);
    }

    /**
     * @return void
     */
    public function useButtonsMessage(): void
    {
        //
    }
}