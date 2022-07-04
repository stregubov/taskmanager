<?php

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Services\NotificationService;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AfterEntityPersistedSubscriber implements EventSubscriberInterface
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function onAfterEntityPersist(AfterEntityPersistedEvent $event): void
    {
        $entityInstance = $event->getEntityInstance();

        if ($entityInstance instanceof Task) {
            $this->notificationService->sendNewTaskNotification($entityInstance);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'onAfterEntityPersist',
        ];
    }
}
