<?php

namespace Project\Dto\Telegram\Response;

use Project\Dto\Telegram\Response\ResponseDto;

class LogDataDto
{
    /**
     * @param ResponseDto $response
     * @param array $messageData
     * @param string $method
     */
    public function __construct(
        public ResponseDto $response,
        public array       $messageData,
        public string      $method
    )
    {
    }
}