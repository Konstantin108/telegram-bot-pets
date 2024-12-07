<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\Telegram\FromDto;
use Project\Dto\Telegram\MessageDto;
use Project\Exceptions\TypeErrorException;
use Project\Exceptions\ConnException;
use Project\Exceptions\DbException;
use Project\Controllers\UserController;
use Project\Response\TelegramResponse;
use Project\Scopes\MembersWithNotificationScope;
use Project\Telegram\Telegram;
use Project\Models\Users\User;
use Project\Exceptions\AccessModifiersException;

spl_autoload_register(function ($className): void {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . "/../../src/$className.php";
});

$config = (include __DIR__ . "/../../src/config.php")["bots"]["pets"];

$allowExtensionsArray = $config["allowExtensionsArray"];
$cats = $config["cats"];
$token = $config["token"];
$from = null;

$telegram = new Telegram($token);

$defaultKeyboard = [
    "keyboard" => [
        [
            ["text" => "Обо мне"],
            ["text" => "Список команд"]
        ],
        [
            ["text" => "Курага"],
            ["text" => "Ватсон"],
            ["text" => "Василиса"]
        ]
    ],
    "resize_keyboard" => true
];

/** @var MessageDto $response */
$response = (new TelegramResponse())->body();

try {
    if (!is_null($response)) {
        //TODO так быть не должно надо выносить в методы
        $from = $response->from;

        try {
            (new UserController())->writeUserDataToDB($response);
        } catch (TypeErrorException $e) {
            $e->showError();
        }

        if (is_null($response->text)) return;

        switch ($response->text) {
            //TODO возможно вынести названия действий в константы
            case "/start":
                $telegram->sendMessage("Бот активирован", $response->from->id, json_encode($defaultKeyboard));
                break;
            case "обо мне":
                aboutBot($response->from->id, $telegram, $defaultKeyboard);
                break;
            case "список команд":
                commandsList($response->from, $telegram, $defaultKeyboard);
                break;
            case "курага":
            case "ватсон":
            case "василиса":
                $telegram->sendChatAction($response->from->id, "upload_photo");
                $photoData = getRandomPhoto($cats[$response->text]["en_nom"], $allowExtensionsArray);
                showCatImage($response->from->id, $telegram, $photoData);

                if (!in_array($response->from->id, $config["adminChatIds"])) {
                    foreach ($config["adminChatIds"] as $oneAdminChatId) {
                        $notifyForAdmin = "$from->firstName $from->lastName сейчас любуется {$cats[$response->text]["ru_ins"]}"
                            . "\nПоказано это замечательное фото 🤩";

                        $telegram->sendMessage($notifyForAdmin, $oneAdminChatId, json_encode($defaultKeyboard));
                        showCatImage($oneAdminChatId, $telegram, $photoData, $defaultKeyboard);
                    }
                }
                break;
            // callback действия
            case "like":
            case "unlike":
                sendReaction($response->text, $telegram, $response->callbackId);
                sendReactionToAdmin($response->text, $from, $telegram, $config, $defaultKeyboard);
                break;
            default:
                $telegram->sendMessage("Используй кнопки с командами", $from->id, json_encode($defaultKeyboard));
                break;
        }
    } else {
        // массовое уведомление
        if ($users = User::filter(new MembersWithNotificationScope())) {
            echo "<pre>";
            var_dump($users);
            $dailyPhotoData = getImageForDailyNotification($allowExtensionsArray, $cats);
            foreach ($users as $user) {
                $dailyNotifyMessage = "Скучаешь, {$user->getFirstName()} {$user->getLastName()}? Вот полюбуйся!";
                $telegram->sendMessage($dailyNotifyMessage, $user->getChatId(), json_encode($defaultKeyboard));
                showCatImage($user->getChatId(), $telegram, $dailyPhotoData);
            }
        }
    }

} catch (ConnException|DbException|AccessModifiersException $e) {
    $e->showError();
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
    $text = "Любимцы бот:\nЯ - простой бот, который умеет только показывать фотки шикарных котиков 😀";
    $telegram->sendMessage($text, $chatId, json_encode($replyMarkup));
}

