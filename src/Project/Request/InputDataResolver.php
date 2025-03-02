<?php

namespace Project\Request;

use Project\Dto\Request\QueryParamsDto;
use Project\Dto\RequestDto;
use Project\Dto\Telegram\Request\InputDataDto;
use Project\Enums\Router\RouteEnum;

class InputDataResolver
{
    protected const bool WITH_RAW = false;
    protected const string DEFAULT_ROUTE = "test_notification";
    protected string|null $input;
    protected array $get;

    public function __construct()
    {
        $this->input = file_get_contents("php://input");
//        $this->input = file_get_contents("../../bots/pets/msg.json");
        $this->get = $_GET;
    }

    /**
     * @return RequestDto
     */
    public function data(): RequestDto
    {
        return new RequestDto(
            route: $this->route(),
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
        $requestRoute = $this->resolveQueryParams()->params["mode"]
            ?? $this->resolveInputData()?->text
            ?? self::DEFAULT_ROUTE;

        foreach (RouteEnum::cases() as $case) {
            if ($case->value === $requestRoute) {
                return $case->name();
            }
        }

        return RouteEnum::USE_BUTTONS->name();
    }
}