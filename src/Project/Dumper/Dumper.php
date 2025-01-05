<?php

namespace Project\Dumper;

class Dumper
{
    /**
     * @param mixed ...$data
     * @return void
     */
    public static function dump(mixed ...$data): void
    {
        self::process(
            debug_backtrace(),
            array_map(fn($item) => $item, $data)
        );
    }

    /**
     * @param mixed ...$data
     * @return void
     */
    public static function dd(mixed ...$data): void
    {
        self::process(
            debug_backtrace(),
            array_map(fn($item) => $item, $data),
            true
        );
    }

    /**
     * @param array $backtrace
     * @param array $data
     * @param bool $die
     * @return void
     */
    private static function process(array $backtrace, array $data, bool $die = false): void
    {
        if (count($data) > 0) {
            foreach ($data as $element) {
                self::printData($backtrace, $element);
            }
        } else {
            self::printBacktrace($backtrace);
        }

        if ($die) {
            die();
        }
    }

    /**
     * @param array $backtrace
     * @return void
     */
    private static function printBacktrace(array $backtrace): void
    {
        print_r($backtrace[0]["file"] . ":" . $backtrace[0]["line"]);
    }

    /**
     * @param array $backtrace
     * @param mixed $element
     * @return void
     */
    private static function printData(array $backtrace, mixed $element): void
    {
        self::printBacktrace($backtrace);

        echo "<pre>";
        var_dump($element);
        echo "</pre>";
    }
}