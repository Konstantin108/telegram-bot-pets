<?php

use Project\Controllers\Pets\MessageController;
use Project\Router\Route;

return [
    Route::setRoute("курага", [MessageController::class, "showCatKuragaImage"])
];