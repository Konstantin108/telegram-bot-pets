<?php

namespace Project\Controllers\Pets;

use Project\Dto\Telegram\Request\InputDataDto;
use Project\Exceptions\ConnException;
use Project\Services\Pets\MessageService;

class MessageController
{
    private MessageService $messageService;

    //TODO организовать DI
    public function __construct()
    {
        $this->messageService = new MessageService();
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function startBot(InputDataDto $inputDataDto): void
    {
        $this->messageService->startBot($inputDataDto);
    }

    /**
     * @return void
     */
    public function useButtonsMessage(): void
    {
        //
    }
}