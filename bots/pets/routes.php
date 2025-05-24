<?php

use Project\Controllers\Pets\MessageController;
use Project\Controllers\Pets\NotificationController;
use Project\Routing\Route;

return [
    Route::post("/start", [MessageController::class, "startBot"]),
    Route::post("обо мне", [MessageController::class, "aboutBot"]),

    //TODO как-нибудь переработаать, чтобы если передан несуществующий mode, то не надо переводить
    // на метод use_buttons
    // нужно добавить json_validate() и кастомную валидацию

    Route::get("test_notification", [NotificationController::class, "notifyTestMembers"]),
    Route::get("daily_notification", [NotificationController::class, "notifyDaily"]),
];

//TODO возможно использовать __call() если идет вызов несуществующего метода