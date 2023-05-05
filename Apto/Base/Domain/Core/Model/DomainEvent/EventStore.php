<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

interface EventStore
{
    /**
     * @param DomainEvent $aDomainEvent
     */
    public function append(DomainEvent $aDomainEvent);

    /**
     * @param $anEventId
     * @return mixed
     */
    public function allStoredEventsSince($anEventId);
}