<?php

namespace Project\Services\Connection;

use CurlHandle;
use Project\Exceptions\ConnException;

class Conn
{
    public const string ERROR = "Ошибка в URL";
    private false|CurlHandle $conn;
    private string $url;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->conn = curl_init();
        $this->url = $url;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ConnException
     */
    public function post(array $data): mixed
    {
        return $this->processResult([
            CURLOPT_URL => $this->url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT_MS => 6000
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ConnException
     */
    public function get(array $data): mixed
    {
        return $this->processResult([
            CURLOPT_URL => $this->url . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true
        ]);
    }

    /**
     * @param array $options
     * @return mixed
     * @throws ConnException
     */
    private function processResult(array $options): mixed
    {
        $result = $this->exec($options);

        if ($message = curl_error($this->conn)) {
            throw new ConnException($message);
        }

        if (!$result) {
            throw new ConnException(self::ERROR);
        }

        return json_decode($result, true);
    }

    /**
     * @param array $data
     * @return bool|string
     */
    private function exec(array $data): bool|string
    {
        curl_setopt_array($this->conn, $data);
        return curl_exec($this->conn);
    }
}