<?php

namespace Project\GoogleApi;

use Project\Exceptions\ConnException;
use Project\Services\Connection\Conn;
use Project\Traits\SingletonTrait as HasSingleton;

class GoogleTranslator
{
    use HasSingleton;

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
        $response = (new Conn($this->url))->get($data);

        return array_reduce(
            array_shift($response),
            fn(string $carry, mixed $item) => $carry . $item[0],
            ""
        );
    }

    /**
     * @return GoogleTranslator
     */
    public static function create(): GoogleTranslator
    {
        return static::getInstance();
    }
}