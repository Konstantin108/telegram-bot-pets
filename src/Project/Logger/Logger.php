<?php

namespace Project\Logger;

use Project\Traits\SingletonTrait as HasSingleton;

class Logger
{
    use HasSingleton;

    private bool $writeLog;
    private string $logFile;
    private string $debugLogFile;

    private function __construct()
    {
        $logSettings = (require __DIR__ . "/../../config.php")["log"];

        $this->writeLog = $logSettings["writeLog"];
        $this->logFile = $logSettings["logFile"];
        $this->debugLogFile = $logSettings["debugLogFile"];
    }

    /**
     * @param string $data
     * @return void
     */
    public static function log(string $data): void
    {
        $logger = self::create();

        if ($logger->isWriteLog()) {
            $logger->write($data, true, $logger->logFile);
        }
    }

    /**
     * @param mixed $data
     * @param string|null $path
     * @param bool $formatWithDate
     * @return void
     */
    public static function debug(
        mixed   $data,
        ?string $path = null,
        bool    $formatWithDate = false
    ): void
    {
        $logger = self::create();
        $logger->write($data, $formatWithDate, $path ?? $logger->debugLogFile);
    }

    //TODO возможно после изменения конфига сделать статичным класс Logger

    /**
     * @return Logger
     */
    private static function create(): Logger
    {
        return static::getInstance();
    }

    /**
     * @return bool
     */
    private function isWriteLog(): bool
    {
        return $this->writeLog;
    }

    /**
     * @param mixed $data
     * @param bool $formatWithDate
     * @param string $path
     * @return void
     */
    private function write(
        mixed  $data,
        bool   $formatWithDate,
        string $path
    ): void
    {
        if (!is_string($data)) {
            $data = print_r($data, true);
        }

        if ($formatWithDate) {
            $data = PHP_EOL
                . "------------------------------ "
                . date("Y-m-d H:i:s")
                . " ------------------------------"
                . PHP_EOL
                . $data
                . PHP_EOL;
        }

        error_log($data, 3, $path);
    }
}