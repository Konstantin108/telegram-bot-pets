<?php

namespace Project\Telegram;

use Project\Dto\Telegram\Response\LogDataDto;
use Project\Dto\Telegram\Response\ResponseDto;
use Project\Enums\User\UserStatusEnum;
use Project\Exceptions\ConnException;
use Project\Exceptions\DbException;
use Project\Exceptions\TelegramException;
use Project\Models\Users\User;
use Project\Services\Conn;

class Telegram
{
    private string $url;
    private string $token;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->url = (require __DIR__ . "/../../config.php")["telegram"]["url"];
        $this->token = $token;
    }

    /**
     * @param string $text
     * @param string $chatId
     * @param string $replyMarkup
     * @return void
     * @throws ConnException
     */
    public function sendMessage(string $text, string $chatId, string $replyMarkup = ""): void
    {
        $this->send(
            [
                "chat_id" => $chatId,
                "text" => $text,
                "parse_mode" => "HTML",
                "reply_markup" => $replyMarkup
            ],
            "/sendMessage"
        );
    }

    /**
     * @param array $photoData
     * @param string $chatId
     * @param string $replyMarkup
     * @return void
     * @throws ConnException
     */
    public function sendPhoto(array $photoData, string $chatId, string $replyMarkup = ""): void
    {
        $this->send(
            [
                "chat_id" => $chatId,
                "photo" => curl_file_create($photoData["photo"]),
                "caption" => $photoData["caption"],
                "reply_markup" => $replyMarkup
            ],
            "/sendPhoto"
        );
    }

    /**
     * @param string $chatId
     * @param string $action
     * @return void
     * @throws ConnException
     */
    public function sendChatAction(string $chatId, string $action = "typing"): void
    {
        //TODO возможно вынести названия экшенов в константы
        $this->send(
            [
                "chat_id" => $chatId,
                "action" => $action
            ],
            "/sendChatAction"
        );
    }

    /**
     * @param string $text
     * @param string $callbackQueryId
     * @return void
     * @throws ConnException
     */
    public function getAnswerCallbackQuery(string $text, string $callbackQueryId): void
    {
        $this->send(
            [
                "text" => $text,
                "callback_query_id" => $callbackQueryId
            ],
            "/answerCallbackQuery"
        );
    }

    /**
     * @param array $data
     * @param string $method
     * @return void
     * @throws ConnException
     */
    private function send(array $data, string $method): void
    {
        try {
            //TODO не конкатенировать внутри метода
            // надо сделать методы, которые отдают целиком нужный url, вместе с токеном
            $url = $this->url . $this->token . $method;
            $responseDto = ResponseDto::fromArray((new Conn($url))->post($data));

            if (!is_null($responseDto->errorCode)) {
                if ($responseDto->errorCode->isBlocked()) {
                    $user = User::where("chat_id", $data["chat_id"]);
                    $user->setStatus(UserStatusEnum::KICKED);
                    $user->save();
                }

                throw new TelegramException(print_r(new LogDataDto($responseDto, $data, $method), true));
            }

        } catch (TelegramException|DbException $e) {
            $e->show();
        }
    }
}