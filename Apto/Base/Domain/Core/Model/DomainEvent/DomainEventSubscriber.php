<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

interface DomainEventSubscriber
{
    /**
     * @param DomainEvent $aDomainEvent
     */
    public function handle(DomainEvent $aDomainEvent);

    /**
     * @param DomainEvent $aDomainEvent
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $aDomainEvent);
}