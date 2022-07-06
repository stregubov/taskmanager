<?php
/**
 * User: Svyatoslav Tregubov
 * DateTime: 06.07.2022, 22:37
 * Company: Asteq
 */

namespace App\EventSubscriber;

use App\Entity\Task;
use App\Services\NotificationService;
use App\Services\TimeConverterService;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BeforePersistAndUpdateSubscriber implements EventSubscriberInterface
{
    public function __construct(private TimeConverterService $timeConverterService)
    {
    }

    public function onBeforeEntityPersist(AbstractLifecycleEvent $event): void
    {
        $entityInstance = $event->getEntityInstance();

        if ($entityInstance instanceof Task) {
            $timeExpression = $entityInstance->getSpenttime();
            $entityInstance->setSpentTimeHours($this->timeConverterService->convertToHours($timeExpression));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityPersist',
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersist',
        ];
    }
}