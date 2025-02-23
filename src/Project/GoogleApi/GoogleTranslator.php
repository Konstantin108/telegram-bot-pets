<?php

namespace Project\GoogleApi;

use Project\Exceptions\ConnException;
use Project\Services\Conn;

class GoogleTranslator
{
    private string $url;
    private array $options;

    private function __construct()
    {
        $this->url = (require __DIR__ . "/../../config.php")["bots"]["googleTranslateBot"]["googleApiUrl"];
        $this->options = [
            "client" => "gtx",
            "sl" => "en",
            "tl" => "ru",
            "dt" => "t"
        ];
    }

    //TODO переработать, возможно добавить Dto

    /**
     * @param string $text
     * @return mixed
     * @throws ConnException
     */
    public function translate(string $text): mixed
    {
        $data = array_merge($this->options, ["q" => $text]);
        $response = (new Conn(url: $this->url))->get($data);

        return array_reduce(
            array_shift($response),
            fn($accumulator, $item) => $accumulator . $item[0]
        );
    }

    /**
     * @return GoogleTranslator
     */
    public static function create(): GoogleTranslator
    {
        return new self();
    }
}