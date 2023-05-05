<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\DomainEvent;

use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventPublisher;
use Apto\Base\Domain\Core\Model\DomainEvent\DomainEventSubscriber;

class DomainEventSubscriberFactory
{
    public function addSubscriber(DomainEventSubscriber $aDomainEventSubscriber)
    {
        DomainEventPublisher::instance()->subscribe($aDomainEventSubscriber);
    }
}