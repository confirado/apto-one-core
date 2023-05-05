<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\DomainEvent\DomainEventQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class DomainEvent extends DomainEventQueryHandler implements MessageSubscriberInterface
{

}