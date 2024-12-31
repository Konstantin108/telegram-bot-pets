<?php

namespace Project\Controllers;

use Project\Dto\Telegram\Request\RequestDto;
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

    /**
     * @param RequestDto $requestDto
     * @return void
     * @throws TypeErrorException
     */
    public function store(RequestDto $requestDto): void
    {
        try {
            if (is_null($user = User::where("chat_id", $requestDto->from->id))) {
                $user = new User();
                $user->setChatId($requestDto->from->id);
            }
            $user->setIsBot($requestDto->from->isBot);
            $user->setFirstName($requestDto->from->firstName);
            $user->setLastName($requestDto->from->lastName);
            $user->setUsername($requestDto->from->username);
            $user->setIsAdmin(in_array($requestDto->from->id, $this->adminChatIds));
            $user->setStatus($requestDto->status);
            $user->setLanguageCode($requestDto->from->languageCode);
            $user->setUpdatedAt(date("Y-m-d H:i:s"));

            $user->save();

        } catch (DbException $e) {
            $e->show();
        } catch (TypeError $e) {
            throw new TypeErrorException($e->getMessage());
        }
    }
}