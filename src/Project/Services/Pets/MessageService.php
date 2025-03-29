<?php

namespace Project\Services\Pets;

use Project\Configuration\Config;
use Project\Dto\Telegram\Request\InputDataDto;
use Project\Exceptions\ConnException;
use Project\Keyboards\Pets\Keyboard;
use Project\Telegram\Telegram;

class MessageService
{
    private Telegram $telegram;

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

    //TODO будут не только коты, надо это учесть

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function aboutBot(InputDataDto $inputDataDto): void
    {
        $text = "Любимцы бот:\nЯ - простой бот, который умеет показывать фотки любимцов 😀";
        $this->telegram->sendMessage($text, $inputDataDto->from->id, Keyboard::DEFAULT);
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function useButtonsMessage(InputDataDto $inputDataDto): void
    {
        $this->telegram->sendMessage("Используй кнопки с командами", $inputDataDto->from->id, Keyboard::DEFAULT);
    }
}