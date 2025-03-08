<?php

namespace Project\Dotenv;

class Dotenv
{
    /**
     * @param string $path
     * @return void
     */
    public function load(string $path): void
    {
        foreach (file($path) as $line) {
            [$key, $value] = explode("=", $line);

            putenv(sprintf(
                "%s=%s",
                trim($key),
                trim($value)
            ));
        }
    }
}