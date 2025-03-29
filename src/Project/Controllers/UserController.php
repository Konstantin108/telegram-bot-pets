<?php

namespace Project\Controllers;

use Project\Dto\Telegram\Request\InputDataDto;
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

    //TODO почему нет метода update
    // дорабоотать на случай если админов будет несколько

    /**
     * @param InputDataDto $inputDataDto
     * @return void
     * @throws TypeErrorException
     */
    public function store(InputDataDto $inputDataDto): void
    {
        try {
            if (is_null($user = User::first("chat_id", $inputDataDto->from->id))) {
                $user = new User();
                $user->setChatId($inputDataDto->from->id);
            }
            $user->setIsBot($inputDataDto->from->isBot);
            $user->setFirstName($inputDataDto->from->firstName);
            $user->setLastName($inputDataDto->from->lastName);
            $user->setUsername($inputDataDto->from->username);
            $user->setIsAdmin(in_array($inputDataDto->from->id, $this->adminChatIds));
            $user->setStatus($inputDataDto->status);
            $user->setLanguageCode($inputDataDto->from->languageCode);
            //TODO для обновления записи нужен отдельный метод
            $user->setUpdatedAt(date("Y-m-d H:i:s"));

            $user->save();

        } catch (DbException $exception) {
            $exception->show();
        } catch (TypeError $exception) {
            throw new TypeErrorException($exception->getMessage());
        }
    }
}