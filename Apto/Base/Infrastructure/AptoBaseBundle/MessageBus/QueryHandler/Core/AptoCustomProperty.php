<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Core;

use Apto\Base\Application\Core\Query\AptoCustomProperty\CustomPropertyQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AptoCustomProperty extends CustomPropertyQueryHandler implements MessageSubscriberInterface
{

}