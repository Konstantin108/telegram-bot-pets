<?php

use Project\Controllers\Pets\MessageController;
use Project\Controllers\Pets\NotificationController;
use Project\Routing\Route;

return [
    Route::post("/start", [MessageController::class, "startBot"]),
    Route::get("test_notification", [NotificationController::class, "notifyTestMembers"]),
];