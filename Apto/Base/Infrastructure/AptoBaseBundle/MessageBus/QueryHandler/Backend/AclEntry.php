<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\QueryHandler\Backend;

use Apto\Base\Application\Backend\Query\AclEntry\AclEntryQueryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AclEntry extends AclEntryQueryHandler implements MessageSubscriberInterface
{

}