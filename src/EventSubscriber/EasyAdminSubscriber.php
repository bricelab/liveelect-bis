<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Scrutin;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EasyAdminSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setScrutinPublishedAtBeforePersist'],
            BeforeEntityUpdatedEvent::class => ['setScrutinPublishedAtBeforeUpdate'],
        ];
    }

    public function setScrutinPublishedAtBeforePersist(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Scrutin) {
            if ($entity->isPublished()) {
                $entity->setPublishedAt(new DateTimeImmutable());
            }
        }
    }

    public function setScrutinPublishedAtBeforeUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Scrutin) {
            if ($entity->isPublished()) {
                $entity->setPublishedAt(new DateTimeImmutable());
            } else {
                $entity->setPublishedAt(null);
            }
        }
    }
}
