<?php

namespace Project\Request;

class Request
{
    protected string|null $input = null;

    public function __construct()
    {
//        if (mb_strlen($input = file_get_contents("../../bots/pets/msg.json")) > 0) {
        if (mb_strlen($input = file_get_contents("php://input")) > 0) {
            $this->input = $input;
        }
    }
}