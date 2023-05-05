<?php

namespace Apto\Base\Application\Core\Query\MessageBusMessage;

use Apto\Base\Application\Core\QueryHandlerInterface;

class FindMessageBusMessagesHandler implements QueryHandlerInterface
{
    /**
     * @var MessageBusMessageFinder
     */
    private $messageBusMessageFinder;

    /**
     * FindMessageBusMessagesHandler constructor.
     * @param MessageBusMessageFinder $messageBusMessageFinder
     */
    public function __construct(MessageBusMessageFinder $messageBusMessageFinder)
    {
        $this->messageBusMessageFinder = $messageBusMessageFinder;
    }

    /**
     * @param FindMessageBusMessages $query
     * @return mixed
     */
    public function handle(FindMessageBusMessages $query)
    {
        return $this->messageBusMessageFinder->findMessages();
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        yield FindMessageBusMessages::class => [
            'method' => 'handle',
            'bus' => 'query_bus'
        ];
    }
}