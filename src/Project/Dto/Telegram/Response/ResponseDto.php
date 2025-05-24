<?php

namespace Project\Dto\Telegram\Response;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;
use Project\Enums\Telegram\ErrorCodeEnum;
use Project\Interfaces\DtoInterface;

#[Immutable]
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
            ok: $data["ok"],
            errorCode: isset($data["error_code"])
                ? ErrorCodeEnum::from($data["error_code"])
                : null,
            description: $data["description"] ?? null
        );
    }
}