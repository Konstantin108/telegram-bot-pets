<?php

namespace Project\Controllers\Pets;

use JetBrains\PhpStorm\ArrayShape;
use Project\Configuration\Config;
use Project\Models\Users\User;
use Project\Scopes\TestMembersScope;
use Project\Telegram\Telegram;

class NotificationController
{
    public Telegram $telegram;

    //TODO надо полностью переделать выброс исключений

    public function __construct()
    {
        //TODO сначало помещать значения в config и потом оттуда доставать, а не сразу из .env
        // надо разделить конфиги на разные файлы
        $this->telegram = new Telegram(Config::get("telegram.bots.pets.token"));
    }

    //TODO возможно контроллеры, методы которых отслылают сообщения в бота надо будет
    // наследовать от BaseController
    // реализовать DI
    // надо будет выносить логику из контроллеров в сервисы Message, Notification и т.д.

    public function notifyTestMembers(): void
    {
        $config = (include __DIR__ . "/../../../config.php")["bots"]["pets"];
        $allowExtensionsArray = $config["allowExtensionsArray"];
        $cats = $config["cats"];

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

        //TODO исправить все конкатенации

        if (count($users = User::filter(new TestMembersScope())) > 0) {
            $dailyPhotoData = $this->getImageForDailyNotification($allowExtensionsArray, $cats);
            foreach ($users as $user) {
                /** @var User $user */
                $dailyNotifyMessage = "Скучаешь, {$user->getFirstName()}? Вот полюбуйся!";
                $this->telegram->sendMessage($dailyNotifyMessage, $user->getChatId(), json_encode($defaultKeyboard));
                $this->showCatImage($user->getChatId(), $this->telegram, $dailyPhotoData);
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
        return $this->getRandomPhoto($randomCatName, $allowExtensionsArray);
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
        foreach (scandir(__DIR__ . "/../../../../bots/pets/cats/$catName") as $file) {
            if (in_array(mb_strtolower(pathinfo($file)["extension"]), $allowExtensionsArray)) {
                $files[] = $file;
            }
        }
        $randomPhoto = $files[rand(0, count($files) - 1)];
        $caption = pathinfo($randomPhoto, PATHINFO_FILENAME);
        $photo = __DIR__ . "/../../../../bots/pets/cats/$catName/$randomPhoto";
        return [
            "caption" => $caption,
            "photo" => $photo
        ];
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
}