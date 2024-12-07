<?php

namespace Project\Controllers;

use Project\Dto\Telegram\FromDto;
use Project\Dto\Telegram\MessageDto;
use Project\Enums\User\UserStatusEnum;
use Project\Exceptions\DbException;
use Project\Exceptions\TypeErrorException;
use Project\Models\Users\User;
use TypeError;

class UserController
{
    private array $adminChatIds;

    public function __construct()
    {
        $this->adminChatIds = (require __DIR__ . "/../../config.php")["bots"]["pets"]["adminChatIds"];
    }

    /**
     * @param MessageDto $messageDto
     * @return void
     * @throws TypeErrorException
     */
    public function writeUserDataToDB(MessageDto $messageDto): void
    {
        try {
            if (is_null($user = User::where("chat_id", $messageDto->from->id))) {
                $user = new User();
                $user->setChatId($messageDto->from->id);
            }
            $user->setIsBot($messageDto->from->isBot);
            $user->setFirstName($messageDto->from->firstName);
            $user->setLastName($messageDto->from->lastName);
            $user->setUsername($messageDto->from->username);
            $user->setIsAdmin(in_array($messageDto->from->id, $this->adminChatIds));
            $user->setStatus($messageDto->status);
            $user->setLanguageCode($messageDto->from->languageCode);

            $user->save();

        } catch (DbException $e) {
            $e->showError();
        } catch (TypeError $e) {
            throw new TypeErrorException($e->getMessage());
        }
    }
}