<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

class PersistDomainEventSubscriber implements DomainEventSubscriber
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * PersistDomainEventSubscriber constructor.
     * @param EventStore $anEventStore
     */
    public function __construct(EventStore $anEventStore)
    {
        $this->eventStore = $anEventStore;
    }

    /**
     * @param DomainEvent $aDomainEvent
     */
    public function handle(DomainEvent $aDomainEvent)
    {
        $this->eventStore->append($aDomainEvent);
    }

    /**
     * @param DomainEvent $aDomainEvent
     * @return bool
     */
    public function isSubscribedTo(DomainEvent $aDomainEvent)
    {
        return true;
    }
}