/**
 * @param FromDto $from
 * @param Telegram $telegram
 * @param array $replyMarkup
 * @return void
 * @throws ConnException
 */
function commandsList(FromDto $from, Telegram $telegram, array $replyMarkup): void
{
    $text = "Привет, $from->firstName $from->lastName, вот команды, что я понимаю:"
        . "\n<b><i>Обо мне</i></b> - информация обо мне"
        . "\n<b><i>Список команд</i></b> - что я умею"
        . "\n<b><i>Курага</i></b> - показать фото Кураги"
        . "\n<b><i>Ватсон</i></b> - показать фото Ватсона"
        . "\n<b><i>Василиса</i></b> - показать фото Василисы";

    $telegram->sendMessage($text, $from->id, json_encode($replyMarkup));
}

/**
 * @param string $chatId
 * @param Telegram $telegram
 * @param array $photoData
 * @param array|null $replyMarkup
 * @return void
 * @throws ConnException
 */
function showCatImage(string $chatId, Telegram $telegram, array $photoData, null|array $replyMarkup = null): void
{
    $replyMarkup ??= [
        "inline_keyboard" => [
            [
                [
                    "text" => "👍",
                    "callback_data" => "like"
                ],
                [
                    "text" => "👎",
                    "callback_data" => "unlike"
                ]
            ]
        ]
    ];

    $telegram->sendPhoto($photoData, $chatId, json_encode($replyMarkup));
}

/**
 * @param string $catName
 * @param array $allowExtensionsArray
 * @return array{caption: array|string, photo: string}
 */
#[ArrayShape(shape: ["caption" => "array|string|string[]", "photo" => "string"])]
function getRandomPhoto(string $catName, array $allowExtensionsArray): array
{
    $files = [];
    foreach (scandir(__DIR__ . "/cats/$catName") as $file) {
        if (in_array(mb_strtolower(pathinfo($file)["extension"]), $allowExtensionsArray)) {
            $files[] = $file;
        }
    }
    $randomPhoto = $files[rand(0, count($files) - 1)];
    $caption = pathinfo($randomPhoto, PATHINFO_FILENAME);
    $photo = __DIR__ . "/cats/$catName/$randomPhoto";
    return [
        "caption" => $caption,
        "photo" => $photo
    ];
}

/**
 * @param string $text
 * @param Telegram $telegram
 * @param string $callbackQueryId
 * @return void
 * @throws ConnException
 */
function sendReaction(string $text, Telegram $telegram, string $callbackQueryId): void
{
    $reactions = [
        "like" => "Вам нравится это фото 😊",
        "unlike" => "Вам не нравится это фото 😢"
    ];

    $telegram->getAnswerCallbackQuery($reactions[$text], $callbackQueryId);
}

/**
 * @param string $text
 * @param FromDto $from
 * @param Telegram $telegram
 * @param array $config
 * @param array $defaultKeyboard
 * @return void
 * @throws ConnException
 */
function sendReactionToAdmin(string $text, FromDto $from, Telegram $telegram, array $config, array $defaultKeyboard): void
{
    $reactions = [
        "like" => "ставит 👍 показаному фото",
        "unlike" => "ставит 👎 показаному фото"
    ];

    if (!in_array($from->id, $config["adminChatIds"])) {
        foreach ($config["adminChatIds"] as $oneAdminChatId) {
            $notifyForAdmin = "$from->firstName $from->lastName $reactions[$text]";
            $telegram->sendMessage($notifyForAdmin, $oneAdminChatId, json_encode($defaultKeyboard));
        }
    }
}

/**
 * @param array $allowExtensionsArray
 * @param array $cats
 * @return array{caption: \array|string|string[], photo: string}
 */
#[ArrayShape(shape: ["caption" => "\array|string|string[]", "photo" => "string"])]
function getImageForDailyNotification(array $allowExtensionsArray, array $cats): array
{
    $catNamesArray = [];
    foreach ($cats as $catName) {
        $catNamesArray[] = $catName["en_nom"];
    }
    $randomCatName = $catNamesArray[rand(0, count($catNamesArray) - 1)];
    return getRandomPhoto($randomCatName, $allowExtensionsArray);
}