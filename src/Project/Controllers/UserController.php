<?php

namespace Project\Controllers;

use Project\Enums\User\UserStatusEnum;
use Project\Exceptions\DbException;
use Project\Exceptions\TypeErrorException;
use Project\Models\Users\User;
use stdClass;
use TypeError;

class UserController
{
    private array $adminChatIds;

    public function __construct()
    {
        $this->adminChatIds = (require __DIR__ . "/../../config.php")["bots"]["pets"]["adminChatIds"];
    }

    /**
     * @param stdClass $from
     * @param UserStatusEnum $status
     * @return void
     * @throws TypeErrorException
     */
    public function writeUserDataToDB(stdClass $from, UserStatusEnum $status): void
    {
        try {
            if (!$user = User::where("chat_id", $from->id)) {
                $user = new User();
                $user->setChatId($from->id);
            }
            $user->setIsBot($from->is_bot);
            $user->setFirstName($from->first_name);
            $user->setLastName($from->last_name);
            $user->setUsername($from->username);
            $user->setIsAdmin(in_array($from->id, $this->adminChatIds));
            $user->setStatus($status);
            $user->setLanguageCode($from->language_code);

            $user->save();

        } catch (DbException $e) {
            $e->showError();
        } catch (TypeError $e) {
            throw new TypeErrorException($e->getMessage());
        }
    }
}