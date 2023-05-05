<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\MessageBus\CommandHandler\Backend;

use Apto\Base\Application\Backend\Commands\AclEntry\AclEntryHandler;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class AclEntry extends AclEntryHandler implements MessageSubscriberInterface
{

}