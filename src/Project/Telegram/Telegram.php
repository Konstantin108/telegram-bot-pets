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
            $this->sendMessageEndpoint()
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
            $this->sendPhotoEndpoint()
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
        $this->send(
            [
                "chat_id" => $chatId,
                "action" => $action
            ],
            $this->sendChatActionEndpoint()
        );
    }

    /**
     * @param string $text
     * @param string $callbackQueryId
     * @return void
     * @throws ConnException
     */
    public function answerCallbackQuery(string $text, string $callbackQueryId): void
    {
        $this->send(
            [
                "text" => $text,
                "callback_query_id" => $callbackQueryId
            ],
            $this->answerCallbackQueryEndpoint()
        );
    }

    /**
     * @return string
     */
    private function mainEndpoint(): string
    {
        return sprintf("%s/bot%s", $this->url, $this->token);
    }

    /**
     * @return string
     */
    private function sendMessageEndpoint(): string
    {
        return $this->mainEndpoint() . "/sendMessage";
    }

    /**
     * @return string
     */
    private function sendPhotoEndpoint(): string
    {
        return $this->mainEndpoint() . "/sendPhoto";
    }

    /**
     * @return string
     */
    private function sendChatActionEndpoint(): string
    {
        return $this->mainEndpoint() . "/sendChatAction";
    }

    /**
     * @return string
     */
    private function answerCallbackQueryEndpoint(): string
    {
        return $this->mainEndpoint() . "/answerCallbackQuery";
    }

    /**
     * @param array $data
     * @param string $endpoint
     * @return void
     * @throws ConnException
     */
    private function send(array $data, string $endpoint): void
    {
        try {
            $responseDto = ResponseDto::fromArray((new Conn(url: $endpoint))->post($data));

            if (!is_null($responseDto->errorCode)) {
                if ($responseDto->errorCode->isBlocked()) {
                    $user = User::where("chat_id", $data["chat_id"]);
                    $user->setStatus(UserStatusEnum::KICKED);
                    $user->save();
                }

                $endpointArray = explode("/", $endpoint);
                $method = end($endpointArray);

                throw new TelegramException(
                    errorMessage: print_r(new LogDataDto(
                        response: $responseDto,
                        messageData: $data,
                        method: $method
                    ), true)
                );
            }

        } catch (TelegramException|DbException $e) {
            $e->show();
        }
    }
}