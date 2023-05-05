<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Apto\Base\Application\Core\EventBusInterface;
use Apto\Base\Application\Core\EventInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus implements EventBusInterface
{
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @see https://symfony.com/doc/5.4/messenger/multiple_buses.html
     * name $eventBus is important because symfony will autowire the correct bus by naming conventions
     *
     * @param MessageBusInterface $eventBus
     */
    public function __construct(MessageBusInterface $eventBus)
    {
        $this->messageBus = $eventBus;
    }

    /**
     * @param EventInterface $message
     * @return void
     */
    public function handle(EventInterface $message)
    {
        $this->messageBus->dispatch($message);
    }
}