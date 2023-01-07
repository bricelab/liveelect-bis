<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\AdminUser;
use App\Entity\Scrutin;
use App\Entity\SuperviseurArrondissement;
use DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $encoder,
        private readonly Security $security,
    ) {
    }

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

        if ($entity instanceof AdminUser) {
            if ($entity->getPlainPassword()) {
                $entity->setPassword($this->encoder->hashPassword($entity, $entity->getPlainPassword()));
                $entity->eraseCredentials();
            }
        }

        if ($entity instanceof SuperviseurArrondissement) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                /** @var AdminUser $user */
                $user = $this->security->getUser();
                $entity->setScrutin($user->getScrutin());
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

        if ($entity instanceof AdminUser) {
            if ($entity->getPlainPassword()) {
                $entity->setPassword($this->encoder->hashPassword($entity, $entity->getPlainPassword()));
                $entity->eraseCredentials();
            }
        }

        if ($entity instanceof SuperviseurArrondissement) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                /** @var AdminUser $user */
                $user = $this->security->getUser();
                $entity->setScrutin($user->getScrutin());
            }
        }
    }
}
