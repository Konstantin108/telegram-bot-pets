<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

use Project\Telegram\Telegram;
use Project\Exceptions\ConnException;
use Project\GoogleApi\GoogleTranslator;

spl_autoload_register(function ($className): void {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . "/../../src/$className.php";
});

$token = (include __DIR__ . "/../../src/config.php")["bots"]["googleTranslateBot"]["token"];

$text = "";
$from = null;

$telegram = new Telegram($token);

$keyboard = [
    "keyboard" => [
        [
            ["text" => "ℹ Обо мне"],
        ]
    ],
    "resize_keyboard" => true
];

$request = file_get_contents("php://input");

if (is_null($request = json_decode($request, true))) return;

try {
    $request = $request["message"];

    //TODO избавиться от этого
    $from = (object)$request["from"];
    $text = $request["text"];

    if (!$text) return;

    $text = mb_strtolower($text);

    switch ($text) {
        case "/start":
            $telegram->sendMessage("Бот активирован", $from->id, json_encode($keyboard));
            break;
        case "ℹ обо мне":
            aboutBot($from->id, $telegram, $keyboard);
            break;
        default:
            $translatedText = GoogleTranslator::create()->translate($text);
            $telegram->sendMessage($translatedText, $from->id, json_encode($keyboard));
            break;
    }

} catch (ConnException $e) {
    $e->show();
}

/**
 * @param string $chatId
 * @param Telegram $telegram
 * @param array $replyMarkup
 * @return void
 * @throws ConnException
 */
function aboutBot(string $chatId, Telegram $telegram, array $replyMarkup): void
{
    $text = "Умею переводить текст с английского языка на русский"
        . "\nДля перевода использую API Google Translate"
        . "\n\nПросто отправь мне текст на английском языке, и я тут же его переведу 👍";
    $telegram->sendMessage($text, $chatId, json_encode($replyMarkup));
}