<?php

namespace Project\Request;

use Project\Dto\Request\QueryParamsDto;
use Project\Dto\Telegram\Request\RequestDto;

class InputDataResolver
{
    protected const bool WITH_RAW = false;
    protected string|null $input;
    protected array $get;

    public function __construct()
    {
        $this->input = file_get_contents("php://input");
//        $this->input = file_get_contents("../../bots/pets/msg.json");
        $this->get = $_GET;
    }

    /**
     * @return RequestDto|null
     */
    public function resolveInputData(): ?RequestDto
    {
        if (mb_strlen($this->input) <= 0) {
            return null;
        }

        $data = json_decode($this->input, true);
        if (self::WITH_RAW) {
            $data["raw_input"] = $this->input;
        }

        return RequestDto::fromArray($data);
    }

    /**
     * @return QueryParamsDto|null
     */
    public function resolveQueryParams(): ?QueryParamsDto
    {
        return count($this->get) > 0
            ? new QueryParamsDto($this->get)
            : null;
    }
}