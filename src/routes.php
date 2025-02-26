<?php

use Project\Controllers\Pets\MessageController;
use Project\Router\Route;

return [
    Route::post("курага", [MessageController::class, "showCatKuragaImage"])
];