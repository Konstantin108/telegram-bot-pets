<?php

namespace Project\Controllers\Pets;

use Project\Dto\Telegram\Request\InputDataDto;
use Project\Services\Pets\MessageService;

class MessageController
{
    public MessageService $messageService;

    //TODO организовать DI
    public function __construct()
    {
        $this->messageService = new MessageService();
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
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