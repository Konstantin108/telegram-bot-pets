<?php

namespace Project\Services\Pets;

use JetBrains\PhpStorm\ArrayShape;
use Project\Configuration\Config;
use Project\Exceptions\ConnException;
use Project\Exceptions\DbException;
use Project\Keyboards\Pets\Keyboard;
use Project\Models\Users\User;
use Project\Scopes\MembersWithNotificationScope;
use Project\Scopes\TestMembersScope;
use Project\Telegram\Telegram;

class NotificationService
{
    private Telegram $telegram;

    public function __construct()
    {
        $this->telegram = new Telegram(Config::get("telegram.bots.pets.token"));
    }

    /**
     * @return void
     * @throws ConnException
     * @throws DbException
     */
    public function notifyTestMembers(): void
    {
        $config = (include __DIR__ . "/../../../config.php")["bots"]["pets"];
        $allowExtensionsArray = $config["allowExtensionsArray"];
        $cats = $config["cats"];

        //TODO исправить все конкатенации
        // все должнол быть вынесено в сервисы
        // надо избавиться от include и require

        if (count($users = User::scoped(new TestMembersScope())) > 0) {
            $dailyPhotoData = $this->getImageForDailyNotification($allowExtensionsArray, $cats);
            foreach ($users as $user) {
                /** @var User $user */
                $dailyNotifyMessage = "Скучаешь, {$user->getFirstName()}? Вот полюбуйся!";
                $this->telegram->sendMessage($dailyNotifyMessage, $user->getChatId(), Keyboard::DEFAULT);
                $this->showCatImage($user->getChatId(), $dailyPhotoData, Keyboard::INLINE);
            }
        }
    }

    //TODO необходимо вынести методы формирования сообщений с картинкой в MessageService

    /**
     * @return void
     * @throws ConnException
     * @throws DbException
     */
    public function notifyDaily(): void
    {
        $config = (include __DIR__ . "/../../../config.php")["bots"]["pets"];
        $allowExtensionsArray = $config["allowExtensionsArray"];
        $cats = $config["cats"];

        if (count($users = User::scoped(new MembersWithNotificationScope())) > 0) {
            $dailyPhotoData = $this->getImageForDailyNotification($allowExtensionsArray, $cats);
            foreach ($users as $user) {
                /** @var User $user */
                $dailyNotifyMessage = "Скучаешь, {$user->getFirstName()}? Вот полюбуйся!";
                $this->telegram->sendMessage($dailyNotifyMessage, $user->getChatId(), Keyboard::DEFAULT);
                //TODO возможно методы надо изменить, принимают слишком много параметров
                $this->showCatImage($user->getChatId(), $dailyPhotoData, Keyboard::INLINE);
            }
        }
    }

    /**
     * @param array $allowExtensionsArray
     * @param array $cats
     * @return array{caption: \array|string|string[], photo: string}
     */
    #[ArrayShape(shape: ["caption" => "\array|string|string[]", "photo" => "string"])]
    private function getImageForDailyNotification(array $allowExtensionsArray, array $cats): array
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
    private function getRandomPhoto(string $catName, array $allowExtensionsArray): array
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
     * @param array $photoData
     * @param array $replyMarkup
     * @return void
     * @throws ConnException
     */
    private function showCatImage(string $chatId, array $photoData, array $replyMarkup): void
    {
        $this->telegram->sendPhoto($photoData, $chatId, $replyMarkup);
    }
}