<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\MessageBusMessage\FindMessageBusMessagesHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class MessageBusMessages extends FindMessageBusMessagesHandler implements MessageSubscriberInterface
{

}