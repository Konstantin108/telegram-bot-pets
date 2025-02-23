<?php

namespace Project\Logger;

class Logger
{
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

    //TODO возможно после изменения конфига сделать статичным класс Logger

    /**
     * @return Logger
     */
    public static function create(): Logger
    {
        return new self();
    }

    /**
     * @param string $data
     * @return void
     */
    public function log(string $data): void
    {
        if ($this->writeLog) {
            $this->write($data, true, $this->logFile);
        }
    }

    /**
     * @param mixed $data
     * @param string|null $path
     * @param bool $formatWithDate
     * @return void
     */
    public function debug(
        mixed   $data,
        ?string $path = null,
        bool    $formatWithDate = false
    ): void
    {
        $this->write($data, $formatWithDate, $path ?? $this->debugLogFile);
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