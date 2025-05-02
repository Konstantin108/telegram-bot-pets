<?php

namespace Project\Configuration;

use Project\Traits\SingletonTrait as HasSingleton;

class Config
{
    use HasSingleton;

    private Dotenv $dotenv;
    private array $configurations;

    //TODO возможно пути до файлов должны быть в константах
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
     * @return void
     */
    private function configuration(): void
    {
        $path = __DIR__ . "/../../../config";
        if (count($configs = glob($path . "/*.php")) < 1) {
            return;
        }

        foreach ($configs as $config) {
            $configKey = basename($config, ".php");
            $this->configurations[$configKey] = require $config;
        }
    }

    /**
     * @param string $key
     * @param string|int|float|null $defaultValue
     * @return mixed
     */
    public static function get(string $key, string|int|float|null $defaultValue = null): mixed
    {
        return static::create()->getConfigParamByKey($key) ?: $defaultValue;
    }

    /**
     * @return Config
     */
    private static function create(): Config
    {
        return static::getInstance();
    }
}