<?php

namespace Project\Configuration;

use Project\Traits\SingletonTrait;

class Config
{
    use SingletonTrait;

    private Dotenv $dotenv;
    private array $configurations;

    //TODO возможно путь до файлов должны быть в константах
    private function __construct()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load(__DIR__ . "/../../../.env");
        $this->configuration();
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getConfigParamByKey(string $key): mixed
    {
        $keys = explode(".", $key);
        $current = $this->configurations;

        foreach ($keys as $subKey) {
            if (isset($current[$subKey])) {
                $current = $current[$subKey];
            } else {
                return null;
            }
        }

        return $current;
    }

    /**
     * @return void|null
     */
    private function configuration()
    {
        $path = __DIR__ . "/../../../config";
        if (count($configs = glob($path . "/*.php")) < 1) {
            return null;
        }

        foreach ($configs as $config) {
            $configKey = basename($config, ".php");
            $this->configurations[$configKey] = require $config;
        }
    }


    /**
     * @param string $key
     * @return mixed
     */
    public static function get(string $key): mixed
    {
        return static::create()->getConfigParamByKey($key);
    }

    /**
     * @return Config
     */
    private static function create(): Config
    {
        return static::getInstance();
    }
}