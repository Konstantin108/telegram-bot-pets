<?php

namespace Project\Dto\Telegram\Response;

use JetBrains\PhpStorm\ArrayShape;
use Project\Enums\Telegram\ErrorCodeEnum;

class ResponseDto
{
    public bool $ok;
    public ErrorCodeEnum|null $errorCode;
    public string|null $description;

    /**
     * @param bool $ok
     * @param ErrorCodeEnum|null $errorCode
     * @param string|null $description
     */
    private function __construct(bool $ok, ?ErrorCodeEnum $errorCode, ?string $description)
    {
        $this->ok = $ok;
        $this->errorCode = $errorCode;
        $this->description = $description;
    }

    /**
     * @return array{ok: bool, error_code: int, description: null|string}
     */
    #[ArrayShape(shape: ["ok" => "bool", "error_code" => "int", "description" => "null|string"])]
    public function toArray(): array
    {
        return [
            "ok" => $this->ok,
            "error_code" => $this->errorCode->value,
            "description" => $this->description
        ];
    }

    /**
     * @param array $data
     * @return ResponseDto
     */
    public static function fromArray(array $data): ResponseDto
    {
        return new self(
            $data["ok"],
            isset($data["error_code"])
                ? ErrorCodeEnum::from($data["error_code"])
                : null,
            $data["description"] ?? null
        );
    }
}