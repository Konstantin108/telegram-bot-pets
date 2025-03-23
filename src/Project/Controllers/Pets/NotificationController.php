<?php

namespace Project\Controllers\Pets;

use Project\Exceptions\ConnException;
use Project\Exceptions\DbException;
use Project\Services\Pets\NotificationService;

class NotificationController
{
    private NotificationService $notificationService;

    //TODO надо полностью переделать выброс исключений

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    //TODO реализовать DI
    // почему нет анимации отправки картинки

    /**
     * @return void
     * @throws ConnException
     * @throws DbException
     */
    public function notifyTestMembers(): void
    {
        $this->notificationService->notifyTestMembers();
    }

    /**
     * @return void
     * @throws ConnException
     * @throws DbException
     */
    public function notifyDaily(): void
    {
        $this->notificationService->notifyDaily();
    }
}