<?php
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

//ini_set("display_errors", 0);
//ini_set("log_errors", 1);
//error_reporting(E_ALL);
//ini_set("error_log", "errors.log");

use JetBrains\PhpStorm\ArrayShape;
use Project\Controllers\UserController;
use Project\Dto\Telegram\Request\FromDto;
use Project\Exceptions\AccessModifiersException;
use Project\Exceptions\ConnException;
use Project\Exceptions\DbException;
use Project\Exceptions\TypeErrorException;
use Project\Models\Users\User;
use Project\Request\Request;
use Project\Scopes\MembersWithNotificationScope;
use Project\Scopes\TestMembersScope;
use Project\Telegram\Telegram;

spl_autoload_register(function (string $className): void {
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

$inputDataDto = (new Request())->getData()->inputDataDto;

try {
    if (!is_null($inputDataDto)) {
        $from = $inputDataDto->from;

        try {
            (new UserController())->store($inputDataDto);
        } catch (TypeErrorException $exception) {
            $exception->show();
        }

        if (is_null($inputDataDto->text)) {
            return;
        }

        switch ($inputDataDto->text) {
            case "/start":
                $telegram->sendMessage("Бот активирован", $inputDataDto->from->id, $defaultKeyboard);
                break;
            case "обо мне":
                aboutBot($inputDataDto->from->id, $telegram, $defaultKeyboard);
                break;
            case "список команд":
                commandsList($inputDataDto->from, $telegram, $defaultKeyboard);
                break;
            case "курага":
            case "ватсон":
            case "василиса":
                $telegram->sendChatAction($inputDataDto->from->id, "upload_photo");
                $photoData = getRandomPhoto($cats[$inputDataDto->text]["en_nom"], $allowExtensionsArray);
                showCatImage($inputDataDto->from->id, $telegram, $photoData);

                if (!in_array($inputDataDto->from->id, $config["adminChatIds"])) {
                    foreach ($config["adminChatIds"] as $oneAdminChatId) {
                        $notifyForAdmin = "$from->firstName $from->lastName сейчас любуется {$cats[$inputDataDto->text]["ru_ins"]}"
                            . "\nПоказано это замечательное фото 🤩";

                        $telegram->sendMessage($notifyForAdmin, $oneAdminChatId, $defaultKeyboard);
                        showCatImage($oneAdminChatId, $telegram, $photoData, $defaultKeyboard);
                    }
                }
                break;
            // callback действия
            case "like":
            case "unlike":
                sendReaction($inputDataDto->text, $telegram, $inputDataDto->callbackId);
                sendReactionToAdmin($inputDataDto->text, $from, $telegram, $config, $defaultKeyboard);
                break;
            default:
                $telegram->sendMessage("Используй кнопки с командами", $from->id, $defaultKeyboard);
                break;
        }
    } else {
        // массовое уведомление
        if (count($users = User::scoped(new TestMembersScope())) > 0) {
//        if (count($users = User::scoped(new MembersWithNotificationScope())) > 0) {
            $dailyPhotoData = getImageForDailyNotification($allowExtensionsArray, $cats);
            foreach ($users as $user) {
                /** @var User $user */
                $dailyNotifyMessage = "Скучаешь, {$user->getFirstName()}? Вот полюбуйся!";
                $telegram->sendMessage($dailyNotifyMessage, $user->getChatId(), $defaultKeyboard);
                showCatImage($user->getChatId(), $telegram, $dailyPhotoData);
            }
        }
    }

    //TODO возможно неправильно ловлю исключения
} catch (ConnException|DbException|AccessModifiersException $exception) {
    $exception->show();
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
    $telegram->sendMessage($text, $chatId, $replyMarkup);
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

    $telegram->sendMessage($text, $from->id, $replyMarkup);
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

    $telegram->sendPhoto($photoData, $chatId, $replyMarkup);
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

    $telegram->answerCallbackQuery($reactions[$text], $callbackQueryId);
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

    //TODO полностью переделать, надо использовать скоп
    if (!in_array($from->id, $config["adminChatIds"])) {
        foreach ($config["adminChatIds"] as $oneAdminChatId) {
            $notifyForAdmin = "$from->firstName $from->lastName $reactions[$text]";
            $telegram->sendMessage($notifyForAdmin, $oneAdminChatId, $defaultKeyboard);
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
