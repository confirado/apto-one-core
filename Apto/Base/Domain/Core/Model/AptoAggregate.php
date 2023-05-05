<?php

namespace Apto\Base\Domain\Core\Model;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;

abstract class AptoAggregate extends AptoEventCapableEntity
{
    /**
     * publishEvents
     */
    public function publishEvents()
    {
        foreach ($this->eventsToPublish as $eventToPublish) {
            DomainEventPublisher::instance()->publish($eventToPublish);
        }
    }
}