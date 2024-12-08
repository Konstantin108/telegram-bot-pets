<?php

namespace Project\Request;

class Request
{
    protected array|null $data = null;

    public function __construct()
    {
//        $this->data = json_decode(file_get_contents("../../bots/pets/msg2.json"), true);
        if (!is_null($input = file_get_contents("php://input"))) {
            $this->data = json_decode($input, true);
        }
    }
}