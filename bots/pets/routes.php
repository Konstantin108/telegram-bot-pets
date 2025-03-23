<?php

use Project\Controllers\Pets\MessageController;
use Project\Controllers\Pets\NotificationController;
use Project\Routing\Route;

return [
    Route::post("/start", [MessageController::class, "startBot"]),
    Route::post("обо мне", [MessageController::class, "aboutBot"]),
    Route::post("список команд", [MessageController::class, "commandsList"]),

    Route::get("test_notification", [NotificationController::class, "notifyTestMembers"]),
    Route::get("daily_notification", [NotificationController::class, "notifyDaily"]),
];