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
        //TODO исправить так как обновлен Conn
        $data = array_merge($this->options, ["q" => $text]);
        $response = (new Conn($this->url))->getResult($data, "get");

        return array_reduce(array_shift($response), function ($a, $b) {
            return $a . $b[0];
        });
    }

    /**
     * @return GoogleTranslator
     */
    public static function create(): GoogleTranslator
    {
        return new self();
    }
}