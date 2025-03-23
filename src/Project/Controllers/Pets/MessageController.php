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
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function aboutBot(InputDataDto $inputDataDto): void
    {
        $this->messageService->aboutBot($inputDataDto);
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function commandsList(InputDataDto $inputDataDto): void
    {
        $this->messageService->commandsList($inputDataDto);
    }

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws ConnException
     */
    public function useButtonsMessage(InputDataDto $inputDataDto): void
    {
        $this->messageService->useButtonsMessage($inputDataDto);
    }
}