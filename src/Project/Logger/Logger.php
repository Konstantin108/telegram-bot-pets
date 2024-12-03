<?php

namespace Project\Logger;

use stdClass;

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
     * @param string|array|stdClass $data
     * @param bool $formatWithDate
     * @return void
     */
    public function debug(string|array|stdClass $data, bool $formatWithDate = false): void
    {
        $this->write($data, $formatWithDate, $this->debugLogFile);
    }

    /**
     * @param string|array|stdClass $data
     * @param bool $formatWithDate
     * @param string $path
     * @return void
     */
    private function write(string|array|stdClass $data, bool $formatWithDate, string $path): void
    {
        if(!is_string($data)){
            $data = print_r($data, true);
        }

        if($formatWithDate){
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

    /**
     * @return Logger
     */
    public static function create(): Logger
    {
        return new self();
    }
}