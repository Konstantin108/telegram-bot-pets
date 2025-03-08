<?php

return [
    "url" => getenv("TELEGRAM_URL"),
    "bots" => [
        "pets" => [
            "token" => getenv("PETS_BOT_TOKEN"),
        ],
        "google_translate_bot" => [
            "token" => getenv("GOOGLE_TRANSLATE_BOT_TOKEN"),
            "google_api_url" => getenv("GOOGLE_API_URL"),
        ]
    ],
];