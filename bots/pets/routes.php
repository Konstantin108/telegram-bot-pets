<?php

use Project\Controllers\Pets\MessageController;
use Project\Controllers\Pets\NotificationController;
use Project\Router\Route;

return [
    Route::post("/start", [MessageController::class, "startBot"]),
    Route::post("test_notification", [NotificationController::class, "notifyTestMembers"]),
];