<?php

namespace Project\Dto\Telegram\Response;

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