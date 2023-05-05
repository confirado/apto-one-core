<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus;

use Apto\Base\Application\Core\Query\MessageBusMessage\MessageBusMessageFinder as MessageBusMessageFinderInterface;

class MessageBusMessageFinder implements MessageBusMessageFinderInterface
{
    /**
     * @var MessageBusManager
     */
    private $messageBusManager;

    /**
     * MessageBusMessageFinder constructor.
     * @param MessageBusManager $messageBusManager
     */
    public function __construct(MessageBusManager $messageBusManager)
    {
        $this->messageBusManager = $messageBusManager;
    }

    /**
     * @return array
     */
    public function findMessages()
    {
        return [
            'commands' => $this->messageBusManager->getRegisteredCommands(),
            'queries' => $this->messageBusManager->getRegisteredQueries()
        ];
    }
}