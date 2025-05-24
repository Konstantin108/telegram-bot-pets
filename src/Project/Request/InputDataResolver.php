<?php

namespace Project\Request;

use Project\Dto\Request\QueryParamsDto;
use Project\Dto\RequestDto;
use Project\Dto\Telegram\Request\InputDataDto;

class InputDataResolver
{
    protected const bool WITH_RAW = false;
    //TODO это параметр WITH_RAW можно вынести в env
    protected const string DEFAULT_ROUTE = "test_notification";
    protected string|null $input;
    protected array $get;
    protected string $method;

    public function __construct()
    {
        //TODO добавить валидацию через json_validate() и далее добавить свои правила для валидации
        $this->input = file_get_contents("php://input");
//        $this->input = file_get_contents("../../bots/pets/message.json");
        $this->get = $_GET;
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    /**
     * @return RequestDto
     */
    public function data(): RequestDto
    {
        return new RequestDto(
            route: $this->route(),
            method: strtolower($this->method),
            inputDataDto: $this->resolveInputData(),
            queryParamsDto: $this->resolveQueryParams()
        );
    }

    /**
     * @return InputDataDto|null
     */
    private function resolveInputData(): ?InputDataDto
    {
        if (mb_strlen($this->input) <= 0) {
            return null;
        }

        $data = json_decode($this->input, true);
        if (self::WITH_RAW) {
            $data["raw_input"] = $this->input;
        }

        return InputDataDto::fromArray($data);
    }

    /**
     * @return QueryParamsDto|null
     */
    private function resolveQueryParams(): ?QueryParamsDto
    {
        return count($this->get) > 0
            ? new QueryParamsDto($this->get)
            : null;
    }

    /**
     * @return string
     */
    private function route(): string
    {
        return $this->resolveQueryParams()->params["mode"]
            ?? $this->resolveInputData()?->text
            ?? self::DEFAULT_ROUTE;
    }
}