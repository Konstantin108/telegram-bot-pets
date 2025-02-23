<?php

namespace Project\Controllers\Pets;

use Project\Dto\Telegram\Request\InputDataDto;

class MessageController
{
    /**
     * @param InputDataDto $data
     * @return void
     */
    public function showCatKuragaImage(InputDataDto $data): void
    {
        //TODO пока это просто тест
        error_log(print_r($data, true), 3, "msg.txt");
    }
}