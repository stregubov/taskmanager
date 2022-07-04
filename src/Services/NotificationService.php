<?php
/**
 * User: Svyatoslav Tregubov
 * DateTime: 04.07.2022, 21:38
 * Company: Asteq
 */

namespace App\Services;

use App\Entity\Task;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotificationService
{
    public function __construct(private NotifierInterface $notifier)
    {
    }

    public function sendNewTaskNotification(Task $task): void
    {
        $this->notifier->send((new Notification('Задача успешно добавлена!',
            ['browser']))->emoji('money'));

        $this->notifier->send((new Notification('Вам поступила новая задача!',
            ['email']))->content('Вам поступила новая задача!'), new Recipient($task->getResponsible()->getEmail()));
    }
}