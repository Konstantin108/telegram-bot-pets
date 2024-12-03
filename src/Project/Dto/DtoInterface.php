<?php

namespace Project\Dto;

interface DtoInterface
{
    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param array $data
     * @return mixed
     */
    public static function fromArray(array $data): mixed;
}