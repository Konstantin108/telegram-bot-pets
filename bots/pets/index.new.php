<?php
//TODO так же возможно все это надо будет вынести
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

//ini_set("display_errors", 0);
//ini_set("log_errors", 1);
//error_reporting(E_ALL);
//ini_set("error_log", "errors.log");

use Project\Routing\Route;
use Project\Routing\Router;
use Project\Controllers\Pets\MessageController;

//TODO надо избавиться от двойных скобок
// надо раньше отлавливать исключения из ActiveRecordEntity

spl_autoload_register(function (string $className): void {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . "/../../src/$className.php";
});

//TODO тут нужно будет отлавливать исключения
// продумать на какие контроллеры поделить логику
// - MessageController
// - CallbackQueryController
// - MyChatMemberController
// - AdminActionController
// - NotificationController

$routes = require_once __DIR__ . "/routes.php";
$anyInputTextRoute = Route::post("use_buttons", [MessageController::class, "useButtonsMessage"]);

(new Router($routes, $anyInputTextRoute))->routing();