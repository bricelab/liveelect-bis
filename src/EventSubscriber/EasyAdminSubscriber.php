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
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setScrutinPublishedAtBeforePersist'],
            BeforeEntityUpdatedEvent::class => ['setScrutinPublishedAtBeforeUpdate'],
        ];
    }

    public function setScrutinPublishedAtBeforePersist(BeforeEntityPersistedEvent $event): void
    {
        $scrutin = $event->getEntityInstance();

        if ($scrutin instanceof Scrutin) {
            if ($scrutin->isPublished()) {
                $scrutin->setPublishedAt(new DateTimeImmutable());
            }
        }
    }

    public function setScrutinPublishedAtBeforeUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $scrutin = $event->getEntityInstance();

        if ($scrutin instanceof Scrutin) {
            if ($scrutin->isPublished()) {
                $scrutin->setPublishedAt(new DateTimeImmutable());
            } else {
                $scrutin->setPublishedAt(null);
            }
        }
    }
}
