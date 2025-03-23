<?php

namespace Project\Keyboards\Pets;

class Keyboard
{
    public const array DEFAULT = [
        "keyboard" => [
            [
                ["text" => "Обо мне"],
                ["text" => "Список команд"]
            ],
            [
                ["text" => "Курага"],
                ["text" => "Ватсон"],
                ["text" => "Василиса"]
            ]
        ],
        "resize_keyboard" => true
    ];

    public const array INLINE = [
        "inline_keyboard" => [
            [
                [
                    "text" => "👍",
                    "callback_data" => "like"
                ],
                [
                    "text" => "👎",
                    "callback_data" => "unlike"
                ]
            ]
        ]
    ];
}