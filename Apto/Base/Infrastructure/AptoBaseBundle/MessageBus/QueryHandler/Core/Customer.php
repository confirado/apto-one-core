<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\Customer\CustomerQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class Customer extends CustomerQueryHandler implements MessageSubscriberInterface
{

}