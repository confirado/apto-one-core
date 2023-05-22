<?php

namespace Apto\Base\Domain\Core\Model\DomainEvent;

use BadMethodCallException;

class DomainEventPublisher
{
    /**
     * @var array
     */
    private $subscribers;

    /**
     * @var DomainEventPublisher|null
     */
    private static $instance = null;

    /**
     * @return DomainEventPublisher
     */
    public static function instance(): DomainEventPublisher
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * DomainEventPublisher constructor.
     */
    private function __construct()
    {
        $this->subscribers = [];
    }

    /**
     * @throws BadMethodCallException
     */
    public function __clone()
    {
        throw new BadMethodCallException('Clone is not supported');
    }

    /**
     * @param DomainEventSubscriber $aDomainEventSubscriber
     */
    public function subscribe(DomainEventSubscriber $aDomainEventSubscriber)
    {
        $this->subscribers[] = $aDomainEventSubscriber;
    }

    /**
     * @param DomainEvent $anEvent
     */
    public function publish(DomainEvent $anEvent)
    {
        foreach ($this->subscribers as $aSubscriber) {
            if ($aSubscriber->isSubscribedTo($anEvent)) {
                $aSubscriber->handle($anEvent);
            }
        }
    }
}
