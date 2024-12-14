<?php

namespace Project\Dto\Telegram\Response;

use JetBrains\PhpStorm\ArrayShape;
use Project\Dto\DtoInterface;
use Project\Enums\Telegram\ErrorCodeEnum;

class ResponseDto implements DtoInterface
{
    /**
     * @param bool $ok
     * @param ErrorCodeEnum|null $errorCode
     * @param string|null $description
     */
    private function __construct(
        public bool           $ok,
        public ?ErrorCodeEnum $errorCode,
        public ?string        $description
    )
    {
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