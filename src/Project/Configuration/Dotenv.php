<?php

namespace Project\Configuration;

class Dotenv
{
    /**
     * @param string $path
     * @return void
     */
    public function load(string $path): void
    {
        foreach (file($path) as $line) {
            $line = trim($line);
            if (mb_strlen($line) < 1) {
                continue;
            }
            if (mb_substr($line, 0, 1, "UTF-8") === "#") {
                continue;
            }

            [$key, $value] = explode("=", $line);
            putenv(sprintf(
                "%s=%s",
                trim($key),
                trim($value)
            ));
        }
    }
}