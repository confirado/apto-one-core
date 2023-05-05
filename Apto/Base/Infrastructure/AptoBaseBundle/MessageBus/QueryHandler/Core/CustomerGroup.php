<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\CustomerGroup\CustomerGroupQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CustomerGroup extends CustomerGroupQueryHandler implements MessageSubscriberInterface
{

}