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

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function aboutBot(InputDataDto $inputDataDto): void
    {
        $text = "Любимцы бот:\nЯ - простой бот, который умеет только показывать фотки шикарных котиков 😀";
        $this->telegram->sendMessage($text, $inputDataDto->from->id, Keyboard::DEFAULT);
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function commandsList(InputDataDto $inputDataDto): void
    {
        //TODO переработать синтаксис строк
        $text = "Привет, {$inputDataDto->from->firstName} {$inputDataDto->from->lastName}, вот команды, что я понимаю:"
            . "\n<b><i>Обо мне</i></b> - информация обо мне"
            . "\n<b><i>Список команд</i></b> - что я умею"
            . "\n<b><i>Курага</i></b> - показать фото Кураги"
            . "\n<b><i>Ватсон</i></b> - показать фото Ватсона"
            . "\n<b><i>Василиса</i></b> - показать фото Василисы";

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